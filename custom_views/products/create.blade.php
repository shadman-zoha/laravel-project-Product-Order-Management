<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Add New Product</h1>

        @if (session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 rounded shadow-md">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" name="name" id="name"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>

                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                <input type="text" name="sku" id="sku"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                @error('sku')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="unit" class="block text-sm font-medium text-gray-700">Unit (e.g., kg, liter, pieces)</label>
                <input type="text" name="unit" id="unit"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                @error('unit')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="unit_value" class="block text-sm font-medium text-gray-700">Unit Value (e.g., 1kg,
                    3liters)</label>
                <input type="text" name="unit_value" id="unit_value"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                @error('unit_value')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price</label>
                <input type="number" step="0.01" name="selling_price" id="selling_price"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                @error('selling_price')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price</label>
                <input type="number" step="0.01" name="purchase_price" id="purchase_price"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                @error('purchase_price')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="discount" class="block text-sm font-medium text-gray-700">Discount (%)</label>
                <input type="number" step="0.01" name="discount" id="discount"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                @error('discount')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="tax" class="block text-sm font-medium text-gray-700">Tax (%)</label>
                <input type="number" step="0.01" name="tax" id="tax"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                @error('tax')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Product Image</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full">
                @error('image')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <h2 class="text-xl font-semibold mb-4">Product Variations</h2>
            <div id="variations-container">
                <div class="variation mb-4 p-4 border rounded">
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Attribute Name (e.g., Color,
                            Size)</label>
                        <input type="text" name="variations[0][attribute_name]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Attribute Value (e.g., Red, L)</label>
                        <input type="text" name="variations[0][attribute_value]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Purchase Price</label>
                        <input type="number" step="0.01" name="variations[0][purchase_price]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Selling Price</label>
                        <input type="number" step="0.01" name="variations[0][selling_price]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                </div>
            </div>

            <button type="button" id="add-variation" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Another
                Variation</button>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save Product</button>
        </form>
    </div>

    <script>
    let variationCount = 1;
    document.getElementById('add-variation').addEventListener('click', () => {
        const container = document.getElementById('variations-container');
        const newVariation = document.createElement('div');
        newVariation.classList.add('variation', 'mb-4', 'p-4', 'border', 'rounded');
        newVariation.innerHTML = `
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">Attribute Name (e.g., Color, Size)</label>
                    <input type="text" name="variations[${variationCount}][attribute_name]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">Attribute Value (e.g., Red, L)</label>
                    <input type="text" name="variations[${variationCount}][attribute_value]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">Purchase Price</label>
                    <input type="number" step="0.01" name="variations[${variationCount}][purchase_price]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">Selling Price</label>
                    <input type="number" step="0.01" name="variations[${variationCount}][selling_price]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
            `;
        container.appendChild(newVariation);
        variationCount++;
    });
    </script>
</body>

</html>