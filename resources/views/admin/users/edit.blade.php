@extends('admin.layout')

@section('title', 'Modifier Utilisateur')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-6">Modifier l'Utilisateur</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nouveau mot de passe</label>
                    <input type="password" name="password"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           placeholder="Laissez vide pour ne pas changer">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour conserver le mot de passe actuel</p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rôle</label>
                    <select name="is_admin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <option value="0" {{ !$user->is_admin ? 'selected' : '' }}>Client</option>
                        <option value="1" {{ $user->is_admin ? 'selected' : '' }}>Administrateur</option>
                    </select>
                </div>

                @if($user->provider)
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Connexion sociale</label>
                    <p class="text-sm text-gray-600">Connecté via {{ ucfirst($user->provider) }}</p>
                </div>
                @endif
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

