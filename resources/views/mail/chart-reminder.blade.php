<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Checkout!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            color: #1e40af;
            margin-bottom: 10px;
        }

        p {
            margin: 10px 0;
            font-size: 15px;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .cta {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #2563eb;
            color: #fff !important;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
        }

        .cta:hover {
            background: #1d4ed8;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Halo, {{ $user->name }}</h2>
        <p>Kamu masih punya produk di keranjang yang belum di-checkout:</p>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartItems as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Yuk segera selesaikan pesananmu sebelum kehabisan!</p>

        <a href="{{ route('checkout.index') }}" class="cta">Lanjutkan Checkout</a>

        <div class="footer">
            &copy; {{ date('Y') }} Trendigo Store. Semua hak dilindungi.
        </div>
    </div>
</body>

</html>
