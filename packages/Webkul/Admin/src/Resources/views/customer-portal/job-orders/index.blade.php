@extends('admin::customer-portal.layouts.app', ['title' => 'Job Orders'])

@section('content')
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="px-4 py-3">Job Order #</th><th class="px-4 py-3">Issue Date</th><th class="px-4 py-3">ETD</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Total Qty</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse ($records as $jobOrder)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950">
                            <td class="px-4 py-3"><a class="font-medium text-brandColor" href="{{ route('customer_portal.job_orders.view', $jobOrder->id) }}">{{ $jobOrder->job_order_number }}</a></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ optional($jobOrder->issue_date)->format('Y-m-d') ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ optional($jobOrder->required_delivery_date)->format('Y-m-d') ?: '-' }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium capitalize text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ str_replace('_', ' ', $jobOrder->status ?: '-') }}</span></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ rtrim(rtrim(number_format((float) $jobOrder->total_order_qty, 2, '.', ''), '0'), '.') }}</td>
                        </tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($records, 'links'))
        <div class="mt-4">{{ $records->links() }}</div>
    @endif
@endsection
