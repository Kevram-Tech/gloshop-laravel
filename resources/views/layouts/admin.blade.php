<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mishop Admin')</title>
    
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
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-56 bg-gray-800 border-r-2 border-gray-900 flex flex-col">
            <!-- Logo -->
            <div class="px-4 py-3 border-b-2 border-gray-900">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gray-900 border border-gray-700 flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-gray-300 text-xs"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold text-gray-100 uppercase tracking-tight">Mishop</h1>
                        <p class="text-xs text-gray-400 font-mono">ADMIN</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-2 py-2 space-y-0.5 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-home w-4 mr-2"></i>
                    <span>DASHBOARD</span>
                </a>
                
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.products.*') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-box w-4 mr-2"></i>
                    <span>PRODUITS</span>
                </a>
                
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tags w-4 mr-2"></i>
                    <span>CATÉGORIES</span>
                </a>
                
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.orders.*') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-shopping-cart w-4 mr-2"></i>
                    <span>COMMANDES</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-users w-4 mr-2"></i>
                    <span>UTILISATEURS</span>
                </a>
                
                <a href="{{ route('admin.promotions.index') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.promotions.*') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-percent w-4 mr-2"></i>
                    <span>PROMOTIONS</span>
                </a>
                
                <a href="{{ route('admin.promo-codes.index') }}" class="flex items-center px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.promo-codes.*') ? 'bg-gray-900 text-white border-l-2 border-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-ticket-alt w-4 mr-2"></i>
                    <span>CODES PROMOS</span>
                </a>
            </nav>
            
            <!-- Footer -->
            <div class="p-2 border-t-2 border-gray-900">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-sign-out-alt w-4 mr-2"></i>
                        <span>LOGOUT</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-white">
            <!-- Header -->
            <header class="bg-white border-b-2 border-gray-300">
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 uppercase tracking-tight">@yield('page-title', 'DASHBOARD')</h2>
                        <p class="text-xs text-gray-500 font-mono mt-0.5">@yield('page-subtitle', 'OVERVIEW')</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-gray-900">{{ Auth::user()->name ?? 'ADMIN' }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <div class="w-8 h-8 bg-gray-800 border-2 border-gray-900 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="mb-4 bg-gray-100 border-l-4 border-gray-600 p-3 border border-gray-300">
                        <div class="flex items-center">
                            <span class="text-gray-600 mr-2 font-mono">[OK]</span>
                            <p class="text-xs font-medium text-gray-900">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

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

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
