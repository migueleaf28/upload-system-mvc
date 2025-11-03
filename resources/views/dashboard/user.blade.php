<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <tbody>
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-3 text-blue-600 underline">document_report.pdf</td>
                    <td class="py-2 px-3">PDF</td>
                    <td class="py-2 px-3">1.2 MB</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-3 text-blue-600 underline">holiday_photos.docx</td>
                    <td class="py-2 px-3">DOCX</td>
                    <td class="py-2 px-3">0.8 MB</td>
                </tr>
            </tbody>
        </table>

        <hr class="my-6">

        <h3 class="text-md font-semibold text-blue-600 mb-3">Subir nuevo archivo</h3>

        <form action="#" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-3">
            @csrf
            <input 
                type="file" 
                name="file" 
                class="border border-gray-300 rounded-lg px-3 py-2 w-full sm:w-auto text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg text-sm transition">
                Subir archivo
            </button>
        </form>
    </main>

</body>
</html>
