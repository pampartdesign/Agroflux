@php($role=app(\App\Services\UserRoleService::class))
<div class='w-64 bg-white border-r min-h-screen p-4'>
<h2 class='font-semibold mb-4'>{{ __('app.app_name') }}</h2>
<nav class='space-y-2 text-sm'>
<a href='/dashboard'>{{ __('app.nav_dashboard') }}</a>
@if($role->isAdmin()||$role->isFarmer())
<div class='pt-2 font-semibold'>{{ __('app.nav_agroflux_core') }}</div>
<a href='/core/farms'>{{ __('app.farms') }}</a>
<a href='/core/products'>{{ __('app.nav_products_catalog') }}</a>
<a href='/core/orders'>{{ __('app.nav_orders') }}</a>
<a href='/core/traceability'>{{ __('app.nav_traceability') }}</a>
@endif
@if($role->isAdmin()||$role->isFarmer())
<div class='pt-2 font-semibold'>{{ __('app.nav_logistics_delivery') }}</div>
<a href='/logistics/requests'>{{ __('app.nav_my_delivery_requests') }}</a>
@endif
@if($role->isTrucker())
<div class='pt-2 font-semibold'>{{ __('app.nav_logistics_delivery') }}</div>
<a href='/logistics/market'>{{ __('app.nav_available_requests') }}</a>
@endif
@if($role->isAdmin())
<div class='pt-2 font-semibold'>{{ __('app.nav_organization') }}</div>
<a href='/org/members'>{{ __('app.nav_members') }}</a>
@endif
</nav></div>