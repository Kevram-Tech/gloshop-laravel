@extends('admin.layout')

@section('title', 'Modifier Catégorie')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-6">Modifier la Catégorie</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('description', $category->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Image actuelle</label>
                    @if($category->image)
                        <img src="{{ strpos($category->image, 'http') === 0 ? $category->image : asset('storage/' . $category->image) }}" 
                             alt="{{ $category->name }}" 
                             class="w-32 h-32 object-cover rounded mb-2">
                    @else
                        <p class="text-gray-500 text-sm">Aucune image</p>
                    @endif
                    <label class="block text-gray-700 text-sm font-bold mb-2 mt-2">Nouvelle image</label>
                    <input type="file" name="image" accept="image/*"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Statut</label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm text-gray-600">Actif</span>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

