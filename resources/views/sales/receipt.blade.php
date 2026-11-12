<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $sale->invoice_number }}</title>
    <style>
        body { font-family: monospace; width: 58mm; margin: 0 auto; padding: 5px; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()">Cetak</button>
        <a href="{{ route('sales.kasir') }}">Kembali ke Kasir</a>
    </div>

    <div class="text-center">
        <strong>{{ \App\Models\Setting::get('store_name') }}</strong><br>
        {{ \App\Models\Setting::get('store_address') }}<br>
        Telp: {{ \App\Models\Setting::get('store_phone') }}
    </div>

    <div class="line"></div>

    <div>
        No: {{ $sale->invoice_number }}<br>
        Tgl: {{ $sale->sale_date }}<br>
        Kasir: {{ $sale->user->name }}<br>
        Pelanggan: {{ $sale->customer->name ?? 'Umum' }}
    </div>

    <div class="line"></div>

    <table>
        @foreach($sale->items as $item)
            <tr><td colspan="2"><strong>{{ $item->product_name }}</strong></td></tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($sale->discount > 0)
        <tr>
            <td>Diskon:</td>
            <td class="text-right">Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>Total:</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($sale->payment_method) }}):</td>
            <td class="text-right">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="text-center">{{ \App\Models\Setting::get('receipt_footer') }}</div>
</body>
</html>