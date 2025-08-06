<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik - Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                        'dancing': ['Dancing Script', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        .font-dancing { font-family: 'Dancing Script', cursive; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800" x-data="adminTableData()" x-init="init()">

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
                        <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reports</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm font-semibold text-red-600 hover:text-red-800 transition">Logout</a></form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <main class="bg-white rounded-2xl shadow-sm p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative">
                        <button @click="tableDropdownOpen = !tableDropdownOpen" class="flex items-center space-x-3 bg-gray-100 px-4 py-2 md:px-6 md:py-3 rounded-xl">
                            <h1 class="text-xl md:text-3xl font-bold text-gray-800 capitalize" x-text="activeTable"></h1>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="tableDropdownOpen" @click.away="tableDropdownOpen = false" x-cloak class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg z-20">
                            <a href="#users" @click.prevent="activeTable = 'users'; tableDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Users</a>
                            <a href="#orders" @click.prevent="activeTable = 'orders'; tableDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Orders</a>
                            <a href="#promos" @click.prevent="activeTable = 'promos'; tableDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Promos</a>
                            <a href="#notifications" @click.prevent="activeTable = 'notifications'; tableDropdownOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Notifications</a>
                        </div>
                    </div>
                    <div x-show="activeTable === 'promos'" x-cloak>
                        <button @click.prevent="createPromoModalOpen = true" class="bg-green-600 text-white font-semibold py-2 px-4 md:py-3 md:px-6 rounded-xl hover:bg-green-700 transition">Create Promo</button>
                    </div>
                    <div x-show="activeTable === 'notifications'" x-cloak>
                        <button @click.prevent="createNotificationModalOpen = true" class="bg-green-600 text-white font-semibold py-2 px-4 md:py-3 md:px-6 rounded-xl hover:bg-green-700 transition">Send Notification</button>
                    </div>
                </div>
                
                <div class="relative w-full sm:w-auto">
                    <input type="text" x-model="searchTerm" placeholder="Search..." class="w-full sm:w-64 pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <div id="users-table" x-show="activeTable === 'users'" class="overflow-x-auto" x-cloak>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="user.id"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="user.name"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="user.email"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm">
                                    <span x-text="user.email_verified_at ? 'Verified' : 'Not Verified'" :class="{ 'text-green-600 font-semibold': user.email_verified_at, 'text-yellow-600 font-semibold': !user.email_verified_at }"></span>
                                </td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button @click="openEditUserModal(user)" class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700 transition-colors">Edit</button>

                                        <form :action="`/admin/users/${user.id}/verify`" method="POST" class="inline-block" @submit.prevent="handleFormSubmit($event, 'User verification status changed!', `Failed to change status.` )">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 text-white text-xs font-semibold rounded-md transition-colors" :class="user.email_verified_at ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600'">
                                                <span x-text="user.email_verified_at ? 'Unverify' : 'Verify'"></span>
                                            </button>
                                        </form>
                                        <form :id="`delete-user-form-${user.id}`" :action="`/admin/users/${user.id}`" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="openDeleteModal(user.id, 'user')" class="px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition-colors">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div id="orders-table" x-show="activeTable === 'orders'" class="overflow-x-auto" x-cloak>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-2 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th scope="col" class="px-2 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">User ID</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="order in filteredOrders" :key="order.id_pesanan">
                            <tr class="hover:bg-gray-50 cursor-pointer" @click="showOrderModal(order)">
                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center" x-text="order.id_pesanan"></td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-600 text-center" x-text="order.id_user"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(order.total)"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold" :class="{ 'text-yellow-600': order.status === 'Pending', 'text-blue-600': order.status === 'In Progress', 'text-green-600': order.status === 'Ready', 'text-red-600': order.status === 'Cancelled', 'text-green-700': order.status === 'Completed' }" x-text="order.status"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="new Date(order.tanggal_pesan).toLocaleDateString('id-ID')"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div id="promos-table" x-show="activeTable === 'promos'" class="overflow-x-auto" x-cloak>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Value</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uses</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Expires At</th>
                            <th scope="col" class="px-2 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="promo in filteredPromos" :key="promo.id">
                            <tr>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="promo.code"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize" x-text="promo.type"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="promo.type === 'percentage' ? promo.value + '%' : 'Rp ' + new Intl.NumberFormat('id-ID').format(promo.value)"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="`${promo.current_uses || 0} / ${promo.max_uses || '∞'}`"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="promo.expires_at ? new Date(promo.expires_at).toLocaleDateString('id-ID') : 'Never'"></td>
                                <td class="px-2 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button @click="openEditModal(promo)" class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700 transition-colors">Edit</button>
                                        
                                        <form :id="`delete-promo-form-${promo.id}`" :action="`/admin/promos/${promo.id}`" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="openDeleteModal(promo.id, 'promo')" class="px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition-colors">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div id="notifications-table" x-show="activeTable === 'notifications'" class="overflow-x-auto" x-cloak>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[150px]">User</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[150px]">Title</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[300px]">Message</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Sent At</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="notification in filteredNotifications" :key="notification.id">
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="notification.user.name"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600" x-text="notification.title"></td>
                                <td class="px-4 py-4 whitespace-normal text-sm text-gray-600" x-text="notification.message"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600" x-text="new Date(notification.created_at).toLocaleString('id-ID')"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button @click="openEditNotificationModal(notification)" class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700 transition-colors">Edit</button>
                                        <form :id="`delete-notification-form-${notification.id}`" :action="`/admin/notifications/${notification.id}`" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="openDeleteModal(notification.id, 'notification')" class="px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition-colors">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <div x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" x-cloak>
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
        <div x-show="open" x-transition class="relative bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:max-w-6xl sm:w-full overflow-y-auto" style="max-height: 90vh;">
            <template x-if="selectedOrder">
                <div>
                    <div id="order-details-content">
                        <div class="px-4 sm:px-6 py-4 border-b sticky top-0 bg-white z-10"><h3 class="text-xl md:text-2xl leading-6 font-bold text-gray-900">Order Details</h3></div>
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                                <div class="flex flex-col">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                                        <div class="space-y-3 text-sm">
                                            <p><strong class="font-semibold text-gray-900 block">Order ID:</strong> <span class="text-gray-600" x-text="selectedOrder.id_pesanan"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Customer Name:</strong> <span class="text-gray-600" x-text="selectedOrder.nama"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Email:</strong> <span class="text-gray-600" x-text="selectedOrder.email"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Phone:</strong> <span class="text-gray-600" x-text="selectedOrder.no_telepon"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Address:</strong> <span class="text-gray-600" x-text="selectedOrder.alamat"></span></p>
                                        </div>
                                        <div class="space-y-3 text-sm">
                                            <p class="md:text-right text-gray-500">last update: <span x-text="new Date(selectedOrder.updated_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Item:</strong> <span class="text-gray-600">Custom Batik</span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Size:</strong> <span class="text-gray-600" x-text="selectedOrder.ukuran"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Total Price:</strong> <span class="text-gray-800 font-bold">Rp <span x-text="new Intl.NumberFormat('id-ID').format(selectedOrder.total)"></span></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Payment Method:</strong> <span class="text-gray-600" x-text="selectedOrder.metode_bayar"></span></p>
                                            <p><strong class="font-semibold text-gray-900 block">Order Date:</strong> <span class="text-gray-600" x-text="new Date(selectedOrder.tanggal_pesan).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' })"></span></p>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <label for="note" class="block text-sm font-semibold text-gray-900">Note:</label>
                                        <textarea id="note" name="note" x-model="selectedOrder.nota" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm bg-gray-100 cursor-not-allowed" readonly></textarea>
                                    </div>
                                </div>
                                <div class="mt-6 lg:mt-0">
                                    <div x-show="selectedOrder.desain">
                                        <label class="block text-sm font-semibold text-gray-900">Design Picture:</label>
                                        <div class="mt-2 p-2 border border-gray-200 rounded-lg bg-gray-50 h-full flex items-center justify-center">
                                            <img :src="'/storage/' + selectedOrder.desain" alt="Batik Design" class="w-full h-auto rounded-md object-contain max-h-[450px]" crossorigin="anonymous">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:px-6 sm:py-4 flex flex-col-reverse gap-y-4 sm:flex-row sm:justify-between sm:items-center border-t bg-gray-50 sticky bottom-0 z-10">
                        <div class="flex flex-col gap-y-3 sm:flex-row sm:items-center sm:gap-x-6 w-full sm:w-auto">
                            <div class="flex items-center gap-x-3">
                                <label for="status" class="block text-sm font-bold text-gray-900">Status:</label>
                                <select x-model="selectedOrder.status" id="status" name="status" @change="updateNotificationMessageOnStatusChange()"  class="block w-full pl-3 pr-10 py-1.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option>Pending</option>
                                    <option>Hold</option>
                                    <option>Cancelled</option>
                                    <option>In Progress</option>
                                    <option>Ready</option>
                                    <option>Completed</option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input id="send-notification-checkbox" type="checkbox" x-model="sendNotification" class="h-4 w-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                                <label for="send-notification-checkbox" class="ml-2 block text-sm font-medium text-gray-900">
                                    Send Notification
                                </label>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-2 w-full sm:w-auto">
                            <button 
                                x-show="selectedOrder.bukti_pembayaran"
                                type="button" 
                                @click="$dispatch('open-proof-modal', { imageUrl: '/storage/' + selectedOrder.bukti_pembayaran })"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg font-semibold hover:bg-indigo-600 transition-colors text-sm">
                                View Payment
                            </button>
                            <button type="button" @click="exportOrder()" :disabled="isExporting" class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors disabled:bg-green-300 disabled:cursor-not-allowed text-sm">
                                <span x-show="!isExporting">Export</span><span x-show="isExporting">Exporting...</span>
                            </button>
                            <button type="button" @click="open = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition-colors text-sm">Cancel</button>
                            <button type="button" @click="handleSaveClick()" class="px-6 py-2 bg-cyan-500 text-white rounded-lg font-semibold hover:bg-cyan-600 transition-colors text-sm">Save</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div 
        x-data="{ 
            show: false, 
            imageUrl: '',
            zoomLevel: 1,
            panning: false,
            startX: 0, startY: 0,
            translateX: 0, translateY: 0,
            reset() {
                this.zoomLevel = 1;
                this.translateX = 0;
                this.translateY = 0;
            }
        }"
        @open-proof-modal.window="show = true; imageUrl = $event.detail.imageUrl; reset()"
        @keydown.escape.window="show = false"
        x-show="show"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4" 
        x-cloak>

        <div @click="show = false" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>

        <div class="relative w-full h-full flex items-center justify-center overflow-hidden cursor-grab active:cursor-grabbing"
            @mousedown="panning = true; startX = $event.pageX - translateX; startY = $event.pageY - translateY;"
            @mouseup="panning = false"
            @mousemove.window="if (panning) { event.preventDefault(); translateX = $event.pageX - startX; translateY = $event.pageY - startY; }"
            @mouseleave="panning = false"
            @wheel.prevent="zoomLevel = Math.max(0.5, zoomLevel - event.deltaY * 0.005); event.preventDefault();">

            <img :src="imageUrl" alt="Payment Proof"
                class="transition-transform duration-75 ease-out"
                :style="`transform: scale(${zoomLevel}) translate(${translateX}px, ${translateY}px); max-width: none; max-height: none;`">
        </div>

        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-x-2 bg-gray-900/50 text-white p-2 rounded-lg backdrop-blur-sm shadow-lg">
            <button @click="zoomLevel = Math.max(0.5, zoomLevel - 0.2)" title="Zoom Out" class="w-10 h-10 rounded-md hover:bg-white/20 flex items-center justify-center text-xl font-bold">-</button>
            <button @click="reset()" title="Reset Zoom" class="px-4 h-10 rounded-md hover:bg-white/20 text-sm">Reset</button>
            <button @click="zoomLevel += 0.2" title="Zoom In" class="w-10 h-10 rounded-md hover:bg-white/20 flex items-center justify-center text-xl font-bold">+</button>
        </div>
        
        <button @click="show = false" title="Close" class="absolute top-4 right-4 w-10 h-10 text-white bg-gray-900/50 rounded-full hover:bg-white/20 text-2xl flex items-center justify-center leading-none">&times;</button>
    </div>

    <div x-show="deleteModalOpen" @keydown.escape.window="closeDeleteModal()" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
         <div @click.away="closeDeleteModal()" class="bg-white p-6 md:p-8 rounded-lg shadow-xl text-center max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4" x-text="`Confirm ${deleteType.charAt(0).toUpperCase() + deleteType.slice(1)} Deletion`"></h3>
            <p class="mb-6">Are you sure you want to delete this <span x-text="deleteType"></span>? <br> This action cannot be undone.</p>
            <div class="flex justify-center gap-4">
                <button @click="confirmDelete()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg">Delete</button>
                <button @click="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
            </div>
        </div>
    </div>

    <div x-show="editUserModalOpen" @keydown.escape.window="editUserModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
        <div @click.away="editUserModalOpen = false" class="bg-white p-6 md:p-8 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-2xl font-bold mb-6">Edit User</h3>
            <template x-if="editingUser">
                <form :action="`/admin/users/${editingUser.id}`" method="POST" @submit.prevent="handleFormSubmit($event, 'User updated successfully!', 'Failed to update user.')">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_user_name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="edit_user_name" x-model="editingUser.name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3" required>
                        </div>
                        <div>
                            <label for="edit_user_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit_user_email" x-model="editingUser.email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3" required>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" @click="editUserModalOpen = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Update</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <div x-show="createPromoModalOpen" 
        @keydown.escape.window="createPromoModalOpen = false" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
        x-cloak>
        <div @click.away="createPromoModalOpen = false" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-2xl font-bold mb-6">Create New Promo Code</h3>
            <form id="create-promo-form" action="{{ route('admin.promo.store') }}" method="POST" @submit.prevent="handleFormSubmit($event, 'Promo created successfully!', 'Failed to create promo.')">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Promo Code</label>
                        <input type="text" name="code" id="code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700">Value</label>
                        <input type="number" name="value" id="value" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" required step="any">
                    </div>
                    <div class="flex items-end gap-x-4">
                        <div class="flex-1">
                            <label for="max_uses" class="block text-sm font-medium text-gray-700">Max Uses (optional)</label>
                            <input type="number" name="max_uses" id="max_uses" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label for="max_uses_scope" class="block text-sm font-medium text-gray-700">Usage Scope</label>
                            <select name="max_uses_scope" id="max_uses_scope" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="global">Global</option>
                                <option value="personal">Per User</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700">Expires At (optional)</label>
                        <input type="date" name="expires_at" id="expires_at" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Constraint (optional)</h4>
                        <div id="create-constraints-container" class="space-y-3">
                            <template x-for="(constraint, index) in createPromoConstraints" :key="index">
                                <div class="flex items-start gap-2 p-2 border rounded-md bg-gray-50">
                                    <div class="flex-shrink-0 w-1/3">
                                        <select :name="'constraints[' + index + '][type]'" x-model="constraint.type" @change="constraint.value = constraintOptions[constraint.type][0]" class="w-full mt-1 block border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="fabric_type">Fabric Type</option>
                                            <option value="payment_method">Payment Method</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow">
                                        <input type="hidden" :name="'constraints[' + index + '][value]'" :value="constraint.value">
                                        <div x-show="constraint.type === 'fabric_type'" class="flex flex-wrap gap-x-4 gap-y-2 items-center text-sm">
                                            <template x-for="option in constraintOptions.fabric_type" :key="option">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" :name="'create_fabric_constraint_' + index" :value="option" x-model="constraint.value" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-2 text-gray-700" x-text="option"></span>
                                                </label>
                                            </template>
                                        </div>
                                        <div x-show="constraint.type === 'payment_method'" class="flex flex-wrap gap-x-4 gap-y-2 items-center text-sm">
                                            <template x-for="option in constraintOptions.payment_method" :key="option">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" :name="'create_payment_constraint_' + index" :value="option" x-model="constraint.value" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-2 text-gray-700" x-text="option"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                    <button type="button" @click="createPromoConstraints.splice(index, 1)" class="flex-shrink-0 text-red-500 hover:text-red-700 font-bold text-xl self-center">&times;</button>
                                </div>
                            </template>
                        </div>
                        <button x-show="createPromoConstraints.length < 2" type="button" @click="createPromoConstraints.push({ type: 'fabric_type', value: constraintOptions.fabric_type[0] })" class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                            + Add Constraint
                        </button>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-4">
                    <button type="button" @click="createPromoModalOpen = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editPromoModalOpen" 
        @keydown.escape.window="editPromoModalOpen = false" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
        x-cloak>
        <div @click.away="editPromoModalOpen = false" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-2xl font-bold mb-6">Edit Promo Code</h3>
            <template x-if="editingPromo">
                <form :action="`/admin/promos/${editingPromo.id}`" method="POST" @submit.prevent="handleFormSubmit($event, 'Promo updated successfully!', 'Failed to update promo.')">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_code" class="block text-sm font-medium text-gray-700">Promo Code</label>
                            <input type="text" name="code" id="edit_code" x-model="editingPromo.code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label for="edit_type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="edit_type" x-model="editingPromo.type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_value" class="block text-sm font-medium text-gray-700">Value</label>
                            <input type="number" name="value" id="edit_value" x-model="editingPromo.value" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" required step="any">
                        </div>
                        <div class="flex items-end gap-x-4">
                            <div class="flex-1">
                                <label for="edit_max_uses" class="block text-sm font-medium text-gray-700">Max Uses (optional)</label>
                                <input type="number" name="max_uses" id="edit_max_uses" x-model="editingPromo.max_uses" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="flex-1">
                                <label for="edit_max_uses_scope" class="block text-sm font-medium text-gray-700">Usage Scope</label>
                                <select name="max_uses_scope" id="edit_max_uses_scope" x-model="editingPromo.max_uses_scope" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="global">Global</option>
                                    <option value="personal">Per User</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="edit_expires_at" class="block text-sm font-medium text-gray-700">Expires At (optional)</label>
                            <input type="date" name="expires_at" id="edit_expires_at" :value="editingPromo.expires_at ? editingPromo.expires_at.split(' ')[0] : ''" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Constraint (optional)</h4>
                            <div class="space-y-3">
                                <template x-for="(constraint, index) in editingPromo.constraints" :key="index">
                                    <div class="flex items-start gap-2 p-2 border rounded-md bg-gray-50">
                                        <div class="flex-shrink-0 w-1/3">
                                            <select :name="'constraints[' + index + '][type]'" x-model="constraint.type" @change="constraint.value = constraintOptions[constraint.type][0]" class="w-full mt-1 block border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="fabric_type">Fabric Type</option>
                                                <option value="payment_method">Payment Method</option>
                                            </select>
                                        </div>
                                        <div class="flex-grow">
                                            <input type="hidden" :name="'constraints[' + index + '][value]'" :value="constraint.value">
                                            <div x-show="constraint.type === 'fabric_type'" class="flex flex-wrap gap-x-4 gap-y-2 items-center text-sm">
                                                <template x-for="option in constraintOptions.fabric_type" :key="option">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="radio" :name="'edit_fabric_constraint_' + index" :value="option" x-model="constraint.value" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span class="ml-2 text-gray-700" x-text="option"></span>
                                                    </label>
                                                </template>
                                            </div>
                                            <div x-show="constraint.type === 'payment_method'" class="flex flex-wrap gap-x-4 gap-y-2 items-center text-sm">
                                                <template x-for="option in constraintOptions.payment_method" :key="option">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="radio" :name="'edit_payment_constraint_' + index" :value="option" x-model="constraint.value" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span class="ml-2 text-gray-700" x-text="option"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                        <button type="button" @click="editingPromo.constraints.splice(index, 1)" class="flex-shrink-0 text-red-500 hover:text-red-700 font-bold text-xl self-center">&times;</button>
                                    </div>
                                </template>
                            </div>
                            <button x-show="!editingPromo.constraints || editingPromo.constraints.length < 2" type="button" @click="if (!Array.isArray(editingPromo.constraints)) { editingPromo.constraints = [] }; editingPromo.constraints.push({ type: 'fabric_type', value: constraintOptions.fabric_type[0] })" class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                + Add Constraint
                            </button>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" @click="editPromoModalOpen = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Update</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <div x-show="notificationModalOpen" @keydown.escape.window="notificationModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4" x-cloak>
        <div @click.away="notificationModalOpen = false" class="bg-white p-6 md:p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h3 class="text-2xl font-bold mb-6">Compose Notification</h3>
            <div class="space-y-4">
                <div>
                    <label for="modal_notification_title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="modal_notification_title" x-model="notificationTitle" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label for="modal_notification_message" class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea id="modal_notification_message" rows="5" x-model="notificationMessage" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-cyan-500 focus:border-cyan-500"></textarea>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-4">
                <button type="button" @click="notificationModalOpen = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                <button type="button" @click="confirmUpdateAndNotify()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 px-6 rounded-lg">Send Notification</button>
            </div>
        </div>
    </div>
    
    <div x-show="createNotificationModalOpen" @keydown.escape.window="closeCreateNotificationModal()" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
		<div @click.away="closeCreateNotificationModal()" class="bg-white p-6 md:p-8 rounded-2xl shadow-xl w-full max-w-lg">
			<h3 class="text-2xl font-bold mb-6">Send New Notification</h3>
			
			<form id="create-notification-form" action="{{ route('admin.notifications.store') }}" method="POST" @submit.prevent="handleNotificationSubmit($event)">
				@csrf
				<div class="space-y-4">
					<div>
						<label class="block text-sm font-medium text-gray-700">Recipient(s)</label>
						<div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm h-36 overflow-y-auto text-sm">
							<div @click="toggleUserSelection('all')" class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between" :class="{'bg-blue-100 hover:bg-blue-200': selectedUserIds.includes('all')}">
								<span class="font-semibold">-- ALL USERS --</span>
								<svg x-show="selectedUserIds.includes('all')" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
							</div>
							<template x-for="user in usersData.filter(u => u.role !== 'admin')" :key="user.id">
								<div @click="toggleUserSelection(user.id)" class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between" :class="{'bg-blue-100 hover:bg-blue-200': selectedUserIds.includes(user.id)}">
									<span x-text="user.name + ' (' + user.email + ')'"></span>
									<svg x-show="selectedUserIds.includes(user.id)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
								</div>
							</template>
						</div>
					</div>

					<div x-show="selectedUserIds.length > 0 && !selectedUserIds.includes('all')" x-transition>
						<label class="block text-sm font-medium text-gray-700">Attach Order (Optional)</label>
						<div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm h-36 overflow-y-auto text-sm">
							<template x-if="isFetchingOrders"><p class="text-center text-gray-500 p-4">Loading orders...</p></template>
							<template x-if="!isFetchingOrders && !selectedUserOrders.length"><p class="text-center text-gray-500 p-4">No orders found for the selected user(s).</p></template>
							
							<template x-for="order in selectedUserOrders" :key="order.id_pesanan">
								<div @click="toggleOrderSelection(order.id_pesanan)" class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex justify-between items-center" :class="{'bg-green-100 hover:bg-green-200': selectedOrderIds.includes(order.id_pesanan)}">
									<span>Order #<span x-text="order.id_pesanan"></span> - <span x-text="order.status"></span> - <span x-text="order.user.name"></span></span>
									<svg x-show="selectedOrderIds.includes(order.id_pesanan)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
								</div>
							</template>
						</div>
					</div>

					<div>
						<label for="title" class="block text-sm font-medium text-gray-700">Title</label>
						<input type="text" name="title" id="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-cyan-500 focus:border-cyan-500" required>
					</div>
					
					<div>
						<label for="message" class="block text-sm font-medium text-gray-700">Message</label>
						<textarea name="message" id="message" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-cyan-500 focus:border-cyan-500" required></textarea>
					</div>
				</div>

				<div class="mt-8 flex justify-end gap-4">
					<button type="button" @click="closeCreateNotificationModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
					<button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">Send</button>
				</div>
			</form>
		</div>
	</div>
    
    <div x-show="editNotificationModalOpen" @keydown.escape.window="editNotificationModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
        <div @click.away="editNotificationModalOpen = false" class="bg-white p-6 md:p-8 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-2xl font-bold mb-6">Edit Notification</h3>
            <template x-if="editingNotification">
                <form :action="`/admin/notifications/${editingNotification.id}`" method="POST" @submit.prevent="handleFormSubmit($event, 'Notification updated successfully!', 'Failed to update notification.')">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sent to</label>
                            <input type="text" :value="editingNotification.user ? editingNotification.user.name : 'N/A'" class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3" readonly>
                        </div>
                        <div>
                            <label for="edit_title" class="block text-sm font-medium text-gray-700">Title</label>
                            <textarea name="title" id="edit_title" x-model="editingNotification.title" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3" required></textarea>
                        </div>
                        <div>
                            <label for="edit_message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="message" id="edit_message" x-model="editingNotification.message" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3" required></textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" @click="editNotificationModalOpen = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Update</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
    <x-alert />
    <x-logout-modal />

    <script>
    function adminTableData() {
        return {
            activeTable: 'users',
            tableDropdownOpen: false,
            searchTerm: '',
            usersData: @json($users ?? []),
            ordersData: @json($orders ?? []),
            promosData: @json($promos ?? []),
            notificationsData: @json($notifications ?? []),
            
            open: false,
            selectedOrder: null,
            isExporting: false,

            deleteModalOpen: false,
            deleteId: null,
            deleteType: '',

            createPromoModalOpen: false,
            createPromoConstraints: [],
            editPromoModalOpen: false,
            editingPromo: null,
            constraintOptions: {
                fabric_type: ['kain mori', 'kain sutera'],
                payment_method: ['bank_transfer', 'qris']
            },

            editUserModalOpen: false,
            editingUser: null,

            editNotificationModalOpen: false,
            editingNotification: null,

            createNotificationModalOpen: false,
            selectedUserIds: [],
            selectedUserOrders: [],
            selectedOrderIds: [],
            isFetchingOrders: false,
            sendNotification: true,
            notificationModalOpen: false,
            notificationTitle: '',
            notificationMessage: '',
            
            init() {
                const hash = window.location.hash.substring(1);
                if (['promos', 'orders', 'users', 'notifications'].includes(hash)) { this.activeTable = hash; }
                
                window.addEventListener('hashchange', () => {
                    const newHash = window.location.hash.substring(1);
                    if (['promos', 'orders', 'users', 'notifications'].includes(newHash)) { this.activeTable = newHash; }
                });

                this.$watch('selectedOrder', (newOrder) => {
                    if (newOrder && this.sendNotification) {
                        const content = this.getNotificationContent(newOrder.status);
                        this.notificationTitle = content.title;
                        this.notificationMessage = content.message;
                    }
                });
                
                this.$watch('sendNotification', (isSending) => {
                    if (isSending && this.selectedOrder) {
                        const content = this.getNotificationContent(this.selectedOrder.status);
                        this.notificationTitle = content.title;
                        this.notificationMessage = content.message;
                    } else {
                        this.notificationTitle = '';
                        this.notificationMessage = '';
                    }
                });

                @if (session('success'))
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'success', message: '{{ session('success') }}' }}));
                @endif
                @if (session('error'))
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: '{{ session('error') }}' }}));
                @endif
            },

            getNotificationContent(status) {
                if (!this.selectedOrder) return { title: '', message: '' };

                const orderId = this.selectedOrder.id_pesanan;
                const customerName = this.selectedOrder.nama;
                let title = `Update for Order #${orderId}`;
                let message = '';

                switch (status) {
                    case 'Hold':
                        message = `Hi ${customerName}, your order #${orderId} is currently on hold. We will notify you with any updates.`;
                        break;
                    case 'Cancelled':
                        message = `Hi ${customerName}, we are sorry to inform you that your order #${orderId} has been cancelled.`;
                        break;
                    case 'In Progress':
                        message = `Hi ${customerName}, great news! Your order #${orderId} is now in progress. We estimate it will be completed in approximately 7 days.`;
                        break;
                    case 'Ready':
                        message = `Hi ${customerName}, your order #${orderId} is now ready for pickup or shipment. We will provide tracking details shortly if applicable.`;
                        break;
                    case 'Completed':
                        message = `Hi ${customerName}, your order #${orderId} has been completed. We hope you enjoy your custom batik! Thank you for your purchase.`;
                        break;
                    default:
                        message = `Hi ${customerName}, the status of your order #${orderId} has been updated to "${status}".`;
                        break;
                }

                return { title, message };
            },

            updateNotificationMessageOnStatusChange() {
                if (this.selectedOrder && this.sendNotification) {
                    const content = this.getNotificationContent(this.selectedOrder.status);
                    this.notificationTitle = content.title;
                    this.notificationMessage = content.message;
                }
            },

            openCreateNotificationModal() {
                this.createNotificationModalOpen = true;
                this.selectedUserIds = [];
                this.selectedUserOrders = [];
                this.selectedOrderIds = [];
                document.getElementById('create-notification-form').reset();
            },
            closeCreateNotificationModal() {
                this.createNotificationModalOpen = false;
            },

            toggleUserSelection(userId) {
                this.selectedUserOrders = [];
                this.selectedOrderIds = [];
                
                const index = this.selectedUserIds.indexOf(userId);

                if (userId === 'all') {
                    this.selectedUserIds = this.selectedUserIds.includes('all') ? [] : ['all'];
                } else {
                    if (this.selectedUserIds.includes('all')) this.selectedUserIds = [];
                    
                    if (index === -1) this.selectedUserIds.push(userId);
                    else this.selectedUserIds.splice(index, 1);
                }
                
                if (this.selectedUserIds.length > 0 && !this.selectedUserIds.includes('all')) {
                    this.fetchUserOrders(this.selectedUserIds);
                }
            },

            toggleOrderSelection(orderId) {
                const index = this.selectedOrderIds.indexOf(orderId);
                if (index === -1) this.selectedOrderIds.push(orderId);
                else this.selectedOrderIds.splice(index, 1);
            },

            async fetchUserOrders(userIds) {
                if (!userIds.length) return;
                this.isFetchingOrders = true;
                try {
                    const response = await fetch(`/admin/users/${userIds.join(',')}/orders`);
                    if (!response.ok) throw new Error('Failed to fetch orders from server.');
                    
                    const data = await response.json();
                    this.selectedUserOrders = data;
                } catch (error) {
                    console.error('Error fetching user orders:', error);
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: 'Could not load user orders.' }}));
                } finally {
                    this.isFetchingOrders = false;
                }
            },

            async handleNotificationSubmit(event) {
                const form = event.target;
                const formData = new FormData(form);
                
                if (!this.selectedUserIds.length) {
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: 'Please select at least one recipient.' }}));
                    return;
                }
                
                this.selectedUserIds.forEach(id => formData.append('user_ids[]', id));
                this.selectedOrderIds.forEach(id => formData.append('order_ids[]', id));
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Failed to send notification(s).');
                    
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'success', message: data.message }}));
                    this.closeCreateNotificationModal();
                    setTimeout(() => window.location.reload(), 1500);
                } catch (error) {
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: error.message }}));
                }
            },

            async handleFormSubmit(event, successMessage, errorMessage) {
                const form = event.target;
                const formData = new FormData(form);
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        let message = data.message || errorMessage;
                        if(data.errors) {
                            message = Object.values(data.errors).flat().join(' ');
                        }
                        throw new Error(message);
                    }
                    
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'success', message: data.message || successMessage }}));
                    setTimeout(() => window.location.reload(), 1500);

                } catch (error) {
                    console.error('Form submission error:', error);
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: error.message || errorMessage }}));
                }
            },

            openEditUserModal(user) {
                this.editingUser = JSON.parse(JSON.stringify(user));
                this.editUserModalOpen = true;
            },

            openCreatePromoModal() {
                this.createPromoConstraints = [];
                document.getElementById('create-promo-form').reset();
                this.createPromoModalOpen = true;
            },
            
            openEditModal(promo) {
                this.editingPromo = JSON.parse(JSON.stringify(promo));
                if (!this.editingPromo.constraints) {
                    this.editingPromo.constraints = [];
                }
                this.editPromoModalOpen = true;
            },

            openEditNotificationModal(notification) {
                this.editingNotification = JSON.parse(JSON.stringify(notification));
                this.editNotificationModalOpen = true;
            },

            openDeleteModal(id, type) {
                this.deleteId = id;
                this.deleteType = type;
                this.deleteModalOpen = true;
            },

            closeDeleteModal() {
                this.deleteId = null;
                this.deleteType = '';
                this.deleteModalOpen = false;
            },

            confirmDelete() {
                if (this.deleteId && this.deleteType) {
                    const form = document.getElementById(`delete-${this.deleteType}-form-${this.deleteId}`);
                    if (form) {
                        this.handleFormSubmit({ target: form }, `${this.deleteType.charAt(0).toUpperCase() + this.deleteType.slice(1)} deleted successfully!`, `Failed to delete ${this.deleteType}.`);
                    }
                }
                this.closeDeleteModal();
            },

            showOrderModal(order) {
                this.selectedOrder = JSON.parse(JSON.stringify(order));
                this.sendNotification = true;
                
                this.notificationTitle = `Update for Order #${this.selectedOrder.id_pesanan}`;
                this.notificationMessage = `Hi ${this.selectedOrder.nama}, the status of your order has been updated to "${this.selectedOrder.status}".`;

                this.open = true;
            },

            handleSaveClick() {
				if (!this.sendNotification) {
					this.confirmUpdateAndNotify();
				} else {
					this.notificationModalOpen = true;
				}
			},

            async confirmUpdateAndNotify() {
                if (!this.selectedOrder) return;
                
                try {
                    let payload = { 
                        status: this.selectedOrder.status, 
                        nota: this.selectedOrder.nota,
                        send_notification: this.sendNotification
                    };
                    
                    if (this.sendNotification) {
                        payload.notification_title = this.notificationTitle;
                        payload.notification_message = this.notificationMessage;
                    }

                    const response = await fetch(`/admin/orders/${this.selectedOrder.id_pesanan}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(payload)
                    });
                    
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Server responded with an error');

                    const index = this.ordersData.findIndex(o => o.id_pesanan === data.id_pesanan);
                    if (index !== -1) this.ordersData.splice(index, 1, data);
                    
                    this.notificationModalOpen = false;
                    this.open = false;
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'success', message: 'Order successfully updated!' }}));

                } catch (error) {
                    console.error('Error details:', error);
                    window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: error.message || 'Update failed. Please try again.' }}));
                }
            },

            exportOrder() {
                if (!this.selectedOrder) return;
                this.isExporting = true;
                const content = document.getElementById('order-details-content');
                content.parentElement.scrollTop = 0;

                const waitForImagesToLoad = (element) => {
                    const images = element.querySelectorAll('img');
                    return Promise.all(Array.from(images).map(img => {
                        if (img.complete) return Promise.resolve();
                        return new Promise(resolve => { img.onload = img.onerror = resolve; });
                    }));
                };

                setTimeout(() => {
                    waitForImagesToLoad(content).then(() => {
                        html2canvas(content, { scale: 2, useCORS: true }).then(canvas => {
                            const imageData = canvas.toDataURL('image/png');
                            const pdf = new window.jspdf.jsPDF({ orientation: 'l', unit: 'mm', format: 'a4' });
                            const pdfWidth = pdf.internal.pageSize.getWidth();
                            const pdfHeight = pdf.internal.pageSize.getHeight();
                            const canvasAspectRatio = canvas.width / canvas.height;
                            let imgWidth = pdfWidth - 20;
                            let imgHeight = imgWidth / canvasAspectRatio;
                            if (imgHeight > pdfHeight - 20) {
                                imgHeight = pdfHeight - 20;
                                imgWidth = imgHeight * canvasAspectRatio;
                            }
                            const x = (pdfWidth - imgWidth) / 2;
                            const y = 10;
                            pdf.addImage(imageData, 'PNG', x, y, imgWidth, imgHeight);
                            pdf.save(`order_${this.selectedOrder.id_pesanan}_details.pdf`);
                            this.isExporting = false;
                            window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'success', message: 'Order exported to PDF successfully!' }}));
                        }).catch(error => {
                            console.error('Error during PDF export:', error);
                            this.isExporting = false;
                            window.dispatchEvent(new CustomEvent('alert', { detail: { type: 'error', message: 'Could not export to PDF.' }}));
                        });
                    });
                }, 250);
            },

            get filteredUsers() {
                if (!this.searchTerm) return this.usersData;
                const term = this.searchTerm.toLowerCase();
                return this.usersData.filter(user => (user.name && user.name.toLowerCase().includes(term)) || (user.email && user.email.toLowerCase().includes(term)));
            },
            get filteredOrders() {
                if (!this.searchTerm) return this.ordersData;
                const term = this.searchTerm.toLowerCase();
                return this.ordersData.filter(order => (order.nama && order.nama.toLowerCase().includes(term)) || (String(order.id_pesanan).toLowerCase().includes(term)) || (order.status && order.status.toLowerCase().includes(term)));
            },
            get filteredPromos() {
                if (!this.searchTerm) return this.promosData;
                const term = this.searchTerm.toLowerCase();
                return this.promosData.filter(promo => (promo.code && promo.code.toLowerCase().includes(term)) || (promo.type && promo.type.toLowerCase().includes(term)));
            },
            get filteredNotifications() {
                if (!this.searchTerm) return this.notificationsData;
                const term = this.searchTerm.toLowerCase();
                return this.notificationsData.filter(notification => (notification.user.name && notification.user.name.toLowerCase().includes(term)) || (notification.title && notification.title.toLowerCase().includes(term)) ||(notification.message && notification.message.toLowerCase().includes(term)));
            }
        }
    }
</script>
</body>
</html>