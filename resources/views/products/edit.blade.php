@extends('layouts.admin')

@section('content')
<div class="px-6 py-8">
    <div class="bg-white shadow rounded-xl p-6 w-full max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">Edit Produk</h1>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                    class="w-full rounded border px-3 py-2">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded border px-3 py-2">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Barcode <span class="text-red-500">*</span></label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" required
                    class="w-full rounded border px-3 py-2">
                @error('barcode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Harga Modal <span class="text-red-500">*</span></label>
                <input type="number" name="harga_modal" id="harga_modal" value="{{ old('harga_modal', $product->harga_modal) }}" min="0" step="1" required
                    class="w-full rounded border px-3 py-2">
                @error('harga_modal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Harga Jual <span class="text-red-500">*</span></label>
                <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', $product->harga_jual) }}" min="0" step="1" required
                    class="w-full rounded border px-3 py-2">
                @error('harga_jual')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Kategori</label>
                <select name="category_id" id="category_id"
                    class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Subkategori</label>
                <select name="subcategory_id" id="subcategory_id"
                    class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Pilih Subkategori --</option>
                    @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}"
                            {{ $product->subcategory_id == $subcategory->id ? 'selected' : '' }}>
                            {{ $subcategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔹 Bahan Baku --}}
            <div class="mb-6">
                <label class="block font-semibold mb-2">Bahan Baku yang Digunakan</label>
                @foreach ($ingredients as $ingredient)
                    @php
                        $pivot = $product->ingredients->find($ingredient->id)?->pivot;
                    @endphp
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="ingredients[{{ $ingredient->id }}][selected]" value="1"
                            {{ $pivot ? 'checked' : '' }} class="mr-2">
                        <span class="w-40">{{ $ingredient->name }}</span>
                        <input type="number" name="ingredients[{{ $ingredient->id }}][quantity]"
                            value="{{ $pivot->quantity ?? 0 }}" step="0.01" min="0"
                            class="ml-2 w-24 border rounded p-1 text-sm">
                        <span class="ml-1 text-gray-500 text-sm">{{ $ingredient->unit }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mb-6">
                <label class="block font-medium">Gambar Produk</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- Current Image Display -->
                @if ($product->hasImage() && $product->image_url)
                    <div class="mt-3">
                        <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            id="current-image" class="h-40 max-h-40 object-cover rounded-lg shadow-sm border border-gray-300"
                            style="max-width: 320px; max-height: 160px;"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2U1ZTdlYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5Y2EzYWYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub3QgZm91bmQ8L3RleHQ+PC9zdmc+'; this.style.objectFit='contain';">
                    </div>
                @endif

                <!-- New Image Preview -->
                <div id="image-preview" class="mt-4 hidden">
                    <p class="text-sm text-gray-600 mb-2">Preview gambar baru:</p>
                    <img id="preview-img" src="" alt="Preview" class="max-w-xs h-40 max-h-40 object-cover rounded-lg shadow-sm border border-gray-300"
                         style="max-width: 320px; max-height: 160px;">
                    <button type="button" id="remove-preview" class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Hapus Preview
                    </button>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                    Update
                </button>
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-400 text-white text-sm font-medium rounded hover:bg-gray-500">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Image Preview Functionality
    const imageInput = document.getElementById('image');
    const previewDiv = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeBtn = document.getElementById('remove-preview');
    const currentImage = document.getElementById('current-image');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.classList.remove('hidden');
                    if (currentImage) {
                        currentImage.classList.add('opacity-50');
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                imageInput.value = '';
                previewDiv.classList.add('hidden');
                previewImg.src = '';
                if (currentImage) {
                    currentImage.classList.remove('opacity-50');
                }
            });
        }
    }
});
</script>
@endsection
