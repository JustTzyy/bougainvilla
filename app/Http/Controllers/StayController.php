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
            $room = Room::with(['level', 'accommodations.rates'])->findOrFail($id);

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
            $rates = Rate::query()
                        ->whereHas('accommodations', function($q) use ($accommodationId) {
                            $q->where('accommodations.id', $accommodationId);
                        })
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
                    // Link address to the authenticated user to satisfy FK constraint
                    'userID' => Auth::id()
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
                    'stayID' => $stay->id
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

    public function extend(Request $request, $id)
    {
        try {
            $request->validate([
                'rate_id' => 'required|exists:rates,id',
                'payment_amount' => 'required|numeric|min:0',
                'payment_change' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            $stay = Stay::findOrFail($id);
            if ($stay->status !== 'Active') {
                return response()->json(['success' => false, 'message' => 'Stay is not active'], 400);
            }

            $rate = Rate::findOrFail($request->rate_id);

            // Extend checkout from the later of current checkout or now
            $base = $stay->checkOut && $stay->checkOut->isFuture() ? $stay->checkOut : now();
            $newCheckout = $base->copy()->addHours($this->parseDurationToHours($rate->duration));
            $stay->update(['checkOut' => $newCheckout]);

            // Calculate amounts using previous guest count
            $guestCount = GuestStay::where('stayID', $stay->id)->count();
            if ($guestCount <= 0) { $guestCount = 1; }
            $subtotal = $rate->price * $guestCount;
            $taxRate = 0.12;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            $payment = Payment::create([
                'amount' => $total,
                'tax' => $tax,
                'subtotal' => $subtotal,
                'status' => 'Completed',
                'change' => $request->payment_change,
                'stayID' => $stay->id,
            ]);

            $receipt = Receipt::create([
                'status' => 'Issued',
                'paymentID' => $payment->id,
                'userID' => Auth::id()
            ]);

            History::create([
                'userID' => Auth::id(),
                'status' => 'Extended stay for room ' . ($stay->room->room ?? $stay->roomID) . ' - Receipt #' . $receipt->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stay extended successfully!',
                'checkout_time' => $newCheckout->format('Y-m-d H:i:s'),
                'receipt_id' => $receipt->id,
                'guest_count' => $guestCount,
                'subtotal' => number_format($subtotal, 2),
                'tax' => number_format($tax, 2),
                'total' => number_format($total, 2),
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to extend stay: ' . $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            $stay = Stay::findOrFail($id);
            $room = $stay->room;
            
            // Soft delete the stay
            $stay->delete();
            
            // Update room status to Available
            $room->update(['status' => 'Available']);
            
            // Log history
            History::create([
                'userID' => Auth::id(),
                'status' => 'Stay #' . $stay->id . ' deleted, Room ' . $room->room . ' made available'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stay deleted successfully'
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete stay: ' . $e->getMessage()
            ], 500);
        }
    }

    public function archived(Request $request)
    {
        try {
            $perPage = 15;
            
            // Get current logged-in user ID
            $currentUserId = Auth::id();
            
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Get archived stays with all related data including receipts and users - filtered by current user
            $stays = Stay::onlyTrashed()
                ->with(['room.level', 'rate.accommodations', 'guests', 'payments.receipts.user'])
                ->whereBetween('deleted_at', [$fromCarbon, $toCarbon])
                ->whereHas('payments.receipts', function($query) use ($currentUserId) {
                    $query->where('userID', $currentUserId); // Only include stays with payments that have receipts from current user
                })
                ->orderBy('deleted_at', 'desc')
                ->paginate($perPage);

            // Transform the data for display - keep the paginator structure
            $stays->getCollection()->transform(function ($stay) {
                $guestName = 'Unknown Guest';
                $roomNumber = 'N/A';
                $accommodationName = 'N/A';
                $amount = 0;
                $userFullName = 'Unknown User';
                
                if ($stay->guests && $stay->guests->count() > 0) {
                    $guest = $stay->guests->first();
                    $guestName = $guest->firstName . ' ' . $guest->lastName;
                }
                
                if ($stay->room) {
                    $roomNumber = $stay->room->room;
                }
                
                if ($stay->rate && $stay->rate->accommodations && $stay->rate->accommodations->count() > 0) {
                    $accommodationName = $stay->rate->accommodations->first()->name;
                }
                
                if ($stay->payments && $stay->payments->count() > 0) {
                    $amount = $stay->payments->sum('amount');
                    
                    // Get user from the first payment's receipt
                    $firstPayment = $stay->payments->first();
                    if ($firstPayment && $firstPayment->receipts && $firstPayment->receipts->count() > 0) {
                        $receipt = $firstPayment->receipts->first();
                        if ($receipt && $receipt->user) {
                            $userFullName = $receipt->user->firstName . ' ' . $receipt->user->lastName;
                        }
                    }
                }
                
                return (object) [
                    'id' => $stay->id,
                    'guest_name' => $guestName,
                    'user_full_name' => $userFullName,
                    'room' => $roomNumber,
                    'accommodation_name' => $accommodationName,
                    'check_in' => $stay->checkIn,
                    'check_out' => $stay->checkOut,
                    'amount' => $amount,
                    'status' => $stay->status,
                    'deleted_at' => $stay->deleted_at
                ];
            });

            return view('adminPages.archivetransactions', ['transactions' => $stays]);
            
        } catch (Exception $e) {
            \Log::error('Archived Transactions Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load archived transactions: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            $stay = Stay::onlyTrashed()->findOrFail($id);
            $room = $stay->room;
            
            // Check if room is available before restoring
            if ($room->status !== 'Available') {
                return redirect()->back()->with('error', 'Cannot restore stay: Room ' . $room->room . ' is currently ' . $room->status);
            }
            
            // Restore the stay
            $stay->restore();
            
            // Update room status to In Use
            $room->update(['status' => 'In Use']);
            
            // Log history
            History::create([
                'userID' => Auth::id(),
                'status' => 'Stay #' . $stay->id . ' restored, Room ' . $room->room . ' marked as In Use'
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Stay restored successfully!');
            
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Restore Stay Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restore stay: ' . $e->getMessage());
        }
    }

    public function getActiveStays()
    {
        try {
            // Load related room and rate with accommodations to infer accommodation for extension
            $stays = Stay::with(['room', 'rate.accommodations', 'guests'])
                         ->where('status', 'Active')
                         ->get();

            // Map accommodation id used for the rate (first of rate->accommodations) if available
            $staysTransformed = $stays->map(function ($stay) {
                $accommodationId = null;
                if ($stay->rate && $stay->rate->accommodations && $stay->rate->accommodations->count() > 0) {
                    $accommodationId = $stay->rate->accommodations->first()->id;
                }
                $guestCount = GuestStay::where('stayID', $stay->id)->count();
                return [
                    'id' => $stay->id,
                    'room' => $stay->room,
                    'rate' => $stay->rate,
                    'checkIn' => $stay->checkIn,
                    'checkOut' => $stay->checkOut,
                    'accommodation_id' => $accommodationId,
                    'guest_count' => $guestCount,
                ];
            });

            return response()->json([
                'success' => true,
                'stays' => $staysTransformed
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load active stays: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports(Request $request)
    {
        try {
            $from = $request->input('from');
            $to = $request->input('to');
            // Defaults: last 30 days
            $toCarbon = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $from ? Carbon::parse($from)->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();

            $paymentsQuery = Payment::query()
                ->where('status', 'Completed')
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('stay', function($query) {
                    $query->whereNull('deleted_at'); // Only include payments from non-deleted stays
                });

            $totals = $paymentsQuery->clone()
                ->selectRaw('COALESCE(SUM(subtotal),0) as subtotal_sum')
                ->selectRaw('COALESCE(SUM(tax),0) as tax_sum')
                ->selectRaw('COALESCE(SUM(amount),0) as amount_sum')
                ->selectRaw('COALESCE(SUM(`change`),0) as change_sum')
                ->selectRaw('COUNT(*) as payments_count')
                ->first();

            // Daily breakdown (for charts)
            $daily = Payment::query()
                ->where('status', 'Completed')
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('stay', function($query) {
                    $query->whereNull('deleted_at'); // Only include payments from non-deleted stays
                })
                ->selectRaw('DATE(created_at) as day')
                ->selectRaw('SUM(amount) as amount')
                ->selectRaw('SUM(subtotal) as subtotal')
                ->selectRaw('SUM(tax) as tax')
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            // KPI Metrics (no reservations list)
            $roomsTotal = Room::count();
            $roomsAvailable = Room::where('status', 'Available')->count();
            $roomsOccupied = Room::where('status', 'In Use')->count();
            $roomsMaintenance = Room::where('status', 'Under Maintenance')->count();
            $occupancyRate = $roomsTotal > 0 ? round(($roomsOccupied / $roomsTotal) * 100, 2) : 0;

            // Date-range check-ins/outs for filters
            $checkinsRange = Stay::whereBetween('checkIn', [$fromCarbon, $toCarbon])
                ->whereNull('deleted_at')
                ->count();
            $checkoutsRange = Stay::whereBetween('checkOut', [$fromCarbon, $toCarbon])
                ->whereNull('deleted_at')
                ->count();

            // Revenue shortcuts
            // Guests involved within date range (unique guest-stay rows for stays that started in range)
            $guestsRange = GuestStay::whereIn('stayID', Stay::whereBetween('checkIn', [$fromCarbon, $toCarbon])
                ->whereNull('deleted_at')
                ->pluck('id'))
                ->count();

            // Recent Active Stays (Guests & Stays list) — no reservations
            $activeStays = Stay::with(['room', 'guests'])
                ->where('status', 'Active')
                ->whereNull('deleted_at')
                ->orderByDesc('checkIn')
                ->limit(12)
                ->get()
                ->map(function($s){
                    return [
                        'id' => $s->id,
                        'room' => optional($s->room)->room,
                        'checkIn' => optional($s->checkIn)->format('Y-m-d H:i:s'),
                        'checkOut' => optional($s->checkOut)->format('Y-m-d H:i:s'),
                        'status' => $s->status,
                        'guests' => $s->guests->map(function($g){
                            return trim($g->firstName.' '.($g->middleName ? $g->middleName.' ' : '').$g->lastName);
                        })->implode(', ')
                    ];
                });

            // Guest counts - based on date range
            $guestsInRange = GuestStay::whereIn('stayID', Stay::whereBetween('checkIn', [$fromCarbon, $toCarbon])
                ->whereNull('deleted_at')
                ->pluck('id'))->count();
            $guestsTotal = Guest::withTrashed()->count();
            $activeStayIds = Stay::where('status', 'Active')
                ->whereNull('deleted_at')
                ->pluck('id');
            $guestsInHouse = GuestStay::whereIn('stayID', $activeStayIds)->count();
            

            if ($request->wantsJson()) {
                $response = [
                    'success' => true,
                    'from' => $fromCarbon->toDateString(),
                    'to' => $toCarbon->toDateString(),
                    'totals' => [
                        'subtotal' => (float) $totals->subtotal_sum,
                        'tax' => (float) $totals->tax_sum,
                        'amount' => (float) $totals->amount_sum,
                        'change' => (float) $totals->change_sum,
                        'count' => (int) $totals->payments_count,
                        'avg_amount' => $totals->payments_count ? (float) ($totals->amount_sum / $totals->payments_count) : 0,
                    ],
                    'daily' => $daily,
                    'stays' => $activeStays,
                    'kpis' => [
                        'rooms_total' => (int) $roomsTotal,
                        'rooms_available' => (int) $roomsAvailable,
                        'rooms_occupied' => (int) $roomsOccupied,
                        'rooms_maintenance' => (int) $roomsMaintenance,
                        'occupancy_rate' => (float) $occupancyRate,
                        'checkins' => (int) $checkinsRange,
                        'checkouts' => (int) $checkoutsRange,
                        'guests' => (int) $guestsInRange,
                        'guests_total' => (int) $guestsTotal,
                        'guests_inhouse' => (int) $guestsInHouse,
                    ],
                ];
                return response()->json($response);
            }

            // Fallback to dashboard view with preloaded figures
            return view('adminPages.dashboard', [
                'reportFrom' => $fromCarbon->toDateString(),
                'reportTo' => $toCarbon->toDateString(),
                'reportTotals' => $totals,
                'reportDaily' => $daily,
                'reportStays' => $activeStays,
                'reportKpis' => [
                    'rooms_total' => (int) $roomsTotal,
                    'rooms_available' => (int) $roomsAvailable,
                    'rooms_occupied' => (int) $roomsOccupied,
                    'rooms_maintenance' => (int) $roomsMaintenance,
                    'occupancy_rate' => (float) $occupancyRate,
                    'checkins' => (int) $checkinsRange,
                    'checkouts' => (int) $checkoutsRange,
                    'guests' => (int) $guestsInRange,
                    'guests_total' => (int) $guestsTotal,
                    'guests_inhouse' => (int) $guestsInHouse,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load reports: ' . $e->getMessage(),
            ], 500);
        }
    }
    private function parseDurationToHours($duration)
    {
        // Parse strings like "2 hours", "1 day", "30 minutes", "1 hr" etc.
        $s = strtolower(trim((string) $duration));
        if ($s === '') { return 1; }
        if (!preg_match('/(\d+(?:\.\d+)?)\s*(hour|hours|hr|hrs|minute|minutes|min|day|days)/', $s, $m)) {
            // Fallback: try to cast a leading number
            if (preg_match('/^\d+(?:\.\d+)?/', $s, $n)) {
                return max(1, (int) $n[0]);
            }
            return 1;
        }
        $value = (float) $m[1];
        $unit = $m[2];
        if (str_starts_with($unit, 'day')) { return (int) round($value * 24); }
        if (str_starts_with($unit, 'min')) { return max(1, (int) round($value / 60)); }
        // hour/hr/hrs
        return max(1, (int) round($value));
    }

    public function getGuestDetails($id)
    {
        try {
            // First try to find as a Stay (for archived transactions)
            $stay = Stay::withTrashed()
                ->with(['guests.address', 'room', 'rate.accommodations', 'payments.receipts'])
                ->find($id);

            if ($stay) {
                // This is a Stay record (archived transactions)
                $transaction = [
                    'room' => $stay->room ? $stay->room->room : 'N/A',
                    'accommodation' => $stay->rate && $stay->rate->accommodations->count() > 0
                        ? $stay->rate->accommodations->first()->name
                        : 'N/A',
                    'amount' => $stay->payments->sum('amount')
                ];

                // Get guest details - include soft-deleted guests through pivot table
                $guests = [];
                $stayGuests = \App\Models\Guest::withTrashed()
                    ->with('address')
                    ->whereHas('stays', function($query) use ($stay) {
                        $query->withTrashed()->where('stays.id', $stay->id);
                    })
                    ->get();

                foreach ($stayGuests as $guest) {
                    $guests[] = [
                        'firstName' => $guest->firstName,
                        'middleName' => $guest->middleName,
                        'lastName' => $guest->lastName,
                        'number' => $guest->number,
                        'address' => $guest->address ? [
                            'street' => $guest->address->street,
                            'city' => $guest->address->city,
                            'province' => $guest->address->province,
                            'zipcode' => $guest->address->zipcode
                        ] : null
                    ];
                }

                return response()->json([
                    'success' => true,
                    'guests' => $guests,
                    'transaction' => $transaction
                ]);
            }

            // If not found as Stay, try as Receipt (for regular transactions)
            $receipt = Receipt::withTrashed()
                ->with([
                    'payment.stay' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay.guests' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay.guests.address',
                    'payment.stay.room',
                    'payment.stay.rate.accommodations'
                ])
                ->find($id);

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Get transaction details from Receipt
            $transaction = [
                'room' => $receipt->payment && $receipt->payment->stay && $receipt->payment->stay->room 
                    ? $receipt->payment->stay->room->room 
                    : 'N/A',
                'accommodation' => $receipt->payment && $receipt->payment->stay && $receipt->payment->stay->rate && $receipt->payment->stay->rate->accommodations->count() > 0
                    ? $receipt->payment->stay->rate->accommodations->first()->name
                    : 'N/A',
                'amount' => $receipt->payment ? $receipt->payment->amount : 0
            ];

                // Get guest details - include soft-deleted guests through pivot table
                $guests = [];
                if ($receipt->payment && $receipt->payment->stay) {
                    // Get guests with soft-deleted ones included through pivot table
                    $stayGuests = \App\Models\Guest::withTrashed()
                        ->with('address')
                        ->whereHas('stays', function($query) use ($receipt) {
                            $query->withTrashed()->where('stays.id', $receipt->payment->stay->id);
                        })
                        ->get();

                foreach ($stayGuests as $guest) {
                    $guests[] = [
                        'firstName' => $guest->firstName,
                        'middleName' => $guest->middleName,
                        'lastName' => $guest->lastName,
                        'number' => $guest->number,
                        'address' => $guest->address ? [
                            'street' => $guest->address->street,
                            'city' => $guest->address->city,
                            'province' => $guest->address->province,
                            'zipcode' => $guest->address->zipcode
                        ] : null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'guests' => $guests,
                'transaction' => $transaction
            ]);

        } catch (Exception $e) {
            \Log::error('Get Guest Details Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch guest details: ' . $e->getMessage()
            ], 500);
        }
    }
}
