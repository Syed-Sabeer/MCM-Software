@extends('admin::customer-portal.layouts.app', ['title' => 'Profile & Security', 'subtitle' => 'Manage your portal identity and keep your account credentials current.'])

@section('content')
    <div class="grid gap-4 xl:grid-cols-[minmax(0,680px)_320px]">
        <form method="POST" action="{{ route('customer_portal.security.update') }}" class="portal-card overflow-hidden">
            @csrf
            @method('PUT')

            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800"><p class="portal-kicker">Account settings</p><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Personal details and password</h2></div>
            <div class="grid gap-5 p-5">
                @if(session('success'))<div class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">{{ session('success') }}</div>@endif
                @if($portalUser->must_change_password)<div class="rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300">Choose a new password before continuing to use your account.</div>@endif

                <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-gray-200">Display name<input name="name" value="{{ old('name', $portalUser->name) }}" required class="rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950">@error('name')<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>

                <div class="border-t border-gray-200 pt-5 dark:border-gray-800"><p class="text-sm font-semibold text-gray-900 dark:text-white">Change password</p><p class="mt-1 text-xs text-gray-500">Leave these fields empty to keep your current password.</p></div>

                <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-gray-200">Current password<input name="current_password" type="password" autocomplete="current-password" class="rounded-md border border-gray-300 bg-white px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950">@error('current_password')<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-gray-200">New password<input name="password" type="password" minlength="8" autocomplete="new-password" class="rounded-md border border-gray-300 bg-white px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950">@error('password')<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="grid gap-1.5 text-sm font-medium text-gray-800 dark:text-gray-200">Confirm password<input name="password_confirmation" type="password" autocomplete="new-password" class="rounded-md border border-gray-300 bg-white px-3 py-2.5 font-normal outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950"></label>
                </div>
            </div>
            <div class="flex justify-end border-t border-gray-200 bg-slate-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-950"><button class="primary-button">Save changes</button></div>
        </form>

        <aside class="portal-card h-fit p-5">
            <span class="portal-icon-box"><i class="icon-setting text-xl"></i></span>
            <h2 class="mt-4 font-semibold text-gray-900 dark:text-white">Account overview</h2>
            <dl class="mt-4 grid gap-4 text-sm"><div><dt class="text-xs text-gray-500">Login email</dt><dd class="mt-1 break-all font-medium text-gray-900 dark:text-gray-200">{{ $portalUser->email }}</dd></div><div><dt class="text-xs text-gray-500">Portal role</dt><dd class="mt-1 font-medium capitalize text-gray-900 dark:text-gray-200">{{ str_replace('_', ' ', $portalUser->role) }}</dd></div><div><dt class="text-xs text-gray-500">Last login</dt><dd class="mt-1 font-medium text-gray-900 dark:text-gray-200">{{ $portalUser->last_login_at?->format('M d, Y, H:i') ?: 'Not recorded' }}</dd></div></dl>
        </aside>
    </div>
@endsection
