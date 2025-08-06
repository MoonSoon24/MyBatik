<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik - Profile</title>
    
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
        .font-dancing {
            font-family: 'Dancing Script', cursive;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 pb-28 style="padding-bottom: 0px;">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center p-4 md:p-6">
            <div class="flex items-center gap-x-12">
                <a href="/" class="font-dancing text-3xl md:text-4xl font-bold text-gray-900">myBatik</a>
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="font-semibold text-gray-700 hover:text-black transition">Home</a>
                </nav>
            </div>
             
            <div class="flex items-center space-x-3">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" id="logout-link" class="block px-4 py-2 text-sm font-semibold text-red-600 hover:text-red-800 transition">Logout</a>
                </form>
            </div>
        </div>
    </header>

    <!-- main -->
    <main class="flex flex-col items-center justify-center bg-gray-50 py-12 px-4">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 w-full max-w-2xl" x-data="{ passwordOpen: false }">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">My Profile</h2>
                <p class="text-gray-500 mt-2">Manage your account details and password.</p>
            </div>
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!Auth::user()->hasVerifiedEmail())
                <div class="mt-2 text-sm text-gray-600">
                    Your email address is unverified.
                    <form method="POST" action="{{ route('verification.send') }}" class="inline">
                        @csrf
                        <button type="submit" class="underline text-blue-600 hover:text-blue-800 font-medium">Click here to send the verification email.</button>
                    </form>
                </div>
            @else
                <div class="mt-2 text-sm text-green-600">
                    Your email address is verified.
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" class="mt-6">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <div class="mt-1">
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                autocomplete="name" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                                value="{{ old('name', Auth::user()->name) }}"
                                required 
                                minlength="3"
                                title="Full name must be at least 3 characters long.">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" value="{{ old('email', Auth::user()->email) }}" >
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button @click="passwordOpen = !passwordOpen" type="button" class="text-sm font-semibold text-blue-600 hover:text-blue-800 focus:outline-none">
                        <span x-show="!passwordOpen">Change Password</span>
                        <span x-show="passwordOpen">Cancel</span>
                    </button>
                </div>

                <div x-show="passwordOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-6 pt-8 mt-8 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800">Change Password</h3>
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <div class="mt-1">
                            <input id="current_password" name="current_password" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                     <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <div class="mt-1">
                            <input id="new_password" name="new_password" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                     <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <div class="mt-1">
                            <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                <div class="pt-8 mt-8 border-t border-gray-200">
                    <button type="submit" class="w-full bg-black text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-all duration-300 text-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <x-logout-modal />
    
</body>
</html>
