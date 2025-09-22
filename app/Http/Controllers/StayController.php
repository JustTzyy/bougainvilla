<?php

namespace App\Http\Controllers;

use App\Models\Stay;
use App\Models\Room;
use App\Models\Rate;
use App\Models\Guest;
use App\Models\GuestStay;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Address;
use App\Models\History;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StayController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get rooms for room dashboard
            $rooms = Room::with(['level', 'accommodations', 'stays' => function($query) {
                $query->where('status', 'Active')->where('checkOut', '>', now());
            }])
            ->where('status', '!=', 'Under Maintenance')
            ->orderBy('level_id')
            ->orderBy('room')
            ->get();

            // Get levels for filtering
            $levels = \App\Models\Level::where('status', 'Active')->get();
            
            return view('adminPages.transactions', compact('rooms', 'levels'));
        } catch (Exception $e) {
            \Log::error('Stay Controller Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load room dashboard: ' . $e->getMessage());
        }
    }

    public function getRoomDetails($id)
    {
        try {
            $room = Room::with(['level', 'accommodations.rates' => function($query) {
                $query->where('status', 'Active');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'room' => $room,
                'accommodations' => $room->accommodations
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load room details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRatesForAccommodation($accommodationId)
    {
        try {
            $rates = Rate::where('accommodation_id', $accommodationId)
                        ->where('status', 'Active')
                        ->get();

            return response()->json([
                'success' => true,
                'rates' => $rates
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load rates: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculateTotal(Request $request)
    {
        try {
            $request->validate([
                'rate_id' => 'required|exists:rates,id',
                'guest_count' => 'required|integer|min:1'
            ]);

            $rate = Rate::findOrFail($request->rate_id);
            $subtotal = $rate->price * $request->guest_count;
            $taxRate = 0.12; // 12% tax
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            return response()->json([
                'success' => true,
                'subtotal' => number_format($subtotal, 2),
                'tax' => number_format($tax, 2),
                'total' => number_format($total, 2),
                'tax_rate' => $taxRate
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate total: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processStay(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'rate_id' => 'required|exists:rates,id',
                'guests' => 'required|array|min:1',
                'guests.*.firstName' => 'required|string|max:255',
                'guests.*.lastName' => 'required|string|max:255',
                'guests.*.middleName' => 'nullable|string|max:255',
                'guests.*.number' => 'nullable|string|max:255',
                'guests.*.address' => 'required|array',
                'guests.*.address.street' => 'required|string|max:255',
                'guests.*.address.city' => 'required|string|max:255',
                'guests.*.address.province' => 'required|string|max:255',
                'guests.*.address.zipcode' => 'required|string|max:255',
                'payment_amount' => 'required|numeric|min:0',
                'payment_change' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Get rate details
            $rate = Rate::findOrFail($request->rate_id);
            $room = Room::findOrFail($request->room_id);
            
            // Calculate amounts
            $subtotal = $rate->price * count($request->guests);
            $taxRate = 0.12;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            // Create stay
            $stay = Stay::create([
                'checkIn' => now(),
                'checkOut' => now()->addHours($this->parseDurationToHours($rate->duration)),
                'status' => 'Active',
                'rateID' => $rate->id,
                'roomID' => $room->id
            ]);

            // Create guests and addresses
            $guestIds = [];
            foreach ($request->guests as $guestData) {
                // Create address
                $address = Address::create([
                    'street' => $guestData['address']['street'],
                    'city' => $guestData['address']['city'],
                    'province' => $guestData['address']['province'],
                    'zipcode' => $guestData['address']['zipcode'],
                    'userID' => null // Guest address, not user address
                ]);

                // Create guest
                $guest = Guest::create([
                    'firstName' => $guestData['firstName'],
                    'middleName' => $guestData['middleName'] ?? null,
                    'lastName' => $guestData['lastName'],
                    'number' => $guestData['number'] ?? null,
                    'addressID' => $address->id
                ]);

                $guestIds[] = $guest->id;

                // Create guest-stay relationship
                GuestStay::create([
                    'guestID' => $guest->id,
                    'stayID' => $stay->id,
                    'addressID' => $address->id
                ]);
            }

            // Create payment
            $payment = Payment::create([
                'amount' => $total,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'status' => 'Completed',
                'change' => $request->payment_change,
                'stayID' => $stay->id
            ]);

            // Create receipt
            $receipt = Receipt::create([
                'status' => 'Issued',
                'paymentID' => $payment->id,
                'userID' => Auth::id()
            ]);

            // Update room status
            $room->update(['status' => 'In Use']);

            // Log history
            History::create([
                'userID' => Auth::id(),
                'status' => 'Created stay for room ' . $room->room . ' - Receipt #' . $receipt->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stay processed successfully!',
                'stay_id' => $stay->id,
                'receipt_id' => $receipt->id,
                'checkout_time' => $stay->checkOut->format('Y-m-d H:i:s')
            ]);

        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function endStay(Request $request, $id)
    {
        try {
            $stay = Stay::findOrFail($id);
            $room = $stay->room;

            DB::beginTransaction();

            // Update stay status
            $stay->update([
                'status' => 'Completed',
                'checkOut' => now()
            ]);

            // Update room status
            $room->update(['status' => 'Available']);

            // Log history
            History::create([
                'userID' => Auth::id(),
                'status' => 'Ended stay for room ' . $room->room
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stay ended successfully!'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to end stay: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getActiveStays()
    {
        try {
            $stays = Stay::with(['room', 'rate.accommodation', 'guests'])
                         ->where('status', 'Active')
                         ->get();

            return response()->json([
                'success' => true,
                'stays' => $stays
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load active stays: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parseDurationToHours($duration)
    {
        // Parse duration string like "2 hours", "1 day", etc.
        $duration = strtolower(trim($duration));
        
        if (strpos($duration, 'hour') !== false) {
            return (int) $duration;
        } elseif (strpos($duration, 'day') !== false) {
            return (int) $duration * 24;
        } elseif (strpos($duration, 'minute') !== false) {
            return (int) $duration / 60;
        }
        
        return 1; // Default to 1 hour
    }
}
