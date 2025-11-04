<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Grupo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Editar Grupo: {{ $group->name }}</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Formulario de edición del grupo -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Información del Grupo</h2>
                
                <form action="{{ route('admin.groups.update', $group) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $group->name) }}" 
                               class="w-full px-3 py-2 border rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                        <textarea name="description" class="w-full px-3 py-2 border rounded" rows="3">{{ old('description', $group->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Límite de Almacenamiento (MB)
                        </label>
                        <input type="number" name="storage_quota" 
                               value="{{ old('storage_quota', $group->storage_quota ? round($group->storage_quota / 1048576) : '') }}"
                               class="w-32 px-3 py-2 border rounded"
                               placeholder="Vacío = global">
                        <p class="text-xs text-gray-500 mt-1">Dejar vacío para usar límite global</p>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Actualizar Grupo
                    </button>
                </form>
            </div>

            <!-- Gestión de usuarios del grupo -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Usuarios del Grupo</h2>
                
                <!-- Formulario para agregar usuario -->
                <form action="{{ route('admin.groups.add-user', $group) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex gap-2">
                        <select name="user_id" class="flex-1 px-3 py-2 border border-gray-300 rounded" required>
                            <option value="">Seleccionar usuario...</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Agregar
                        </button>
                    </div>
                </form>

                <!-- Lista de usuarios en el grupo -->
                <div class="space-y-3">
                    @foreach($group->users as $user)
                        <div class="flex justify-between items-center bg-gray-50 px-4 py-3 rounded border">
                            <div>
                                <span class="font-medium">{{ $user->name }}</span>
                                <span class="text-gray-600 text-sm">({{ $user->email }})</span>
                            </div>
                            <form action="{{ route('admin.groups.remove-user', [$group, $user]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 text-sm"
                                        onclick="return confirm('¿Remover a {{ $user->name }} del grupo?')">
                                    × Remover
                                </button>
                            </form>
                        </div>
                    @endforeach
                    
                    @if($group->users->isEmpty())
                        <p class="text-gray-500 text-center py-4">No hay usuarios en este grupo</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>