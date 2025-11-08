<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->invoice }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }
        .container { max-width: 360px; margin: 0 auto; padding: 16px; }
        .center { text-align: center; }
        .muted { color: #6b7280; font-size: 12px; }
        .bold { font-weight: 700; }
        .items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .items th, .items td { padding: 6px 0; font-size: 12px; }
        .items tr + tr { border-top: 1px dashed #e5e7eb; }
        .total { margin-top: 8px; padding-top: 8px; border-top: 1px dashed #9ca3af; font-size: 14px; }
        .btns { margin-top: 16px; display: flex; gap: 8px; }
        .btn { flex: 1; padding: 8px 10px; font-size: 12px; border-radius: 6px; cursor: pointer; border: 1px solid #e5e7eb; background: #f9fafb; }
        .btn.primary { background: #005281; color: #fff; border-color: #005281; }
        .right { text-align: right; }
        img.logo { max-height: 48px; margin-bottom: 8px; }
    </style>
    <script>
        function printReceipt() { window.print(); }
        function sendDigital() {
            alert('Kirim struk digital: Integrasi WhatsApp/Email dapat ditambahkan di sini.');
        }
    </script>
    </head>
<body>
    <div class="container">
        <div class="center">
            <img class="logo" src="{{ asset('assets/image/logo.png') }}" alt="Logo">
            <div class="bold">Struk Pembelian</div>
            <div class="muted">Invoice: {{ $transaction->invoice }}</div>
        </div>

        <div style="margin-top:8px; font-size:12px;">
            <div><span class="muted">Kasir:</span> <span class="bold">{{ $transaction->user->name ?? 'Kasir' }}</span></div>
            <div><span class="muted">Waktu:</span> {{ \Carbon\Carbon::parse($transaction->transaction_time ?? $transaction->created_at)->format('d/m/Y H:i') }}</div>
            <div><span class="muted">Metode:</span> {{ strtoupper($transaction->payment_method) }}</div>
            <div><span class="muted">Status:</span> {{ strtoupper($transaction->status) }}</div>
        </div>

        @php
            $groupedItems = [];
            foreach ($transaction->items as $it) {
                $key = $it->product_id . '|' . $it->harga_jual;
                if (!isset($groupedItems[$key])) {
                    $groupedItems[$key] = [
                        'name' => $it->product->name,
                        'qty' => 0,
                        'harga_jual' => $it->harga_jual,
                        'subtotal' => 0,
                    ];
                }
                $groupedItems[$key]['qty'] += (int) $it->qty;
                $groupedItems[$key]['subtotal'] += (int) $it->subtotal;
            }
        @endphp

        <table class="items">
            <thead>
                <tr>
                    <th align="left">Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtot.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedItems as $g)
                <tr>
                    <td>{{ $g['name'] }}</td>
                    <td class="right">{{ $g['qty'] }}</td>
                    <td class="right">Rp {{ number_format($g['harga_jual'],0,',','.') }}</td>
                    <td class="right">Rp {{ number_format($g['subtotal'],0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <div class="right bold">Total: Rp {{ number_format($transaction->total,0,',','.') }}</div>
        </div>

        <div class="center muted" style="margin-top:8px;">Terima kasih telah berbelanja!</div>

        <div class="btns">
            <button class="btn primary" onclick="printReceipt()">Cetak Struk</button>
            <button class="btn" onclick="sendDigital()">Kirim Digital</button>
        </div>
    </div>
</body>
</html>


