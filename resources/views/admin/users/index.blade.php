@extends('layouts.admin')

@section('title', 'Utilisateurs - GloShop Admin')
@section('page-title', 'UTILISATEURS')
@section('page-subtitle', 'GESTION')

@section('content')
<div class="bg-white border-2 border-gray-300">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">ID</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Nom</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Email</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Commandes</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Date</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2 font-mono">#{{ $user->id }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2 font-mono">{{ $user->email }}</td>
                    <td class="px-4 py-2 font-mono">{{ $user->orders_count }}</td>
                    <td class="px-4 py-2 font-mono">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500 font-mono">Aucun utilisateur</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t-2 border-gray-300">
        {{ $users->links() }}
    </div>
</div>
@endsection

