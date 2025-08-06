<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - My Batik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'sans': ['Poppins', 'sans-serif'], 'dancing': ['Dancing Script', 'cursive'] }
                }
            }
        }
    </script>
    <style>
        .font-dancing { font-family: 'Dancing Script', cursive; }
        [x-cloak] { display: none !important; }
        .modal-active {
            overflow-x: hidden;
            overflow-y: auto !important;
        }
        .tooltip {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .has-tooltip:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }
        .pagination li a, .pagination li span {
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            background-color: white;
            transition: background-color 0.2s;
        }
        .pagination li a:hover {
            background-color: #f7fafc;
        }
        .pagination li.active span {
            background-color: #4299e1;
            color: white;
            border-color: #4299e1;
        }
        .pagination li.disabled span {
            color: #a0aec0;
            background-color: #f7fafc;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center p-4 md:p-6">
            <div class="flex items-center gap-x-12">
                <a href="/admin" class="font-dancing text-3xl md:text-4xl font-bold text-gray-900">myBatik</a>
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('admin.home') }}" class="font-semibold text-gray-700 hover:text-black transition">Home</a>
                    <a href="{{ route('admin.reports.index') }}" class="font-bold text-black transition">Reports</a>
                </nav>
            </div>
            <div class="flex items-center space-x-3">
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-3">
                        <span class="font-semibold text-gray-700 hover:text-black transition">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" /></svg>
                        </div>
                    </button>
                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50" x-cloak>
                        <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm font-semibold text-red-600 hover:text-red-800 transition">Logout</a></form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <main class="bg-white rounded-2xl shadow-sm p-6 md:p-8" x-data="reports()" x-init="init()">
            <div class="flex flex-col md:flex-row gap-8">
                <aside class="w-full md:w-1/4">
                    <h2 class="text-xl font-bold mb-4">Reports Menu</h2>
                    <ul class="space-y-2">
                        <li><a href="#" @click.prevent="activeReport = 'sales'" class="block px-4 py-2 rounded-md" :class="{ 'bg-blue-100 text-blue-700 font-bold': activeReport === 'sales', 'hover:bg-gray-100 font-semibold text-gray-600': activeReport !== 'sales' }">Monthly Sales</a></li>
                        <li><a href="#" @click.prevent="activeReport = 'promo'" class="block px-4 py-2 rounded-md" :class="{ 'bg-blue-100 text-blue-700 font-bold': activeReport === 'promo', 'hover:bg-gray-100 font-semibold text-gray-600': activeReport !== 'promo' }">Promo Code Usage</a></li>
                        <li><a href="#" @click.prevent="activeReport = 'users'" class="block px-4 py-2 rounded-md" :class="{ 'bg-blue-100 text-blue-700 font-bold': activeReport === 'users', 'hover:bg-gray-100 font-semibold text-gray-600': activeReport !== 'users' }">User Registrations</a></li>
                        <li><a href="#" @click.prevent="activeReport = 'customers'" class="block px-4 py-2 rounded-md" :class="{ 'bg-blue-100 text-blue-700 font-bold': activeReport === 'customers', 'hover:bg-gray-100 font-semibold text-gray-600': activeReport !== 'customers' }">Top Customers</a></li>
                    </ul>
                </aside>

                <div class="w-full md:w-3/4">
                    
                    <section id="sales-report" x-show="activeReport === 'sales'" x-cloak>
                        <h1 class="text-3xl font-bold text-gray-800 mb-6">Monthly Sales Report</h1>
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Month</th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Orders</th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($salesData as $data)
                                        <tr class="hover:bg-gray-50 cursor-pointer" @click="getDetails({{ $data->year }}, {{ $data->month }})">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap font-semibold">{{ date('F Y', mktime(0, 0, 0, $data->month, 1, $data->year)) }}</p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">{{ $data->total_orders }}</p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">Rp {{ number_format($data->total_sales, 0, ',', '.') }}</p>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">No sales data found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                    
                    <section id="promo-report" x-show="activeReport === 'promo'" x-cloak>
                        <div class="flex flex-col items-start gap-4 sm:flex-row sm:justify-between sm:items-center mb-6">
                            <h1 class="text-3xl font-bold text-gray-800">Promo Code Usage Report</h1>
                            <div x-show="mainPromoFilterMonths.length > 0" class="w-full sm:w-auto">
                                <select x-model="selectedMainPromoMonth" @change="filterMainPromoReport()" class="w-full sm:w-48 p-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="all">All Months</option>
                                    <template x-for="month in mainPromoFilterMonths" :key="month">
                                        <option :value="month" x-text="formatMonthForDisplay(month)"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                <div @click="sortBy('code', 'promo')" class="flex items-center gap-1 cursor-pointer hover:text-gray-800">
                                                    <span>Promo Code</span>
                                                    <div class="w-4 h-4">
                                                        <svg x-show="promoSortColumn === 'code' && promoSortDirection === 'desc'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                        <svg x-show="promoSortColumn === 'code' && promoSortDirection === 'asc'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                                                        <svg x-show="promoSortColumn !== 'code'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Discount</th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                <div @click="sortBy('usage_count', 'promo')" class="flex items-center gap-1 cursor-pointer hover:text-gray-800">
                                                    <span>Times Used</span>
                                                    <div class="w-4 h-4">
                                                        <svg x-show="promoSortColumn === 'usage_count' && promoSortDirection === 'desc'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                        <svg x-show="promoSortColumn === 'usage_count' && promoSortDirection === 'asc'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                                                        <svg x-show="promoSortColumn !== 'usage_count'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-if="filteredPromoData.length === 0">
                                            <tr>
                                                <td colspan="3" class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">No promo code data found for the selected month.</td>
                                            </tr>
                                        </template>
                                        <template x-for="promo in filteredPromoData" :key="promo.code">
                                            <tr class="hover:bg-gray-50 cursor-pointer" @click="getPromoDetails(promo.code, selectedMainPromoMonth)">
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap font-semibold" x-text="promo.code"></p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="promo.type === 'percentage' ? `${promo.value}%` : `Rp ${new Intl.NumberFormat('id-ID').format(promo.value)}`"></p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap" x-text="promo.usage_count"></p>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                        
                    <section id="users-report" x-show="activeReport === 'users'" x-cloak>
                        <h1 class="text-3xl font-bold text-gray-800 mb-6">New User Registrations</h1>
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Month</th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">New Users</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($userData as $data)
                                        <tr class="hover:bg-gray-50 cursor-pointer" @click="getUserDetails({{ $data->year }}, {{ $data->month }})">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">{{ date('F Y', mktime(0, 0, 0, $data->month, 1, $data->year)) }}</p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">{{ $data->total_users }}</p>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">No user registration data found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <section id="customers-report" x-show="activeReport === 'customers'" x-cloak>
                        <div class="flex flex-col items-start gap-4 sm:flex-row sm:justify-between sm:items-center mb-6">
                            <h1 class="text-3xl font-bold text-gray-800">Top Customers Report</h1>
                            <div class="w-full sm:w-auto">
                                <form action="{{ route('admin.reports.index') }}#customers-report" method="GET">
                                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'total_spent') }}">
                                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
                                    <select name="month" onchange="this.form.submit()" class="w-full sm:w-48 p-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="all">All Months</option>
                                        @foreach($topCustomersFilterMonths as $month)
                                            <option value="{{ $month->month_value }}" {{ request('month') == $month->month_value ? 'selected' : '' }}>
                                                {{ $month->month_display }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                @php $nextDir = (request('sort_by') == 'name' && request('sort_dir') == 'asc') ? 'desc' : 'asc'; @endphp
                                                <a href="{{ route('admin.reports.index', ['sort_by' => 'name', 'sort_dir' => $nextDir, 'month' => request('month', 'all')]) }}#customers-report" class="flex items-center gap-1 cursor-pointer hover:text-gray-800">
                                                    <span>Customer Name</span>
                                                    <div class="w-4 h-4">
                                                        @if(request('sort_by') == 'name' && request('sort_dir') == 'desc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') == 'name' && request('sort_dir') == 'asc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') != 'name') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg> @endif
                                                    </div>
                                                </a>
                                            </th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                @php $nextDir = (request('sort_by') == 'orders_count' && request('sort_dir') == 'asc') ? 'desc' : 'asc'; @endphp
                                                <a href="{{ route('admin.reports.index', ['sort_by' => 'orders_count', 'sort_dir' => $nextDir, 'month' => request('month', 'all')]) }}#customers-report" class="flex items-center justify-center gap-1 cursor-pointer hover:text-gray-800">
                                                    <span>Total Orders</span>
                                                    <div class="w-4 h-4">
                                                        @if(request('sort_by') == 'orders_count' && request('sort_dir') == 'desc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') == 'orders_count' && request('sort_dir') == 'asc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') != 'orders_count') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg> @endif
                                                    </div>
                                                </a>
                                            </th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                @php $nextDir = (request('sort_by') == 'orders_sum_jumlah' && request('sort_dir') == 'asc') ? 'desc' : 'asc'; @endphp
                                                <a href="{{ route('admin.reports.index', ['sort_by' => 'orders_sum_jumlah', 'sort_dir' => $nextDir, 'month' => request('month', 'all')]) }}#customers-report" class="flex items-center justify-center gap-1 cursor-pointer hover:text-gray-800">
                                                    <span>Total Items</span>
                                                    <div class="w-4 h-4">
                                                        @if(request('sort_by') == 'orders_sum_jumlah' && request('sort_dir') == 'desc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') == 'orders_sum_jumlah' && request('sort_dir') == 'asc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') != 'orders_sum_jumlah') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg> @endif
                                                    </div>
                                                </a>
                                            </th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                @php $nextDir = (request('sort_by', 'total_spent') == 'total_spent' && request('sort_dir') == 'asc') ? 'desc' : 'asc'; @endphp
                                                <a href="{{ route('admin.reports.index', ['sort_by' => 'total_spent', 'sort_dir' => $nextDir, 'month' => request('month', 'all')]) }}#customers-report" class="flex items-center justify-end gap-1 cursor-pointer hover:text-gray-800">
                                                    <span>Total Spent</span>
                                                    <div class="w-4 h-4">
                                                        @if(request('sort_by', 'total_spent') == 'total_spent' && request('sort_dir', 'desc') == 'desc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') == 'total_spent' && request('sort_dir') == 'asc') <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg> @endif
                                                        @if(request('sort_by') != 'total_spent' && request('sort_by') != null) <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg> @endif
                                                    </div>
                                                </a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topCustomers as $customer)
                                        <tr class="hover:bg-gray-50 cursor-pointer" @click="getCustomerDetails({{ $customer->id }}, '{{ addslashes($customer->name) }}')">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap">{{ $customer->name }}</p></td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap">{{ $customer->email }}</p></td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-center text-sm"><p class="text-gray-900 whitespace-no-wrap">{{ $customer->orders_count }}</p></td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-center text-sm"><p class="text-gray-900 whitespace-no-wrap">{{ $customer->orders_sum_jumlah ?? 0 }} items</p></td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-right text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap font-semibold">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center px-5 py-5 border-b border-gray-200 bg-white text-sm">No customer data found for the selected period.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $topCustomers->withQueryString()->fragment('customers-report')->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </section>

                </div>
            </div>

            <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-full overflow-y-auto" @click.away="closeModal()">
                    <div class="p-6 border-b sticky top-0 bg-white z-10">
                        <h3 class="text-2xl font-bold" x-text="`Sales Details for ${modalMonth}`"></h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            <div class="relative has-tooltip inline-flex gap-x-1 cursor-pointer">
                                                <span>Fabric</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                                <div class="tooltip absolute z-20 w-56 p-3 -translate-x-1/2 left-1/2 mt-2 transition-opacity duration-300 bg-gray-800 text-white text-sm rounded-lg shadow-lg">
                                                    <div class="font-bold mb-2 text-center">Fabric Summary</div>
                                                    <div x-html="fabricTooltipContent"></div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            <div class="relative has-tooltip inline-flex gap-x-1 cursor-pointer">
                                                <span>Payment Method</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                                <div class="tooltip absolute z-20 w-64 p-3 -translate-x-1/2 left-1/2 mt-2 transition-opacity duration-300 bg-gray-800 text-white text-sm rounded-lg shadow-lg">
                                                    <div class="font-bold mb-2 text-center">Payment Summary</div>
                                                    <div x-html="paymentTooltipContent"></div>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Discount</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody x-html="monthlyDetails">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t sticky bottom-0 flex justify-end">
                        <button @click="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold">Close</button>
                    </div>
                </div>
            </div>

            <div x-show="isPromoModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-full overflow-y-auto" @click.away="closePromoModal()">
                    <div class="p-6 border-b sticky top-0 bg-white z-10">
                        <div class="flex justify-between items-center">
                            <h3 class="text-2xl font-bold" x-text="promoModalTitle"></h3>
                            <div x-show="promoUsageMonths.length > 0">
                                <select x-model="selectedPromoMonth" @change="updatePromoTable()" class="w-48 p-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="all">All Months</option>
                                    <template x-for="month in promoUsageMonths" :key="month">
                                        <option :value="month" x-text="formatMonthForDisplay(month)"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Discount Applied</th>
                                    </tr>
                                </thead>
                                <tbody x-html="promoDetailsContent">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t sticky bottom-0 flex justify-end">
                        <button @click="closePromoModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold">Close</button>
                    </div>
                </div>
            </div>

            <div x-show="isUserModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-full overflow-y-auto" @click.away="closeUserModal()">
                    <div class="p-6 border-b sticky top-0 bg-white z-10">
                        <h3 class="text-2xl font-bold" x-text="userModalTitle"></h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Registered On</th>
                                    </tr>
                                </thead>
                                <tbody x-html="userDetailsContent"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t sticky bottom-0 flex justify-end">
                        <button @click="closeUserModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold">Close</button>
                    </div>
                </div>
            </div>

            <div x-show="isCustomerModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl max-h-full overflow-y-auto" @click.away="closeCustomerModal()">
                    <div class="p-6 border-b sticky top-0 bg-white z-10">
                        <h3 class="text-2xl font-bold" x-text="customerModalTitle"></h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Date</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Items</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody x-html="customerDetailsContent"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t sticky bottom-0 flex justify-end">
                        <button @click="closeCustomerModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold">Close</button>
                    </div>
                </div>
            </div>

            <div id="promoReportJsonData" data-promos='{!! $promoReportJsonData ?? '{}' !!}' x-cloak></div>

        </main>
    </div>

    <script>
        function reports() {
            return {
                init() {
                    const promoDataEl = document.getElementById('promoReportJsonData');
                    if (promoDataEl) {
                        const promoData = JSON.parse(promoDataEl.getAttribute('data-promos'));
                        this.promoReportData = promoData;
                        this.mainPromoFilterMonths = Object.keys(promoData).filter(k => k !== 'all').sort().reverse();
                        this.filterMainPromoReport();
                    }
                    if (window.location.hash) {
                        const hash = window.location.hash.substring(1);
                        if (['sales', 'promo', 'users', 'customers'].includes(hash.replace('-report', ''))) {
                            this.activeReport = hash.replace('-report', '');
                        }
                    }
                },
                activeReport: 'sales',
                
                isModalOpen: false,
                modalMonth: '',
                monthlyDetails: '',
                fabricTooltipContent: 'Hover to see summary',
                paymentTooltipContent: 'Hover to see summary',

                promoReportData: {},
                filteredPromoData: [],
                mainPromoFilterMonths: [],
                selectedMainPromoMonth: 'all',

                isPromoModalOpen: false,
                promoModalTitle: '',
                promoDetailsContent: '',
                fullPromoUsageData: [],
                promoUsageMonths: [],
                selectedPromoMonth: 'all',
                promoSortColumn: 'usage_count',
                promoSortDirection: 'desc',

                isUserModalOpen: false,
                userModalTitle: '',
                userDetailsContent: '',

                isCustomerModalOpen: false,
                customerModalTitle: '',
                customerDetailsContent: '',
                topCustomersData: {},
                filteredTopCustomers: [],
                topCustomersFilterMonths: [],
                selectedTopCustomersMonth: 'all',
                customerSortColumn: 'total_spent',
                customerSortDirection: 'desc',

                getDetails(year, month) {
                    const monthName = new Date(year, month - 1, 1).toLocaleString('en-UK', { month: 'long', year: 'numeric' });
                    this.modalMonth = `${monthName}`;

                    fetch(`/admin/report/sales-details/${year}/${month}`)
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            let tableRows = '';
                            let totalMonthlySales = 0;
                            let fabricSummary = {};
                            let paymentSummary = {};
                            if (data && data.length > 0) {
                                data.forEach((order, index) => {
                                    totalMonthlySales += parseFloat(order.total || 0);
                                    const fabric = order.fabric_type || 'Unknown';
                                    const quantity = parseInt(order.jumlah || 0);
                                    const payment = order.metode_bayar || 'Unknown';
                                    fabricSummary[fabric] = (fabricSummary[fabric] || 0) + quantity;
                                    paymentSummary[payment] = (paymentSummary[payment] || 0) + 1;
                                    tableRows += `
                                        <tr class="border-b border-gray-200">
                                            <td class="px-5 py-4 bg-white text-sm">${index + 1}</td>
                                            <td class="px-5 py-4 bg-white text-sm font-semibold text-blue-600">${order.id_pesanan || 'N/A'}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${order.nama || 'N/A'}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${new Date(order.tanggal_pesan).toLocaleDateString('id-ID')}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${order.fabric_type || 'N/A'}</td>
                                            <td class="px-5 py-4 bg-white text-sm text-center">${order.jumlah || 0}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${order.metode_bayar || 'N/A'}</td>
                                            <td class="px-5 py-4 bg-white text-sm text-right text-red-600">${order.discount_amount > 0 ? `Rp ${new Intl.NumberFormat('id-ID').format(order.discount_amount)}` : 'Rp 0'}</td>
                                            <td class="px-5 py-4 bg-white text-sm text-right font-bold">Rp ${new Intl.NumberFormat('id-ID').format(order.total)}</td>
                                        </tr>
                                    `;
                                });
                                let fabricTooltipHtml = '<ul class="space-y-1 text-left">';
                                Object.entries(fabricSummary).forEach(([type, qty]) => { fabricTooltipHtml += `<li class="flex justify-between"><span>${type}</span><span class="font-bold ml-2">${qty} pcs</span></li>`; });
                                this.fabricTooltipContent = fabricTooltipHtml + '</ul>';
                                let paymentTooltipHtml = '<ul class="space-y-1 text-left">';
                                Object.entries(paymentSummary).forEach(([method, count]) => { paymentTooltipHtml += `<li class="flex justify-between"><span>${method}</span><span class="font-bold ml-2">${count}x</span></li>`; });
                                this.paymentTooltipContent = paymentTooltipHtml + '</ul>';
                                tableRows += `
                                    <tr class="font-bold bg-gray-50 border-t-2 border-gray-300">
                                        <td colspan="8" class="px-5 py-4 text-right">Total Sales for ${monthName} :</td>
                                        <td class="px-5 py-4 text-right">Rp ${new Intl.NumberFormat('id-ID').format(totalMonthlySales)}</td>
                                    </tr>
                                `;
                            } else {
                                tableRows = `<tr><td colspan="9" class="text-center p-5">No detailed sales data for this month.</td></tr>`;
                            }
                            this.monthlyDetails = tableRows;
                            this.isModalOpen = true;
                            document.body.classList.add('modal-active');
                        })
                        .catch(error => {
                            console.error('Error fetching monthly details:', error);
                            this.monthlyDetails = `<tr><td colspan="9" class="text-center p-5 text-red-500">Failed to load data. ${error.message}</td></tr>`;
                            this.isModalOpen = true;
                            document.body.classList.add('modal-active');
                        });
                },
                closeModal() {
                    this.isModalOpen = false;
                    document.body.classList.remove('modal-active');
                    this.monthlyDetails = '';
                    this.modalMonth = '';
                    this.fabricTooltipContent = 'Hover to see summary';
                    this.paymentTooltipContent = 'Hover to see summary';
                },

                filterMainPromoReport() {
                    this.filteredPromoData = this.promoReportData[this.selectedMainPromoMonth] || [];
                    this.applySort('promo');
                },
                sortBy(column, type) {
                    if (type === 'promo') {
                        if (this.promoSortColumn === column) {
                            this.promoSortDirection = this.promoSortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.promoSortColumn = column;
                            this.promoSortDirection = 'desc';
                        }
                        this.applySort(type);
                    }
                },
                applySort(type) {
                    let data, column, direction;
                    if (type === 'promo') {
                        data = this.filteredPromoData;
                        column = this.promoSortColumn;
                        direction = this.promoSortDirection;
                    } else {
                        return;
                    }
                    data.sort((a, b) => {
                        let valA = a[column];
                        let valB = b[column];
                        if (typeof valA === 'string' && isNaN(valA)) {
                            valA = valA.toLowerCase();
                            valB = valB.toLowerCase();
                            return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                        }
                        const numA = parseFloat(valA);
                        const numB = parseFloat(valB);
                        return direction === 'asc' ? numA - numB : numB - numA;
                    });
                },

                getPromoDetails(promoCode, monthToSelect = 'all') {
                    this.promoModalTitle = `Usage Details for "${promoCode}"`;
                    fetch(`/admin/report/promo-details/${promoCode}`)
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            this.fullPromoUsageData = data;
                            const months = new Set(data.map(usage => usage.tanggal_pesan.substring(0, 7)));
                            this.promoUsageMonths = Array.from(months).sort().reverse();
                            
                            this.selectedPromoMonth = this.promoUsageMonths.includes(monthToSelect) ? monthToSelect : 'all';

                            this.updatePromoTable();
                            this.isPromoModalOpen = true;
                            document.body.classList.add('modal-active');
                        })
                        .catch(error => {
                            console.error('Error fetching promo details:', error);
                            this.promoDetailsContent = `<tr><td colspan="5" class="text-center p-5 text-red-500">Failed to load data. ${error.message}</td></tr>`;
                            this.isPromoModalOpen = true;
                            document.body.classList.add('modal-active');
                        });
                },
                updatePromoTable() {
                    let filteredData = this.fullPromoUsageData;
                    if (this.selectedPromoMonth !== 'all') {
                        filteredData = this.fullPromoUsageData.filter(usage => {
                            return usage.tanggal_pesan.startsWith(this.selectedPromoMonth);
                        });
                    }

                    let tableRows = '';
                    let totalDiscountApplied = 0;
                    if (filteredData && filteredData.length > 0) {
                        filteredData.forEach((usage, index) => {
                            const discount = parseFloat(usage.discount_amount || 0);
                            totalDiscountApplied += discount;
                            tableRows += `
                                <tr class="border-b border-gray-200">
                                    <td class="px-5 py-4 bg-white text-sm">${index + 1}</td>
                                    <td class="px-5 py-4 bg-white text-sm font-semibold text-blue-600">${usage.id_pesanan || 'N/A'}</td>
                                    <td class="px-5 py-4 bg-white text-sm">${usage.customer_name || 'N/A'}</td>
                                    <td class="px-5 py-4 bg-white text-sm">${new Date(usage.tanggal_pesan).toLocaleDateString('en-UK', {day: '2-digit', month: 'long', year: 'numeric'})}</td>
                                    <td class="px-5 py-4 bg-white text-sm text-right text-red-600 font-semibold">Rp ${new Intl.NumberFormat('id-ID').format(discount)}</td>
                                </tr>`;
                        });
                        tableRows += `
                            <tr class="font-bold bg-gray-50 border-t-2 border-gray-300">
                                <td colspan="4" class="px-5 py-4 text-right">Total Discount Given:</td>
                                <td class="px-5 py-4 text-right">Rp ${new Intl.NumberFormat('id-ID').format(totalDiscountApplied)}</td>
                            </tr>`;
                    } else {
                        tableRows = `<tr><td colspan="5" class="text-center p-5">No usage data found for this period.</td></tr>`;
                    }
                    this.promoDetailsContent = tableRows;
                },
                closePromoModal() {
                    this.isPromoModalOpen = false;
                    document.body.classList.remove('modal-active');
                    this.promoDetailsContent = '';
                    this.promoModalTitle = '';
                    this.fullPromoUsageData = [];
                    this.promoUsageMonths = [];
                    this.selectedPromoMonth = 'all';
                },
                formatMonthForDisplay(ymString) {
                    if (!ymString || ymString === 'all') return "All Months";
                    const [year, month] = ymString.split('-');
                    return new Date(year, month - 1).toLocaleString('en-UK', { month: 'long', year: 'numeric' });
                },

                getUserDetails(year, month) {
                    const monthName = new Date(year, month - 1, 1).toLocaleString('en-UK', { month: 'long', year: 'numeric' });
                    this.userModalTitle = `User Registrations for ${monthName}`;
                    
                    fetch(`/admin/report/user-details/${year}/${month}`)
                        .then(response => response.json())
                        .then(data => {
                            let tableRows = '';
                            if (data && data.length > 0) {
                                data.forEach((user, index) => {
                                    tableRows += `
                                        <tr class="border-b border-gray-200">
                                            <td class="px-5 py-4 bg-white text-sm">${index + 1}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${user.name}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${user.email}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${new Date(user.created_at).toLocaleDateString('en-UK', {day: '2-digit', month: 'long', year: 'numeric'})}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                tableRows = '<tr><td colspan="4" class="text-center p-5">No new users found for this month.</td></tr>';
                            }
                            this.userDetailsContent = tableRows;
                            this.isUserModalOpen = true;
                            document.body.classList.add('modal-active');
                        })
                        .catch(error => {
                            console.error('Error fetching user details:', error);
                            this.userDetailsContent = '<tr><td colspan="4" class="text-center p-5 text-red-500">Failed to load data.</td></tr>';
                            this.isUserModalOpen = true;
                        });
                },
                closeUserModal() {
                    this.isUserModalOpen = false;
                    document.body.classList.remove('modal-active');
                    this.userDetailsContent = '';
                },

                getCustomerDetails(userId, customerName) {
                    this.customerModalTitle = `Order History for ${customerName}`;
                    fetch(`/admin/report/customer-details/${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            let tableRows = '';
                            let lifetimeValue = 0;
                            if (data && data.length > 0) {
                                data.forEach((order, index) => {
                                    const total = parseFloat(order.total || 0);
                                    lifetimeValue += total;
                                    tableRows += `
                                        <tr class="border-b border-gray-200">
                                            <td class="px-5 py-4 bg-white text-sm">${index + 1}</td>
                                            <td class="px-5 py-4 bg-white text-sm font-semibold text-blue-600">${order.id_pesanan}</td>
                                            <td class="px-5 py-4 bg-white text-sm">${new Date(order.tanggal_pesan).toLocaleDateString('en-UK', {day: '2-digit', month: 'long', year: 'numeric'})}</td>
                                            <td class="px-5 py-4 bg-white text-sm text-center">${order.jumlah || 0}</td>
                                            <td class="px-5 py-4 bg-white text-sm text-right font-semibold">Rp ${new Intl.NumberFormat('id-ID').format(total)}</td>
                                        </tr>
                                    `;
                                });
                                tableRows += `
                                    <tr class="font-bold bg-gray-50 border-t-2 border-gray-300">
                                        <td colspan="4" class="px-5 py-4 text-right">Total Lifetime Value:</td>
                                        <td class="px-5 py-4 text-right">Rp ${new Intl.NumberFormat('id-ID').format(lifetimeValue)}</td>
                                    </tr>`;
                            } else {
                                tableRows = '<tr><td colspan="5" class="text-center p-5">This customer has no order history.</td></tr>';
                            }
                            this.customerDetailsContent = tableRows;
                            this.isCustomerModalOpen = true;
                            document.body.classList.add('modal-active');
                        })
                        .catch(error => {
                            console.error('Error fetching customer details:', error);
                            this.customerDetailsContent = '<tr><td colspan="5" class="text-center p-5 text-red-500">Failed to load data.</td></tr>';
                            this.isCustomerModalOpen = true;
                        });
                },
                closeCustomerModal() {
                    this.isCustomerModalOpen = false;
                    document.body.classList.remove('modal-active');
                    this.customerDetailsContent = '';
                },
                
            }
        }
    </script>
</body>
</html>