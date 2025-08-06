<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik</title>
    
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
        
        .star-rating-display .star {
            font-size: 1.5rem;
            color: #d1d5db;
        }
        .star-rating-display .star.filled {
            color: #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <x-header />
    
    <main class="flex flex-col items-center justify-center py-16 sm:py-24 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 md:p-12 w-full max-w-6xl">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-10 sm:mb-12">How It Works</h2>
            <div class="flex flex-col md:flex-row justify-around items-center gap-10 md:gap-8">
                <div class="text-center flex flex-col items-center">
                    <img src="{{ asset('images/home_1.png') }}" alt="A blue shirt representing design creation" class="h-48 sm:h-56 md:h-64 w-auto mb-4" onerror="this.onerror=null;this.src='https://placehold.co/256x256';">
                    <p class="font-semibold text-lg">1. Create your design!</p>
                </div>
                
                <div class="text-4xl text-blue-500 font-light transform rotate-90 md:rotate-0">&rarr;</div>
                
                <div class="text-center flex flex-col items-center">
                    <img src="{{ asset('images/home_2.png') }}" alt="A hand choosing a size" class="h-48 sm:h-56 md:h-64 w-auto mb-4" onerror="this.onerror=null;this.src='https://placehold.co/256x256';">
                    <p class="font-semibold text-lg">2. Choose your size</p>
                </div>

                <div class="text-4xl text-blue-500 font-light transform rotate-90 md:rotate-0">&rarr;</div>
                
                <div class="text-center flex flex-col items-center">
                    <img src="{{ asset('images/home_3.png') }}" alt="A person working on a sewing pattern" class="h-48 sm:h-56 md:h-64 w-auto mb-4" onerror="this.onerror=null;this.src='https://placehold.co/256x256';">
                    <p class="font-semibold text-lg">3. Let us do the work!</p>
                </div>
            </div>
        </div>
    </main>

    <section id="reviews" class="py-16 sm:py-24 bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12">What Our Customers Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @forelse ($reviews as $review)
                    <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col">
                        <div class="flex-grow">
                            <p class="text-gray-600 italic mb-4">"{{ $review->comment }}"</p>
                        </div>
                        <div>
                            <div class="star-rating-display flex items-center mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>
                            <p class="font-bold text-gray-900">
                                @php
                                    $name = $review->user->name;
                                    $length = strlen($name);
                                    $displayName = substr($name, 0, 2) . str_repeat('*', max(0, $length - 2));
                                    echo htmlspecialchars($displayName);
                                @endphp
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center text-gray-500">
                        <p>No reviews yet. Be the first to share your experience!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="top-gallery" class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12">Top Gallery Posts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @forelse ($topGalleryPosts as $post)
                    <a href="/gallery" class="block group">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden h-full flex flex-col transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-1">
                            <div class="h-56 sm:h-64 w-full bg-gray-50">
                                <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ htmlspecialchars($post->title) }}" class="w-full h-full object-contain" onerror="this.onerror=null;this.src='https://placehold.co/400x400/eeeeee/222222?text=Batik';">
                            </div>
                            
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2 truncate" title="{{ htmlspecialchars($post->title) }}">{{ $post->title }}</h3>
                                <div class="flex justify-between items-center text-gray-500 mt-auto pt-2">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $post->likes_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.08-3.239A8.995 8.995 0 011 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM4.416 12.584A7 7 0 0010 15c3.309 0 6-2.239 6-5s-2.691-5-6-5-6 2.239-6 5c0 1.282.47 2.45 1.253 3.388L4.416 12.584z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $post->comments_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center text-gray-500 py-10">
                        <p>No popular designs to show yet. Check back soon!</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-12">
                <a href="/gallery" class="bg-blue-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg hover:bg-blue-600 transition-all duration-300 text-lg">View Full Gallery</a>
            </div>
        </div>
    </section>

    <section id="about" class="py-16 sm:py-24 bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold mb-4">About Us</h2>
            <p class="max-w-3xl mx-auto text-gray-600 leading-relaxed">
                Welcome to myBatik, where ancient tradition meets modern expression. We are passionate about preserving the rich heritage of Indonesian Batik, a UNESCO Intangible Cultural Heritage of Humanity. Our mission is to bring this beautiful art form to the world, allowing you to create personalized apparel that tells a story.
            </p>
        </div>
    </section>

    <section id="faq" class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto space-y-8">
                <div>
                    <h3 class="font-semibold text-lg mb-2">How do I create my own Batik design?</h3>
                    <p class="text-gray-600">Simply click the "Create" button! Our design tool allows you to choose from traditional patterns, upload your own motifs, and select colors.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-2">What materials do you use?</h3>
                    <p class="text-gray-600">We use high-quality, natural fabrics like silk, cotton and mori, which are ideal for the Batik process.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-2">How long does it take to receive my order?</h3>
                    <p class="text-gray-600">Because each piece is handmade, the process takes time. Please allow 3-7 days for creation.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="fixed bottom-5 left-1/2 -translate-x-1/2 z-40">
        <a href="/create" class="bg-black text-white font-semibold py-3 px-8 sm:py-4 sm:px-12 rounded-full shadow-lg hover:bg-gray-800 transition-all duration-300 text-base sm:text-lg">Create</a>
    </div>

    <footer id="contact" class="bg-gray-800 text-white pt-16 pb-24 sm:pt-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold mb-4">Contact Us</h2>
            <p class="mb-4 text-gray-300">Have a question? We'd love to hear from you.</p>
            <div class="space-y-1">
                <p class="font-semibold">mybatik@mybatik.com</p>
                <p>+62 21 1234 5678</p>
                <p>Jalan Batik No. 10, Yogyakarta, Indonesia</p>
            </div>
            <div class="flex justify-center space-x-6 mt-6">
                <a href="#" class="hover:text-gray-300 transition-colors">Facebook</a>
                <a href="#" class="hover:text-gray-300 transition-colors">Instagram</a>
            </div>
            <div class="mt-10 border-t border-gray-700 pt-8">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} myBatik. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
    
</body>
</html>