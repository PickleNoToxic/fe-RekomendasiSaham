<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Saham Indonesia</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ url('/') }}" class="font-bold text-lg text-primary">SahamKu</a>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="mt-6 py-6 border-t text-center text-sm text-white bg-gray-800">
        &copy; {{ date('Y') }} Analisis Saham Indonesia
    </footer>

    @vite('resources/js/app.js')
</body>
</html>
