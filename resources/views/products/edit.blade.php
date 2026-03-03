<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }} - {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                               class="shadow appearance-none border @error('name') border-red-500 @enderror rounded w-full py-2 px-3" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">SKU * (Unique)</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" 
                               class="shadow appearance-none border @error('sku') border-red-500 @enderror rounded w-full py-2 px-3" maxlength="50" required>
                        @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Stock Quantity *</label>
                        <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock) }}" 
                               class="shadow appearance-none border @error('stock') border-red-500 @enderror rounded w-full py-2 px-3" required>
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Price * ($)</label>
                        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" 
                               class="shadow appearance-none border @error('price') border-red-500 @enderror rounded w-full py-2 px-3" required>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-green-500  rounded hover:bg-green-600 font-semibold">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
