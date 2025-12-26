@extends('admin.layout')

@section('title', 'Statistiques de la Plateforme')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Statistiques de la Plateforme</h1>
        
        <!-- Period Filter -->
        <form method="GET" action="{{ route('admin.statistics') }}" class="flex gap-2">
            <select name="period" onchange="this.form.submit()" class="border rounded px-4 py-2">
                <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Cette année</option>
                <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Tout</option>
            </select>
        </form>
    </div>

    <!-- General Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Utilisateurs Totaux</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $generalStats['total_users'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">+{{ $generalStats['new_users'] }} nouveaux</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Produits</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $generalStats['total_products'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $generalStats['active_products'] }} actifs</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-box text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Commandes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $generalStats['total_orders'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Période sélectionnée</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Chiffre d'Affaires</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($generalStats['total_revenue'], 0) }} FCFA</p>
                    <p class="text-xs text-gray-500 mt-1">Période sélectionnée</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Revenus Quotidiens (30 derniers jours)</h2>
            <canvas id="dailyRevenueChart"></canvas>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Revenus Mensuels (12 derniers mois)</h2>
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>

    <!-- User Growth Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Croissance des Utilisateurs (12 derniers mois)</h2>
        <canvas id="userGrowthChart"></canvas>
    </div>

    <!-- Statistics Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Orders Statistics -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">Statistiques des Commandes</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">En attente</span>
                        <span class="font-bold text-yellow-600">{{ $ordersStats['pending'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">En traitement</span>
                        <span class="font-bold text-blue-600">{{ $ordersStats['processing'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Expédiées</span>
                        <span class="font-bold text-indigo-600">{{ $ordersStats['shipped'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Livrées</span>
                        <span class="font-bold text-green-600">{{ $ordersStats['delivered'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Annulées</span>
                        <span class="font-bold text-red-600">{{ $ordersStats['cancelled'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Statistics -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">Statistiques des Paiements</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">En attente</span>
                        <span class="font-bold text-yellow-600">{{ $paymentStats['pending'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Complétés</span>
                        <span class="font-bold text-green-600">{{ $paymentStats['completed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Échoués</span>
                        <span class="font-bold text-red-600">{{ $paymentStats['failed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Annulés</span>
                        <span class="font-bold text-gray-600">{{ $paymentStats['cancelled'] }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t">
                        <span class="text-gray-700 font-semibold">Montant Total</span>
                        <span class="font-bold text-blue-600">{{ number_format($paymentStats['total_amount'], 0) }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold">Méthodes de Paiement</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($paymentMethods as $method)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $methodLabels = [
                                        'paygate_flooz' => 'Flooz',
                                        'paygate_tmoney' => 'T-Money',
                                        'card_visa' => 'Carte Visa',
                                        'card_mastercard' => 'Carte Mastercard',
                                    ];
                                    $label = $methodLabels[$method->payment_method] ?? ucfirst(str_replace('_', ' ', $method->payment_method));
                                @endphp
                                {{ $label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $method->count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($method->total, 0) }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune donnée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold">Top 10 Produits les Plus Vendus</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité Vendue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($topProducts as $product)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->product_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->total_quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($product->total_revenue, 0) }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucun produit vendu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Statistics -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold">Statistiques par Catégorie</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre de Produits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ventes (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categoryStats as $category)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $category['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category['products_count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($category['sales'], 0) }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune catégorie</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Daily Revenue Chart
    const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
    new Chart(dailyRevenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($dailyRevenue, 'date')) !!},
            datasets: [{
                label: 'Revenus (FCFA)',
                data: {!! json_encode(array_column($dailyRevenue, 'revenue')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                }
            }
        }
    });

    // Monthly Revenue Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyRevenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
            datasets: [{
                label: 'Revenus (FCFA)',
                data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                }
            }
        }
    });

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($userGrowth, 'month')) !!},
            datasets: [{
                label: 'Nouveaux Utilisateurs',
                data: {!! json_encode(array_column($userGrowth, 'count')) !!},
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection

