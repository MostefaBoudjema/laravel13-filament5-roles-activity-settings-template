<?php

use App\Http\Controllers\InvoiceExcelController;
use App\Http\Controllers\PaymentReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'ar', 'fr'], true)) {
        abort(404);
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::middleware(['auth', 'role:super-admin|accountant'])->group(function () {
    Route::get('payments/{payment}/receipt', [PaymentReceiptController::class, 'show'])
        ->name('payments.receipt');
    Route::get('payments/{payment}/details-receipt', [PaymentReceiptController::class, 'showDetails'])
        ->name('payments.details-receipt');
    Route::get('invoice/download', [InvoiceExcelController::class, 'download'])
        ->name('invoice.download');
});
