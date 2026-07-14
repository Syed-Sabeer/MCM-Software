@extends('admin::customer-portal.auth.layout', ['heading' => 'Set up portal access'])
@section('content')
<p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $invitation->user->organization->name }} access for {{ $invitation->user->email }}</p>
<form method="POST" action="{{ route('customer_portal.invitation.accept', $token) }}" class="mt-5 grid gap-4">@csrf
    <label class="grid gap-1 text-sm dark:text-white">Password<input name="password" type="password" required minlength="8" autocomplete="new-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <label class="grid gap-1 text-sm dark:text-white">Confirm password<input name="password_confirmation" type="password" required autocomplete="new-password" class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <button class="rounded bg-brandColor px-4 py-2 font-medium text-white">Set password</button>
</form>
@endsection
