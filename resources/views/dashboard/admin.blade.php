<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex">
            <div class="w-64 bg-gray-800 text-white">
                <div class="p-5 text-2xl font-bold border-b border-gray-700">
                    Admin Panel
                </div>
                <nav class="flex-1 p-4 space-y-2">
                    <a href="#" class="block px-4 py-2 rounded-lg bg-blue-600 text-white">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Usuarios</a>
                    <a href="{{ route('admin.groups.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Grupos</a>
                </nav>
            </div>
            <div class="fixed top-0 right-0 left-64 bg-white shadow flex justify-end items-center px-6 py-3 z-50">
                <p class="text-gray-700">
                    Bienvenido, <span class="font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                </p>
                <form action="{{ route('logout') }}" method="POST" class="ml-3">
                    @csrf
                    <button 
                        type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-1.5 rounded-lg transition"
                    >
                        Cerrar sesión
                    </button>
                </form>
            </div>
            <div class="flex-1 p-8 mt-20">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Configuración del Sistema</h1>

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

                <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Configuración de Subida de Archivos</h2>

                    <form action="{{ route('admin.config.update') }}" method="POST" id="configForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                            <div>
                                <label class="block text-lg font-semibold text-gray-700 mb-2">
                                    Límite Global por Defecto (MB)
                                </label>
                                <input type="number" name="user_default_storage" 
                                    value="{{ old('user_default_storage', round($userDefaultStorage / 1048576)) }}"
                                    class="w-32 px-3 py-2 border border-gray-300 rounded"
                                    min="1" required>
                                <p class="text-sm text-gray-500 mt-1">
                                    Aplicado a todos los usuarios sin límite específico
                                </p>
                            </div>
                            <div>
                                <label class="block text-lg font-semibold text-gray-700 mb-2">
                                    Tamaño Máximo por Archivo (MB)
                                </label>
                                <input type="number" name="max_file_size" 
                                    value="{{ old('max_file_size', round($maxFileSize / 1048576)) }}"
                                    class="w-32 px-3 py-2 border border-gray-300 rounded"
                                    min="1" required>
                                <p class="text-sm text-gray-500 mt-1">
                                    Límite físico del servidor
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                                Guardar Configuración
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <label class="block text-lg font-semibold text-gray-700 mb-2">
                            Extensiones Prohibidas
                        </label>
                        <p class="text-sm text-gray-500 mb-2">Ej: exe, php, js</p>
                        
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($blockedExtensions as $extension)
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                                    .{{ $extension }}
                                    <form action="{{ route('admin.config.remove-extension', $extension) }}" method="POST" class="ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('¿Eliminar .{{ $extension }}?')">
                                            ×
                                        </button>
                                    </form>
                                </span>
                            @endforeach
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-4">
                            Estas extensiones serán bloqueadas, incluso dentro de archivos ZIP
                        </p>

                        <div class="flex gap-2">
                            <input type="text" name="new_extension" id="new_extension" placeholder="Ej: exe" 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded"
                                pattern="[a-zA-Z0-9]+" maxlength="10">
                            <button type="button" onclick="addExtension()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>