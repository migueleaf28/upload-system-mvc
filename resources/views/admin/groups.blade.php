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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Grupos</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600">Aquí irá la gestión de grupos.</p>
            <a href="{{ route('dashboard.admin') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Volver al Panel Admin
            </a>
        </div>
    </div>
</body>
</html>