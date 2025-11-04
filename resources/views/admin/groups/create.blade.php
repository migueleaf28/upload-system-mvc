<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Grupo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Crear Nuevo Grupo</h1>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('admin.groups.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Grupo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Ej: Desarrollo, Ventas, Administración">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                                  placeholder="Descripción opcional del grupo">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="storage_quota" class="block text-sm font-medium text-gray-700">Cuota de Almacenamiento (MB)</label>
                        <input type="number" name="storage_quota" id="storage_quota" value="{{ old('storage_quota') }}" min="1"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Dejar vacío para cuota ilimitada">
                        <p class="text-xs text-gray-500 mt-1">Cuota de almacenamiento en megabytes. Dejar vacío para ilimitado.</p>
                        @error('storage_quota')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.groups.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition duration-200">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">
                            Crear Grupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>