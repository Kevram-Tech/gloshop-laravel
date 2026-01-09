<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - GloShop Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', monospace;
            font-size: 13px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white border-2 border-gray-300 p-8 w-full max-w-md">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-800 border-2 border-gray-900 mb-4">
                    <i class="fas fa-shopping-bag text-white text-2xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900 uppercase tracking-tight">GloShop</h1>
                <p class="text-xs text-gray-500 font-mono mt-1">ADMIN PANEL</p>
            </div>

            @if($errors->any())
                <div class="mb-4 bg-gray-100 border-l-4 border-gray-800 p-3 border border-gray-300">
                    <div class="flex items-center">
                        <span class="text-gray-800 mr-2 font-mono">[ERR]</span>
                        <p class="text-xs font-medium text-gray-900">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 border-2 border-gray-300 focus:border-gray-800 focus:outline-none text-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border-2 border-gray-300 focus:border-gray-800 focus:outline-none text-sm">
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-xs text-gray-700">Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gray-800 text-white py-2 text-xs font-bold uppercase hover:bg-gray-900 border-2 border-gray-900">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</body>
</html>

