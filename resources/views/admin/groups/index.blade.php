<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Grupos</h1>
            <a href="{{ route('admin.groups.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                + Nuevo Grupo
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuota (MB)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($groups as $group)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $group->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $group->name }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 max-w-xs truncate">{{ $group->description ?? 'Sin descripción' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                   @if($group->storage_limit_in_mb)
                                        {{ number_format($group->storage_limit_in_mb) }} MB
                                    @else
                                        <span class="text-gray-500">Global</span>
                                    @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.groups.edit', $group) }}" class="text-green-600 hover:text-green-900">
                                    Editar
                                </a>
                                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('¿Estás seguro de eliminar este grupo?')"
                                            class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('dashboard.admin') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                ← Volver al Panel Admin
            </a>
        </div>
    </div>
</body>
</html>