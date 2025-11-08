@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
<div class="container mx-auto px-4 py-12 max-w-7xl">
    
    <div class="mb-12 text-center lg:text-left">
        <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Profil Saya</h1>
        <p class="text-gray-600 text-base mt-2">Kelola informasi akun dan keamanan Anda.</p>
    </div>


    @if(session('status') === 'profile-updated')
        <div class="flex items-center justify-between bg-emerald-100 border border-emerald-200 text-emerald-900 px-5 py-3 rounded-lg mb-6 shadow-sm">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="bi bi-check-circle-fill"></i>
                <span>Profil berhasil diperbarui!</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-900 px-5 py-4 rounded-lg mb-6 shadow-sm">
            <p class="font-medium text-sm mb-2">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-md border border-slate-100 p-8 transition hover:shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-8 pb-5 border-b border-gray-200 flex items-center gap-3">
                    <i class="bi bi-person-circle text-indigo-600 text-xl"></i> 
                    Informasi Profil
                </h2>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="relative group flex-shrink-0">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatar-preview"
                                class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 shadow-sm group-hover:opacity-80 transition">
                            <button type="button" onclick="document.getElementById('avatar-input').click()"
                                class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full shadow-md hover:bg-indigo-700 transition">
                                <i class="bi bi-camera-fill text-xs"></i>
                            </button>
                        </div>
                        <div>
                            <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                            <button type="button" onclick="document.getElementById('avatar-input').click()"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition font-medium">
                                <i class="bi bi-upload mr-1"></i> Ganti Foto
                            </button>
                            <p class="text-xs text-gray-500 mt-2">Unggah foto profil baru (maks. 2MB)</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea id="address" name="address" rows="2"
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm resize-none">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-dark rounded-lg font-medium text-sm hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-md border border-slate-100 p-8 transition hover:shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-8 pb-5 border-b border-gray-200 flex items-center gap-3">
                    <i class="bi bi-lock-fill text-indigo-600 text-lg"></i> 
                    Ubah Password
                </h2>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                        <input name="current_password" type="password" required
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <input name="password" type="password" required
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                            <input name="password_confirmation" type="password" required
                                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-4 py-2.5 shadow-sm">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-4">
                        
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-dark rounded-lg font-medium text-sm hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="bi bi-shield-lock"></i> Ubah Password
                        </button>

                        @if (session('status') === 'password-updated')
                            <span class="text-sm text-emerald-600 flex items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i> Password berhasil diperbarui
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-8">
            @if(auth()->user()->hasRole('kasir') || auth()->user()->hasRole('admin'))
            <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <h3 class="text-base font-semibold text-gray-800 mb-5">Statistik</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg">
                        <i class="bi bi-receipt text-blue-600 text-xl"></i>
                        <div>
                            <p class="text-xs text-gray-600">Total Transaksi</p>
                            <p class="text-lg font-semibold text-gray-800">{{ number_format($statistics['total_transactions']) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg">
                        <i class="bi bi-cash-stack text-green-600 text-xl"></i>
                        <div>
                            <p class="text-xs text-gray-600">Total Pendapatan</p>
                            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($statistics['total_revenue'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($recentTransactions->count() > 0)
            <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Transaksi Terakhir</h3>
                    <a href="{{ route('admin.transactions.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition hover:underline">
                        Lihat Semua →
                    </a>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    @foreach($recentTransactions->take(5) as $transaction)
                        <div class="py-3 flex justify-between items-center hover:bg-gray-50/50 rounded-lg transition px-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->invoice }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <p class="font-semibold text-indigo-600 text-sm">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <h3 class="text-base font-semibold text-gray-800 mb-5">Aksi Cepat</h3>
                <div class="flex flex-col gap-3">
                    @if(auth()->user()->hasRole('kasir') || auth()->user()->hasRole('admin'))
                    <a href="{{ route('kasir.index') }}"
                       class="flex items-center gap-3 px-4 py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium border border-indigo-100 transition">
                        <i class="bi bi-cash-coin text-base"></i> <span>Buka Kasir</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-medium border border-green-100 transition">
                        <i class="bi bi-bar-chart text-base"></i> <span>Lihat Laporan</span>
                    </a>
                    @endif
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-sm font-medium border border-gray-200 transition">
                        <i class="bi bi-house-door text-base"></i> <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@push('scripts')

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('avatar-preview').src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endpush
@endsection