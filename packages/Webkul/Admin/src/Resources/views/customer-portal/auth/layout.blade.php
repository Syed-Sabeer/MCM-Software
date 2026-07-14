<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('customer_portal.name') }}</title>
    {{ vite()->set(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js']) }}
</head>
<body class="min-h-screen bg-gray-100 font-inter dark:bg-gray-950">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-4 py-10">
        <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $heading }}</h1>
            @if(session('success'))<p class="mt-4 rounded bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</p>@endif
            @if($errors->any())<p class="mt-4 rounded bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p>@endif
            @yield('content')
        </div>
    </main>
</body>
</html>
