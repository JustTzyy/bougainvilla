<?php

namespace App\Http\Controllers\FrontDesk;

use App\Http\Controllers\Controller;
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
    use \App\Http\Controllers\SafeDataAccessTrait;
    use \App\Http\Controllers\EnhancedLoggingTrait;
    public function payments() { return view('frontdeskPages.payments'); }
    public function totalAmount() { return view('frontdeskPages.total_amount'); }
    public function tax() { return view('frontdeskPages.tax'); }
    public function subtotal() { return view('frontdeskPages.subtotal'); }
    public function checkins() { return view('frontdeskPages.checkins'); }
    public function checkouts() { return view('frontdeskPages.checkouts'); }
    public function guests() { return view('frontdeskPages.guests'); }
    
    
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
            
            // Get all activity logs for the currently logged-in user
            $histories = History::with('user')
                ->where('userID', auth()->id())
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->orderByDesc('created_at')
                ->get(); // Get all records instead of paginating
            
            // Get all users for the filter dropdown
            $users = User::orderBy('name')->get();
            
            return view('frontdeskPages.auditlogs', compact('histories', 'users'));
            
        } catch (Exception $e) {
            \Log::error('FrontDesk Audit Logs Error: ' . $e->getMessage());
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
            
            // Get ALL transactions for the logged-in user with all related data (no pagination)
            // Exclude transactions from soft-deleted stays
            $transactions = Receipt::with(['payment.stay.room', 'payment.stay.rate.accommodations', 'user'])
                ->where('userID', auth()->id())
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('payment.stay', function($query) {
                    $query->whereNull('deleted_at'); // Only include stays that are not soft-deleted
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
                    $roomNumber = $this->getRoomNumber($receipt->payment->stay->room);
                    $checkIn = $receipt->payment->stay->checkIn;
                    $checkOut = $receipt->payment->stay->checkOut;
                    $status = in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                        ? $receipt->status_type 
                        : Receipt::STATUS_TYPE_STANDARD;
                    
                    if ($receipt->payment->stay->rate && $receipt->payment->stay->rate->accommodations->count() > 0) {
                        $accommodationName = $receipt->payment->stay->rate->accommodations->first()->name;
                    }
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

            return view('frontdeskPages.transactionreports', ['transactions' => $transactions]);
            
        } catch (Exception $e) {
            \Log::error('FrontDesk Transaction Reports Error: ' . $e->getMessage());
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

            // Get ALL transactions from ALL users with all related data (no pagination)
            // Exclude transactions from soft-deleted stays
            $transactions = Receipt::with(['payment.stay.room', 'payment.stay.rate.accommodations', 'user'])
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('payment.stay', function($query) {
                    $query->whereNull('deleted_at'); // Only include stays that are not soft-deleted
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
                    $roomNumber = $this->getRoomNumber($receipt->payment->stay->room);
                    $checkIn = $receipt->payment->stay->checkIn; // Pass Carbon object
                    $checkOut = $receipt->payment->stay->checkOut; // Pass Carbon object
                    $status = in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                        ? $receipt->status_type 
                        : Receipt::STATUS_TYPE_STANDARD;

                    if ($receipt->payment->stay->rate && $receipt->payment->stay->rate->accommodations->count() > 0) {
                        $accommodationName = $receipt->payment->stay->rate->accommodations->first()->name;
                    }
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

            return view('frontdeskPages.alltransactions', ['transactions' => $transactions]);

        } catch (Exception $e) {
            \Log::error('FrontDesk All Transactions Error: ' . $e->getMessage());
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

            // Debug: Check if there are any soft-deleted stays
            $softDeletedStays = Stay::onlyTrashed()->count();
            \Log::info('FrontDesk Soft-deleted stays count: ' . $softDeletedStays);
            
            // Debug: Check if there are any receipts with soft-deleted stays
            $receiptsWithSoftDeletedStays = Receipt::whereHas('payment.stay', function($query) {
                $query->onlyTrashed();
            })->count();
            \Log::info('FrontDesk Receipts with soft-deleted stays count: ' . $receiptsWithSoftDeletedStays);

            // Alternative approach: Get soft-deleted stays first, then get their receipts
            $softDeletedStayIds = Stay::onlyTrashed()->pluck('id')->toArray();
            \Log::info('FrontDesk Soft-deleted stay IDs: ' . json_encode($softDeletedStayIds));

                // Get ALL transactions from ALL users with all related data (no pagination)
            // ONLY include transactions from soft-deleted stays
            $archivedTransactions = Receipt::with(['payment.stay.room', 'payment.stay.rate.accommodations', 'user'])
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('payment', function($query) use ($softDeletedStayIds) {
                    $query->whereIn('stayID', $softDeletedStayIds);
                })
                ->orderByDesc('created_at')
                ->get(); // Changed from paginate() to get() to load all records

            // Debug: Log the count of archived transactions found
            \Log::info('FrontDesk Archived transactions found: ' . $archivedTransactions->count());

            // Transform the data for display
            $archivedTransactions->transform(function ($receipt) {
                $roomNumber = 'N/A';
                $accommodationName = 'N/A';
                $checkIn = null;
                $checkOut = null;
                $status = 'Unknown';

                if ($receipt->payment) {
                    // Get the stay with trashed included
                    $stay = Stay::withTrashed()->with(['room', 'rate.accommodations'])->find($receipt->payment->stayID);
                    
                    if ($stay) {
                        $roomNumber = $stay->room ? $stay->room->room : 'N/A';
                        $checkIn = $stay->checkIn; // Pass Carbon object
                        $checkOut = $stay->checkOut; // Pass Carbon object
                        $status = in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                            ? $receipt->status_type 
                            : Receipt::STATUS_TYPE_STANDARD;

                        if ($stay->rate && $stay->rate->accommodations->count() > 0) {
                            $accommodationName = $stay->rate->accommodations->first()->name;
                        }
                    }
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

            return view('frontdeskPages.allarchivetransactions', ['archivedTransactions' => $archivedTransactions]);

        } catch (Exception $e) {
            \Log::error('FrontDesk All Archived Transactions Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load archived transactions: ' . $e->getMessage());
        }
    }

    // Generic data endpoint: /frontdeskPages/reports/data?type=payments&from=YYYY-MM-DD&to=YYYY-MM-DD
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
                            $roomNumber = $this->getRoomNumber($receipt->payment->stay->room);
                            
                            $accommodationName = $this->getAccommodationName($receipt->payment->stay->rate);
                        }
                        
                        return [
                            'id' => $receipt->id,
                            'user_name' => $this->getUserFullName($receipt->user),
                            'room_number' => $roomNumber,
                            'accommodation_name' => $accommodationName,
                            'subtotal' => $receipt->payment ? $receipt->payment->subtotal : 0,
                            'tax' => $receipt->payment ? $receipt->payment->tax : 0,
                            'amount' => $receipt->payment ? $receipt->payment->amount : 0,
                            'change' => $receipt->payment ? $receipt->payment->change : 0,

                            'status' => in_array($receipt->status_type, Receipt::getValidStatusTypes()) 
                                ? $receipt->status_type 
                                : Receipt::STATUS_TYPE_STANDARD,
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
            // Get ALL logs with all related data (no pagination)
            $logs = Log::with('user')
                ->orderByDesc('created_at')
                ->get(); // Changed from paginate() to get() to load all records
            
            // Debug: Log the count
            \Log::info('FrontDesk Logs count: ' . $logs->count());
            
            return view('frontdeskPages.logs', compact('logs'));
        } catch (Exception $e) {
            \Log::error('FrontDesk Logs Report Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load logs report: ' . $e->getMessage());
        }
    }
}
