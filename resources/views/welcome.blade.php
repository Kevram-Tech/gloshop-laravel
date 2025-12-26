<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Gloshop - L'application mobile de mode féminine">

        <title>Gloshop - Application Mobile</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#fdf2f8',
                                100: '#fce7f3',
                                200: '#fbcfe8',
                                300: '#f9a8d4',
                                400: '#f472b6',
                                500: '#ec4899',
                                600: '#db2777',
                                700: '#be185d',
                                800: '#9d174d',
                                900: '#831843',
                            }
                        },
                        fontFamily: {
                            'sans': ['Figtree', 'system-ui', '-apple-system', 'sans-serif'],
                        },
                        animation: {
                            'float': 'float 3s ease-in-out infinite',
                            'pulse-slow': 'pulse 3s ease-in-out infinite',
                        }
                    }
                }
            }
        </script>

        <style>
            .hero-gradient {
                background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #fbcfe8 100%);
            }
            .app-preview {
                transform: perspective(1000px) rotateY(-15deg) rotateX(5deg);
                box-shadow: 35px 35px 70px rgba(236, 72, 153, 0.2);
            }
            .app-preview:hover {
                transform: perspective(1000px) rotateY(-10deg) rotateX(5deg);
                transition: transform 0.5s ease;
            }
            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-20px);
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Navigation -->
        <nav class="bg-white/90 backdrop-blur-sm fixed w-full z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-white font-bold text-xl">G</span>
                            </div>
                            <span class="ml-3 text-2xl font-bold text-gray-900">Glo<span class="text-primary-600">shop</span></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/home') }}" class="text-gray-600 hover:text-primary-600 font-medium">Mon compte</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-600 font-medium">Connexion</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300">S'inscrire</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="hero-gradient pt-24 pb-20 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="animate-float">
                        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                            La mode féminine
                            <span class="text-primary-600 block">dans votre poche</span>
                        </h1>
                        <p class="text-xl text-gray-600 mb-8">
                            Découvrez l'application Gloshop pour shopper vos articles préférés depuis votre smartphone.
                            Mode, accessoires et beauté à portée de main.
                        </p>
                        
                        <!-- Store Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-8">
                            <a href="#" class="bg-gray-900 hover:bg-black text-white rounded-xl px-6 py-4 flex items-center justify-center gap-3 transition duration-300">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.05 20.28c-.98.95-2.05.86-3.08.38-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.38C2.79 15.36 3.74 7.8 9.08 7.45c1.08.07 1.87.74 2.76.86 1.14.15 1.73-.68 2.96-.68 1.21 0 1.91.68 2.92.74 1.36.07 2.29-.93 3.22-1.66-1.43-2.07-3.44-2.3-4.78-2.33-1.83-.03-3.44 1.04-4.33 1.04-.9 0-2.26-1.01-3.81-.96-1.96.03-3.76 1.14-4.76 2.91-2.04 3.54-.52 8.79 1.46 11.67 1.03 1.48 2.26 3.15 3.87 3.09 1.53-.07 2.11-.99 3.89-.99 1.76 0 2.31.99 3.89.95 1.61-.03 2.67-1.51 3.68-3.01 1.16-1.69 1.64-3.33 1.67-3.41-.03-.01-3.19-1.22-3.22-4.85-.03-3.03 2.47-4.48 2.58-4.56-1.42-2.07-3.59-2.3-4.35-2.33-1.98-.16-3.64 1.11-4.58 1.11zM15.53 3.75c.8-1 1.34-2.38 1.19-3.75-1.16.05-2.56.78-3.39 1.76-.75.86-1.4 2.24-1.22 3.56 1.29.1 2.6-.66 3.42-1.57z"/>
                                </svg>
                                <div class="text-left">
                                    <div class="text-xs">Téléchargez sur</div>
                                    <div class="text-lg font-semibold">App Store</div>
                                </div>
                            </a>
                            
                            <a href="#" class="bg-gray-900 hover:bg-black text-white rounded-xl px-6 py-4 flex items-center justify-center gap-3 transition duration-300">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3.609 1.814C4.03 1.375 4.822 1 5.728 1c.272 0 .518.02.764.061.245.041.49.102.736.183.748.244 1.415.667 1.883 1.231.45.543.713 1.2.713 1.87 0 .306-.04.61-.102.917-.02.103-.04.184-.061.265-.102.367-.286.712-.53 1.039-.653.814-1.485 1.354-2.242 1.506-.163.02-.347.041-.53.041-.55 0-1.058-.163-1.506-.47-.49-.347-.856-.835-1.079-1.415-.204-.53-.306-1.1-.306-1.69 0-1.079.367-2.037 1.058-2.75zM8.649 24c1.485 0 2.344-.795 3.55-.795 1.241 0 1.873.754 3.528.754 1.424 0 2.547-.692 3.447-1.935 1.058-1.527 1.506-3.038 1.527-3.12-.082-.04-2.995-1.16-3.018-4.624-.02-2.915 2.383-4.298 2.486-4.38-1.363-2.006-3.488-2.108-4.237-2.148-1.873-.143-3.65 1.058-4.585 1.058-.938 0-2.138-1.037-3.57-1.037C6.44 6.115 4 7.82 4 11.504c0 2.383.937 4.913 2.108 6.544 1.058 1.485 2.301 2.669 3.65 2.669.774 0 1.363-.245 1.935-.47.407-.142.815-.265 1.282-.265.489 0 .998.123 1.424.265.52.183 1.058.408 1.69.408 1.14 0 2.148-.754 3.018-2.005.143-.204.265-.408.387-.612-1.424-.53-2.383-1.832-2.383-3.345 0-1.873 1.526-3.406 3.65-3.406 1.465 0 2.383.632 3.08 1.527-.816.49-1.424 1.24-1.751 2.15-.184.51-.245 1.058-.245 1.628 0 1.14.387 2.22 1.058 3.12-.49.286-.998.53-1.526.734-.49.184-1 .326-1.506.326-1.139 0-2.158-.795-3.345-.795-1.24 0-2.219.795-3.57.795z"/>
                                </svg>
                                <div class="text-left">
                                    <div class="text-xs">Disponible sur</div>
                                    <div class="text-lg font-semibold">Google Play</div>
                                </div>
                            </a>
                        </div>

                        <!-- Ratings -->
                        <div class="flex items-center gap-2">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <span class="text-gray-600 font-medium">4.8/5 • 10K+ téléchargements</span>
                        </div>
                    </div>

                    <!-- App Preview -->
                    <div class="relative flex justify-center lg:justify-end">
                        <div class="app-preview bg-white rounded-[40px] p-4 w-72 h-[600px] relative shadow-2xl">
                            <!-- Phone frame -->
                            <div class="absolute top-0 left-0 w-full h-full rounded-[40px] border-[10px] border-gray-900 pointer-events-none"></div>
                            <!-- Notch -->
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-32 h-6 bg-gray-900 rounded-b-2xl"></div>
                            <!-- App screen -->
                            <div class="h-full rounded-[32px] overflow-hidden bg-gradient-to-b from-primary-50 to-white">
                                <!-- App header -->
                                <div class="bg-primary-500 text-white p-4">
                                    <div class="flex justify-between items-center">
                                        <div class="font-bold text-lg">Glo<span class="text-primary-200">shop</span></div>
                                        <div class="flex gap-2">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm opacity-90">Bienvenue Sarah !</div>
                                </div>
                                
                                <!-- App content -->
                                <div class="p-4">
                                    <!-- Product card -->
                                    <div class="bg-white rounded-xl p-3 shadow-md mb-3">
                                        <div class="flex gap-3">
                                            <div class="w-16 h-16 bg-gradient-to-r from-pink-200 to-primary-200 rounded-lg"></div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">Robe d'été</div>
                                                <div class="text-sm text-gray-600">Collection printemps</div>
                                                <div class="flex justify-between items-center mt-2">
                                                    <div class="text-primary-600 font-bold">59,99 FCFA</div>
                                                    <button class="bg-primary-100 text-primary-600 text-xs px-3 py-1 rounded-full">+ Panier</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Navigation -->
                                    <div class="grid grid-cols-4 gap-2 mt-4">
                                        <div class="text-center">
                                            <div class="bg-primary-100 w-10 h-10 rounded-full mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                </svg>
                                            </div>
                                            <div class="text-xs mt-1">Accueil</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="bg-gray-100 w-10 h-10 rounded-full mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="text-xs mt-1">Recherche</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="bg-gray-100 w-10 h-10 rounded-full mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="text-xs mt-1">Panier</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="bg-gray-100 w-10 h-10 rounded-full mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div class="text-xs mt-1">Profil</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Pourquoi choisir l'app Gloshop ?</h2>
                    <p class="text-xl text-gray-600">Une expérience shopping mobile optimisée</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-primary-50 rounded-2xl p-8">
                        <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Commandes en 1 clic</h3>
                        <p class="text-gray-600">Achetez vos articles favoris en quelques secondes grâce à notre interface intuitive.</p>
                    </div>

                    <div class="bg-primary-50 rounded-2xl p-8">
                        <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Notifications exclusives</h3>
                        <p class="text-gray-600">Soyez la première informée des nouvelles collections et promotions flash.</p>
                    </div>

                    <div class="bg-primary-50 rounded-2xl p-8">
                        <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Assistance 24/7</h3>
                        <p class="text-gray-600">Notre service client est disponible directement dans l'application.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- App Screenshots -->
        <div class="py-20 bg-gradient-to-b from-white to-primary-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Découvrez l'application</h2>
                    <p class="text-xl text-gray-600">Une interface pensée pour votre confort de shopping</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex flex-col items-center">
                        <div class="w-64 h-[500px] bg-white rounded-[32px] shadow-xl p-2 mb-6">
                            <div class="h-full rounded-[28px] bg-gradient-to-b from-gray-50 to-white overflow-hidden">
                                <!-- App screenshot 1 -->
                                <div class="bg-primary-500 text-white p-4">
                                    <div class="font-bold text-center">Catalogue</div>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-gradient-to-r from-pink-200 to-primary-200 h-32 rounded-lg"></div>
                                        <div class="bg-gradient-to-r from-purple-200 to-pink-200 h-32 rounded-lg"></div>
                                        <div class="bg-gradient-to-r from-blue-200 to-cyan-200 h-32 rounded-lg"></div>
                                        <div class="bg-gradient-to-r from-green-200 to-emerald-200 h-32 rounded-lg"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Catalogue interactif</h3>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="w-64 h-[500px] bg-white rounded-[32px] shadow-xl p-2 mb-6">
                            <div class="h-full rounded-[28px] bg-gradient-to-b from-gray-50 to-white overflow-hidden">
                                <!-- App screenshot 2 -->
                                <div class="bg-primary-500 text-white p-4">
                                    <div class="font-bold text-center">Produit</div>
                                </div>
                                <div class="p-4">
                                    <div class="bg-gradient-to-r from-pink-200 to-primary-200 h-48 rounded-lg mb-4"></div>
                                    <div class="space-y-2">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                                    </div>
                                    <button class="w-full mt-6 bg-primary-500 text-white py-3 rounded-lg font-semibold">Ajouter au panier</button>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Fiches produits détaillées</h3>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="w-64 h-[500px] bg-white rounded-[32px] shadow-xl p-2 mb-6">
                            <div class="h-full rounded-[28px] bg-gradient-to-b from-gray-50 to-white overflow-hidden">
                                <!-- App screenshot 3 -->
                                <div class="bg-primary-500 text-white p-4">
                                    <div class="font-bold text-center">Panier</div>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-4">
                                        <div class="flex gap-3">
                                            <div class="w-16 h-16 bg-gradient-to-r from-pink-200 to-primary-200 rounded-lg"></div>
                                            <div class="flex-1">
                                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-16 h-16 bg-gradient-to-r from-purple-200 to-pink-200 rounded-lg"></div>
                                            <div class="flex-1">
                                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 border-t pt-4">
                                        <div class="flex justify-between mb-2">
                                            <span>Total</span>
                                            <span class="font-bold">129,98 FCFA</span>
                                        </div>
                                        <button class="w-full bg-primary-500 text-white py-3 rounded-lg font-semibold">Commander</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Paiement sécurisé</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-primary-600 py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold text-white mb-6">Téléchargez Gloshop maintenant</h2>
                <p class="text-xl text-primary-100 mb-8">Rejoignez notre communauté de milliers de fashionistas satisfaites</p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-6">
                    <a href="#" class="bg-white hover:bg-gray-100 text-primary-600 font-bold text-lg px-8 py-4 rounded-xl flex items-center justify-center gap-4 transition duration-300">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.05 20.28c-.98.95-2.05.86-3.08.38-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.38C2.79 15.36 3.74 7.8 9.08 7.45c1.08.07 1.87.74 2.76.86 1.14.15 1.73-.68 2.96-.68 1.21 0 1.91.68 2.92.74 1.36.07 2.29-.93 3.22-1.66-1.43-2.07-3.44-2.3-4.78-2.33-1.83-.03-3.44 1.04-4.33 1.04-.9 0-2.26-1.01-3.81-.96-1.96.03-3.76 1.14-4.76 2.91-2.04 3.54-.52 8.79 1.46 11.67 1.03 1.48 2.26 3.15 3.87 3.09 1.53-.07 2.11-.99 3.89-.99 1.76 0 2.31.99 3.89.95 1.61-.03 2.67-1.51 3.68-3.01 1.16-1.69 1.64-3.33 1.67-3.41-.03-.01-3.19-1.22-3.22-4.85-.03-3.03 2.47-4.48 2.58-4.56-1.42-2.07-3.59-2.3-4.35-2.33-1.98-.16-3.64 1.11-4.58 1.11zM15.53 3.75c.8-1 1.34-2.38 1.19-3.75-1.16.05-2.56.78-3.39 1.76-.75.86-1.4 2.24-1.22 3.56 1.29.1 2.6-.66 3.42-1.57z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-sm">Téléchargez sur</div>
                            <div class="text-xl">App Store</div>
                        </div>
                    </a>
                    
                    <a href="#" class="bg-gray-900 hover:bg-black text-white font-bold text-lg px-8 py-4 rounded-xl flex items-center justify-center gap-4 transition duration-300">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3.609 1.814C4.03 1.375 4.822 1 5.728 1c.272 0 .518.02.764.061.245.041.49.102.736.183.748.244 1.415.667 1.883 1.231.45.543.713 1.2.713 1.87 0 .306-.04.61-.102.917-.02.103-.04.184-.061.265-.102.367-.286.712-.53 1.039-.653.814-1.485 1.354-2.242 1.506-.163.02-.347.041-.53.041-.55 0-1.058-.163-1.506-.47-.49-.347-.856-.835-1.079-1.415-.204-.53-.306-1.1-.306-1.69 0-1.079.367-2.037 1.058-2.75zM8.649 24c1.485 0 2.344-.795 3.55-.795 1.241 0 1.873.754 3.528.754 1.424 0 2.547-.692 3.447-1.935 1.058-1.527 1.506-3.038 1.527-3.12-.082-.04-2.995-1.16-3.018-4.624-.02-2.915 2.383-4.298 2.486-4.38-1.363-2.006-3.488-2.108-4.237-2.148-1.873-.143-3.65 1.058-4.585 1.058-.938 0-2.138-1.037-3.57-1.037C6.44 6.115 4 7.82 4 11.504c0 2.383.937 4.913 2.108 6.544 1.058 1.485 2.301 2.669 3.65 2.669.774 0 1.363-.245 1.935-.47.407-.142.815-.265 1.282-.265.489 0 .998.123 1.424.265.52.183 1.058.408 1.69.408 1.14 0 2.148-.754 3.018-2.005.143-.204.265-.408.387-.612-1.424-.53-2.383-1.832-2.383-3.345 0-1.873 1.526-3.406 3.65-3.406 1.465 0 2.383.632 3.08 1.527-.816.49-1.424 1.24-1.751 2.15-.184.51-.245 1.058-.245 1.628 0 1.14.387 2.22 1.058 3.12-.49.286-.998.53-1.526.734-.49.184-1 .326-1.506.326-1.139 0-2.158-.795-3.345-.795-1.24 0-2.219.795-3.57.795z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-sm">Disponible sur</div>
                            <div class="text-xl">Google Play</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-white font-bold">G</span>
                            </div>
                            <span class="ml-2 text-xl font-bold">Glo<span class="text-primary-400">shop</span></span>
                        </div>
                        <p class="text-gray-400">L'application mobile de mode féminine</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4">L'application</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Fonctionnalités</a></li>
                            <li><a href="#" class="hover:text-white">Télécharger</a></li>
                            <li><a href="#" class="hover:text-white">Avis</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4">Support</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Centre d'aide</a></li>
                            <li><a href="#" class="hover:text-white">Contact</a></li>
                            <li><a href="#" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4">Suivez-nous</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.12-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <span class="sr-only">TikTok</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} Gloshop. Tous droits réservés.</p>
                    <p class="mt-2 text-sm">L'application mobile de shopping féminin</p>
                </div>
            </div>
        </footer>
    </body>
</html>
