<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SafeDataAccessTrait;
use App\Models\GuestStay;
use App\Models\History;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Stay;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportBackupController extends Controller
{
    use SafeDataAccessTrait;

    public function backup(Request $request)
    {
        $type = $request->input('type');
        $toCarbon   = $request->filled('to')   ? Carbon::parse($request->input('to'))->endOfDay()     : Carbon::now()->endOfDay();
        $fromCarbon = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : $toCarbon->copy()->subDays(29)->startOfDay();

        try {
            [$title, $columns, $rows] = match ($type) {
                'dashboard'                => $this->dashboard($fromCarbon, $toCarbon),
                'transactionreports'       => $this->transactionReports($fromCarbon, $toCarbon),
                'all-transactions'         => $this->allTransactions($fromCarbon, $toCarbon),
                'all-archived-transactions'=> $this->allArchivedTransactions($fromCarbon, $toCarbon),
                'payments'                 => $this->payments($fromCarbon, $toCarbon),
                'guests'                   => $this->guests($fromCarbon, $toCarbon),
                'auditlogs'                => $this->auditLogs($fromCarbon, $toCarbon),
                'logs'                     => $this->logs(),
                default                    => throw new \InvalidArgumentException("Unknown report type: $type"),
            };

            $pdf = Pdf::loadView('reports.report-backup', compact('title', 'columns', 'rows', 'fromCarbon', 'toCarbon'))
                ->setPaper('a4', 'landscape');

            $dateRange  = $fromCarbon->format('Ymd') . '-' . $toCarbon->format('Ymd');
            $path = 'reports/' . date('Y/m') . '/' . $type . '-' . $dateRange . '-' . now()->format('His') . '.pdf';

            Storage::disk('s3')->put($path, $pdf->output(), 'private');

            return response()->json(['success' => true, 'path' => $path]);
        } catch (\Exception $e) {
            \Log::error('ReportBackupController: failed to backup report', [
                'type'  => $type,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Report data methods ──────────────────────────────────────────────────

    private function dashboard(Carbon $from, Carbon $to): array
    {
        $totals = Payment::where('status', 'Completed')
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('stay', fn($q) => $q->withTrashed())
            ->selectRaw('COALESCE(SUM(subtotal),0) as subtotal_sum, COALESCE(SUM(tax),0) as tax_sum, COALESCE(SUM(amount),0) as total_sum, COUNT(*) as count')
            ->first();

        $rooms = \App\Models\Room::with(['stays' => fn($q) => $q->whereIn('status', Stay::getValidStatuses())->where('checkOut', '>', now())])->get();
        $occupied  = $rooms->filter(fn($r) => $r->stays->count() > 0)->count();
        $available = $rooms->count() - $occupied;

        $rows = [
            ['Metric', 'Value'],
            ['Revenue (Subtotal)', '₱' . number_format($totals->subtotal_sum, 2)],
            ['Tax Collected', '₱' . number_format($totals->tax_sum, 2)],
            ['Total Revenue', '₱' . number_format($totals->total_sum, 2)],
            ['Completed Payments', $totals->count],
            ['Occupied Rooms', $occupied],
            ['Available Rooms', $available],
            ['Total Rooms', $rooms->count()],
        ];

        return ['Dashboard Summary', ['Metric', 'Value'], array_slice($rows, 1)];
    }

    private function transactionReports(Carbon $from, Carbon $to): array
    {
        $receipts = $this->queryReceipts($from, $to, Auth::id());
        return ['My Transaction Report', ['Receipt#', 'Room', 'Accommodation', 'Check-in', 'Check-out', 'Type', 'Amount', 'Cashier'], $receipts];
    }

    private function allTransactions(Carbon $from, Carbon $to): array
    {
        $receipts = $this->queryReceipts($from, $to);
        return ['All Transactions', ['Receipt#', 'Room', 'Accommodation', 'Check-in', 'Check-out', 'Type', 'Amount', 'Cashier'], $receipts];
    }

    private function allArchivedTransactions(Carbon $from, Carbon $to): array
    {
        $stays = Stay::onlyTrashed()
            ->with(['room' => fn($q) => $q->withTrashed(), 'rate' => fn($q) => $q->withTrashed(), 'rate.accommodationsWithTrashed', 'guests', 'payments.receipts.user'])
            ->whereBetween('deleted_at', [$from, $to])
            ->orderBy('deleted_at', 'desc')
            ->get();

        $rows = $stays->map(function ($stay) {
            $guest = $stay->guests->first();
            $receipt = $stay->payments->first()?->receipts->first();
            return [
                $stay->id,
                $stay->room?->room ?? 'N/A',
                $this->getAccommodationNameWithTrashed($stay->rate),
                $stay->checkIn?->format('m/d/Y H:i') ?? 'N/A',
                '₱' . number_format($stay->payments->sum('amount'), 2),
                $guest ? $guest->firstName . ' ' . $guest->lastName : 'N/A',
                $receipt?->user ? $receipt->user->firstName . ' ' . $receipt->user->lastName : 'N/A',
            ];
        })->toArray();

        return ['All Archived Transactions', ['Stay#', 'Room', 'Accommodation', 'Check-in', 'Total', 'Guest', 'Cashier'], $rows];
    }

    private function payments(Carbon $from, Carbon $to): array
    {
        $rows = Receipt::with(['payment' => fn($q) => $q->withTrashed(), 'payment.stay' => fn($q) => $q->withTrashed(), 'payment.stay.room' => fn($q) => $q->withTrashed(), 'payment.stay.rate' => fn($q) => $q->withTrashed(), 'payment.stay.rate.accommodationsWithTrashed', 'user' => fn($q) => $q->withTrashed()])
            ->whereBetween('created_at', [$from, $to])
            ->withTrashed()
            ->whereHas('payment.stay', fn($q) => $q->withTrashed())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                '#' . $r->id,
                $r->payment?->stay?->room?->room ?? 'N/A',
                $this->getAccommodationNameWithTrashed($r->payment?->stay?->rate),
                $r->status_type,
                '₱' . number_format($r->payment?->subtotal ?? 0, 2),
                '₱' . number_format($r->payment?->tax ?? 0, 2),
                '₱' . number_format($r->payment?->amount ?? 0, 2),
                $r->created_at->format('m/d/Y H:i'),
            ])->toArray();

        return ['Payments Report', ['Receipt#', 'Room', 'Accommodation', 'Type', 'Subtotal', 'Tax', 'Total', 'Date'], $rows];
    }

    private function guests(Carbon $from, Carbon $to): array
    {
        $rows = GuestStay::with(['guest.address', 'stay' => fn($q) => $q->withTrashed(), 'stay.room' => fn($q) => $q->withTrashed(), 'stay.rate' => fn($q) => $q->withTrashed(), 'stay.rate.accommodationsWithTrashed'])
            ->whereHas('stay', fn($q) => $q->whereBetween('checkIn', [$from, $to])->withTrashed())
            ->withTrashed()
            ->orderByDesc('stayID')
            ->get()
            ->map(function ($gs) {
                $g = $gs->guest;
                $addr = $g?->address;
                return [
                    $g ? $g->firstName . ' ' . $g->lastName : 'N/A',
                    $g?->number ?? 'N/A',
                    $gs->stay?->room?->room ?? 'N/A',
                    $this->getAccommodationNameWithTrashed($gs->stay?->rate),
                    $gs->stay?->checkIn?->format('m/d/Y H:i') ?? 'N/A',
                    $gs->stay?->checkOut?->format('m/d/Y H:i') ?? 'N/A',
                    $addr ? "{$addr->street}, {$addr->city}, {$addr->province}" : 'N/A',
                ];
            })->toArray();

        return ['Guests Report', ['Guest Name', 'Contact', 'Room', 'Accommodation', 'Check-in', 'Check-out', 'Address'], $rows];
    }

    private function auditLogs(Carbon $from, Carbon $to): array
    {
        $rows = History::with('user')
            ->where('userID', Auth::id())
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($h) => [
                $h->created_at->format('m/d/Y H:i'),
                $h->user ? $h->user->firstName . ' ' . $h->user->lastName : 'N/A',
                $h->status,
            ])->toArray();

        return ['Audit / Activity Logs', ['Date & Time', 'User', 'Action'], $rows];
    }

    private function logs(): array
    {
        $rows = Log::with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($l) => [
                $l->created_at->format('m/d/Y H:i'),
                $l->user ? $l->user->firstName . ' ' . $l->user->lastName : 'N/A',
                $l->timeIn  ? Carbon::parse($l->timeIn)->format('H:i')  : '-',
                $l->timeOut ? Carbon::parse($l->timeOut)->format('H:i') : '-',
                $l->status,
            ])->toArray();

        return ['System Logs', ['Date', 'User', 'Time In', 'Time Out', 'Status'], $rows];
    }

    // ── Shared query helper ──────────────────────────────────────────────────

    private function queryReceipts(Carbon $from, Carbon $to, ?int $userId = null): array
    {
        $query = Receipt::with([
                'payment'                               => fn($q) => $q->withTrashed(),
                'payment.stay'                          => fn($q) => $q->withTrashed(),
                'payment.stay.room'                     => fn($q) => $q->withTrashed(),
                'payment.stay.rate'                     => fn($q) => $q->withTrashed(),
                'payment.stay.rate.accommodationsWithTrashed',
                'user'                                  => fn($q) => $q->withTrashed(),
            ])
            ->whereBetween('created_at', [$from, $to])
            ->withTrashed()
            ->whereHas('payment.stay', fn($q) => $q->withTrashed())
            ->orderByDesc('created_at');

        if ($userId) {
            $query->where('userID', $userId);
        }

        return $query->get()->map(function ($r) {
            $stay = $r->payment?->stay;
            return [
                '#' . $r->id,
                $stay?->room?->room ?? 'N/A',
                $this->getAccommodationNameWithTrashed($stay?->rate),
                $stay?->checkIn?->format('m/d/Y H:i')  ?? 'N/A',
                $stay?->checkOut?->format('m/d/Y H:i') ?? 'N/A',
                $r->status_type,
                '₱' . number_format($r->payment?->amount ?? 0, 2),
                $r->user ? $r->user->firstName . ' ' . $r->user->lastName : 'N/A',
            ];
        })->toArray();
    }
}
