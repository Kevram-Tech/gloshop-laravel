@extends('layouts.admin')

@section('title', 'Nouvelle Catégorie - GloShop Admin')
@section('page-title', 'NOUVELLE CATÉGORIE')
@section('page-subtitle', 'CRÉATION')

@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border-2 border-gray-300 p-6">
    @csrf
    
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-gray-900 uppercase mb-2">Nom *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-3 py-2 border-2 border-gray-300 focus:border-gray-800 focus:outline-none text-sm">
            @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-900 uppercase mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full px-3 py-2 border-2 border-gray-300 focus:border-gray-800 focus:outline-none text-sm">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-900 uppercase mb-2">Image</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full px-3 py-2 border-2 border-gray-300 focus:border-gray-800 focus:outline-none text-sm">
            <p class="text-xs text-gray-500 mt-1 font-mono">Format: JPEG, PNG, JPG, GIF (max 2MB)</p>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-gray-800 text-white px-6 py-2 text-xs font-bold uppercase hover:bg-gray-900 border-2 border-gray-900">
                Créer
            </button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 text-xs font-bold uppercase hover:bg-gray-300 border-2 border-gray-300">
                Annuler
            </a>
        </div>
    </div>
</form>
@endsection

