@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $user = Auth::user();
    $today = Carbon::now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<div class="bg-white p-6 rounded-xl shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute right-0 top-0 w-32 h-32 bg-[#005281]/5 rounded-bl-full"></div>

    <div class="flex items-center gap-6 relative">
        @if ($user->profile_image ?? false)
            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Foto Profil"
                class="w-16 h-16 rounded-full object-cover border-2 border-[#005281]">
        @else
            <div class="flex items-center justify-center w-16 h-16 bg-[#005281]/10 rounded-full">
                <i class="bi bi-person-circle text-4xl text-[#005281]"></i>
            </div>
        @endif

        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-gray-700">Halo {{ $user->name ?? 'User' }}!</h2>
                @if ($user->role ?? false)
                    <span class="px-3 py-1 text-sm bg-[#005281]/10 text-[#005281] rounded-full capitalize">
                        {{ $user->role }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-500">
                <i class="bi bi-clock"></i>
                <span>{{ $today }}</span>
            </div>

            <p class="text-gray-600 text-sm">
                Selamat datang di dashboard untuk melihat data real-time dan performa bisnis Anda.
            </p>
        </div>
    </div>
</div>

@endsection


