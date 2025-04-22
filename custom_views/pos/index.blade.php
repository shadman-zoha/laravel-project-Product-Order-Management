<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-item input {
        width: 60px;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4 flex">
        <!-- Product Section -->
        <div class="w-3/4 pr-4">
            <h1 class="text-xl font-bold mb-2">Product Section</h1>
            <div class="mb-4">
                <form method="GET" action="{{ route('pos.index') }}" class="flex items-center">
                    <input type="text" name="search" placeholder="Filter product by product name or SKU"
                        class="border p-2 rounded w-full" value="{{ request('search') }}">
                    <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">Search</button>
                </form>
            </div>

            <div class="grid grid-cols-3 gap-4">
                @foreach ($products as $product)
                <div class="bg-white p-4 rounded shadow-md">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/150' }}"
                        alt="{{ $product->name }}" class="w-full h-32 object-cover mb-2">
                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p>{{ $product->unit }}: {{ $product->unit_value }}</p>
                    <p class="text-gray-600">Price: ${{ $product->selling_price }}</p>
                    @if ($product->discount > 0)
                    <p class="text-green-600">Discounted:
                        ${{ number_format($product->selling_price * (1 - $product->discount / 100), 2) }}</p>
                    @endif
                    @if ($product->variations->count() > 0)
                    <select class="variation-select w-full mt-2 border p-1 rounded" data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}">
                        <option value="">Select Variation</option>
                        @foreach ($product->variations as $variation)
                        <option value="{{ $variation->id }}" data-price="{{ $variation->selling_price }}"
                            data-variation-name="{{ $variation->attribute_name }}: {{ $variation->attribute_value }}">
                            {{ $variation->attribute_name }}: {{ $variation->attribute_value }}
                            (${{ $variation->selling_price }})
                        </option>
                        @endforeach
                    </select>
                    @endif
                    <button class="add-to-cart bg-blue-500 text-white px-4 py-2 rounded mt-2 w-full"
                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
                        data-price="{{ $product->selling_price }}" data-discount="{{ $product->discount }}"
                        data-tax="{{ $product->tax }}" @if ($product->variations->count() == 0)
                        data-has-variation="false"
                        @endif>
                        Add to Cart
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Billing Section -->
        <div class="w-1/4 bg-white p-4 rounded shadow-md">
            <h1 class="text-xl font-bold mb-2">Billing Section</h1>
            <div id="cart-items" class="mb-4"></div>

            <div class="border-t pt-2">
                <p>Sub Total: $<span id="subtotal">0.00</span></p>
                <p>Product Discount: $<span id="discount">0.00</span></p>
                <p>Tax: $<span id="tax">0.00</span></p>
                <p class="font-bold">Total: $<span id="total">0.00</span></p>
            </div>

            <button id="place-order" class="bg-green-500 text-white px-4 py-2 rounded w-full">Place Order</button>
        </div>
    </div>

    <script>
    let cart = [];

    // Add to Cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            let price = parseFloat(button.dataset.price);
            const discount = parseFloat(button.dataset.discount);
            const tax = parseFloat(button.dataset.tax);
            const hasVariation = button.dataset.hasVariation !== 'false';

            let variationId = null;
            let variationName = '';
            if (hasVariation) {
                const select = button.parentElement.querySelector('.variation-select');
                if (!select.value) {
                    alert('Please select a variation!');
                    return;
                }
                variationId = select.value;
                variationName = select.options[select.selectedIndex].dataset.variationName;
                price = parseFloat(select.options[select.selectedIndex].dataset.price);
            }

            const cartItem = {
                product_id: productId,
                variation_id: variationId,
                name: hasVariation ? `${productName} (${variationName})` : productName,
                price: price,
                discount: discount,
                tax: tax,
                quantity: 1
            };

            const existingItem = cart.find(item =>
                item.product_id === cartItem.product_id &&
                item.variation_id === cartItem.variation_id
            );

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push(cartItem);
            }

            updateCart();
        });
    });

    // Update Cart Display
    function updateCart() {
        const cartItemsDiv = document.getElementById('cart-items');
        cartItemsDiv.innerHTML = '';

        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;

        cart.forEach((item, index) => {
            const itemSubtotal = item.price * item.quantity;
            const itemDiscount = (item.price * (item.discount / 100)) * item.quantity;
            const itemTax = ((item.price - (item.price * (item.discount / 100))) * (item.tax / 100)) * item
                .quantity;

            subtotal += itemSubtotal;
            totalDiscount += itemDiscount;
            totalTax += itemTax;

            const cartItemDiv = document.createElement('div');
            cartItemDiv.classList.add('cart-item', 'mb-2');
            cartItemDiv.innerHTML = `
                    <span>${item.name}</span>
                    <div>
                        <input type="number" min="1" value="${item.quantity}" class="quantity-input border p-1 rounded" data-index="${index}">
                        <span>$${itemSubtotal.toFixed(2)}</span>
                        <button class="remove-item text-red-500 ml-2" data-index="${index}">✕</button>
                    </div>
                `;
            cartItemsDiv.appendChild(cartItemDiv);
        });

        const total = subtotal - totalDiscount + totalTax;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('discount').textContent = totalDiscount.toFixed(2);
        document.getElementById('tax').textContent = totalTax.toFixed(2);
        document.getElementById('total').textContent = total.toFixed(2);

        // Update quantities
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const index = e.target.dataset.index;
                const newQuantity = parseInt(e.target.value);
                if (newQuantity < 1) {
                    cart.splice(index, 1);
                } else {
                    cart[index].quantity = newQuantity;
                }
                updateCart();
            });
        });

        // Remove items
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = e.target.dataset.index;
                cart.splice(index, 1);
                updateCart();
            });
        });
    }

    // Place Order
    document.getElementById('place-order').addEventListener('click', () => {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        const subtotal = parseFloat(document.getElementById('subtotal').textContent);
        const discount = parseFloat(document.getElementById('discount').textContent);
        const tax = parseFloat(document.getElementById('tax').textContent);
        const total = parseFloat(document.getElementById('total').textContent);

        // Add validation to ensure values are valid numbers
        if (isNaN(subtotal) || isNaN(discount) || isNaN(tax) || isNaN(total)) {
            alert('Invalid cart totals. Please refresh the page and try again.');
            return;
        }

        const requestData = {
            items: cart,
            subtotal: subtotal,
            discount: discount,
            tax: tax,
            total: total
        };
        console.log('Sending Place Order Request:', requestData);

        fetch('{{ route("pos.placeOrder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response Status:', response.status);
                console.log('Response Headers:', response.headers.get('content-type'));
                return response.text();
            })
            .then(text => {
                console.log('Raw Response:', text);
                return JSON.parse(text);
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    cart = [];
                    updateCart();
                } else {
                    alert('Failed to place order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to place order: ' + error.message);
            });
    });
    </script>
</body>

</html>