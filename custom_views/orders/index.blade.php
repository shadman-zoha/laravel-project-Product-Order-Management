<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Order List</h1>

        <div class="mb-4">
            <form method="GET" action="{{ route('orders.index') }}" class="flex items-center">
                <input type="date" name="date" class="border p-2 rounded" value="{{ request('date') }}">
                <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            </form>
        </div>

        <table class="w-full bg-white rounded shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Order ID</th>
                    <th class="p-2 text-left">Items</th>
                    <th class="p-2 text-left">Subtotal</th>
                    <th class="p-2 text-left">Discount</th>
                    <th class="p-2 text-left">Tax</th>
                    <th class="p-2 text-left">Total</th>
                    <th class="p-2 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td class="p-2">{{ $order->id }}</td>
                    <td class="p-2">
                        @php
                        $items = json_decode($order->items, true);
                        @endphp
                        <ul>
                            @foreach ($items as $item)
                            <li>{{ $item['name'] }} (Qty: {{ $item['quantity'] }}) -
                                ${{ $item['price'] * $item['quantity'] }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="p-2">${{ $order->subtotal }}</td>
                    <td class="p-2">${{ $order->discount }}</td>
                    <td class="p-2">${{ $order->tax }}</td>
                    <td class="p-2">${{ $order->total }}</td>
                    <td class="p-2">{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</body>

</html>