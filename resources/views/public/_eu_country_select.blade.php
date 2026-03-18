@php
$euCountries = [
    'AT' => 'Austria', 'BE' => 'Belgium', 'BG' => 'Bulgaria', 'CY' => 'Cyprus',
    'CZ' => 'Czech Republic', 'DE' => 'Germany', 'DK' => 'Denmark',
    'EE' => 'Estonia', 'ES' => 'Spain', 'FI' => 'Finland', 'FR' => 'France',
    'GR' => 'Greece', 'HR' => 'Croatia', 'HU' => 'Hungary', 'IE' => 'Ireland',
    'IT' => 'Italy', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'LV' => 'Latvia',
    'MT' => 'Malta', 'NL' => 'Netherlands', 'PL' => 'Poland', 'PT' => 'Portugal',
    'RO' => 'Romania', 'SE' => 'Sweden', 'SI' => 'Slovenia', 'SK' => 'Slovakia',
];
$name     = $name ?? 'vat_country';
$selected = $selected ?? 'GR';
@endphp
<select name="{{ $name }}" {{ isset($xModel) ? "x-model=\"{$xModel}\"" : '' }}
        class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
    @foreach($euCountries as $code => $label)
        <option value="{{ $code }}" {{ $selected === $code ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
