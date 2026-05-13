<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Barbershop POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">

    <div class="bg-gray-800 text-white p-8 rounded-2xl shadow-lg w-full max-w-md">
        
        <h2 class="text-2xl font-bold text-center mb-6">💈 Barbershop POS</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" 
                    class="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" 
                    class="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none">
            </div>

            <!-- Remember -->
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" class="mr-2">
                <span>Remember me</span>
            </div>

            <!-- Button -->
            <button type="submit" 
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 rounded">
                Login
            </button>

            <!-- Error -->
            @if ($errors->any())
                <div class="mt-4 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif
        </form>
    </div>

</body>
</html>