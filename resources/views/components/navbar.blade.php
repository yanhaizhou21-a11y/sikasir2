<header
    class="fixed top-0 left-0 lg:left-64 right-0 z-30 bg-[#ab5c16] text-stone-50 shadow-sm border-b border-gray-200 select-none h-16">
    <div class="flex items-center justify-between px-4 h-full">
        <!-- Mobile Menu -->
        <button class="lg:hidden p-2 text-fuchsia-50 hover:bg-amber-950 rounded-lg" onclick="toggleSidebar()">
            <i class="bi bi-list text-xl"></i>
        </button>

        <!-- Title -->
        <div class="hidden lg:block">
            <h1 class="text-xl font-semibold text-fuchsia-50">Sistem Kasir</h1>
            @if(request()->routeIs('dashboard'))
                <p class="text-sm text-fuchsia-50">Selamat datang, {{ auth()->user()->name }}!</p>
            @endif
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-4">
            <!-- Notifications -->
            <button class="relative p-2 text-fuchsia-50 hover:bg-amber-950 rounded-lg">
                <i class="bi bi-bell text-xl"></i>
                <span
                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
            </button>

            <!-- Search -->
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <input type="text" placeholder="Cari..."
                        class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-950 focus:border-amber-950">
                    <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-fuchsia-50"></i>
                </div>
            </div>

            <!-- Profile -->
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-1.5 hover:bg-amber-950 rounded-lg transition-colors">
                <span class="hidden md:block text-xs font-medium text-fuchsia-50 max-w-48 whitespace-normal break-words">{{ auth()->user()->name }}</span>
                <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-white/20 flex items-center justify-center flex-shrink-0">
                    <img src="{{ auth()->user()->avatar_url }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-full h-full object-cover"
                         style="max-width: 36px; max-height: 36px;"
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=e17f12&color=fff&size=36';">
                </div>
            </a>
        </div>
    </div>
</header>