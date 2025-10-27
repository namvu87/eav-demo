<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'EAV System' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <div class="w-64 bg-white border-r border-gray-200 p-4">
            <h1 class="text-xl font-bold mb-4">EAV System</h1>
            <nav class="space-y-2">
                <a href="/" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                <a href="/entity-types" class="block px-3 py-2 rounded hover:bg-gray-100">Entity Types</a>
                <a href="/attributes" class="block px-3 py-2 rounded hover:bg-gray-100">Attributes</a>
                <a href="/eav" class="block px-3 py-2 rounded hover:bg-gray-100">Entities</a>
            </nav>
        </div>
        <div class="flex-1 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</body>
</html>
