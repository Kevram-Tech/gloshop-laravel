<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - GloShop')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#8b5cf6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="p-6 border-b border-gray-800">
                <h1 class="text-2xl font-bold">GloShop Admin</h1>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.products.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-box w-5"></i>
                    <span>Produits</span>
                </a>
                
                <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-tags w-5"></i>
                    <span>Catégories</span>
                </a>
                
                <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span>Commandes</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-users w-5"></i>
                    <span>Utilisateurs</span>
                </a>
            </nav>
            
            <div class="p-4 border-t border-gray-800">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 text-red-400">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">{{ Auth::user()->name }}</span>
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

