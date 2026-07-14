@extends('admin::customer-portal.auth.layout', ['heading' => 'Choose a new password'])
@section('content')
<form method="POST" action="{{ route('customer_portal.password.update') }}" class="mt-5 grid gap-4">@csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <label class="grid gap-1 text-sm dark:text-white">Email<input name="email" type="email" value="{{ old('email', $email) }}" required class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <label class="grid gap-1 text-sm dark:text-white">New password<input name="password" type="password" required minlength="8" autocomplete="new-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <label class="grid gap-1 text-sm dark:text-white">Confirm password<input name="password_confirmation" type="password" required autocomplete="new-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <button class="rounded bg-brandColor px-4 py-2 font-medium text-white">Reset password</button>
</form>
@endsection
