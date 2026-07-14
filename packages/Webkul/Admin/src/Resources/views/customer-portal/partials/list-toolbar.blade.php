<form method="GET" action="{{ route($route) }}" class="portal-card mb-4 flex flex-wrap items-center justify-between gap-3 p-3">
    <div class="flex min-w-[280px] flex-1 flex-wrap items-center gap-2">
    <div class="relative min-w-[220px] flex-1">
        <span class="icon-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-400"></span>
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="{{ $placeholder ?? 'Search records' }}"
            class="w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm text-gray-900 outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950 dark:text-white"
        >
    </div>

    @if(! empty($statuses))
        <select name="status" class="min-w-[160px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
            <option value="">All statuses</option>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    @endif

    <button class="primary-button inline-flex items-center gap-2">
        <span class="icon-filter text-base"></span>
        Filter
    </button>

    @if(request()->filled('q') || request()->filled('status'))
        <a href="{{ route($route) }}" class="secondary-button">Clear</a>
    @endif
    </div>

    @if(isset($records) && method_exists($records, 'total'))
        <div class="ml-auto flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
            <label class="flex items-center gap-2 whitespace-nowrap">Per Page
                <select name="per_page" class="rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm outline-none focus:border-brandColor dark:border-gray-700 dark:bg-gray-950" onchange="this.form.submit()">
                    @foreach([10, 15, 25, 50] as $size)<option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>@endforeach
                </select>
            </label>
            <span class="whitespace-nowrap">{{ $records->total() ? $records->firstItem().' - '.$records->lastItem().' of '.$records->total() : '0 records' }}</span>
        </div>
    @endif
</form>
