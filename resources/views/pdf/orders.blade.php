<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Reports</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #f2f2f2;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table th {
            font-weight: bold;
        }

        table tbody tr:nth-child(even) {
            background-color: #fafafa;
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
            <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
        </div>
    </div>


    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Order Status</th>
                <th>Code</th>
                <th>Username</th>
                <th>Payment Type</th>
                <th>Number of product</th>
                <th>Date</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->order_status }}</td>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ $order->midtrans_payment_type }}</td>
                    <td>{{ $order->order_items->count() }}</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i:s') }}</td>
                    <td>{{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style=" text-align:center">Grand Total</td>
                <td>{{ number_format(collect($orders)->sum('total'), '0', ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
