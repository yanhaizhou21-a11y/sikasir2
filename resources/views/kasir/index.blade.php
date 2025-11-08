@extends('layouts.admin')

@section('content')
    <div class="flex flex-row min-h-screen bg-gray-100  pr-4 gap-4">
        <div class="flex-1 flex flex-col">
            <!-- Search Section -->
            <div class="p-4 bg-white shadow-md rounded-lg mx-4 mt-4">
                <div class="relative w-full max-w-2xl mx-auto">
                    <input type="text" id="searchInput"
                        class="w-full pl-12 pr-10 py-3 rounded-full border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base"
                        placeholder="Search products..." />
                    <div class="absolute top-1/2 left-4 transform -translate-y-1/2 text-gray-400 text-lg">
                        <i class="bi bi-search"></i>
                    </div>
                    <button id="clearSearch"
                        class="absolute top-1/2 right-4 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                        onclick="clearSearchInput()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- View Mode Toggle -->
            <div class="px-4 py-4 bg-white shadow-sm rounded-lg mx-4 mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-gray-700 font-semibold text-sm sm:text-base">View Mode:</span>
                    <label class="flex items-center cursor-pointer">
                        <span class="mr-3 text-sm text-gray-600">Detailed</span>
                        <div class="relative">
                            <input type="checkbox" id="view-mode-toggle" class="sr-only peer">
                            <div
                                class="w-12 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-400 rounded-full peer peer-checked:bg-orange-500 transition-colors duration-300">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300 peer-checked:translate-x-6">
                            </div>
                        </div>
                        <span class="ml-3 text-sm text-gray-600">Quick Cards</span>
                    </label>
                </div>
            </div>

            <!-- Filter Section -->

            <div class="px-4 py-4 bg-white shadow-sm rounded-lg mx-4 mt-4">
                <div class="relative">
                    <div class="flex items-center space-x-2 overflow-hidden">
                        <!-- Scroll Left -->
                        <button id="scrollLeft" class="p-2 text-gray-400 hover:text-gray-600 transition"
                            onclick="scrollFilter('left')">
                            <i class="bi bi-chevron-left text-lg"></i>
                        </button>

                        <!-- Filter Container -->
                        <div id="filterContainer" class="flex-1 overflow-x-auto scrollbar-hide">
                            <div id="filterMenu"
                                class="flex gap-4 whitespace-nowrap transition-transform duration-300 ease-in-out">
                                <button
                                    class="relative filter-btn text-sm font-medium text-gray-700 after:block after:mt-1 after:h-0.5 after:w-0 after:bg-orange-500 after:transition-all after:duration-300 hover:after:w-full active"
                                    data-category="all" onclick="updateSubcategories('')">
                                    Semua Kategori
                                </button>
                                @foreach ($categories as $category)
                                    <button
                                        class="relative filter-btn text-sm font-medium text-gray-700 after:block after:mt-1 after:h-0.5 after:w-0 after:bg-orange-500 after:transition-all after:duration-300 hover:after:w-full"
                                        data-category="{{ $category->name }}"
                                        onclick="updateSubcategories('{{ $category->name }}')">
                                        {{ $category->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Scroll Right -->
                        <button id="scrollRight" class="p-2 text-gray-400 hover:text-gray-600 transition"
                            onclick="scrollFilter('right')">
                            <i class="bi bi-chevron-right text-lg"></i>
                        </button>
                    </div>

                    <!-- Subcategory Select -->
                    <div class="mt-4">
                        <select id="subcategorySelect"
                            class="w-full max-w-xs border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">-- Pilih Subkategori --</option>
                        </select>
                    </div>
                </div>
            </div>


            <!-- Products Container -->
            <div class="p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <!-- DETAILED VIEW CONTAINER -->
                <div id="detailed-view" class="space-y-6">
                    @foreach ($products as $product)
                        <form class="product-item detailed-view bg-white rounded-lg shadow-lg p-6"
                            data-category="{{ $product->category->name }}" method="POST" action="#"
                            onsubmit="addToOrder(event)" data-product-name="{{ $product->name }}"
                            data-product-harga_jual="{{ $product->harga_jual }}" data-cost-harga_jual="{{ $product->cost_harga_jual }}"
                            data-product-id="{{ $product->id }}" data-base-harga_jual="{{ $product->harga_jual }}"
                            data-item-harga_jual="{{ $product->harga_jual }}" data-customizations="[]">

                            <header class="mb-4">
                                <h2 class="font-bold text-xl text-green-700">
                                    {{ $loop->iteration < 10 ? '0' . $loop->iteration : $loop->iteration }}.
                                    {{ $product->name }} (Rp {{ number_format($product->harga_jual, 0, ',', '.') }})</h2>
                            </header>

                            <div class="flex gap-6">
                                <!-- Product Image -->
                                <figure class="w-1/3 flex-shrink-0 max-w-xs">
                                    @if($product->hasImage() && $product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                            class="w-full h-48 max-h-48 object-cover object-center rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 cursor-pointer"
                                            style="max-width: 100%; max-height: 192px;"
                                            onclick="showImageModal('{{ $product->image_url }}', '{{ $product->name }}')"
                                            data-product-image="{{ $product->image_url }}"
                                            data-product-name="{{ $product->name }}"
                                            onerror="handleImageError(this);">
                                        <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center hidden" data-fallback-image>
                                            <div class="text-center">
                                                <i class="bi bi-image text-gray-400 text-4xl mb-2"></i>
                                                <p class="text-gray-500 text-xs">No Image</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <div class="text-center">
                                                <i class="bi bi-image text-gray-400 text-4xl mb-2"></i>
                                                <p class="text-gray-500 text-xs">No Image</p>
                                            </div>
                                        </div>
                                    @endif
                                </figure>

                                <!-- Controls -->
                                <div class="flex-1 grid grid-cols-2 gap-4">
                                    <!-- Amount -->
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <label class="block font-medium text-gray-700 mb-2">Amount:</label>
                                        <div class="flex items-center justify-center space-x-2">
                                            <button type="button"
                                                class="px-3 py-1 bg-[#e17f12] rounded-full w-12 h-12 shadow text-white font-bold hover:bg-[#d16d0a] transition-colors"
                                                onclick="decrementAmount(this)">-</button>
                                            <input type="text" name="amount" value="1"
                                                class="w-12 text-center border rounded h-12 focus:outline-none focus:ring-2 focus:ring-[#005281]"
                                                onchange="updateDetailedHarga_jual(this)">
                                            <button type="button"
                                                class="px-3 py-1 bg-[#e17f12] rounded-full w-12 h-12 shadow text-white hover:bg-[#d16d0a] transition-colors"
                                                onclick="incrementAmount(this)">+</button>
                                        </div>
                                    </div>

                                    <!-- Size -->
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <label class="block font-medium text-gray-700 mb-2">Size:</label>
                                        <div class="flex items-center justify-center space-x-4">
                                            @foreach (['M', 'L'] as $size)
                                                <div class="relative">
                                                    <input type="radio" name="size_{{ $product->id }}"
                                                        id="size-{{ $size }}-{{ $product->id }}"
                                                        value="{{ $size }}" class="sr-only peer"
                                                        onchange="updateDetailedHarga_jual(this)"
                                                        {{ $size === 'M' ? 'checked' : '' }}
                                                        data-harga_jual-modifier="{{ $size === 'L' ? '3000' : '0' }}">
                                                    <label for="size-{{ $size }}-{{ $product->id }}"
                                                        class="flex items-center justify-center px-3 py-1 border rounded-full w-12 h-12 text-center cursor-pointer peer-checked:bg-[#e17f12] peer-checked:text-white hover:bg-gray-100 transition-colors">{{ $size }}</label>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <!-- Sugar -->
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <label class="block font-medium text-gray-700 mb-2">Sugar:</label>
                                        <div class="flex items-center justify-center space-x-2">
                                            @foreach ([25, 50, 75] as $sugar)
                                                <div class="relative">
                                                    <input type="radio" name="sugar_{{ $product->id }}"
                                                        id="sugar-{{ $sugar }}-{{ $product->id }}"
                                                        value="{{ $sugar }}" class="sr-only peer"
                                                        onchange="updateDetailedHarga_jual(this)"
                                                        {{ $sugar === 50 ? 'checked' : '' }}>
                                                    <label for="sugar-{{ $sugar }}-{{ $product->id }}"
                                                        class="flex items-center justify-center px-2 py-1 border rounded-full w-12 h-12 text-center cursor-pointer peer-checked:bg-[#e17f12] peer-checked:text-white hover:bg-gray-100 transition-colors text-xs">{{ $sugar }}%</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Ice -->
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <label class="block font-medium text-gray-700 mb-2">Ice:</label>
                                        <div class="flex items-center justify-center space-x-2">
                                            @foreach ([25, 50, 75] as $ice)
                                                <div class="relative">
                                                    <input type="radio" name="ice_{{ $product->id }}"
                                                        id="ice-{{ $ice }}-{{ $product->id }}"
                                                        value="{{ $ice }}" class="sr-only peer"
                                                        onchange="updateDetailedHarga_jual(this)"
                                                        {{ $ice === 50 ? 'checked' : '' }}>
                                                    <label for="ice-{{ $ice }}-{{ $product->id }}"
                                                        class="flex items-center justify-center px-2 py-1 border rounded-full w-12 h-12 text-center cursor-pointer peer-checked:bg-[#e17f12] peer-checked:text-white hover:bg-gray-100 transition-colors text-xs">{{ $ice }}%</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Topping and Price -->
                                    <div class="col-span-2 grid grid-cols-2 gap-4 mt-4">
                                        <div class="p-4 bg-gray-50 rounded-lg">
                                            <label class="block font-medium text-gray-700 mb-2">Customized:</label>
                                            <div class="flex flex-col space-y-2">
                                                <div class="relative">
                                                    <input type="radio" name="topping_{{ $product->id }}"
                                                        id="topping-none-{{ $product->id }}" value="No Topping"
                                                        class="sr-only peer" onchange="updateDetailedHarga_jual(this)" checked
                                                        data-harga_jual-modifier="0">
                                                    <label for="topping-none-{{ $product->id }}"
                                                        class="block w-full px-4 py-2 border rounded cursor-pointer peer-checked:bg-[#e17f12] peer-checked:text-white hover:bg-gray-100 text-center transition-colors">No
                                                        Topping</label>
                                                </div>
                                                <div class="relative">
                                                    <input type="radio" name="topping_{{ $product->id }}"
                                                        id="topping-oat-{{ $product->id }}" value="Susu Oat"
                                                        class="sr-only peer" onchange="updateDetailedHarga_jual(this)"
                                                        data-harga_jual-modifier="5000">
                                                    <label for="topping-oat-{{ $product->id }}"
                                                        class="block w-full px-4 py-2 border rounded cursor-pointer peer-checked:bg-[#e17f12] peer-checked:text-white hover:bg-gray-100 text-center transition-colors">Susu
                                                        Oat +(5K)</label>
                                                </div>
                                                <div class="relative">
                                                    <input type="radio" name="topping_{{ $product->id }}"
                                                        id="topping-espresso-{{ $product->id }}" value="Espresso"
                                                        class="sr-only peer" onchange="updateDetailedHarga_jual(this)"
                                                        data-harga_jual-modifier="4000">
                                                    <label for="topping-espresso-{{ $product->id }}"
                                                        class="block w-full px-4 py-2 border rounded cursor-pointer peer-checked:bg-[#e17f12] peer-checked:text-white hover:bg-gray-100 text-center transition-colors">Espresso
                                                        +(4K)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-4 bg-gray-50 rounded-lg flex flex-col justify-between">
                                            <div class="text-center">
                                                <label class="block font-medium text-gray-700 mb-2">Total Harga:</label>
                                                <span id="total-harga-{{ $product->id }}"
                                                    class="text-2xl font-bold text-[#e17f12] block mb-4">Rp
                                                    {{ number_format($product->harga_jual, 0, ',', '.') }}</span>
                                            </div>
                                            <button type="submit"
                                                class="w-full px-6 py-3 bg-[#005281] text-white rounded-lg hover:bg-[#004371] transition-colors font-medium">Add
                                                to Order</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>

                <!-- CARD VIEW CONTAINER -->
                <div id="card-view" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        <form
                            class="product-item card-view bg-white rounded-lg shadow-sm border hover:shadow-md transition-all duration-300 w-full"
                            data-category="{{ $product->category->name }}" method="POST" action="#"
                            onsubmit="addToOrder(event)" data-product-name="{{ $product->name }}"
                            data-product-harga_jual="{{ $product->harga_jual }}" data-cost-harga_jual="{{ $product->cost_harga_jual }}"
                            data-product-id="{{ $product->id }}" data-base-harga_jual="{{ $product->harga_jual }}"
                            data-item-harga_jual="{{ $product->harga_jual }}" data-customizations="[]">

                            <div class="w-full h-32 overflow-hidden rounded-t-lg bg-gray-200 relative">
                                @if($product->hasImage() && $product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="object-cover w-full h-full transition-transform duration-300 ease-in-out hover:scale-105 cursor-pointer"
                                        style="max-width: 100%; max-height: 128px;"
                                        onclick="showImageModal('{{ $product->image_url }}', '{{ $product->name }}')"
                                        data-product-image="{{ $product->image_url }}"
                                        data-product-name="{{ $product->name }}"
                                        onerror="handleImageError(this);" />
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 absolute inset-0 hidden" data-fallback-image>
                                        <div class="text-center">
                                            <i class="bi bi-image text-gray-400 text-3xl"></i>
                                            <p class="text-gray-500 text-xs mt-1">No Image</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <div class="text-center">
                                            <i class="bi bi-image text-gray-400 text-3xl"></i>
                                            <p class="text-gray-500 text-xs mt-1">No Image</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="px-3 pt-2 pb-1">
                                <h3 class="font-semibold text-sm text-[#005281] leading-tight whitespace-normal break-words">
                                    {{ $product->name }}</h3>
                                <p class="text-[#e17f12] font-semibold text-xs mt-1">
                                    Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="px-3 pb-3">
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-600">Qty:</span>
                                    <div class="flex items-center space-x-1">
                                        <button type="button"
                                            class="w-6 h-6 bg-[#e17f12] text-white rounded hover:bg-[#c9690a] text-xs"
                                            onclick="decrementAmount(this)">-</button>
                                        <input type="text" name="amount" value="1"
                                            class="w-8 h-6 text-center border border-gray-300 rounded text-xs" />
                                        <button type="button"
                                            class="w-6 h-6 bg-[#e17f12] text-white rounded hover:bg-[#c9690a] text-xs"
                                            onclick="incrementAmount(this)">+</button>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="mt-3 w-full bg-[#005281] text-white py-1.5 text-xs rounded-md hover:bg-[#00426a] transition-colors">
                                    Quick Order
                                </button>
                            </div>
                        </form>
                    @endforeach
                </div>


            </div>
        </div>

        <!-- Receipt/Order Summary Section (Right Side) -->
        <div class="w-64 bg-white shadow-lg flex-shrink-0 overflow-y-auto max-h-[calc(100vh-64px)]">
            <section class="h-full p-6 font-mono text-sm">
                <!-- Receipt Header -->
                <div class="text-center mb-6 border-b pb-4">
                    <h1 class="text-xl font-bold mb-2">Struk Pesanan</h1>
                    <p class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse(session('login_time'))->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}
                    </p>

                    <p class="text-xs text-gray-500" id="cashier-name">{{ Auth::user()->name ?? 'Unknown User' }}</p>
                </div>

                <!-- Item List -->
                <div class="space-y-4 max-h-96 overflow-y-auto mb-6" id="item-list">
                    <!-- Items will be dynamically added here -->
                </div>

                <!-- Discount Section -->
                <div class="border-t border-dashed pt-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm">Diskon (%):</span>
                        <input type="number" id="discount-input" value="0" min="0" max="100"
                            class="w-16 text-center border rounded text-sm py-1" onchange="updateTotals()">
                    </div>
                    <div class="flex justify-between text-sm text-green-600 mb-2">
                        <span>Diskon:</span>
                        <span id="discount-amount">Rp 0</span>
                    </div>
                </div>

                <!-- Tax Section -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm">Pajak (%):</span>
                        <input type="number" id="tax-input" value="10" min="0" max="100"
                            class="w-16 text-center border rounded text-sm py-1" onchange="updateTotals()">
                    </div>
                    <div class="flex justify-between text-sm text-red-600 mb-2">
                        <span>Pajak:</span>
                        <span id="tax-amount">Rp 0</span>
                    </div>
                </div>

                <!-- Subtotal -->
                <div class="border-t border-dashed pt-4 mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-green-600 mb-2">
                        <span>Total:</span>
                        <span id="grand-total">Rp 0</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button onclick="processPayment()"
                        class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        Pay Cash
                    </button>
                    <button onclick="processQRISPayment()"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Pay with QRIS
                    </button>
                    <button onclick="processDebitPayment()"
                        class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        Pay with Debit
                    </button>
                    <button onclick="printReceipt()"
                        class="w-full border border-gray-300 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Print Receipt
                    </button>
                    <button onclick="clearOrder()"
                        class="w-full border border-red-300 text-red-600 py-3 rounded-lg hover:bg-red-50 transition-colors font-medium">
                        Clear Order
                    </button>
                </div>

                <!-- Hidden Forms -->
              <form id="transactionForm" action="{{ route('kasir.transaksi.store') }}" method="POST">

                    @csrf
                    <input type="hidden" name="subtotal" id="transaction_subtotal">
                    <input type="hidden" name="total_cost_harga_jual" id="transaction_total_cost_harga_jual">
                    <input type="hidden" name="name_user" id="transaction_name_user" value="{{ Auth::user()->name }}">
                    <input type="hidden" name="payment_method" id="transaction_payment_method" value="cash">
                    <input type="hidden" name="timestamp" id="transaction_timestamp">
                </form>

                <form id="transactionQRISForm" method="POST" action="{{ route('kasir.transaksi.store') }}"
                    style="display:none;">
                    @csrf
                    <input type="hidden" name="subtotal" id="transaction_qris_subtotal">
                    <input type="hidden" name="total_cost_harga_jual" id="transaction_qris_total_cost_harga_jual">
                    <input type="hidden" name="name_user" id="transaction_qris_name_user"
                        value="{{ Auth::user()->name }}">
                    <input type="hidden" name="payment_method" id="transaction_qris_payment_method" value="qris">
                    <input type="hidden" name="timestamp" id="transaction_qris_timestamp">
                </form>

                <form id="transactionDebitForm" method="POST" action="{{ route('kasir.transaksi.store') }}"
                    style="display:none;">
                    @csrf
                    <input type="hidden" name="subtotal" id="transaction_debit_subtotal">
                    <input type="hidden" name="total_cost_harga_jual" id="transaction_debit_total_cost_harga_jual">
                    <input type="hidden" name="name_user" id="transaction_debit_name_user"
                        value="{{ Auth::user()->name }}">
                    <input type="hidden" name="payment_method" id="transaction_debit_payment_method" value="debit">
                    <input type="hidden" name="timestamp" id="transaction_debit_timestamp">
                    <input type="hidden" name="payment_method" value="debit">
                </form>

            </section>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- SweetAlert2 for better alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Cashier System JavaScript Files -->
    <script src="{{ asset('assets/js/cashier/utils.js') }}"></script>
    <script src="{{ asset('assets/js/cashier/cashier-main.js') }}"></script>
    <script src="{{ asset('assets/js/cashier/product-controls.js') }}"></script>
    <script src="{{ asset('assets/js/cashier/order-management.js') }}"></script>
    <script src="{{ asset('assets/js/cashier/payment-system.js') }}"></script>
    <script src="{{ asset('assets/js/cashier/kasir-init.js') }}"></script>
    
    <!-- Image Modal Function and Error Handler -->
    <script>
        // Dynamic subcategory population from backend
        const subcategoryOptions = @json($subcategoryMap);

        function updateSubcategories(selectedCategory) {
            const subcategorySelect = document.getElementById('subcategorySelect');
            subcategorySelect.innerHTML = `<option value="">-- Pilih Subkategori --</option>`;
            if (selectedCategory && subcategoryOptions[selectedCategory]) {
                subcategoryOptions[selectedCategory].forEach(function (sub) {
                    const opt = document.createElement('option');
                    opt.value = sub;
                    opt.textContent = sub;
                    subcategorySelect.appendChild(opt);
                });
            }
        }

        function showImageModal(imageSrc, productName) {
            if (!imageSrc || !productName) return;
            
            Swal.fire({
                html: `<img src="${imageSrc}" alt="${productName}" class="max-w-full max-h-80 rounded-lg shadow-lg mx-auto object-contain" style="max-width: 600px; max-height: 320px;" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2U1ZTdlYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5Y2EzYWYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub3QgZm91bmQ8L3RleHQ+PC9zdmc+'; this.style.width='200px'; this.style.height='200px'; this.style.objectFit='contain';" />`,
                title: productName,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                width: 'auto',
                maxWidth: '90vw',
                padding: '1.5rem',
                background: '#fff',
                customClass: {
                    popup: 'rounded-lg'
                }
            });
        }

        // Handle image loading errors
        function handleImageError(imgElement) {
            imgElement.style.display = 'none';
            const fallback = imgElement.nextElementSibling || imgElement.parentElement.querySelector('[data-fallback-image]');
            if (fallback) {
                fallback.classList.remove('hidden');
                fallback.classList.add('flex');
            }
        }

        // Ensure all product images handle errors properly
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img[data-product-image]').forEach(function(img) {
                if (!img.onerror) {
                    img.onerror = function() {
                        handleImageError(this);
                    };
                }
            });
        });
    </script>
    
    
@endpush

