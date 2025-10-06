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
    use SafeDataAccessTrait;
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
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Validate date range - from date should not be later than to date
            if ($fromCarbon->isAfter($toCarbon)) {
                return redirect()->back()->with('error', 'The "From" date cannot be later than the "To" date. Please adjust your date range.');
            }
            
            // Validate that from date is not in the future
            if ($fromCarbon->isFuture()) {
                return redirect()->back()->with('error', 'The "From" date cannot be in the future. Please select a past or current date.');
            }
            
            // Get all activity logs for the currently logged-in user
            $histories = History::with('user')
                ->where('userID', auth()->id())
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->orderByDesc('created_at')
                ->get(); // Get all records instead of paginating
            
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
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Validate date range - from date should not be later than to date
            if ($fromCarbon->isAfter($toCarbon)) {
                return redirect()->back()->with('error', 'The "From" date cannot be later than the "To" date. Please adjust your date range.');
            }
            
            // Validate that from date is not in the future
            if ($fromCarbon->isFuture()) {
                return redirect()->back()->with('error', 'The "From" date cannot be in the future. Please select a past or current date.');
            }
            
            // Get ALL transactions for the logged-in user with all related data (no pagination)
            // Include transactions from soft-deleted stays, rooms, and users
            $transactions = Receipt::with([
                    'payment' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay.room' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay.rate' => function($query) {
                        $query->withTrashed();
                    },
                     'payment.stay.rate.accommodationsWithTrashed' => function($query) {
                         $query->withTrashed();
                     },
                    'user' => function($query) {
                        $query->withTrashed();
                    }
                ])
                ->where('userID', auth()->id())
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->withTrashed() // Include soft-deleted receipts
                ->whereHas('payment.stay', function($query) {
                    $query->withTrashed(); // Include soft-deleted stays
                })
                ->orderByDesc('created_at')
                ->get(); // Changed from paginate() to get() to load all records

            // Transform the data for display
            $transactions->transform(function ($receipt) {
                $roomNumber = 'N/A';
                $accommodationName = 'N/A';
                $checkIn = 'N/A';
                $checkOut = 'N/A';
                $status = 'Unknown';
                
                if ($receipt->payment && $receipt->payment->stay) {
                    $stay = $receipt->payment->stay;
                    $roomNumber = $this->getRoomNumber($stay->room);
                    $checkIn = $stay->checkIn; // Return Carbon object directly
                    $checkOut = $stay->checkOut; // Return Carbon object directly
                    // Use receipt status_type instead of stay status
                    $status = in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                        ? $receipt->status_type 
                        : Receipt::STATUS_TYPE_STANDARD;
                    
                    $accommodationName = $this->getAccommodationNameWithTrashed($stay->rate);
                }
                
                return (object) [
                    'id' => $receipt->id,
                    'user_name' => $this->getUserFullName($receipt->user),
                    'room_number' => $roomNumber,
                    'accommodation_name' => $accommodationName,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
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
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Validate date range - from date should not be later than to date
            if ($fromCarbon->isAfter($toCarbon)) {
                return redirect()->back()->with('error', 'The "From" date cannot be later than the "To" date. Please adjust your date range.');
            }
            
            // Validate that from date is not in the future
            if ($fromCarbon->isFuture()) {
                return redirect()->back()->with('error', 'The "From" date cannot be in the future. Please select a past or current date.');
            }

            // Get ALL transactions from ALL users with all related data (no pagination)
            // Include transactions from soft-deleted stays, rooms, and users
            $transactions = Receipt::with([
                    'payment' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay.room' => function($query) {
                        $query->withTrashed();
                    },
                    'payment.stay.rate' => function($query) {
                        $query->withTrashed();
                    },
                     'payment.stay.rate.accommodationsWithTrashed' => function($query) {
                         $query->withTrashed();
                     },
                    'user' => function($query) {
                        $query->withTrashed();
                    }
                ])
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->withTrashed() // Include soft-deleted receipts
                ->whereHas('payment.stay', function($query) {
                    $query->withTrashed(); // Include soft-deleted stays
                })
                ->orderByDesc('created_at')
                ->get(); // Changed from paginate() to get() to load all records

            // Transform the data for display
            $transactions->transform(function ($receipt) {
                $roomNumber = 'N/A';
                $accommodationName = 'N/A';
                $checkIn = null;
                $checkOut = null;
                $status = 'Unknown';

                if ($receipt->payment && $receipt->payment->stay) {
                    $roomNumber = $receipt->payment->stay->room ? $receipt->payment->stay->room->room : 'N/A';
                    $checkIn = $receipt->payment->stay->checkIn; // Pass Carbon object
                    $checkOut = $receipt->payment->stay->checkOut; // Pass Carbon object
                    // Use receipt status_type instead of stay status
                    $status = in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                        ? $receipt->status_type 
                        : Receipt::STATUS_TYPE_STANDARD;

                    $accommodationName = $this->getAccommodationNameWithTrashed($receipt->payment->stay->rate);
                }

                return (object) [
                    'id' => $receipt->id,
                    'user_name' => $this->getUserFullName($receipt->user),
                    'room_number' => $roomNumber,
                    'accommodation_name' => $accommodationName,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
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

    public function allArchivedTransactions(Request $request)
    {
        try {
            // Handle date filtering
            $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
            
            // Validate date range - from date should not be later than to date
            if ($fromCarbon->isAfter($toCarbon)) {
                return redirect()->back()->with('error', 'The "From" date cannot be later than the "To" date. Please adjust your date range.');
            }
            
            // Validate that from date is not in the future
            if ($fromCarbon->isFuture()) {
                return redirect()->back()->with('error', 'The "From" date cannot be in the future. Please select a past or current date.');
            }

            // Get ALL soft-deleted stays from ALL users with all related data (no pagination)
            $archivedStays = Stay::onlyTrashed()
                ->with(['room' => function($query) { $query->withTrashed(); }, 'room.level', 'rate' => function($query) { $query->withTrashed(); }, 'rate.accommodationsWithTrashed', 'guests', 'payments.receipts.user'])
                ->whereBetween('deleted_at', [$fromCarbon, $toCarbon])
                ->orderBy('deleted_at', 'desc')
                ->get(); // Changed from paginate() to get() to load all records

            // Transform the data for display
            $archivedStays->transform(function ($stay) {
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
                
                $accommodationName = $this->getAccommodationNameWithTrashed($stay->rate);
                
                $status = Stay::STATUS_STANDARD; // Default status
                if ($stay->payments && $stay->payments->count() > 0) {
                    $amount = $stay->payments->sum('amount');
                    
                    // Get user and status from the last payment's receipt (most recent transaction)
                    $lastPayment = $stay->payments->last();
                    if ($lastPayment && $lastPayment->receipts && $lastPayment->receipts->count() > 0) {
                        $receipt = $lastPayment->receipts->first();
                        if ($receipt && $receipt->user) {
                            $userFullName = $receipt->user->firstName . ' ' . $receipt->user->lastName;
                        }
                        // Get status from receipt
                        if ($receipt && $receipt->status_type) {
                            $status = in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                                ? $receipt->status_type 
                                : Receipt::STATUS_TYPE_STANDARD;
                        }
                    }
                }
                
                return (object) [
                    'id' => $stay->id,
                    'user_name' => $userFullName,
                    'room_number' => $roomNumber,
                    'accommodation_name' => $accommodationName,
                    'check_in' => $stay->checkIn,
                    'status' => $status,
                    'amount' => $amount,
                    'created_at' => $stay->deleted_at
                ];
            });

            return view('adminPages.allarchivetransactions', ['archivedTransactions' => $archivedStays]);

        } catch (Exception $e) {
            \Log::error('All Archived Transactions Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load archived transactions: ' . $e->getMessage());
        }
    }

    // Generic data endpoint: /adminPages/reports/data?type=payments&from=YYYY-MM-DD&to=YYYY-MM-DD
    public function data(Request $request)
    {
        $type = $request->query('type', 'payments');
        $toCarbon = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : Carbon::now()->endOfDay();
        
        // For guests, use a wider default date range (1 year) to show all historical data
        if ($type === 'guests') {
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subYear()->startOfDay();
        } else {
            $fromCarbon = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();
        }
        
        // Validate date range - from date should not be later than to date
        if ($fromCarbon->isAfter($toCarbon)) {
            return response()->json([
                'success' => false,
                'message' => 'The "From" date cannot be later than the "To" date. Please adjust your date range.'
            ], 400);
        }
        
        // Validate that from date is not in the future
        if ($fromCarbon->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'The "From" date cannot be in the future. Please select a past or current date.'
            ], 400);
        }

        switch ($type) {
            case 'payments':
                $rows = Receipt::with([
                        'payment' => function($query) {
                            $query->withTrashed();
                        },
                        'payment.stay' => function($query) {
                            $query->withTrashed();
                        },
                        'payment.stay.room' => function($query) {
                            $query->withTrashed();
                        },
                        'payment.stay.rate' => function($query) {
                            $query->withTrashed();
                        },
                     'payment.stay.rate.accommodationsWithTrashed' => function($query) {
                         $query->withTrashed();
                     },
                        'user' => function($query) {
                            $query->withTrashed();
                        }
                    ])
                    ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                    ->withTrashed() // Include soft-deleted receipts
                    ->whereHas('payment.stay', function($query) {
                        $query->withTrashed(); // Include soft-deleted stays
                    })
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(function($receipt) {
                        $roomNumber = 'N/A';
                        $accommodationName = 'N/A';
                        
                        if ($receipt->payment && $receipt->payment->stay) {
                            $roomNumber = $receipt->payment->stay->room ? $receipt->payment->stay->room->room : 'N/A';
                            
                            $accommodationName = $this->getAccommodationNameWithTrashed($receipt->payment->stay->rate);
                        }
                        
                        return [
                            'id' => $receipt->id,
                            'user_name' => $this->getUserFullName($receipt->user),
                            'room_number' => $roomNumber,
                            'accommodation_name' => $accommodationName,
                            'status' => in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                                ? $receipt->status_type 
                                : Receipt::STATUS_TYPE_STANDARD,
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
                $rows = GuestStay::with([
                        'guest.address' => function($query) {
                            $query->withTrashed();
                        },
                        'stay' => function($query) {
                            $query->withTrashed();
                        },
                        'stay.room' => function($query) {
                            $query->withTrashed();
                        },
                        'stay.rate' => function($query) {
                            $query->withTrashed();
                        },
                        'stay.rate.accommodationsWithTrashed' => function($query) {
                            $query->withTrashed();
                        }
                    ])
                    ->whereHas('stay', function($query) use ($fromCarbon, $toCarbon) {
                        $query->whereBetween('checkIn', [$fromCarbon, $toCarbon])
                              ->withTrashed();
                    })
                    ->withTrashed() // Include soft-deleted guest stays
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
                            
                            $accommodationName = $this->getAccommodationNameWithTrashed($guestStay->stay->rate);
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
            // Get ALL logs with all related data (no pagination)
            $logs = Log::with('user')
                ->orderByDesc('created_at')
                ->get(); // Changed from paginate() to get() to load all records
            
            // Debug: Log the count
            \Log::info('Logs count: ' . $logs->count());
            
            return view('adminPages.logs', compact('logs'));
        } catch (Exception $e) {
            \Log::error('Logs Report Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load logs report: ' . $e->getMessage());
        }
    }
}




