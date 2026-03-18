<?php

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\OnboardingController;
use App\Http\Controllers\App\TenantSwitchController;
use App\Http\Controllers\Core\FarmController;
use App\Http\Controllers\Core\ListingController;
use App\Http\Controllers\Core\OrderController;
use App\Http\Controllers\Core\ProductController;
use App\Http\Controllers\Core\SellDashboardController;
use App\Http\Controllers\Core\TraceabilityController;
use App\Http\Controllers\Plus\IoTController;
use App\Http\Controllers\Plus\SensorRuleController;
use App\Http\Controllers\Public\DeliveryRequestController;
use App\Http\Controllers\Public\AuthorizedSellersController;
use App\Http\Controllers\Public\MarketplaceController;
use App\Http\Controllers\Public\ProductTraceController;
use App\Http\Controllers\Public\TracePublicController;
use App\Http\Controllers\Public\VatValidationController;
use App\Http\Controllers\Public\Customer\CustomerAuthController;
use App\Http\Controllers\Public\Customer\CustomerAccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\App\TenantSelectController;

Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/select', [TenantSelectController::class, 'index'])->name('tenant.select');
});

Route::get('/debug-session', function () {
    return response()->json([
        'app_locale'       => app()->getLocale(),
        'session_locale'   => session('locale'),
        'user_locale'      => auth()->user()?->locale,
        'cookie_locale'    => request()->cookie('locale'),
        'available_locales'=> array_keys(config('agroflux.locales', [])),
        'session_all'      => session()->all(),
    ]);
})->middleware('auth');



Route::get('/', fn () => redirect()->route('dashboard'));

// Public marketplace
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('public.marketplace');
Route::get('/marketplace/{listing}', [MarketplaceController::class, 'show'])->name('public.marketplace.show');
Route::post('/delivery-request', [DeliveryRequestController::class, 'store'])->name('delivery.request.store');

// Public traceability timeline (QR scan -> batch timeline)
Route::get('/trace/{token}', [TracePublicController::class, 'show'])->name('public.trace');

// Public product trace (QR next to product in marketplace)
Route::get('/trace/product/{token}', [ProductTraceController::class, 'show'])->name('public.trace.product');

// Authorized Sellers (accessible to all authenticated users)
Route::middleware(['auth'])->get('/authorized-sellers', [AuthorizedSellersController::class, 'index'])->name('authorized-sellers.index');

