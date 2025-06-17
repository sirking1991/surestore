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
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Admin Panel -->
                        @role('admin')
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900">Admin Panel</h3>
                                <div class="mt-3 text-sm text-gray-500">
                                    <p>You have full administrative access.</p>
                                    <ul class="list-disc pl-5 mt-2">
                                        <li>Manage users</li>
                                        <li>Manage roles</li>
                                        <li>Manage permissions</li>
                                        <li>System settings</li>
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <a href="/admin" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                                        Go to Admin Panel →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endrole

                        <!-- Manager Panel -->
                        @role('manager')
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900">Manager Dashboard</h3>
                                <div class="mt-3 text-sm text-gray-500">
                                    <p>You have manager access.</p>
                                    <ul class="list-disc pl-5 mt-2">
                                        <li>View reports</li>
                                        <li>Manage content</li>
                                        <li>View users</li>
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <a href="/manager/reports" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                                        View Reports →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endrole

                        <!-- Content Management -->
                        @can('view content')
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900">Content Management</h3>
                                <div class="mt-3 text-sm text-gray-500">
                                    <p>You can manage content.</p>
                                    <ul class="list-disc pl-5 mt-2">
                                        <li>View content</li>
                                        @can('create content')
                                        <li>Create content</li>
                                        @endcan
                                        @can('edit content')
                                        <li>Edit content</li>
                                        @endcan
                                        @can('delete content')
                                        <li>Delete content</li>
                                        @endcan
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <a href="/content" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                                        Manage Content →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endcan

                        <!-- Your Account -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900">Your Account</h3>
                                <div class="mt-3 text-sm text-gray-500">
                                    <p>Manage your account settings.</p>
                                    <div class="mt-2">
                                        <p><strong>Roles:</strong> {{ implode(', ', auth()->user()->getRoleNames()->toArray()) }}</p>
                                        <p class="mt-1"><strong>Permissions:</strong></p>
                                        <ul class="list-disc pl-5 mt-1">
                                            @foreach(auth()->user()->getAllPermissions() as $permission)
                                                <li>{{ $permission->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
