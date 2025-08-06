<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik - History</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
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
        html { scroll-behavior: smooth; }
        .star-rating-input input { display: none; }
        .star-rating-input label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
        .star-rating-input label:hover, .star-rating-input label:hover ~ label { color: #f5b301; }
        .star-rating-input input:checked ~ label { color: #f5b301; }
        .star-rating-display .star { font-size: 1.5rem; color: #ddd; }
        .star-rating-display .star.filled { color: #f5b301; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <x-header />

    <main class="container mx-auto py-12 md:py-16 px-4">
        <div class="max-w-4xl mx-auto" x-data="{ showModal: false, selectedOrderId: null, existingRating: 0, existingComment: '' }">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Your Order History</h1>

            <div class="flex flex-col md:flex-row gap-4 mb-8">

                <div class="relative flex-grow">
                    <input type="search" id="search-input" placeholder="Search by Order ID, Name, Status..." class="w-full pl-4 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <div class="flex-shrink-0">
                    <select id="sort-select" class="w-full md:w-auto h-full px-4 py-2 border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="newest">Sort by: Newest First</option>
                        <option value="oldest">Sort by: Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="space-y-8" id="orders-container">
                @forelse ($riwayatPesanan as $riwayat)
                    @if($riwayat->order)
                        <div class="order-card bg-white rounded-xl shadow-lg overflow-hidden"
                             data-id="{{ $riwayat->order->id_pesanan }}"
                             data-date="{{ optional($riwayat->order->tanggal_pesan)->timestamp }}"
                             data-status="{{ strtolower($riwayat->order->status) }}"
                             data-search-content="{{ strtolower($riwayat->order->id_pesanan . ' ' . $riwayat->order->nama . ' ' . $riwayat->order->email . ' ' . $riwayat->order->status) }}">

                            <div class="bg-gray-50 p-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                                <div><p class="text-sm text-gray-600">Order Placed</p><p class="font-semibold text-gray-800">{{ optional($riwayat->order->tanggal_pesan)->format('M d, Y') }}</p></div>
                                <div><p class="text-sm text-gray-600">Total</p><p class="font-semibold text-gray-800">Rp {{ number_format($riwayat->order->total, 0, ',', '.') }}</p></div>
                                <div><p class="text-sm text-gray-600">Order ID</p><p class="font-semibold text-gray-800">{{ $riwayat->order->id_pesanan }}</p></div>
                            </div>
                            <div class="p-6">
                                <h2 class="text-lg font-bold mb-4 {{
                                    $riwayat->order->status == 'Pending' ? 'text-yellow-500' : (
                                    $riwayat->order->status == 'In Progress' ? 'text-blue-500' : (
                                    $riwayat->order->status == 'Ready' ? 'text-green-600' : (
                                    $riwayat->order->status == 'Cancelled' ? 'text-red-600' : 'text-gray-800'
                                ))) }}">{{ $riwayat->order->status }}</h2>
                                <div class="flex items-start ">
                                    <div class="w-20 h-20 flex-shrink-0">
                                        <img src="{{ isset($riwayat->order->desain) ? asset('storage/' . $riwayat->order->desain) : 'https://placehold.co/1088x544' }}" alt="Batik Design" class="w-full h-full object-contain rounded-md">
                                    </div>
                                    <div class="w-full pl-4">
                                        <h3 class="font-semibold text-lg">Custom Batik</h3>
                                        <p class="text-gray-600">Size: {{ $riwayat->order->ukuran }}</p>
                                        <div class="flex mt-2">
                                            <div class="w-1/2 pr-6">
                                                <p class="text-gray-500 text-sm mt-1">
                                                    <span class="font-semibold text-gray-700">Customer Name:</span> {{ $riwayat->order->nama }}
                                                </p>
                                                <p class="text-gray-500 text-sm">
                                                    <span class="font-semibold text-gray-700">Email:</span> {{ $riwayat->order->email }}
                                                </p>
                                                <p class="text-gray-500 text-sm">
                                                    <span class="font-semibold text-gray-700">Address:</span> {{ $riwayat->order->alamat }}
                                                </p>
                                                <p class="text-gray-500 text-sm">
                                                    <span class="font-semibold text-gray-700">Phone Number:</span> {{ $riwayat->order->no_telepon }}
                                                </p>
                                                <p class="text-gray-500 text-sm">
                                                    <span class="font-semibold text-gray-700">Payment Method:</span> 
                                                    @if($riwayat->order->metode_bayar == 'bank_transfer')
                                                        Bank Transfer
                                                    @elseif($riwayat->order->metode_bayar == 'qris')
                                                        Q-RIS
                                                    @endif
                                                </p>
                                                <p class="text-gray-500 text-sm">
                                                    <span class="font-semibold text-gray-700">Last Updated:</span> {{ $riwayat->order->updated_at->format('M d, Y H:i') }}
                                                </p>
                                            </div>
                                            <div class="w-1/2 pl-6">
                                                <p class="text-gray-600 font-semibold">Nota:</p>
                                                <p class="text-gray-500 text-sm">{{ $riwayat->order->nota }}</p>
                                            </div>
                                        </div>
                                        @if($riwayat->order->review)
                                            <div class="mt-4 pt-4 border-t">
                                                <h4 class="font-semibold text-gray-800">Your Review</h4>
                                                <div class="star-rating-display flex items-center mt-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span class="star {{ $i <= $riwayat->order->review->rating ? 'filled' : '' }}">★</span>
                                                    @endfor
                                                </div>
                                                <p class="text-gray-600 text-sm mt-2 italic">"{{ $riwayat->order->review->comment }}"</p>
                                            </div>
                                        @endif

                                        <div class="mt-4 flex items-center gap-x-4">
                                            <a href="{{ route('receipt', $riwayat->order->id_pesanan) }}" class="inline-block bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-600 transition">View Receipt</a>

                                            @if($riwayat->order->status == 'Completed')
                                                @if($riwayat->order->review)
                                                    <button @click="
                                                        showModal = true; 
                                                        selectedOrderId = '{{ $riwayat->order->id_pesanan }}';
                                                        existingRating = {{ $riwayat->order->review->rating }};
                                                        existingComment = '{{ addslashes($riwayat->order->review->comment) }}';
                                                    " class="inline-block bg-yellow-500 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-yellow-600 transition">
                                                        Update Review
                                                    </button>
                                                @else
                                                    <button @click="
                                                        showModal = true; 
                                                        selectedOrderId = '{{ $riwayat->order->id_pesanan }}';
                                                        existingRating = 0;
                                                        existingComment = '';
                                                    " class="inline-block bg-green-500 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-green-600 transition">
                                                        Write a Review
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div id="no-orders-message" class="bg-white rounded-xl shadow-lg text-center p-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <h2 class="mt-4 text-xl font-semibold">No Orders Yet</h2>
                        <p class="mt-2 text-gray-600">When you place orders, they will appear here.</p>
                        <a href="/create" class="mt-6 inline-block bg-purple-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-purple-700">Start Creating!</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $riwayatPesanan->links() }}</div>

            <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
            <div @click.away="showModal = false" class="bg-white rounded-xl shadow-2xl p-6 md:p-8 w-full max-w-md" x-init="$watch('showModal', value => { if (!value) { existingRating = 0; existingComment = '' } })">
                <h3 class="text-2xl font-bold text-gray-900 mb-4" x-text="existingRating > 0 ? 'Update Your Review' : 'Write a Review'"></h3>
                <form action="{{ route('reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_pesanan" :value="selectedOrderId">

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Your Rating</label>
                        
                        <div class="flex flex-row-reverse justify-end items-center">
                            <template x-for="star in [5,4,3,2,1]" :key="star">
                                <div>
                                    <input 
                                        type="radio" 
                                        name="rating"
                                        required
                                        class="hidden"
                                        :id="'star' + star"
                                        :value="star"
                                        :checked="star === existingRating">

                                    <label
                                        :for="'star' + star"
                                        @click="existingRating = star"
                                        class="text-3xl cursor-pointer transition-colors"
                                        :class="{ 'text-yellow-400': star <= existingRating, 'text-gray-300': star > existingRating }"
                                        title="star">
                                        ★
                                    </label>
                                </div>
                            </template>
                        </div>
                        </div>

                    <div class="mb-6">
                        <label for="comment" class="block text-gray-700 font-semibold mb-2">Your Review</label>
                        <textarea name="comment" id="comment" rows="4" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tell us what you thought..." x-model="existingComment"></textarea>
                    </div>

                    <div class="flex justify-end gap-x-4">
                        <button type="button" @click="showModal = false" class="bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit" class="bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-purple-700 transition">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-input');
        const sortSelect = document.getElementById('sort-select');
        const ordersContainer = document.getElementById('orders-container');
        const noResultsMessage = document.getElementById('no-results-message');
        
        const orderCards = Array.from(document.querySelectorAll('.order-card'));
        
        if (orderCards.length === 0) return;

        @if (session('success'))
            window.dispatchEvent(new CustomEvent('alert', { 
                detail: { type: 'success', message: "{{ session('success') }}" }
            }));
        @endif

        @if ($errors->any())
            window.dispatchEvent(new CustomEvent('alert', {
                detail: { type: 'error', message: "{{ $errors->first() }}" }
            }));
        @endif

        function updateOrdersDisplay() {
            const searchTerm = searchInput.value.toLowerCase();
            
            let visibleCards = orderCards.filter(card => {
                const content = card.dataset.searchContent || '';
                return content.includes(searchTerm);
            });

            const sortValue = sortSelect.value;
            visibleCards.sort((a, b) => {
                const dateA = parseInt(a.dataset.date, 10);
                const dateB = parseInt(b.dataset.date, 10);
                const idA = parseInt(a.dataset.id, 10);
                const idB = parseInt(b.dataset.id, 10);

                if (sortValue === 'oldest') {
                    return (dateA - dateB) || (idA - idB);
                } else {
                    return (dateB - dateA) || (idB - idA);
                }
            });
            
            ordersContainer.innerHTML = '';
            if (visibleCards.length > 0) {
                ordersContainer.append(...visibleCards);
                noResultsMessage.classList.add('hidden');
            } else {
                noResultsMessage.classList.remove('hidden');
            }
        }

        searchInput.addEventListener('input', updateOrdersDisplay);
        sortSelect.addEventListener('change', updateOrdersDisplay);

        updateOrdersDisplay();
    });
    </script>

</body>
</html>