// ── Customer auth (public marketplace buyers) ──────────────────────────
Route::get('/customer/login',    [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/customer/login',   [CustomerAuthController::class, 'login']);
Route::get('/customer/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/customer/register',[CustomerAuthController::class, 'register']);
Route::post('/customer/logout',  [CustomerAuthController::class, 'logout'])->name('customer.logout');

// ── Customer account (auth:customer guard) ─────────────────────────────
Route::middleware('auth:customer')->prefix('account')->name('customer.')->group(function () {
    Route::get('/',               [CustomerAccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders',         [CustomerAccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [CustomerAccountController::class, 'orderShow'])->name('order');
    Route::get('/profile',        [CustomerAccountController::class, 'profile'])->name('profile');
    Route::put('/profile',        [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password',       [CustomerAccountController::class, 'updatePassword'])->name('password.update');
});

// ── EU VAT validation (AJAX, used in checkout) ─────────────────────────
Route::post('/vat/validate', [VatValidationController::class, 'validate'])->name('vat.validate');

// Public locale switch — works for guests (no user DB update)
Route::get('/lang/{locale}', function (string $locale) {
    $available = array_keys(config('agroflux.locales', []));
    if (in_array($locale, $available, true)) {
        session(['locale' => $locale]);
        return redirect()->back()
            ->withCookie(cookie()->forever('locale', $locale))
            ->withHeaders(['Cache-Control' => 'no-store, no-cache, must-revalidate']);
    }
    return redirect()->back();
})->name('public.locale.switch');

Route::middleware('auth')->get('/locale/{locale}', function (string $locale) {
    $available = array_keys(config('agroflux.locales', []));
    if (in_array($locale, $available, true)) {
        session(['locale' => $locale]);
        try { auth()->user()->updateQuietly(['locale' => $locale]); } catch (\Throwable) {}
        return redirect()->back()
            ->withCookie(cookie()->forever('locale', $locale))
            ->withHeaders(['Cache-Control' => 'no-store, no-cache, must-revalidate']);
    }
    return redirect()->back();
})->name('locale.switch');

Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/payment', [ProfileController::class, 'updatePayment'])->name('profile.payment.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/onboarding/tenant', [OnboardingController::class, 'show'])->name('onboarding.tenant.create');
    Route::post('/onboarding/tenant', [OnboardingController::class, 'store'])->name('onboarding.tenant.store');

    Route::post('/tenant/switch', [TenantSwitchController::class, 'switch'])->name('tenant.switch');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['tenant.selected'])->group(function () {

        // Core module
        Route::middleware(['module.access:core'])->prefix('core')->name('core.')->group(function () {
            // Sell on Marketplace dashboard
            Route::get('/sell', [SellDashboardController::class, 'index'])->name('sell.dashboard');

            // Farms
            Route::get('/farms', [FarmController::class, 'index'])->name('farms.index');
            Route::get('/farms/create', [FarmController::class, 'create'])->name('farms.create');
            Route::post('/farms', [FarmController::class, 'store'])->name('farms.store');
            Route::get('/farms/{farm}', [FarmController::class, 'show'])->name('farms.show');
            Route::get('/farms/{farm}/edit', [FarmController::class, 'edit'])->name('farms.edit');
            Route::put('/farms/{farm}', [FarmController::class, 'update'])->name('farms.update');

            // Products
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

            // Listings
            Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
            Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
            Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
            Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
            Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
            Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
            Route::post('/listings/{listing}/toggle', [ListingController::class, 'toggle'])->name('listings.toggle');

            // Orders (seller inbox)
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');

            // Traceability
            Route::get('/traceability', [TraceabilityController::class, 'index'])->name('traceability.index');
            Route::get('/traceability/batches/create', [TraceabilityController::class, 'createBatch'])->name('traceability.batch.create');
            Route::post('/traceability/batches', [TraceabilityController::class, 'storeBatch'])->name('traceability.batch.store');
            Route::get('/traceability/batches/{batch}', [TraceabilityController::class, 'showBatch'])->name('traceability.batch.show');
            Route::get('/traceability/batches/{batch}/events/create', [TraceabilityController::class, 'addEvent'])->name('traceability.event.create');
            Route::post('/traceability/batches/{batch}/events', [TraceabilityController::class, 'storeEvent'])->name('traceability.event.store');
        });

        // Plus module (IoT)
        Route::middleware(['module.access:plus'])->prefix('plus')->name('plus.')->group(function () {
            Route::get('/iot', [IoTController::class, 'dashboard'])->name('iot.dashboard');

            Route::get('/iot/sensors', [IoTController::class, 'sensors'])->name('iot.sensors.index');
            Route::get('/iot/sensors/create', [IoTController::class, 'createSensor'])->name('iot.sensors.create');
            Route::post('/iot/sensors', [IoTController::class, 'storeSensor'])->name('iot.sensors.store');
            Route::put('/iot/sensors/{sensor}', [IoTController::class, 'updateSensor'])->name('iot.sensors.update');

            Route::get('/iot/simulator', [IoTController::class, 'simulator'])->name('iot.simulator');
            Route::post('/iot/simulator/ping', [IoTController::class, 'ping'])->name('iot.ping');

            Route::get('/iot/manual-entry', [IoTController::class, 'manualEntry'])->name('iot.manual');
            Route::post('/iot/manual-entry', [IoTController::class, 'storeManualEntry'])->name('iot.manual.store');

            // Sensor Rules & Conditions
            Route::get('/iot/rules', [SensorRuleController::class, 'index'])->name('iot.rules.index');
            Route::get('/iot/rules/create', [SensorRuleController::class, 'create'])->name('iot.rules.create');
            Route::post('/iot/rules', [SensorRuleController::class, 'store'])->name('iot.rules.store');
            Route::get('/iot/rules/{rule}/edit', [SensorRuleController::class, 'edit'])->name('iot.rules.edit');
            Route::put('/iot/rules/{rule}', [SensorRuleController::class, 'update'])->name('iot.rules.update');
            Route::delete('/iot/rules/{rule}', [SensorRuleController::class, 'destroy'])->name('iot.rules.destroy');
            Route::patch('/iot/rules/{rule}/toggle', [SensorRuleController::class, 'toggleActive'])->name('iot.rules.toggle');
            Route::get('/iot/rules/{rule}/logs', [SensorRuleController::class, 'logs'])->name('iot.rules.logs');


            Route::get('/billing/locked', function () {
    return view('billing.locked');
})->name('billing.locked')->middleware('auth');

        });
    });
});

require __DIR__.'/marketplace.php';
require __DIR__.'/auth.php';
require __DIR__.'/logitrace.php';
require __DIR__.'/admin.php';
require __DIR__.'/members.php';
require __DIR__.'/logistics.php';
require __DIR__.'/logistics_offers.php';
require __DIR__.'/logistics_lifecycle.php';
require __DIR__.'/app_shell_aliases.php';
require __DIR__.'/media.php';
require __DIR__.'/profile_locale.php';
require __DIR__.'/notifications.php';
require __DIR__.'/activity.php';
require __DIR__.'/modules.php';
