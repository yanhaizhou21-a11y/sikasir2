@extends('layouts.admin')

@section('content')
<div class="px-6 py-8">
    <div class="bg-white shadow rounded-xl p-6 w-full max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">Tambah Produk</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode <span class="text-red-500">*</span></label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('barcode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="harga_modal" class="block font-medium">Harga Modal <span class="text-red-500">*</span></label>
                <input type="number" name="harga_modal" id="harga_modal" value="{{ old('harga_modal') }}" min="0" step="1" required 
                    class="w-full rounded border px-3 py-2">
                @error('harga_modal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="harga_jual" class="block font-medium">Harga Jual <span class="text-red-500">*</span></label>
                <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}" min="0" step="1" required 
                    class="w-full rounded border px-3 py-2">
                @error('harga_jual')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                <select id="category_id" name="category_id" onchange="filterSubcategories(this.value)">
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="subcategory_id" class="block text-sm font-medium text-gray-700">Subkategori</label>
                <select name="subcategory_id" id="subcategory_id"
                    class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                    <option value="">-- Pilih Subkategori --</option>
                    @foreach ($subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}" data-category="{{ $subcategory->category_id }}">
                        {{ $subcategory->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔹 Bahan Baku --}}
            <div class="mb-6">
                <label class="block font-semibold mb-2">Bahan Baku yang Digunakan</label>
                @foreach ($ingredients as $ingredient)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="ingredients[{{ $ingredient->id }}][selected]" value="1"
                            class="mr-2">
                        <span class="w-40">{{ $ingredient->name }}</span>
                        <input type="number" name="ingredients[{{ $ingredient->id }}][quantity]" step="0.01" min="0"
                            class="ml-2 w-24 border rounded p-1 text-sm" placeholder="Qty">
                        <span class="ml-1 text-gray-500 text-sm">{{ $ingredient->unit }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <!-- Image Preview -->
                <div id="image-preview" class="mt-4 hidden">
                    <img id="preview-img" src="" alt="Preview" class="max-w-xs h-40 max-h-40 object-cover rounded-lg shadow-sm border border-gray-300"
                         style="max-width: 320px; max-height: 160px;">
                    <button type="button" id="remove-preview" class="mt-2 px-3 py-1.5 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors">
                        Hapus Preview
                    </button>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                    Simpan
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
function filterSubcategories(categoryId) {
    document.querySelectorAll('#subcategory_id option').forEach(option => {
        option.style.display = option.getAttribute('data-category') === categoryId ? 'block' : 'none';
    });

    const firstMatch = document.querySelector(`#subcategory_id option[data-category="${categoryId}"]`);
    if (firstMatch) firstMatch.selected = true;
}
document.addEventListener('DOMContentLoaded', () => {
    const selectedCategory = document.getElementById('category_id').value;
    filterSubcategories(selectedCategory);

    // Image Preview Functionality
    const imageInput = document.getElementById('image');
    const previewDiv = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeBtn = document.getElementById('remove-preview');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    removeBtn.addEventListener('click', function() {
        imageInput.value = '';
        previewDiv.classList.add('hidden');
        previewImg.src = '';
    });
});
</script>
@endsection
