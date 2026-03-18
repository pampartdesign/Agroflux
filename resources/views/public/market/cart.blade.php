<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.your_cart') }} — AgroFlux {{ __('market.marketplace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

{{-- Public header --}}
<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div>
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @include('public._locale_switcher')
            @include('public._customer_nav')
            <a href="{{ route('public.marketplace') }}"
               class="inline-flex items-center h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm">
                {{ __('market.back_to_marketplace') }}
            </a>
        </div>
    </div>
</header>

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('market.your_cart') }}</h1>
        @if($sellerName)
            <p class="text-sm text-slate-500 mt-1">{{ __('market.items_from', ['seller' => $sellerName]) }}</p>
        @endif
    </div>

    @if(empty($cart))
        {{-- Empty cart --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-10 text-center">
            <div class="h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.2 6H19M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
            </div>
            <div class="text-slate-700 font-medium mb-1">{{ __('market.cart_empty') }}</div>
            <p class="text-sm text-slate-400 mb-6">{{ __('market.cart_empty_desc') }}</p>
            <a href="{{ route('public.marketplace') }}"
               class="inline-flex items-center h-10 px-6 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-medium">
                {{ __('market.browse_marketplace') }}
            </a>
        </div>
    @else
        @php
            $cust = auth('customer')->user();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Cart items --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart as $listingId => $item)
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4"/>
                                <circle cx="9" cy="20" r="1"/>
                                <circle cx="19" cy="20" r="1"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-900 truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ $item['type'] === 'instock' ? __('market.type_instock') : __('market.type_preorder') }} · {{ $item['unit'] ?? 'unit' }}
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="font-semibold text-slate-900">€{{ number_format((float)$item['price'] * (float)$item['qty'], 2) }}</div>
                            <div class="text-xs text-slate-400">{{ $item['qty'] }} × €{{ number_format($item['price'], 2) }}</div>
                        </div>
                        <form method="POST" action="{{ route('cart.remove', $listingId) }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit"
                                    class="h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition flex items-center justify-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach

                {{-- Order total summary --}}
                <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-4">
                    <div class="space-y-1.5 text-sm mb-3">
                        @foreach($cart as $item)
                            <div class="flex justify-between text-slate-600">
                                <span class="truncate pr-2">{{ $item['name'] }} ×{{ $item['qty'] }}</span>
                                <span class="flex-shrink-0">€{{ number_format((float)$item['price'] * (float)$item['qty'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between font-semibold text-slate-900">
                        <span>{{ __('market.total') }}</span>
                        <span class="text-emerald-700">€{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="text-right">
                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-red-600 transition underline">
                            {{ __('market.clear_cart') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Checkout form --}}
            <div class="lg:col-span-3">
                <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">

                    {{-- Customer banner --}}
                    @if($cust)
                        <div class="px-6 py-3 bg-emerald-50 border-b border-emerald-100 flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ $cust->initials() }}
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold text-emerald-900">{{ $cust->fullName() }}</span>
                                <span class="text-emerald-700 ml-1">— {{ __('market.details_prefilled') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="px-6 py-3 bg-slate-50 border-b border-slate-100 text-sm text-slate-500">
                            {{ __('market.have_account') }}
                            <a href="{{ route('customer.login') }}" class="text-emerald-700 font-medium hover:underline">{{ __('market.login') }}</a>
                            {{ __('market.or') }}
                            <a href="{{ route('customer.register') }}" class="text-emerald-700 font-medium hover:underline">{{ __('market.register') }}</a>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('checkout.store') }}" class="p-6 space-y-5"
                          x-data="{
                              docType: '{{ old('document_type', 'receipt') }}',
                              vatVerified: false,
                              vatVerifying: false,
                              vatError: '',
                              vatCountry: '{{ old('vat_country', 'GR') }}',
                              vatNumber: '{{ old('vat_number', '') }}',
                              companyName: '{{ old('company_name', '') }}',
                              createAccount: {{ old('create_account') ? 'true' : 'false' }},
                              async verifyVat() {
                                  this.vatVerifying = true;
                                  this.vatError = '';
                                  this.vatVerified = false;
                                  try {
                                      const resp = await fetch('{{ route('vat.validate') }}', {
                                          method: 'POST',
                                          headers: {
                                              'Content-Type': 'application/json',
                                              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                          },
                                          body: JSON.stringify({ country: this.vatCountry, vat_number: this.vatNumber })
                                      });
                                      const data = await resp.json();
                                      if (data.valid) {
                                          this.vatVerified = true;
                                          if (data.name && data.name !== '---') this.companyName = data.name;
                                      } else {
                                          this.vatError = '{{ __('market.vat_invalid') }}';
                                      }
                                  } catch(e) {
                                      this.vatError = '{{ __('market.vat_verify_error') }}';
                                  }
                                  this.vatVerifying = false;
                              }
                          }">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">

                        {{-- Document type --}}
                        <div>
                            <div class="text-xs font-medium text-slate-500 mb-2">{{ __('market.document_type') }}</div>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden">
                                <button type="button" @click="docType='receipt'"
                                        :class="docType==='receipt' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                                        class="flex-1 h-10 text-sm font-medium transition">
                                    {{ __('market.receipt') }}
                                </button>
                                <button type="button" @click="docType='invoice'"
                                        :class="docType==='invoice' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                                        class="flex-1 h-10 text-sm font-medium transition border-l border-slate-200">
                                    {{ __('market.invoice') }}
                                </button>
                            </div>
                            <input type="hidden" name="document_type" :value="docType">
                        </div>

                        {{-- Invoice fields --}}
                        <div x-show="docType==='invoice'" x-cloak class="space-y-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
                            <div class="text-xs font-semibold text-blue-800 mb-1">{{ __('market.invoice_details') }}</div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.vat_country') }}</label>
                                @include('public._eu_country_select', ['name' => 'vat_country', 'xModel' => 'vatCountry', 'selected' => old('vat_country', 'GR')])
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.vat_number') }}</label>
                                <div class="flex gap-2">
                                    <input type="text" name="vat_number" x-model="vatNumber"
                                           placeholder="e.g. 123456789"
                                           class="flex-1 h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                    <button type="button" @click="verifyVat()"
                                            :disabled="vatVerifying"
                                            class="h-10 px-4 rounded-xl border border-blue-200 bg-white text-sm text-blue-700 hover:bg-blue-50 transition flex-shrink-0 disabled:opacity-50">
                                        <span x-show="!vatVerifying">{{ __('market.verify_vat') }}</span>
                                        <span x-show="vatVerifying" x-cloak>{{ __('market.vat_verifying') }}</span>
                                    </button>
                                </div>
                                <div x-show="vatVerified" x-cloak class="text-xs text-emerald-700 mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ __('market.vat_verified') }}
                                </div>
                                <div x-show="vatError" x-cloak class="text-xs text-red-600 mt-1" x-text="vatError"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.company_name') }}</label>
                                <input type="text" name="company_name" x-model="companyName"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </div>
                        </div>

                        {{-- Personal details --}}
                        <div class="space-y-3">
                            <div class="text-xs font-medium text-slate-400 uppercase tracking-wide">{{ __('market.personal_details') }}</div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.first_name') }}</label>
                                    <input name="name" type="text" required
                                           value="{{ old('name', $cust?->name ?? '') }}"
                                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('name') border-red-300 @enderror">
                                    @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.surname') }}</label>
                                    <input name="surname" type="text" required
                                           value="{{ old('surname', $cust?->surname ?? '') }}"
                                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('surname') border-red-300 @enderror">
                                    @error('surname') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.email_address') }}</label>
                                <input name="email" type="email" required
                                       value="{{ old('email', $cust?->email ?? '') }}"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('email') border-red-300 @enderror"
                                       placeholder="you@example.com">
                                @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.phone') }}</label>
                                <input name="phone" type="tel"
                                       value="{{ old('phone', $cust?->phone ?? '') }}"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                       placeholder="+30 210 000 0000">
                            </div>
                        </div>

                        {{-- Delivery address --}}
                        <div class="space-y-3">
                            <div class="text-xs font-medium text-slate-400 uppercase tracking-wide">{{ __('market.delivery_address') }}</div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.address') }}</label>
                                <input name="delivery_address" type="text" required
                                       value="{{ old('delivery_address', $cust?->address ?? '') }}"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('delivery_address') border-red-300 @enderror">
                                @error('delivery_address') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.city') }}</label>
                                    <input name="delivery_city" type="text" required
                                           value="{{ old('delivery_city', $cust?->city ?? '') }}"
                                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('delivery_city') border-red-300 @enderror">
                                    @error('delivery_city') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.zip_code') }}</label>
                                    <input name="delivery_zip" type="text" required
                                           value="{{ old('delivery_zip', $cust?->zip_code ?? '') }}"
                                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('delivery_zip') border-red-300 @enderror">
                                    @error('delivery_zip') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.country_label') }}</label>
                                @include('public._country_select', ['name' => 'delivery_country', 'selected' => old('delivery_country', $cust?->country ?? 'GR')])
                                @error('delivery_country') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Create account (guests only) --}}
                        @if(!$cust)
                        <div x-data="{ open: createAccount }" class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="create_account" value="1"
                                       x-model="open"
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
                                <span class="text-sm font-medium text-slate-700">{{ __('market.create_account_prompt') }}</span>
                            </label>
                            <div x-show="open" x-cloak class="mt-3 space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.password') }}</label>
                                    <input type="password" name="password"
                                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('password') border-red-300 @enderror"
                                           placeholder="••••••••">
                                    @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.confirm_password') }}</label>
                                    <input type="password" name="password_confirmation"
                                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                           placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                        @endif

                        <button type="submit"
                                class="w-full h-11 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                            {{ __('market.place_order_btn') }}
                        </button>

                        <p class="text-xs text-slate-400 text-center">
                            {{ __('market.payment_instructions_sent') }}
                        </p>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-400">
    {{ __('market.footer_tagline_short') }}
</footer>

</body>
</html>
