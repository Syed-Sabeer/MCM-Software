<x-admin::layouts>
    <!-- Title of the page. -->
    <x-slot:title>
        @lang('admin::app.website-submissions.title')
    </x-slot>

    <!-- Breadcrumbs -->
    <x-admin::breadcrumbs name="website-submissions" />

    <!-- Heading of the page -->
    <div class="mb-7 flex flex-wrap items-center justify-between">
        <p class="py-3 text-xl font-bold text-gray-800 dark:text-white">
            @lang('admin::app.website-submissions.title')
        </p>
    </div>

    <!-- Page Content -->
    <div class="grid gap-y-8">
        <div>
            <div class="grid gap-1">
                <!-- Title of the Main Card -->
                <p class="font-semibold text-gray-600 dark:text-gray-300">
                    @lang('admin::app.website-submissions.submissions')
                </p>

                <!-- Info of the Main Card -->
                <p class="text-gray-600 dark:text-gray-300">
                    @lang('admin::app.website-submissions.submissions-info')
                </p>
            </div>

            <div class="box-shadow max-1580:grid-cols-3 mt-2 grid grid-cols-4 flex-wrap justify-between gap-x-12 gap-y-6 rounded bg-white p-4 dark:bg-gray-900 max-xl:grid-cols-2 max-lg:gap-y-4 max-sm:grid-cols-1">
                <!-- Contacts Card -->
                <a
                    class="flex max-w-[360px] items-center gap-2 rounded-lg p-2 transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                    href="{{ route('admin.website_submissions.contacts') }}"
                >
                    <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
                        <i class="icon-globe text-3xl"></i>
                    </div>

                    <div class="grid">
                        <p class="mb-1.5 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.website-submissions.contacts')
                        </p>

                        <p class="text-xs text-gray-600 dark:text-gray-300">
                            @lang('admin::app.website-submissions.view-contacts')
                        </p>
                    </div>
                </a>

                <!-- Careers Card -->
                <a
                    class="flex max-w-[360px] items-center gap-2 rounded-lg p-2 transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                    href="{{ route('admin.website_submissions.careers') }}"
                >
                    <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
                        <i class="icon-briefcase text-3xl"></i>
                    </div>

                    <div class="grid">
                        <p class="mb-1.5 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.website-submissions.careers')
                        </p>

                        <p class="text-xs text-gray-600 dark:text-gray-300">
                            @lang('admin::app.website-submissions.view-careers')
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-admin::layouts>
