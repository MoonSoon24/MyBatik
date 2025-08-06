<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik - Receipt</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                        'dancing': ['Dancing Script', 'cursive'],
                    },
                    colors: {
                        'batik-cyan': '#06b6d4',
                        'batik-light-green': '#ecfdf5',
                        'batik-green': '#10b981',
                        'batik-light-blue': '#f0f9ff',
                    }
                }
            }
        }
    </script>
    <style>
        .font-dancing {
            font-family: 'Dancing Script', cursive;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

    <x-header />
    <main class="container mx-auto py-8 md:py-12 px-4">

        @if($order->status == 'Pending')
            <div class="bg-batik-light-green border border-batik-green/50 rounded-xl p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 mb-8">
                <div class="flex-shrink-0">
                    <svg class="w-16 h-16 text-batik-green" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Order Successfully Placed!</h2>
                    <p class="text-gray-700 mt-1">Thank you for your order. Your order number is <span class="font-bold text-batik-cyan">#{{ $order->id_pesanan }}</span></p>
                    <p class="text-gray-600 mt-1">Your order will be processed once payment is confirmed.</p>
                </div>
            </div>

        @elseif($order->status == 'In Progress')
             <div class="bg-blue-50 border border-blue-400/50 rounded-xl p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 mb-8">
                <div class="flex-shrink-0">
                    <svg class="w-16 h-16 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Your Order is In Progress</h2>
                    <p class="text-gray-700 mt-1">Our artisans are crafting your custom batik!<span class="font-bold text-blue-600"></span></p>
                    @if($order->tanggal_estimasi)
                        <p class="text-gray-600 mt-1">Estimated Completion Date: <span class="font-semibold">{{ $order->tanggal_estimasi->format('d F, Y') }}</span></p>
                        <p class="text-gray-600 mt-1">If you have any questions, please contact our support.</p>
                    @endif
                </div>
            </div>

        @elseif($order->status == 'Hold')
             <div class="bg-yellow-50 border border-yellow-400/50 rounded-xl p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 mb-8">
                <div class="flex-shrink-0">
                    <svg class="w-16 h-16 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Action Required: Order on Hold</h2>
                    <p class="text-gray-700 mt-1">There might be an issue with your payment proof. Please review and re-upload if necessary.</p>
                    <p class="text-gray-600 mt-1">Your order number is <span class="font-bold text-yellow-600">#{{ $order->id_pesanan }}</span></p>
                </div>
            </div>
            
        @elseif($order->status == 'Ready')
             <div class="bg-batik-light-green border border-batik-green/50 rounded-xl p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 mb-8">
                <div class="flex-shrink-0">
                    <svg class="w-16 h-16 text-batik-green" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Your Order is Ready for Pickup!</h2>
                    <p class="text-gray-700 mt-1">Please visit our store to collect your custom batik. Thank you for your patience!</p>
                    <p class="text-gray-600 mt-1">Your order number is <span class="font-bold text-batik-cyan">#{{ $order->id_pesanan }}</span></p>
                    <p class="text-gray-600 mt-1">If you have any questions, please contact our support.</p>
                </div>
            </div>
            
        @elseif($order->status == 'Completed')
             <div class="bg-teal-50 border border-teal-500/50 rounded-xl p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 mb-8">
                <div class="flex-shrink-0">
                    <svg class="w-16 h-16 text-batik-green" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Order Completed</h2>
                    <p class="text-gray-700 mt-1">We hope you enjoy your unique batik creation. Thank you for your business!</p>
                    <p class="text-gray-600 mt-1">Order <span class="font-bold text-teal-700">#{{ $order->id_pesanan }}</span></p>
                </div>
            </div>

        @elseif($order->status == 'Cancelled')
             <div class="bg-red-50 border border-red-400/50 rounded-xl p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 mb-8">
                <div class="flex-shrink-0">
                    <svg class="w-16 h-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Order cancelled</h2>
                    <p class="text-gray-700 mt-1">This order has been cancelled. No further action is required.</p>
                    <p class="text-gray-600 mt-1">If you have any questions, please contact our support.</p>
                </div>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-2/3 bg-white p-6 md:p-8 rounded-xl shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b gap-2 sm:gap-0">
                    <div>
                        <h3 class="text-2xl font-bold">Order Receipt</h3>
                        <p class="text-gray-500">{{ $order->created_at->format('d F, Y') }}</p>
                    </div>
                    <p class="text-gray-600 font-semibold">Order #{{ $order->id_pesanan }}</p>
                </div>

                <div class="py-6">
                    <h4 class="font-bold text-lg mb-4">Item Details</h4>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-20 h-20 flex-shrink-0">
                           <img src="{{ asset('storage/' . $order->desain) ? asset('storage/' . $order->desain) : 'https://placehold.co/1088x544' }}" alt="Batik Design" class="w-full h-full object-contain rounded-md">
                        </div>
                        <div class="flex-grow">
                            <h5 class="font-semibold text-lg">Custom Batik Shirt</h5>
                            <p class="text-gray-600 text-sm">Size: {{ $order->ukuran }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 py-4 border-t">
                    <div class="flex justify-between text-gray-700">
                        <span>Product Price</span>
                        <span class="font-medium">Rp. 300.000 x {{ $order->jumlah }}</span>
                    </div>

                    @if($order->fabric_type === 'kain mori')
                        <div class="flex justify-between text-gray-700">
                            <span>Fabric Cost (Kain Mori)</span>
                            <span class="font-medium">+ Rp. 100.000 x {{ $order->jumlah }}</span>
                        </div>
                    @elseif($order->fabric_type === 'kain sutera')
                        <div class="flex justify-between text-gray-700">
                            <span>Fabric Cost (Kain Sutera)</span>
                            <span class="font-medium">+ Rp. 300.000 x {{ $order->jumlah }}</span>
                        </div>
                    @endif

                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-batik-green font-semibold">
                        <span>Discount ({{ $order->promo_code }})</span>
                        <span>- Rp. {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between font-bold text-xl py-4 border-t">
                    <span>Total</span>
                    <span>Rp. {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                <div class="pt-6 border-t">
                     <h4 class="font-bold text-lg mb-2">Customer Information</h4>
                     <p class="font-medium">{{ $order->nama }}</p>
                     <p class="text-gray-600">{{ $order->email }}</p>
                     <p class="text-gray-600">{{ $order->no_telepon }}</p>
                </div>

                <div class="pt-8 mt-8 border-t text-center text-gray-500">
                    <p>Thank you for using our Batik Designer service</p>
                </div>
            </div>

            <div class="w-full lg:w-1/3 space-y-8">
                <div class="bg-white p-6 rounded-xl shadow-md">

                    @if(in_array($order->status, ['Pending', 'Hold']))

                        @if($order->status == 'Hold')
                            <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 mb-5 rounded-r-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 15a1 1 0 110-2 1 1 0 010 2zm-1-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Action required. There may be an issue with your previous submission. Please upload a new proof.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h3 class="font-bold text-lg mb-2">Upload Payment Proof</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            @if($order->bukti_pembayaran)
                                You can update your submitted proof if needed.
                            @else
                                Please upload a photo or screenshot of your payment transfer.
                            @endif
                        </p>
                        
                        <div x-data="{ imageUrl: '{{ $order->bukti_pembayaran ? asset('storage/' . $order->bukti_pembayaran) : null }}' }">
                            <form action="{{ route('order.upload.proof', $order) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <template x-if="imageUrl">
                                    <div class="mb-4">
                                        <img :src="imageUrl" class="w-full rounded-lg object-contain max-h-64 border" alt="Image Preview">
                                    </div>
                                </template>

                                <label for="bukti_pembayaran" class="cursor-pointer block w-full border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-batik-cyan transition">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    <span class="mt-2 block text-sm font-medium text-gray-600">
                                        Click to upload a new image
                                    </span>
                                    <input id="bukti_pembayaran" name="bukti_pembayaran" type="file" class="sr-only" accept="image/*" @change="imageUrl = URL.createObjectURL($event.target.files[0])">
                                </label>
                                
                                @error('bukti_pembayaran')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="w-full text-center bg-batik-cyan text-white font-bold py-3 px-4 rounded-lg mt-6 hover:bg-cyan-600 transition-all duration-300">
                                    Save Payment Proof
                                </button>
                            </form>
                        </div>

                    @elseif(in_array($order->status, ['In Progress', 'Ready', 'Completed']))
                        <h3 class="font-bold text-lg mb-4">Payment Proof Confirmed</h3>
                        <p class="text-sm text-gray-600 mb-4">Thank you! Your payment has been verified and your order is being processed.</p>
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . $order->bukti_pembayaran) }}" alt="Payment Proof" class="w-full rounded-lg object-contain border">
                        </div>

                    @elseif($order->status == 'Cancelled')
                        <h3 class="font-bold text-lg mb-4">Order cancelled</h3>
                        <div class="border-l-4 border-red-400 bg-red-50 p-4 rounded-r-lg">
                            <p class="text-sm text-red-700">This order has been cancelled. No payment is required.</p>
                        </div>
                    
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-bold text-lg mb-4">Need Help?</h3>
                    <div class="space-y-3">
                        <a href="{{ route('home') }}#faq" class="flex items-center gap-3 text-gray-700 hover:text-batik-cyan transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
                            <span>FAQ</span>
                        </a>
                        <a href="{{ route('home') }}#contact" class="flex items-center gap-3 text-gray-700 hover:text-batik-cyan transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                            <span>Contact Us</span>
                        </a>
                    </div>
                </div>
                <div class="bg-batik-light-blue p-6 rounded-xl shadow-md">
                    <h3 class="font-bold text-lg mb-5">What's Next</h3>
                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-batik-cyan text-white font-bold text-sm rounded-full flex items-center justify-center">1</div>
                            <p class="text-gray-700">We'll verify your payment within 24 hours.</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-batik-cyan text-white font-bold text-sm rounded-full flex items-center justify-center">2</div>
                            <p class="text-gray-700">Your custom batik design will be crafted by our artisan.</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-batik-cyan text-white font-bold text-sm rounded-full flex items-center justify-center">3</div>
                            <p class="text-gray-700">We'll notify you when your batik is ready for pick-up.</p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-batik-cyan text-white font-bold text-sm rounded-full flex items-center justify-center">4</div>
                            <p class="text-gray-700">Enjoy your unique batik creation!</p>
                        </div>
                    </div>
                    <a href="{{ route('home') }}" class="block w-full text-center bg-batik-cyan text-white font-bold py-3 px-4 rounded-lg mt-8 hover:bg-cyan-600 transition-all duration-300">
                        Return to Home
                    </a>
                </div>
            </div>

        </div>
    </main>

</body>
</html>