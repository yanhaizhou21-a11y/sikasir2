<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    {{-- Tailwind & Bootstrap Icons --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #005281; border-radius: 2px; }

        /* Sidebar Transition */
        .sidebar { transition: transform 0.3s ease-in-out; }

        @media (max-width: 1023px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }

        /* Dropdown visual + chevron */
        .dropdown-menu { display: none; max-height: 0; opacity: 0; visibility: hidden; overflow: hidden; }
        .dropdown-button { width: 100%; text-align: left; }
        .chevron-icon { transition: transform 0.28s ease; transform-origin: center; }
        .dropdown-container.active > .dropdown-button { background-color: rgba(120,53,15,0.12); }
    </style>
</head>

<body class="min-h-screen bg-stone-50 overflow-x-hidden">
    {{-- Sidebar --}}
    <aside id="sidebar"
        class="sidebar fixed top-0 left-0 z-50 h-full w-64 bg-[#005281] text-white shadow-lg">
        @include('components.sidebar')
    </aside>

    {{-- Overlay (mobile mode) --}}
    <div id="overlay"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"
        onclick="closeSidebar()"></div>

    {{-- Main Content --}}
    <div id="main-content"
        class="flex-1 flex flex-col transition-all duration-300 lg:ml-64">
        {{-- Navbar --}}
        @include('components.navbar')

        {{-- Page Content --}}
        <main class="pt-16 overflow-y-auto h-[calc(100vh-4rem)] p-6">
            @yield('content')
        </main>
    </div>

    {{-- Sidebar Toggle Script --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.add('hidden');
        }
    </script>

    {{-- jQuery & DataTables JS --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    {{-- Feather Icons (Opsional) --}}
    <script>
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>

    {{-- === SIDEBAR DROPDOWN: Robust, delegated init (paste here so layout controls init) === --}}
    <script>
    (function () {
        // Idempotent init guard
        if (window.sidebarDropdownsInitialized) return;
        window.sidebarDropdownsInitialized = true;

        const sidebarEl = document.getElementById('sidebar');
        if (!sidebarEl) return;

        // Initialize menu base styles and ARIA
        const dropdownContainers = sidebarEl.querySelectorAll('.dropdown-container');
        dropdownContainers.forEach((container, idx) => {
            const button = container.querySelector('.dropdown-button');
            const menu = container.querySelector('.dropdown-menu');

            if (!button || !menu) return;

            // assign stable ids if missing (for aria-controls)
            if (!menu.id) menu.id = 'sidebar-dropdown-' + idx;

            // initial aria
            button.setAttribute('aria-controls', menu.id);
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('type', 'button'); // prevent accidental form submit
            button.classList.add('dropdown-button');

            // base styles (in case CSS not applied)
            menu.style.display = 'none';
            menu.style.maxHeight = '0px';
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
            menu.style.overflow = 'hidden';
            menu.style.transition = 'max-height 260ms ease, opacity 180ms ease';
        });

        // Helper functions
        function openDropdown(menu) {
            if (!menu) return;
            // close other open menus
            closeAllDropdowns(menu);

            const container = menu.closest('.dropdown-container');
            const btn = container.querySelector('.dropdown-button');
            const chevron = container.querySelector('.chevron-icon');

            menu.classList.add('open');
            menu.style.display = 'block';

            // Force reflow to make transition reliable
            const full = menu.scrollHeight;
            menu.style.maxHeight = '0px';
            menu.offsetHeight; // reflow
            menu.style.maxHeight = full + 'px';
            menu.style.opacity = '1';
            menu.style.visibility = 'visible';

            container.classList.add('active');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            if (btn) btn.setAttribute('aria-expanded', 'true');

            // After transition, keep maxHeight accommodating content changes
            const onEnd = (e) => {
                if (e.propertyName === 'max-height') {
                    // set to computed height so adding items won't break (optional)
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                }
                menu.removeEventListener('transitionend', onEnd);
            };
            menu.addEventListener('transitionend', onEnd);
        }

        function closeDropdown(menu) {
            if (!menu) return;
            const container = menu.closest('.dropdown-container');
            const btn = container.querySelector('.dropdown-button');
            const chevron = container.querySelector('.chevron-icon');

            // set to current height then animate to 0
            menu.style.maxHeight = menu.scrollHeight + 'px';
            menu.offsetHeight; // reflow
            menu.classList.remove('open');
            menu.style.maxHeight = '0px';
            menu.style.opacity = '0';
            container.classList.remove('active');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            if (btn) btn.setAttribute('aria-expanded', 'false');

            const onEnd = (e) => {
                if (e.propertyName === 'max-height' && !menu.classList.contains('open')) {
                    menu.style.display = 'none';
                    menu.style.visibility = 'hidden';
                }
                menu.removeEventListener('transitionend', onEnd);
            };
            menu.addEventListener('transitionend', onEnd);
        }

        function closeAllDropdowns(exceptMenu) {
            const openMenus = sidebarEl.querySelectorAll('.dropdown-menu.open');
            openMenus.forEach(m => { if (m !== exceptMenu) closeDropdown(m); });
        }

        // Delegated click handler (one listener)
        document.addEventListener('click', function (e) {
            const clickedBtn = e.target.closest('.dropdown-button');

            if (clickedBtn && sidebarEl.contains(clickedBtn)) {
                // Click on a dropdown button inside the sidebar
                e.preventDefault();
                const container = clickedBtn.closest('.dropdown-container');
                const menu = container ? container.querySelector('.dropdown-menu') : null;
                if (!menu) return;

                if (menu.classList.contains('open')) {
                    closeDropdown(menu);
                } else {
                    openDropdown(menu);
                }
                return; // don't let this propagate to "click outside" below
            }

            // Click anywhere else -> close dropdowns (if clicked outside any container)
            if (!e.target.closest('.dropdown-container')) {
                closeAllDropdowns();
            }
        });

        // Keyboard accessibility: Enter/Space toggle, Escape closes all
        document.addEventListener('keydown', function (e) {
            const active = document.activeElement;
            if ((e.key === 'Enter' || e.key === ' ') && active && active.classList && active.classList.contains('dropdown-button')) {
                e.preventDefault();
                active.click(); // delegate to click handler
            }
            if (e.key === 'Escape') {
                closeAllDropdowns();
            }
        });

        // If using Livewire (or markup re-render), re-init after update
        if (window.livewire) {
            document.addEventListener('livewire:update', function () {
                // allow re-init by clearing guard and reloading
                window.sidebarDropdownsInitialized = false;
                // small timeout ensures DOM stabilized
                setTimeout(function () {
                    if (!window.sidebarDropdownsInitialized) {
                        location.reload(); // simplest reliable fallback for dynamic admin panels
                    }
                }, 50);
            });
        }
    })();
    </script>

    {{-- Custom Scripts Stack --}}
    @stack('scripts')

    {{-- Optional Section Script --}}
    @yield('script')
</body>
</html>
