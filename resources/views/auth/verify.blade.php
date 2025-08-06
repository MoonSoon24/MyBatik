<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Batik - Verification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <header class="bg-white shadow-sm">
        <div class="container mx-auto flex justify-between items-center p-4 md:p-6">
            <a href="/" class="font-dancing text-4xl font-bold">myBatik</a>
        </div>
    </header>

    <!-- main section -->
    <main class="flex flex-col items-center justify-center min-h-screen bg-gray-50 py-12 px-4 -mt-20">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 w-full max-w-md text-center">
            <h2 class="text-3xl font-bold text-gray-900">Verify Your Email Address</h2>
            <p class="text-gray-500 mt-4">Thanks for signing up! Before continuing, Press the button below to send a verification link to your email. If you didn't receive the email, we will gladly send you another.</p>

            @if (session('message'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div class="mt-6">
                    <button type="submit" class="w-full bg-black text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-all duration-300 text-lg">
                        Send Verification Email
                    </button>
                </div>
            </form>

            <div class="flex items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="w-full text-center text-gray-800 font-bold py-3 px-6 rounded-lg transition duration-300 ease-in-out">
                    Home
                </a>
            </div>
        </div>
    </main>

</body>
</html>
