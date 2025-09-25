<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Stay;
use App\Models\GuestStay;
use App\Models\History;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function payments() { return view('adminPages.payments'); }
    public function totalAmount() { return view('adminPages.total_amount'); }
    public function tax() { return view('adminPages.tax'); }
    public function subtotal() { return view('adminPages.subtotal'); }
    public function checkins() { return view('adminPages.checkins'); }
    public function checkouts() { return view('adminPages.checkouts'); }
    public function guests() { return view('adminPages.guests'); }
    
    
    public function auditLogs(Request $request)
    {
        try {
            $perPage = 10;
            
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Get activity logs only for the currently logged-in user
            $histories = History::with('user')
                ->where('userID', auth()->id())
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->orderByDesc('created_at')
                ->paginate($perPage);
            
            // Get all users for the filter dropdown
            $users = User::orderBy('name')->get();
            
            return view('adminPages.auditlogs', compact('histories', 'users'));
            
        } catch (Exception $e) {
            \Log::error('Audit Logs Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load activity logs: ' . $e->getMessage());
        }
    }
    
    public function transactionReports(Request $request)
    {
        try {
            $perPage = 15;
            
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Get transactions for the logged-in user with all related data
            // Exclude transactions from soft-deleted stays
            $transactions = Receipt::with(['payment.stay.room', 'payment.stay.rate.accommodations', 'user'])
                ->where('userID', auth()->id())
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('payment.stay', function($query) {
                    $query->whereNull('deleted_at'); // Only include stays that are not soft-deleted
                })
                ->orderByDesc('created_at')
                ->paginate($perPage);

            // Transform the data for display
            $transactions->getCollection()->transform(function ($receipt) {
                $roomNumber = 'N/A';
                $accommodationName = 'N/A';
                $checkIn = 'N/A';
                $checkOut = 'N/A';
                
                if ($receipt->payment && $receipt->payment->stay) {
                    $roomNumber = $receipt->payment->stay->room ? $receipt->payment->stay->room->room : 'N/A';
                    $checkIn = $receipt->payment->stay->checkIn;
                    $checkOut = $receipt->payment->stay->checkOut;
                    
                    if ($receipt->payment->stay->rate && $receipt->payment->stay->rate->accommodations->count() > 0) {
                        $accommodationName = $receipt->payment->stay->rate->accommodations->first()->name;
                    }
                }
                
                return (object) [
                    'id' => $receipt->id,
                    'user_name' => $receipt->user ? $receipt->user->firstName . ' ' . $receipt->user->lastName : 'Unknown User',
                    'room_number' => $roomNumber,
                    'accommodation_name' => $accommodationName,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'amount' => $receipt->payment ? $receipt->payment->amount : 0,
                    'created_at' => $receipt->created_at
                ];
            });

            return view('adminPages.transactionreports', ['transactions' => $transactions]);
            
        } catch (Exception $e) {
            \Log::error('Transaction Reports Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load transaction reports: ' . $e->getMessage());
        }
    }

    public function allTransactions(Request $request)
    {
        try {
            $perPage = 15;
            
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();

            // Get transactions from ALL users with all related data
            // Exclude transactions from soft-deleted stays
            $transactions = Receipt::with(['payment.stay.room', 'payment.stay.rate.accommodations', 'user'])
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('payment.stay', function($query) {
                    $query->whereNull('deleted_at'); // Only include stays that are not soft-deleted
                })
                ->orderByDesc('created_at')
                ->paginate($perPage);

            // Transform the data for display
            $transactions->getCollection()->transform(function ($receipt) {
                $roomNumber = 'N/A';
                $accommodationName = 'N/A';
                $checkIn = null;
                $checkOut = null;

                if ($receipt->payment && $receipt->payment->stay) {
                    $roomNumber = $receipt->payment->stay->room ? $receipt->payment->stay->room->room : 'N/A';
                    $checkIn = $receipt->payment->stay->checkIn; // Pass Carbon object
                    $checkOut = $receipt->payment->stay->checkOut; // Pass Carbon object

                    if ($receipt->payment->stay->rate && $receipt->payment->stay->rate->accommodations->count() > 0) {
                        $accommodationName = $receipt->payment->stay->rate->accommodations->first()->name;
                    }
                }

                return (object) [
                    'id' => $receipt->id,
                    'user_name' => $receipt->user ? $receipt->user->firstName . ' ' . $receipt->user->lastName : 'Unknown User',
                    'room_number' => $roomNumber,
                    'accommodation_name' => $accommodationName,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'amount' => $receipt->payment ? $receipt->payment->amount : 0,
                    'created_at' => $receipt->created_at
                ];
            });

            return view('adminPages.alltransactions', ['transactions' => $transactions]);

        } catch (Exception $e) {
            \Log::error('All Transactions Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load all transactions: ' . $e->getMessage());
        }
    }

    // Generic data endpoint: /adminPages/reports/data?type=payments&from=YYYY-MM-DD&to=YYYY-MM-DD
    public function data(Request $request)
    {
        $type = $request->query('type', 'payments');
        $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
        $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();

        switch ($type) {
            case 'payments':
                $rows = Receipt::with(['payment.stay.room', 'payment.stay.rate.accommodations', 'user'])
                    ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                    ->whereHas('payment.stay', function($query) {
                        $query->whereNull('deleted_at'); // Only include stays that are not soft-deleted
                    })
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(function($receipt) {
                        $roomNumber = 'N/A';
                        $accommodationName = 'N/A';
                        
                        if ($receipt->payment && $receipt->payment->stay) {
                            $roomNumber = $receipt->payment->stay->room ? $receipt->payment->stay->room->room : 'N/A';
                            
                            if ($receipt->payment->stay->rate && $receipt->payment->stay->rate->accommodations->count() > 0) {
                                $accommodationName = $receipt->payment->stay->rate->accommodations->first()->name;
                            }
                        }
                        
                        return [
                            'id' => $receipt->id,
                            'user_name' => $receipt->user ? $receipt->user->firstName . ' ' . $receipt->user->lastName : 'Unknown User',
                            'room_number' => $roomNumber,
                            'accommodation_name' => $accommodationName,
                            'subtotal' => $receipt->payment ? $receipt->payment->subtotal : 0,
                            'tax' => $receipt->payment ? $receipt->payment->tax : 0,
                            'amount' => $receipt->payment ? $receipt->payment->amount : 0,
                            'change' => $receipt->payment ? $receipt->payment->change : 0,
                            'created_at' => $receipt->created_at
                        ];
                    });
                return response()->json(['success'=>true,'rows'=>$rows]);
            case 'total_amount':
                $rows = Payment::where('status', 'Completed')
                    ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                    ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
                    ->groupBy('day')->orderBy('day')->get();
                return response()->json(['success'=>true,'rows'=>$rows]);
            case 'tax':
                $rows = Payment::where('status', 'Completed')
                    ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                    ->selectRaw('DATE(created_at) as day, SUM(tax) as total')
                    ->groupBy('day')->orderBy('day')->get();
                return response()->json(['success'=>true,'rows'=>$rows]);
            case 'subtotal':
                $rows = Payment::where('status', 'Completed')
                    ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                    ->selectRaw('DATE(created_at) as day, SUM(subtotal) as total')
                    ->groupBy('day')->orderBy('day')->get();
                return response()->json(['success'=>true,'rows'=>$rows]);
            case 'checkins':
                $rows = Stay::whereBetween('checkIn', [$fromCarbon, $toCarbon])
                    ->orderByDesc('checkIn')
                    ->get(['id','roomID','checkIn','status']);
                return response()->json(['success'=>true,'rows'=>$rows]);
            case 'checkouts':
                $rows = Stay::whereBetween('checkOut', [$fromCarbon, $toCarbon])
                    ->orderByDesc('checkOut')
                    ->get(['id','roomID','checkOut','status']);
                return response()->json(['success'=>true,'rows'=>$rows]);
            case 'guests':
                $rows = GuestStay::with(['guest.address', 'stay.room', 'stay.rate.accommodations'])
                    ->whereHas('stay', function($query) use ($fromCarbon, $toCarbon) {
                        $query->whereBetween('checkIn', [$fromCarbon, $toCarbon]);
                    })
                    ->orderByDesc('stayID')
                    ->get()
                    ->map(function($guestStay) {
                        $guestName = 'Unknown Guest';
                        $guestNumber = 'N/A';
                        $roomNumber = 'N/A';
                        $accommodationName = 'N/A';
                        $checkIn = 'N/A';
                        $checkOut = 'N/A';
                        $date = 'N/A';
                        $address = 'No address provided';
                        
                        if ($guestStay->guest) {
                            $guestName = $guestStay->guest->firstName . ' ' . $guestStay->guest->lastName;
                            $guestNumber = $guestStay->guest->number ?? 'N/A';
                            
                            if ($guestStay->guest->address) {
                                $addr = $guestStay->guest->address;
                                $address = $addr->street . ', ' . $addr->barangay . ', ' . $addr->city . ', ' . $addr->province . ' ' . $addr->zipCode;
                            }
                        }
                        
                        if ($guestStay->stay) {
                            $roomNumber = $guestStay->stay->room ? $guestStay->stay->room->room : 'N/A';
                            $checkIn = $guestStay->stay->checkIn ? $guestStay->stay->checkIn->format('M d, Y H:i') : 'N/A';
                            $checkOut = $guestStay->stay->checkOut ? $guestStay->stay->checkOut->format('M d, Y H:i') : 'N/A';
                            $date = $guestStay->stay->created_at ? $guestStay->stay->created_at->format('M d, Y H:i') : 'N/A';
                            
                            if ($guestStay->stay->rate && $guestStay->stay->rate->accommodations->count() > 0) {
                                $accommodationName = $guestStay->stay->rate->accommodations->first()->name;
                            }
                        }
                        
                        return [
                            'id' => $guestStay->id,
                            'guest_name' => $guestName,
                            'guest_number' => $guestNumber,
                            'room_number' => $roomNumber,
                            'accommodation_name' => $accommodationName,
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'date' => $date,
                            'address' => $address
                        ];
                    });
                return response()->json(['success'=>true,'rows'=>$rows]);
            default:
                return response()->json(['success'=>false,'message'=>'Unknown type'], 400);
        }
    }

    public function logs(Request $request)
    {
        try {
            $perPage = 10;
            $logs = Log::with('user')
                ->orderByDesc('created_at')
                ->paginate($perPage);
            
            // Debug: Log the count
            \Log::info('Logs count: ' . $logs->count());
            
            return view('adminPages.logs', compact('logs'));
        } catch (Exception $e) {
            \Log::error('Logs Report Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load logs report: ' . $e->getMessage());
        }
    }
}




