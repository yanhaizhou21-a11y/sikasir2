<aside id="sidebar"
    class="sidebar fixed top-0 left-0 h-full w-64 bg-[#ab5c16] text-white shadow-lg z-50 transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 overflow-y-auto">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex flex-col items-center p-3 border-b border-gray-200/20">
            <div class="w-24 h-24 bg-[#ab5c16] rounded-lg flex items-center justify-center mb-2">
                <img src="{{ asset('assets/image/logocafe.png') }}" alt="Logo" class="w-20 h-20 object-contain p-1 max-w-full max-h-full">
            </div>
            <div class="text-center mb-2">
                <div class="flex items-center justify-center mb-2">
                    <img src="{{ auth()->user()->avatar_url }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-12 h-12 rounded-full object-cover border-2 border-white/30 shadow-md max-w-full max-h-full"
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=fff&color=e17f12&size=48';"
                         style="max-width: 48px; max-height: 48px;">
                </div>
                <h1 class="text-base font-semibold text-fuchsia-50 w-full px-2 whitespace-normal break-words">{{ auth()->user()->name }}</h1>
                <p class="text-xs text-fuchsia-50/80">
                    @foreach(auth()->user()->getRoleNames() as $role)
                        {{ ucfirst($role) }}
                    @endforeach
                </p>
                <p class="text-xs text-fuchsia-50/60 mt-1 w-full px-2 whitespace-normal break-words">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 bg-[#ab5c16] font-poppins">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('owner.dashboard') || request()->routeIs('kasir.dashboard') || request()->routeIs('bar.dashboard') || request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner'))
                <li>
                    <a href="{{ route('products.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-cart"></i>
                        <span>Produk</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('bar') || auth()->user()->hasRole('kitchen'))
                <!-- Dropdown: Manajemen Stock -->
                <li class="relative dropdown-container {{ request()->routeIs('admin.ingredients.*') || request()->routeIs('bar.ingredients.*') || request()->routeIs('kitchen.ingredients.*') ? 'active' : '' }}">
                    <button type="button" class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link dropdown-button w-full">
                        <i class="bi bi-box-seam"></i>
                        <span>Manajemen Stock</span>
                        <i class="bi bi-chevron-down ml-auto chevron-icon"></i>
                    </button>
                    <ul class="dropdown-menu hidden mt-1 space-y-1 pl-4">
                        @if(auth()->user()->hasRole('admin'))
                        <li><a href="{{ route('admin.ingredients.index') }}" class="block px-4 py-2 rounded-md hover:bg-[#8b4513]/60 {{ request()->routeIs('admin.ingredients.*') ? 'bg-[#8b4513]/60' : '' }}">Bahan Baku</a></li>
                        @elseif(auth()->user()->hasRole('bar'))
                        <li><a href="{{ route('bar.ingredients.index') }}" class="block px-4 py-2 rounded-md hover:bg-[#8b4513]/60 {{ request()->routeIs('bar.ingredients.*') ? 'bg-[#8b4513]/60' : '' }}">Bahan Baku</a></li>
                        @elseif(auth()->user()->hasRole('kitchen'))
                        <li><a href="{{ route('kitchen.ingredients.index') }}" class="block px-4 py-2 rounded-md hover:bg-[#8b4513]/60 {{ request()->routeIs('kitchen.ingredients.*') ? 'bg-[#8b4513]/60' : '' }}">Bahan Baku</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <!-- Dropdown: Kelola Kategori -->
                <li class="relative dropdown-container {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                    <button type="button" class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link dropdown-button w-full">
                        <i class="bi bi-list"></i>
                        <span>Kelola Kategori</span>
                        <i class="bi bi-chevron-down ml-auto chevron-icon"></i>
                    </button>
                    <ul class="dropdown-menu hidden mt-1 space-y-1 pl-4">
                        <li><a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 rounded-md hover:bg-[#8b4513]/60 {{ request()->routeIs('admin.categories.*') ? 'bg-[#8b4513]/60' : '' }}">Kategori Menu</a></li>
                        <li><a href="{{ route('admin.subcategories.index') }}" class="block px-4 py-2 rounded-md hover:bg-[#8b4513]/60 {{ request()->routeIs('admin.subcategories.*') ? 'bg-[#8b4513]/60' : '' }}">Sub Kategori</a></li>
                    </ul>
                </li>
                @endif

                @if(auth()->user()->hasRole('kasir'))
                <li>
                    <a href="{{ route('kasir.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Kasir</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kasir.transaksi.index') }}"
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('kasir.transaksi.*') ? 'active' : '' }}">
                        <i class="bi bi-card-checklist"></i>
                        <span>Transaksi</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('bar'))
                <li>
                    <a href="{{ route('bar.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('bar.*') ? 'active' : '' }}">
                        <i class="bi bi-cup-hot"></i>
                        <span>Bar</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('kitchen'))
                <li>
                    <a href="{{ route('kitchen.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                        <i class="bi bi-fire"></i>
                        <span>Kitchen</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <li>
                    <a href="{{ route('admin.transactions.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>Transaksi</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner'))
                <li>
                    <a href="{{ route('tables.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                        <i class="bi bi-table"></i>
                        <span>Tables</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner') || auth()->user()->hasRole('kasir'))
                <li>
                    <a href="{{ route('reports.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <li>
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>

                @if(auth()->user()->hasRole('admin'))
                <li>
                    <a href="{{ route('settings.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 text-fuchsia-50 rounded-lg nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <!-- Logout -->
        <div class="mt-auto p-4 border-t border-gray-200/20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-fuchsia-50 hover:bg-[#8b4513]/60 rounded-lg nav-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="flex-1 text-left">{{ __('Log Out') }}</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Overlay (muncul di mobile saat sidebar aktif) -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 lg:hidden"></div>

<!-- Hamburger Button (tampilkan di navbar atas) -->
<button id="menu-toggle"
    class="fixed top-4 left-4 z-50 lg:hidden bg-[#ab5c16] text-white p-3 rounded-md shadow-md focus:outline-none">
    <i class="bi bi-list text-xl"></i>
</button>

<style>
    .nav-link {
        position: relative;
        overflow: hidden;
        transition: background-color 0.3s ease;
    }
    .nav-link:hover {
        background-color: rgba(120, 53, 15, 0.3);
    }
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -100%;
        width: 100%;
        height: 4px;
        background-color: #7d4a1e;
        transition: left 0.3s ease-in-out;
    }
    .nav-link:hover::after,
    .nav-link.active::after {
        left: 0;
    }
    .nav-link.active {
        background-color: rgba(120, 53, 15, 0.5);
        font-weight: 600;
    }

    /* Dropdown styling */
    .dropdown-container.active .dropdown-menu {
        display: block !important;
    }
    .dropdown-button {
        width: 100%;
        text-align: left;
    }
    .chevron-icon {
        transition: transform 0.3s ease;
    }
    .dropdown-container.active .chevron-icon {
        transform: rotate(180deg);
    }
</style>

<script>
    // --- Sidebar Responsive ---
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menu-toggle');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // --- Dropdown Menu ---
    document.querySelectorAll('.dropdown-button').forEach(button => {
        const container = button.closest('.dropdown-container');
        
        // Auto-expand if active child route
        if (container.classList.contains('active')) {
            container.classList.add('active');
            container.querySelector('.dropdown-menu').classList.remove('hidden');
        }
        
        button.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.toggle('active');
            const menu = container.querySelector('.dropdown-menu');
            menu.classList.toggle('hidden');
        });
    });
</script>
