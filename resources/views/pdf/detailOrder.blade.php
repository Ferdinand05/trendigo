<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            font-size: 15px;
            margin: 0 0 8px 0;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .box {
            flex: 1 1 calc(50% - 10px);
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            box-sizing: border-box;
        }

        .box p {
            margin: 2px 0;
        }

        .box-2 {
            margin-top: 5px;

        }

        .box-3 {
            margin-top: 5px;

        }

        .box-4 {
            margin-top: 5px;

        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
            text-align: left;
        }

        td {
            text-align: center;
        }

        td:first-child {
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            text-align: right;
            color: #666;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-left h1 {
            margin: 0;
            font-size: 20px;
        }

        .header-left p {
            margin: 0;
            font-size: 12px;
        }

        .header-right {
            text-align: right;
            font-size: 12px;
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
        </div>
    </div>


    <h1>Detail Order</h1>

    <div class="grid">
        <div class="box box-1">
            <h1>Order Info</h1>
            <p>Status : {{ $order->status }}</p>
            <p>Order Code : {{ $order->order_code }}</p>
            <p>Order Status : {{ $order->order_status }}</p>
            <p>Payment Type : {{ $order->midtrans_payment_type ?? '-' }}</p>
            <p>Total : Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            <p>Date : {{ $order->created_at }}</p>
        </div>

        <div class="box box-2">
            <h1>User Info</h1>
            <p>Pemesan : {{ $order->user->name }}</p>
            <p>Email : {{ $order->user->email }}</p>
            <p>Recipient Name : {{ $order->shippingAddress->recipient_name }}</p>
        </div>

        <div class="box box-3">
            <h1>Transaction Info</h1>
            <p>Transaction Status : {{ $order->midtrans_transaction_status ?? '-' }}</p>
            <p>Transaction Id : {{ $order->midtrans_transaction_id ?? '-' }}</p>
            <p>Fraud Status : {{ $order->fraud_status ?? '-' }}</p>
        </div>

        <div class="box box-4">
            <h1>Shipping Info</h1>
            <p>Ekspedisi : {{ $order->shipping_name ?? '-' }}</p>
            <p>Service : {{ $order->shipping_service }}</p>
            <p>Cost : Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
            <p>Etd : {{ $order->shipping_etd }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->order_items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>

</body>

</html>
