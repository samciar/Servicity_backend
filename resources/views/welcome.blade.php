<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicity API V1</title>
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else

    @endif
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Bienvenido a la API de Servicity</h1>
            <p class="text-xl text-gray-600 mb-8">Esta es la API de backend de la plataforma de Servicityd</p>
            
            <div class="bg-white rounded-xl shadow-md p-8">
                <div class="flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Documentación de la API</h2>
                <p class="text-gray-600 mb-6">Consulte la documentación de la API para conocer los endpoints disponibles</p>
                <a href="#" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Visitar la Documentación
                </a>
            </div>
        </div>
    </div>
</body>
</html>
