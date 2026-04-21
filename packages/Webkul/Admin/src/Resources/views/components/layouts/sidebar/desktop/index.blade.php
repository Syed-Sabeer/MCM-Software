<div
    ref="sidebar"
    class="duration-80 fixed top-[60px] z-[10002] h-full w-[200px] border-gray-200 bg-white pt-4 transition-all dark:border-gray-800 dark:bg-gray-900 max-lg:hidden ltr:border-r rtl:border-l"
    @mouseover="handleMouseOver"
    @mouseleave="handleMouseLeave"
>
    <div class="journal-scroll h-[calc(100vh-100px)] overflow-hidden">
        <nav class="sidebar-rounded grid w-full gap-2">
            <!-- Navigation Menu -->
            @foreach (menu()->getItems('admin') as $menuItem)
                @php
                    $hasInlineChildren = ! in_array($menuItem->getKey(), ['settings', 'configuration'])
                        && $menuItem->haveChildren();
                @endphp

                <div class="px-4 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                    <a
                        class="flex gap-2 p-1.5 items-center cursor-pointer rounded-lg transition-all {{ $menuItem->isActive() == 'active' ? 'bg-brandColor' : 'hover:bg-gray-100 hover:dark:bg-gray-950' }} peer"
                        href="{{ $hasInlineChildren ? 'javascript:void(0)' : $menuItem->getUrl() }}"
                        @if ($hasInlineChildren)
                            @click.prevent="hoveringMenu = hoveringMenu === '{{$menuItem->getKey()}}' ? '' : '{{$menuItem->getKey()}}'; isMenuActive = hoveringMenu !== ''"
                        @endif
                    >
                        <span class="{{ $menuItem->getIcon() }} text-2xl {{ $menuItem->isActive() ? 'text-white' : ''}}"></span>

                        <div class="flex-1 flex justify-between items-center text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap {{ $menuItem->isActive() ? 'text-white' : ''}} group">
                            <p>{{ core()->getConfigData('general.settings.menu.'.$menuItem->getKey()) ?? $menuItem->getName() }}</p>
                        
                            @if ($hasInlineChildren)
                                <i
                                    class="icon-right-arrow rtl:icon-left-arrow text-lg transition-transform duration-200 {{ $menuItem->isActive() ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                    :class="[((hoveringMenu == '{{$menuItem->getKey()}}') || (hoveringMenu == '' && '{{ $menuItem->isActive() }}' === 'active')) ? 'rotate-90 rtl:-rotate-90' : '']"
                                ></i>
                            @endif
                        </div>
                    </a>

                    <!-- Submenu -->
                    @if ($hasInlineChildren)
                        <div
                            class="mt-1 hidden flex-col gap-1 ltr:ml-6 rtl:mr-6 ltr:pl-3 rtl:pr-3 ltr:border-l rtl:border-r border-gray-200 dark:border-gray-700"
                            :class="[((hoveringMenu == '{{$menuItem->getKey()}}') || (hoveringMenu == '' && '{{ $menuItem->isActive() }}' === 'active')) ? '!flex' : 'hidden']"
                        >
                            @foreach ($menuItem->getChildren() as $subMenuItem)
                                <a
                                    href="{{ $subMenuItem->getUrl() }}"
                                    class="group flex items-center gap-2 rounded-md px-2.5 py-1.5 text-sm font-medium transition-colors {{ $subMenuItem->isActive() == 'active' ? 'bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 hover:dark:bg-gray-950' }}"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full {{ $subMenuItem->isActive() == 'active' ? 'bg-red-600 dark:bg-red-300' : 'bg-gray-300 group-hover:bg-gray-400 dark:bg-gray-600 dark:group-hover:bg-gray-500' }}"></span>
                                    {{ core()->getConfigData('general.settings.menu.'.$subMenuItem->getKey()) ?? $subMenuItem->getName() }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </div>
</div>