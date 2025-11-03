<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 flex min-h-screen">

    <aside class="w-64 bg-gray-800 text-gray-200 flex flex-col">
        <div class="p-5 text-2xl font-bold border-b border-gray-700">
            Admin Panel
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="block px-4 py-2 rounded-lg bg-blue-600 text-white">Dashboard</a>
            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Usuarios</a>
            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Grupos</a>
            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Configuraciones</a>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
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

        <section class="bg-white rounded-xl shadow-md p-6 mb-8 mt-10">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Administrar Usuarios</h2>
            <table class="w-full border-collapse mb-4">
                <thead>
                    <tr class="bg-blue-100 text-blue-800">
                        <th class="py-2 px-3 text-left">ID</th>
                        <th class="py-2 px-3 text-left">Nombre</th>
                        <th class="py-2 px-3 text-left">Email</th>
                        <th class="py-2 px-3 text-left">Groupo</th>
                        <th class="py-2 px-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">1</td>
                        <td class="py-2 px-3">Alice Johnson</td>
                        <td class="py-2 px-3">alice@email.com</td>
                        <td class="py-2 px-3">50</td>
                        <td class="py-2 px-3">
                            <button class="text-blue-600 hover:underline mr-2">Editar</button>
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">2</td>
                        <td class="py-2 px-3">Bob Williams</td>
                        <td class="py-2 px-3">bob@email.com</td>
                        <td class="py-2 px-3">22</td>
                        <td class="py-2 px-3">
                            <button class="text-blue-600 hover:underline mr-2">Editar</button>
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                Agregar nuevo usuario
            </button>
        </section>

        <section class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Administrar Grupos</h2>
            <table class="w-full border-collapse mb-4">
                <thead>
                    <tr class="bg-blue-100 text-blue-800">
                        <th class="py-2 px-3 text-left">Nombre del grupo</th>
                        <th class="py-2 px-3 text-left">Descripción</th>
                        <th class="py-2 px-3 text-left">Allowed Types</th>
                        <th class="py-2 px-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">Premium Users</td>
                        <td class="py-2 px-3">Premium Users</td>
                        <td class="py-2 px-3">.pdf, .docx, .jpg</td>
                        <td class="py-2 px-3">
                            <button class="text-red-600 hover:underline mr-2">Eliminar</button>
                            <button class="text-blue-600 hover:underline">Editar</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">Standard Users</td>
                        <td class="py-2 px-3">Standard Users</td>
                        <td class="py-2 px-3">.pdf, .docx</td>
                        <td class="py-2 px-3">
                            <button class="text-red-600 hover:underline mr-2">Eliminar</button>
                            <button class="text-blue-600 hover:underline">Editar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                Crear nuevo grupo
            </button>
        </section>

        <section class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Configurar límites y restricciones</h2>

            <form class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tamaño máximo de archivo (MB)</label>
                    <input type="number" value="50" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de archivo permitidos</label>
                    <input type="text" value=".pdf, .docx, .jpg" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Limite diario de subida (archivos)</label>
                    <input type="number" value="10" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-400">
                </div>
            </form>

            <div class="mt-4">
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg text-sm transition">
                    Guardar configuraciones
                </button>
            </div>
        </section>
    </main>

</body>
</html>
