@extends('admin::customer-portal.auth.layout', ['heading' => 'Reset your password'])
@section('content')
<form method="POST" action="{{ route('customer_portal.password.email') }}" class="mt-5 grid gap-4">@csrf
    <label class="grid gap-1 text-sm dark:text-white">Email<input name="email" type="email" value="{{ old('email') }}" required autofocus class="rounded border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-950"></label>
    <button class="rounded bg-brandColor px-4 py-2 font-medium text-white">Send reset link</button>
    <a class="text-center text-sm text-brandColor" href="{{ route('admin.session.create') }}">Back to sign in</a>
</form>
@endsection
