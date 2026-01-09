<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion Admin - GloShop</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Monospace for engineering style -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', 'JetBrains Mono', monospace;
        }
        body {
            font-size: 13px;
        }
        code, .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white border-2 border-gray-300 p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div class="w-10 h-10 bg-gray-800 border-2 border-gray-900 flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-gray-300 text-sm"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 uppercase tracking-tight">GloShop</h1>
                    <p class="text-xs text-gray-500 font-mono">ADMIN</p>
                </div>
            </div>
            <p class="text-xs text-gray-600 font-mono">[AUTH] CONNEXION REQUISE</p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-gray-100 border-l-4 border-gray-800 p-3 border border-gray-300">
                <div class="flex items-center">
                    <span class="text-gray-800 mr-2 font-mono">[ERR]</span>
                    <p class="text-xs font-medium text-gray-900">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-gray-100 border-l-4 border-gray-800 p-3 border border-gray-300">
                <div class="flex items-start">
                    <span class="text-gray-800 mr-2 font-mono">[ERR]</span>
                    <div>
                        <p class="text-xs font-medium text-gray-900 mb-1">Erreurs de validation:</p>
                        <ul class="list-disc list-inside text-xs text-gray-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-xs font-bold text-gray-900 mb-1 uppercase tracking-tight">
                    <i class="fas fa-envelope w-4 mr-2"></i>EMAIL
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 border-2 border-gray-300 text-xs focus:outline-none focus:border-gray-800 bg-white">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-xs font-bold text-gray-900 mb-1 uppercase tracking-tight">
                    <i class="fas fa-lock w-4 mr-2"></i>MOT DE PASSE
                </label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border-2 border-gray-300 text-xs focus:outline-none focus:border-gray-800 bg-white">
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-xs text-gray-700 font-mono">Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold py-3 px-4 border-2 border-gray-900 uppercase tracking-tight">
                <i class="fas fa-sign-in-alt w-4 mr-2"></i>CONNEXION
            </button>
        </form>
    </div>
</body>
</html>
