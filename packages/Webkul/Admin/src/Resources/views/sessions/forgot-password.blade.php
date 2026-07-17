<x-admin::layouts.anonymous>
    <x-slot:title>Forgot Password</x-slot>

    <main class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10 dark:bg-gray-950">
        <div class="w-full max-w-md">
            <div class="mb-6 flex justify-center">@include('admin::sessions.partials.auth-logo')</div>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-red-50 text-brandColor dark:bg-red-950"><i class="icon-setting text-xl"></i></span>
                        <div><h1 class="text-lg font-bold text-gray-900 dark:text-white">Reset your password</h1><p class="mt-1 text-sm leading-5 text-gray-500">Enter the email used for your MCM account.</p></div>
                    </div>
                </div>

                <x-admin::form :action="route('admin.forgot_password.store')">
                    <div class="p-6">
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">Email address</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="email" id="email" name="email" rules="required|email" :value="old('email')" label="Email address" placeholder="name@company.com" autocomplete="email" />
                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 bg-slate-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950">
                        <a class="text-sm font-semibold text-brandColor hover:underline" href="{{ route('admin.session.create') }}">Back to sign in</a>
                        <button type="submit" class="primary-button inline-flex items-center gap-2"><span class="icon-mail"></span> Send code</button>
                    </div>
                </x-admin::form>
            </section>
        </div>
    </main>
</x-admin::layouts.anonymous>
