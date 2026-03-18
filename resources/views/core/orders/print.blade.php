<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} — AgroFlux</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; font-size: 13px; color: #1e293b; background: #fff; padding: 32px; }

        /* Header */
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #064e3b; }
        .logo-block .logo { font-size: 20px; font-weight: 800; color: #064e3b; }
        .logo-block .logo span { color: #059669; }
        .logo-block .sub { font-size: 11px; color: #64748b; margin-top: 3px; }
        .doc-meta { text-align: right; }
        .doc-meta h1 { font-size: 18px; font-weight: 700; color: #1e293b; }
        .doc-meta .ref { font-size: 12px; color: #64748b; margin-top: 3px; }
        .status-badge { display: inline-block; margin-top: 7px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        /* Info grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .info-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; }
        .info-box h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #059669; padding-bottom: 5px; margin-bottom: 8px; border-bottom: 1px solid #d1fae5; }
        .info-box p { color: #334155; line-height: 1.8; font-size: 12.5px; }
        .info-box p strong { color: #0f172a; font-weight: 600; }
        .info-box .row { display: flex; gap: 6px; align-items: baseline; }
        .info-box .row .lbl { color: #94a3b8; font-size: 11px; min-width: 70px; }
        .info-box .row .val { color: #1e293b; font-size: 12.5px; word-break: break-all; }
        .info-box .val-mono { font-family: monospace; font-size: 12px; color: #1e40af; }

        /* Payment box (full width) */
        .payment-box { margin-bottom: 24px; border: 1px solid #bfdbfe; border-radius: 8px; background: #eff6ff; padding: 14px 16px; }
        .payment-box h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #1d4ed8; padding-bottom: 5px; margin-bottom: 10px; border-bottom: 1px solid #bfdbfe; }
        .payment-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .pay-group { }
        .pay-group .pg-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #3b82f6; margin-bottom: 5px; }
        .pay-row { display: flex; gap: 8px; align-items: baseline; margin-bottom: 3px; }
        .pay-row .pl { color: #64748b; font-size: 11px; min-width: 65px; flex-shrink: 0; }
        .pay-row .pv { color: #1e293b; font-size: 12.5px; }
        .pay-row .pv-mono { font-family: monospace; font-size: 12px; color: #1e40af; letter-spacing: 0.02em; }
        .iris-box { background: #dbeafe; border: 1px solid #93c5fd; border-radius: 6px; padding: 8px 12px; margin-top: 6px; display: flex; align-items: center; gap: 8px; }
        .iris-box .iris-label { font-size: 11px; color: #1d4ed8; font-weight: 600; }
        .iris-box .iris-value { font-size: 14px; color: #1e40af; font-weight: 700; letter-spacing: 0.05em; }

        /* Items table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #f8fafc; }
        th { padding: 9px 12px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #475569; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tbody tr:last-child td { border-bottom: none; }
        .tr { text-align: right; }
        .fw { font-weight: 600; color: #0f172a; }

        /* Totals */
        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 24px; }
        .totals { width: 260px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 14px; font-size: 13px; border-bottom: 1px solid #f1f5f9; color: #475569; }
        .totals-row:last-child { border-bottom: none; border-top: 2px solid #064e3b; font-weight: 700; font-size: 14px; color: #064e3b; background: #f0fdf4; }

        /* Notice */
        .notice { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
        .notice h4 { font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 5px; }
        .notice p { font-size: 12px; color: #78350f; line-height: 1.6; }

        /* Footer */
        .doc-footer { border-top: 1px solid #e2e8f0; padding-top: 12px; display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; }

        @media print {
            body { padding: 20px; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="doc-header">
        <div class="logo-block">
            <div class="logo">Agro<span>Flux</span></div>
            <div class="sub">AgroFlux Marketplace</div>
        </div>
        <div class="doc-meta">
            <h1>Order Confirmation</h1>
            <div class="ref">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp; {{ $order->created_at->format('d M Y, H:i') }}</div>
            <span class="status-badge">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
        </div>
    </div>

    {{-- Seller & Buyer --}}
    <div class="info-grid">
        {{-- Seller --}}
        <div class="info-box">
            <h3>Seller — Farm / Producer</h3>
            @if($order->tenant)
                <p><strong>{{ $order->tenant->name }}</strong></p>
                @if($order->tenant->location_name)
                    <div class="row"><span class="lbl">Location</span><span class="val">{{ $order->tenant->location_name }}</span></div>
                @endif
            @endif
            @if($seller)
                @if($seller->name || $seller->surname)
                    <div class="row"><span class="lbl">Contact</span><span class="val">{{ trim(($seller->name ?? '').' '.($seller->surname ?? '')) }}</span></div>
                @endif
                @if($seller->phone)
                    <div class="row"><span class="lbl">Phone</span><span class="val">{{ $seller->phone }}</span></div>
                @endif
                @if($seller->email)
                    <div class="row"><span class="lbl">Email</span><span class="val">{{ $seller->email }}</span></div>
                @endif
            @endif
            @if(!$order->tenant && !$seller)
                <p style="color:#94a3b8;">Seller details unavailable</p>
            @endif
        </div>

        {{-- Buyer --}}
        <div class="info-box">
            <h3>Buyer</h3>
            <p><strong>{{ $order->customer_name }}</strong></p>
            <div class="row"><span class="lbl">Email</span><span class="val">{{ $order->customer_email }}</span></div>
            <div class="row"><span class="lbl">Date</span><span class="val">{{ $order->created_at->format('d M Y') }}</span></div>
        </div>
    </div>

    {{-- Payment Details --}}
    @if($seller && ($seller->bank_name || $seller->iban || $seller->iris_number))
    <div class="payment-box">
        <h3>💳 Payment Details — How to Pay</h3>
        <div class="payment-cols">
            {{-- Bank Transfer --}}
            <div class="pay-group">
                <div class="pg-label">🏦 Bank Transfer (SEPA / Wire)</div>
                @if($seller->bank_name)
                    <div class="pay-row"><span class="pl">Bank</span><span class="pv">{{ $seller->bank_name }}</span></div>
                @endif
                @if($seller->iban)
                    <div class="pay-row"><span class="pl">IBAN</span><span class="pv pv-mono">{{ $seller->iban }}</span></div>
                @endif
                @if($seller->name || $seller->surname)
                    <div class="pay-row"><span class="pl">Name</span><span class="pv">{{ trim(($seller->name ?? '').' '.($seller->surname ?? '')) }}</span></div>
                @endif
                @if(!$seller->bank_name && !$seller->iban)
                    <p style="color:#94a3b8;font-size:11px;">Bank details not provided. Contact seller directly.</p>
                @endif
            </div>

            {{-- IRIS --}}
            <div class="pay-group">
                <div class="pg-label">⚡ IRIS Instant Payment</div>
                @if($seller->iris_number)
                    <p style="font-size:11px;color:#475569;margin-bottom:6px;">Send instantly via your mobile banking app using the IRIS number below:</p>
                    <div class="iris-box">
                        <div>
                            <div class="iris-label">IRIS Number</div>
                            <div class="iris-value">{{ $seller->iris_number }}</div>
                        </div>
                    </div>
                @else
                    <p style="color:#94a3b8;font-size:11px;">IRIS not available for this seller. Use bank transfer above.</p>
                @endif
            </div>
        </div>
    </div>
    @else
    {{-- Notice when seller has no payment info --}}
    <div class="notice">
        <h4>⚡ Wire Transfer Required</h4>
        <p>
            Please contact <strong>{{ $order->tenant?->name ?? 'the seller' }}</strong> directly to obtain bank transfer details.
            Your order will be processed once payment is received.
        </p>
    </div>
    @endif

    {{-- Items --}}
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Type</th>
                <th>Unit</th>
                <th class="tr">Qty</th>
                <th class="tr">Unit Price</th>
                <th class="tr">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $it)
            <tr>
                <td class="fw">{{ $it->listing?->product?->default_name ?? '—' }}</td>
                <td>{{ ucfirst($it->listing?->type ?? '—') }}</td>
                <td>{{ $it->listing?->product?->unit ?? '—' }}</td>
                <td class="tr">{{ $it->qty }}</td>
                <td class="tr">€{{ number_format($it->price, 2) }}</td>
                <td class="tr fw">€{{ number_format($it->price * $it->qty, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrap">
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>€{{ number_format($order->total, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Total Due</span>
                <span>€{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="doc-footer">
        <span>Generated by AgroFlux Marketplace &nbsp;·&nbsp; {{ now()->format('d M Y, H:i') }}</span>
        <span>Order ref: #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>

</body>
<script>window.print();</script>
</html>
