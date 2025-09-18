<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .order-header {
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .order-header .order-code {
            font-size: 16px;
            font-weight: bold;
        }

        .order-header .detail {
            margin-top: 6px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #f2f2f2;
        }

        thead th {
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        tbody td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        tfoot {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        tfoot td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }


        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .logo {
            display: table-cell;
            vertical-align: middle;
            width: 100px;
        }

        .company-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }

        .company-info h1 {
            margin: 0;
            font-size: 20px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 12px;
        }
    </style>

</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('img/trendigo-logo.svg') }}" alt="Trendigo Logo" width="90" height="90">
        </div>
        <div class="company-info">
            <h1>Trendigo</h1>
            <p>Jl. Jauh No.31, Larangan, Cilegud, Kota Tangerang Selatan</p>
            <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</p>
        </div>
    </div>


    <div class="order-header">
        <div class="order-code"># {{ $order->order_code }}</div>
        <div class="detail">
            <div>Nama: {{ $order->user->name }}</div>
            <div>Pembayaran: {{ $order->midtrans_payment_type }}</div>
            <div>Pengiriman : {{ $order->shipping_name }} - {{ $order->shipping_service }}</div>
            <div>Tanggal Order: {{ $order->created_at }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->order_items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                </tr>
            @endforeach
            <tr>
                <td>(Pengiriman) {{ $order->shipping_service }}</td>
                <td class="text-right">{{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">1</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">Total</td>
                <td class="text-center">{{ number_format($order->total ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
