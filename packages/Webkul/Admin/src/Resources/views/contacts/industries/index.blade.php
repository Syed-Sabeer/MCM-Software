@php
    $currentRouteName = request()->route()?->getName() ?? 'admin.contacts.organizations.industries.index';
    $routePrefix = str_contains($currentRouteName, 'admin.customers.')
        ? 'customers'
        : (str_contains($currentRouteName, 'admin.vendors.') ? 'vendors' : 'contacts');
@endphp

<x-admin::layouts>
    <x-slot:title>Industries</x-slot:title>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div>
                <div class="text-xl font-bold dark:text-white">Industries</div>
                <p class="mt-1 text-xs text-gray-500">Manage industry names used on customer and vendor profiles.</p>
            </div>

            <a href="{{ route('admin.' . $routePrefix . '.organizations.create') }}" class="secondary-button">Back to Organizations</a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form method="POST" action="{{ route('admin.' . $routePrefix . '.organizations.industries.store') }}" class="mb-4 flex gap-3">
                @csrf

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                    placeholder="Industry name"
                    required
                >

                <button type="submit" class="primary-button">Add Industry</button>
            </form>

            <x-admin::form.control-group.error control-name="name" />

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-800">
                            <th class="py-2">Name</th>
                            <th class="w-40 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($industries as $industry)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 dark:text-white">{{ $industry->name }}</td>
                                <td class="py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.' . $routePrefix . '.organizations.industries.edit', $industry->id) }}" class="secondary-button">Edit</a>

                                        <form method="POST" action="{{ route('admin.' . $routePrefix . '.organizations.industries.delete', $industry->id) }}" onsubmit="return confirm('Delete this industry?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="secondary-button">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-6 text-center text-gray-500">No industries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $industries->links() }}
            </div>
        </div>
    </div>
</x-admin::layouts>
