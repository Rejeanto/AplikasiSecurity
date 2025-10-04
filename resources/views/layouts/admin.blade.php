<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-700">
                <span class="text-lg font-semibold">Admin Panel</span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.assessments.index') }}" 
                   class="flex items-center px-3 py-2 rounded-md hover:bg-gray-700 {{ request()->routeIs('assessments.*') ? 'bg-gray-800' : '' }}">
                    <i class="fa fa-clipboard-list mr-2"></i> Assessments
                </a>
                {{-- <a href="{{ route('steps.index') }}" 
                   class="flex items-center px-3 py-2 rounded-md hover:bg-gray-700 {{ request()->routeIs('steps.*') ? 'bg-gray-800' : '' }}">
                    <i class="fa fa-layer-group mr-2"></i> Steps
                </a>
                <a href="{{ route('questions.index') }}" 
                   class="flex items-center px-3 py-2 rounded-md hover:bg-gray-700 {{ request()->routeIs('questions.*') ? 'bg-gray-800' : '' }}">
                    <i class="fa fa-question-circle mr-2"></i> Questions
                </a> --}}
            </nav>
            <div class="p-4 border-t border-gray-700">
                <a href="#" class="block text-sm text-gray-400 hover:text-white">Logout</a>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col">
            {{-- Navbar --}}
            <header class="h-16 bg-white shadow flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Admin</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- FontAwesome for icons --}}
    <script src="https://kit.fontawesome.com/a2e0e6ad5c.js" crossorigin="anonymous"></script>
</body>
</html>
