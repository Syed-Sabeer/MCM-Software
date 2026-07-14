@extends('admin::customer-portal.auth.layout', ['heading' => 'Customer portal sign in'])
@section('content')
<form method="POST" action="{{ route('customer_portal.login.store') }}" class="mt-5 grid gap-4">@csrf
    <label class="grid gap-1 text-sm dark:text-white">Email<input name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <label class="grid gap-1 text-sm dark:text-white">Password<input name="password" type="password" required autocomplete="current-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <label class="flex items-center gap-2 text-sm dark:text-white"><input type="checkbox" name="remember" value="1"> Remember me</label>
    <button class="rounded bg-brandColor px-4 py-2 font-medium text-white">Sign in</button>
    <a class="text-center text-sm text-brandColor" href="{{ route('customer_portal.password.request') }}">Forgot password?</a>
</form>
@endsection
