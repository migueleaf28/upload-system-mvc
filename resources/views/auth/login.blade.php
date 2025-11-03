<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-600 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-md rounded-2xl p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login</h2>

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" id="email" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            @if ($errors->any())
                <div class="text-red-600 text-sm bg-red-100 border border-red-300 rounded-lg px-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                Ingresar
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-6">
            ¿No tienes cuenta?
            <a href="{{ route('register.form') }}" class="text-blue-600 hover:underline font-medium">Regístrate aquí</a>
        </p>
    </div>

</body>
</html>
