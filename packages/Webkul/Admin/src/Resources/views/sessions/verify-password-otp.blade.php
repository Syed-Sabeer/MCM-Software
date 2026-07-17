<x-admin::layouts.anonymous>
    <x-slot:title>Verify Reset Code</x-slot>

    <main class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10 dark:bg-gray-950">
        <div class="w-full max-w-md">
            <div class="mb-6 flex justify-center">@include('admin::sessions.partials.auth-logo')</div>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-6 py-5 text-center dark:border-gray-800">
                    <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-md bg-red-50 text-brandColor dark:bg-red-950"><i class="icon-setting text-xl"></i></span>
                    <h1 class="mt-3 text-lg font-bold text-gray-900 dark:text-white">Verify your email</h1>
                    <p class="mt-1 text-sm text-gray-500">Enter the six-digit code sent to {{ $maskedEmail }}.</p>
                </div>

                <x-admin::form :action="$verifyAction">
                    <div class="p-6">
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">Verification code</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" id="otp" name="otp" rules="required|digits:6" :value="old('otp')" label="Verification code" placeholder="000000" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" class="text-center font-mono text-2xl tracking-[0.35em]" />
                            <x-admin::form.control-group.error control-name="otp" />
                        </x-admin::form.control-group>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 bg-slate-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950">
                        <a class="text-sm font-semibold text-brandColor hover:underline" href="{{ route('admin.forgot_password.create') }}">Use another email</a>
                        <button type="submit" class="primary-button">Verify code</button>
                    </div>
                </x-admin::form>

                <form method="POST" action="{{ $resendAction }}" class="border-t border-gray-200 px-6 py-4 text-center dark:border-gray-800">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-gray-600 hover:text-brandColor dark:text-gray-300">Send a new code</button>
                </form>
            </section>
        </div>
    </main>
</x-admin::layouts.anonymous>
