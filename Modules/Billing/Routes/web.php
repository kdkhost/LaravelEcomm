<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Billing\Http\Controllers\BillingController;
use Modules\Billing\Http\Controllers\InvoiceController;
use Modules\Billing\Http\Controllers\PaymentController;
use Modules\Billing\Http\Controllers\PaypalController;
use Modules\Billing\Http\Controllers\StripeController;
use Modules\Billing\Http\Controllers\MercadoPagoController;
use Modules\Billing\Http\Controllers\PaymentProviderController;
use Modules\Billing\Http\Controllers\WishlistController;

// theme_view() is loaded via composer autoload files

Route::get('wishlist', function (): Illuminate\Contracts\View\View|Illuminate\Contracts\View\Factory {
    return view(theme_view('pages.wishlist'));
})->name('wishlist');
Route::get('/wishlist/{slug}', [WishlistController::class, 'wishlist'])->name('add-to-wishlist');
Route::get('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlist-delete');
// // Payment
Route::get('payment', [PaypalController::class, 'charge'])->name('payment');
Route::get('cancel', [PaypalController::class, 'cancel'])->name('payment.cancel');
Route::get('payment/success', [PaypalController::class, 'success'])->name('payment.success');
// Stripe
Route::get('stripe/{id}', [StripeController::class, 'stripe'])->name('stripe');
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');
// MercadoPago
Route::get('mercadopago/checkout', [MercadoPagoController::class, 'checkout'])->name('mercadopago.checkout');
Route::get('mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
Route::get('mercadopago/failure', [MercadoPagoController::class, 'failure'])->name('mercadopago.failure');
Route::get('mercadopago/pending', [MercadoPagoController::class, 'pending'])->name('mercadopago.pending');
Route::post('mercadopago/webhook', [MercadoPagoController::class, 'webhook'])->name('mercadopago.webhook');
Route::post('mercadopago/refund/{orderId}', [MercadoPagoController::class, 'refund'])->name('mercadopago.refund');

// Billing History (User)
Route::middleware(['auth'])->group(function () {
    Route::get('billing/history', [BillingController::class, 'history'])->name('billing.history');
    Route::get('payments/history', [PaymentController::class, 'history'])->name('payments.history');
});

// Invoices (Admin & User)
Route::middleware(['auth'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

// Payment Providers (Admin CRUD)
Route::middleware(['auth'])->group(function () {
    Route::resource('payment_provider', PaymentProviderController::class)->except('show');
});

// Payment Analytics (Admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('admin/payments/analytics', [PaymentController::class, 'analytics'])->name('payments.analytics');
});
