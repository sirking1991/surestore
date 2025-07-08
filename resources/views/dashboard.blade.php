<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SureStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-amber-600">SureStore</h1>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" class="border-amber-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            
                            @can('view content')
                            <a href="{{ route('content.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Content
                            </a>
                            @endcan
                            
                            @role('admin')
                            <a href="{{ route('admin.settings') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Admin Settings
                            </a>
                            @endrole
                            
                            @role('manager')
                            <a href="{{ route('manager.reports') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Reports
                            </a>
                            @endrole
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-500">
                            @auth
                                {{ auth()->user()->getFilamentName() }}
                                <span class="ml-2 px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs">
                                    {{ implode(', ', auth()->user()->getRoleNames()->toArray()) }}
                                </span>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-10">
            <header>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">Dashboard</h1>
                </div>
            </header>
            <main>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            Welcome to your SureStore Dashboard!
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
