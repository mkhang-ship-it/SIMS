<?php

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Listeners\SePayWebhookListener;

Route::post('/webhook/sepayout', function (Request $request) {
    $listener = new SePayWebhookListener();
    $listener->handle($request->all());

    return response()->json(['status' => 'success']);
});

Route::get('/invoice/check-status', function (Request $request) {
    $invoice = Invoice::where('invoice_code', $request->invoice_code)->first();

    return response()->json(['status' => $invoice->status ?? 'pending']);
});
