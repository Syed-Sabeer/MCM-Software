<x-admin::layouts.anonymous>
    <x-slot:title>Set New Password</x-slot>

    <main class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10 dark:bg-gray-950">
        <div class="w-full max-w-md">
            <div class="mb-6 flex justify-center">@include('admin::sessions.partials.auth-logo')</div>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                    <div class="flex items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-red-50 text-brandColor dark:bg-red-950"><i class="icon-setting text-xl"></i></span><div><h1 class="text-lg font-bold text-gray-900 dark:text-white">Set a new password</h1><p class="mt-1 text-sm text-gray-500">Use at least eight characters and confirm it below.</p></div></div>
                </div>

                <x-admin::form :action="$resetAction">
                    <div class="grid gap-4 p-6">
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">New password</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="password" id="password" name="password" rules="required|min:8" label="New password" autocomplete="new-password" ref="password" />
                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">Confirm new password</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="password" id="password_confirmation" name="password_confirmation" rules="required|confirmed:@password" label="Confirm new password" autocomplete="new-password" />
                            <x-admin::form.control-group.error control-name="password_confirmation" />
                        </x-admin::form.control-group>
                    </div>
                    <div class="flex justify-end border-t border-gray-200 bg-slate-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950"><button type="submit" class="primary-button">Update password</button></div>
                </x-admin::form>
            </section>
        </div>
    </main>
</x-admin::layouts.anonymous>
