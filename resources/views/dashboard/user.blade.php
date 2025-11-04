<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
</head>
    <body class="bg-gray-500 min-h-screen flex flex-col items-center">

        <header class="w-full bg-white shadow-sm py-4 px-8 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600">Dashboard</h1>

            <div class="flex items-center gap-4">
                <span class="text-gray-700 font-medium">
                    {{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button 
                        type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1.5 rounded-lg transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </header>

        <main class="w-full max-w-3xl bg-white rounded-xl shadow-md mt-10 p-8">
            <h2 class="text-lg font-semibold text-blue-600 mb-4">Tus archivos subidos</h2>
            <table class="w-full border-collapse mb-8">
                <thead>
                    <tr class="bg-blue-100 text-blue-700 text-left">
                        <th class="py-2 px-3 border-b">Nombre del archivo</th>
                        <th class="py-2 px-3 border-b">Tipo</th>
                        <th class="py-2 px-3 border-b">Tamaño</th>
                    </tr>
                </thead>
                <tbody id="files-table-body">
                        @forelse($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-3 text-blue-600 underline">{{ $file->original_name }}</td>
                                <td class="py-2 px-3">{{ strtoupper($file->mime_type) }}</td>
                                <td class="py-2 px-3">{{ $file->formatted_size }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-2 px-3 text-gray-500 text-center">No hay archivos</td>
                            </tr>
                        @endforelse
                </tbody>
            </table>

            <hr class="my-6">

            <h3 class="text-md font-semibold text-blue-600 mb-3">Subir nuevo archivo</h3>

            <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-3">
                @csrf
                <input 
                    id="fileInput"
                    type="file" 
                    name="file" 
                    class="border border-gray-300 rounded-lg px-3 py-2 w-full sm:w-auto text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <button 
                    type="submit" 
                    id="uploadButton"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg text-sm transition">
                    <span id="uploadText">Subir archivo</span>
                    <span id="uploadSpinner" class="hidden">Subiendo...</span>
                </button>
            </form>
            <div id="uploadMessage" class="mt-3"></div>
        </main>
        <style>
        .hidden {
            display: none !important;
        }
        </style>
    </body>
</html>