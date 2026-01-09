@extends('layouts.admin')

@section('title', 'Utilisateurs - Admin')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div class="mb-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-search"></i> Rechercher
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commandes</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrit le</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->orders_count ?? 0 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun utilisateur trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection

