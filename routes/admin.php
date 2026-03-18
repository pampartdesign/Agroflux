<?php

use App\Http\Controllers\Admin\AuthorizedSellerController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\CatalogCategoryController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TenantSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web','auth','super.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Users
    Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserAdminController::class, 'create'])->name('users.create');
    Route::post('/users', [UserAdminController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserAdminController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/assign-tenant', [UserAdminController::class, 'assignTenant'])->name('users.assignTenant');
    Route::delete('/users/{user}/tenant/{tenant}', [UserAdminController::class, 'removeTenant'])->name('users.removeTenant');

    // Quick-create an organisation inline (used by the create/edit user forms)
    Route::post('/orgs/quick', [UserAdminController::class, 'quickCreateOrg'])->name('orgs.quick');

    // Languages — CRUD
    Route::get('/languages',                [LanguageController::class, 'index'])->name('languages.index');
    Route::get('/languages/create',         [LanguageController::class, 'create'])->name('languages.create');
    Route::post('/languages',               [LanguageController::class, 'store'])->name('languages.store');
    Route::get('/languages/{language}/edit',[LanguageController::class, 'edit'])->name('languages.edit');
    Route::put('/languages/{language}',     [LanguageController::class, 'update'])->name('languages.update');

    // Translation lines — must come before /{language} wildcard routes
    Route::get('/languages/lines',          [LanguageController::class, 'lines'])->name('languages.lines');
    Route::put('/languages/lines/{line}',   [LanguageController::class, 'updateLine'])->name('languages.lines.update');
    Route::get('/languages/lines/create',   [LanguageController::class, 'createLine'])->name('languages.lines.create');
    Route::post('/languages/lines',         [LanguageController::class, 'storeLine'])->name('languages.lines.store');
    Route::post('/languages/lines/sync',          [LanguageController::class, 'syncFromFiles'])->name('languages.lines.sync');
    Route::post('/languages/lines/sync-to-files', [LanguageController::class, 'syncToFiles'])->name('languages.lines.sync-to-files');
    Route::get('/languages/lines/export',         [LanguageController::class, 'export'])->name('languages.lines.export');
    Route::post('/languages/lines/import',        [LanguageController::class, 'import'])->name('languages.lines.import');

    // Product Categories (global — shared by all tenants)
    Route::get('/categories', [CatalogCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CatalogCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CatalogCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CatalogCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CatalogCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CatalogCategoryController::class, 'destroy'])->name('categories.destroy');

    // Regions
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::get('/regions/create', [RegionController::class, 'create'])->name('regions.create');
    Route::post('/regions', [RegionController::class, 'store'])->name('regions.store');
    Route::get('/regions/{region}/edit', [RegionController::class, 'edit'])->name('regions.edit');
    Route::put('/regions/{region}', [RegionController::class, 'update'])->name('regions.update');
    Route::delete('/regions/{region}', [RegionController::class, 'destroy'])->name('regions.destroy');

    // Subscription Plans
    Route::get('/plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [SubscriptionPlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [SubscriptionPlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('plans.update');
    Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])->name('plans.toggle');

    // Authorized Sellers
    Route::get('/authorized-sellers', [AuthorizedSellerController::class, 'index'])->name('authorized-sellers.index');
    Route::get('/authorized-sellers/create', [AuthorizedSellerController::class, 'create'])->name('authorized-sellers.create');
    Route::post('/authorized-sellers', [AuthorizedSellerController::class, 'store'])->name('authorized-sellers.store');
    Route::get('/authorized-sellers/{authorizedSeller}/edit', [AuthorizedSellerController::class, 'edit'])->name('authorized-sellers.edit');
    Route::put('/authorized-sellers/{authorizedSeller}', [AuthorizedSellerController::class, 'update'])->name('authorized-sellers.update');
    Route::delete('/authorized-sellers/{authorizedSeller}', [AuthorizedSellerController::class, 'destroy'])->name('authorized-sellers.destroy');
    Route::post('/authorized-sellers/{authorizedSeller}/toggle', [AuthorizedSellerController::class, 'toggle'])->name('authorized-sellers.toggle');

    // Tenant Subscriptions
    Route::get('/subscriptions', [TenantSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{tenant}/edit', [TenantSubscriptionController::class, 'edit'])->name('subscriptions.edit');
    Route::put('/subscriptions/{tenant}', [TenantSubscriptionController::class, 'update'])->name('subscriptions.update');
});
