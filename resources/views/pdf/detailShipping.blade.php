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
            padding: 0;
        }

        .label {
            width: 100%;
            padding: 16px;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .section {
            margin-bottom: 12px;
            width: 100%;
            padding: 16px;
            box-sizing: border-box;
        }

        .section p {
            margin: 2px 0;
        }

        .from-to {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .from-to .box-2 {
            margin-top: 5px;
        }

        .box {
            border: 1px solid #000;
            border-radius: 6px;
            padding: 8px;
            flex: 1;
        }

        .box h4 {
            margin: 0 0 4px 0;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="label">
        <div class="header">
            <p><strong>{{ $order->shipping_name }} - {{ $order->shipping_service }}</strong></p>
        </div>

        <div class="from-to">
            <div class="box box-1">
                <h4>From</h4>
                <p><strong>Trendigo</strong></p>
                <p>Kreo, Larangan</p>
                <p>Kota Tangerang Selatan, 15156</p>
            </div>
            <div class="box box-2">
                <h4>To</h4>
                <p><strong>{{ $order->shippingAddress->recipient_name }}</strong>
                    ({{ $order->shippingAddress->phone }})</p>
                <p>
                    KEL. {{ $order->shippingAddress->sub_district }},
                    KEC. {{ $order->shippingAddress->district }},
                    {{ $order->shippingAddress->city }},
                    {{ $order->shippingAddress->province }} -
                    {{ $order->shippingAddress->postal_code }}
                </p>
                <p>{{ $order->shippingAddress->address }}</p>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->order_items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <small>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </small>
</body>

</html>
