<!-- resources/views/layouts/main.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
<header class="bg-gray-800 text-white py-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Link Harvester App</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="{{ route('urls.index') }}" class="hover:text-gray-300">Home</a></li>
                <li><a href="{{ route('urls.create') }}" class="hover:text-gray-300">Create URL</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container mx-auto mt-4">
    @yield('content')
</div>
</body>
</html>
