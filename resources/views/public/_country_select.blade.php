@php
$countries = [
    'AL' => 'Albania', 'AD' => 'Andorra', 'AT' => 'Austria', 'BE' => 'Belgium',
    'BA' => 'Bosnia & Herzegovina', 'BG' => 'Bulgaria', 'HR' => 'Croatia',
    'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark',
    'EE' => 'Estonia', 'FI' => 'Finland', 'FR' => 'France', 'DE' => 'Germany',
    'GR' => 'Greece', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IE' => 'Ireland',
    'IT' => 'Italy', 'XK' => 'Kosovo', 'LV' => 'Latvia', 'LI' => 'Liechtenstein',
    'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MT' => 'Malta', 'MD' => 'Moldova',
    'MC' => 'Monaco', 'ME' => 'Montenegro', 'NL' => 'Netherlands',
    'MK' => 'North Macedonia', 'NO' => 'Norway', 'PL' => 'Poland',
    'PT' => 'Portugal', 'RO' => 'Romania', 'SM' => 'San Marino',
    'RS' => 'Serbia', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'ES' => 'Spain',
    'SE' => 'Sweden', 'CH' => 'Switzerland', 'TR' => 'Turkey', 'UA' => 'Ukraine',
    'GB' => 'United Kingdom', 'VA' => 'Vatican City',
];
$name     = $name ?? 'country';
$selected = $selected ?? '';
@endphp
<select name="{{ $name }}"
        class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
    <option value="">—</option>
    @foreach($countries as $code => $label)
        <option value="{{ $code }}" {{ $selected === $code ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
