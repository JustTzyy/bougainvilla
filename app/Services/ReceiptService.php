<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Stay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    /**
     * Generate a PDF for the given receipt and upload it to S3.
     * Stores the S3 path on the receipt record.
     */
    public function uploadToS3(Receipt $receipt): void
    {
        try {
            $receipt->load(['payment.stay.room.level', 'payment.stay.rate.accommodations', 'payment.stay.guests', 'user']);

            $stay = $receipt->payment?->stay;
            $payment = $receipt->payment;

            if (!$stay || !$payment) {
                return;
            }

            $amount = $payment->amount;
            $tax = round($amount * 0.12 / 1.12, 2);
            $subtotal = $amount - $tax;

            $accommodation = $stay->rate?->accommodations?->first();
            $duration = $stay->checkIn && $stay->checkOut
                ? $stay->checkIn->diffForHumans($stay->checkOut, true)
                : 'N/A';

            $pdf = Pdf::loadView('receipts.receipt', [
                'receipt'       => $receipt,
                'cashier'       => $receipt->user ? $receipt->user->firstName . ' ' . $receipt->user->lastName : 'Staff',
                'room'          => $stay->room?->room ?? 'N/A',
                'level'         => $stay->room?->level?->description ?? 'N/A',
                'accommodation' => $accommodation?->name ?? 'N/A',
                'rate'          => $stay->rate?->description ?? 'N/A',
                'duration'      => $duration,
                'checkIn'       => $stay->checkIn?->format('m/d/Y H:i') ?? 'N/A',
                'checkOut'      => $stay->checkOut?->format('m/d/Y H:i') ?? 'N/A',
                'guestCount'    => $stay->guests->count(),
                'guests'        => $stay->guests,
                'subtotal'      => $subtotal,
                'tax'           => $tax,
                'total'         => $amount,
            ])->setPaper([0, 0, 226.77, 600], 'portrait'); // ~80mm wide thermal-style

            $path = 'receipts/' . date('Y/m') . '/receipt-' . $receipt->id . '.pdf';

            Storage::disk('s3')->put($path, $pdf->output(), 'private');

            $receipt->update(['file_path' => $path]);
        } catch (\Exception $e) {
            \Log::error('ReceiptService: failed to upload receipt to S3', [
                'receipt_id' => $receipt->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
