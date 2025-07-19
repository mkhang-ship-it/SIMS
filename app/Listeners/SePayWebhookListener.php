<?php

namespace App\Listeners;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class SePayWebhookListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if (($event['transferType'] ?? null) !== 'in') {
                return;
            }

            $rawInvoiceCode = $event['code'] ?? null;
            if (!$rawInvoiceCode) {
                Log::error("Missing invoice code in SePay Webhook event", ['event' => $event]);
                return;
            }

            if (preg_match('/^INV(\d{4})(\d{6})$/', $rawInvoiceCode, $matches)) {
                $invoiceCode = "INV-{$matches[1]}-{$matches[2]}";
            } else {
                $invoiceCode = $rawInvoiceCode;
            }

            $invoice = Invoice::where('invoice_code', $invoiceCode)->first();

            if (!$invoice) {
                Log::error("Invoice not found", ['invoice_code' => $invoiceCode]);
                return;
            }

            if ($invoice->status !== 'paid') {
                $invoice->update(['status' => 'paid']);

                $transaction = Transaction::where('invoice_id', $invoice->id)->first();

                if ($transaction) {
                    $transaction->fill([
                        'paid_amount'    => $event['transferAmount'] ?? 0,
                        'payment_date'   => $event['transactionDate'] ?? now(),
                        'type'           => 'income',
                        'payment_method' => 'Sepay',
                        'note'           => 'Hóa đơn: ' . $invoice->invoice_code . ' được xử lý thanh toán tự động bởi cổng thanh toán Sepay.',
                    ])->save();
                }
            }
        } catch (\Throwable $e) {
            Log::error("Error handling SePay Webhook", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'event'   => $event,
            ]);
        }
    }
}
