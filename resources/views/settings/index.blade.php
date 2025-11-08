@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Pengaturan</h1>
        <p class="text-gray-600 mt-2">Kelola pengaturan aplikasi</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <!-- General Settings -->
                <div>
                    <h2 class="text-xl font-semibold mb-4 pb-2 border-b">Pengaturan Umum</h2>
                    
                    <div class="mb-4">
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Aplikasi
                        </label>
                        <input type="text" name="app_name" id="app_name" 
                            value="{{ old('app_name', config('app.name')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Nama yang akan ditampilkan di aplikasi</p>
                    </div>

                    <div class="mb-4">
                        <label for="app_timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Zona Waktu
                        </label>
                        <select name="app_timezone" id="app_timezone" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Asia/Jakarta" {{ old('app_timezone', config('app.timezone')) == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" {{ old('app_timezone', config('app.timezone')) == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" {{ old('app_timezone', config('app.timezone')) == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-2">
                            Simbol Mata Uang
                        </label>
                        <input type="text" name="currency_symbol" id="currency_symbol" 
                            value="{{ old('currency_symbol', 'Rp') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Simbol yang digunakan untuk menampilkan harga (contoh: Rp, $)</p>
                    </div>
                </div>

                <!-- Display Settings -->
                <div>
                    <h2 class="text-xl font-semibold mb-4 pb-2 border-b">Pengaturan Tampilan</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Format Tanggal
                        </label>
                        <select name="date_format" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="d/m/Y">DD/MM/YYYY (contoh: 02/11/2025)</option>
                            <option value="Y-m-d">YYYY-MM-DD (contoh: 2025-11-02)</option>
                            <option value="d M Y">DD MMM YYYY (contoh: 02 Nov 2025)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Format Waktu
                        </label>
                        <select name="time_format" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="H:i">24 Jam (contoh: 14:30)</option>
                            <option value="h:i A">12 Jam (contoh: 02:30 PM)</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4 border-t">
                    <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Simpan Pengaturan
                    </button>
                    <a href="{{ route('dashboard') }}" 
                        class="px-6 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Additional Settings Sections -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- System Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Informasi Sistem</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Versi Laravel:</span>
                    <span class="font-medium">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Environment:</span>
                    <span class="font-medium">{{ config('app.env') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Debug Mode:</span>
                    <span class="font-medium">{{ config('app.debug') ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Aksi Cepat</h2>
            <div class="space-y-2">
                <a href="{{ route('products.index') }}" 
                    class="block px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    <i class="bi bi-box-seam mr-2"></i>Kelola Produk
                </a>
                <a href="{{ route('admin.users.index') }}" 
                    class="block px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    <i class="bi bi-people mr-2"></i>Kelola Pengguna
                </a>
                <a href="{{ route('reports.index') }}" 
                    class="block px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    <i class="bi bi-bar-chart mr-2"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

