<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
    <body class="bg-gray-600 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold text-center mb-6">Registrarse</h1>
            
            <form method="POST" action="{{ route('register.form') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border rounded" required>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border rounded" required>
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded" required>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
                    Registrarse
                </button>
                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </form>
        </div>
    </body>
</html>