@php
    $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.organizations.industries.edit';
    $routePrefix = str_contains($currentRouteName, 'admin.customers.')
        ? 'customers'
        : (str_contains($currentRouteName, 'admin.vendors.') ? 'vendors' : 'contacts');
@endphp

<x-admin::layouts>
    <x-slot:title>Edit Industry</x-slot:title>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="text-xl font-bold dark:text-white">Edit Industry</div>

            <a href="{{ route('admin.' . $routePrefix . '.organizations.industries.index') }}" class="secondary-button">Back</a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form method="POST" action="{{ route('admin.' . $routePrefix . '.organizations.industries.update', $industry->id) }}" class="grid max-w-xl gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Industry Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $industry->name) }}"
                        class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                        required
                    >
                    <x-admin::form.control-group.error control-name="name" />
                </div>

                <div>
                    <button type="submit" class="primary-button">Save Industry</button>
                </div>
            </form>
        </div>
    </div>
</x-admin::layouts>
