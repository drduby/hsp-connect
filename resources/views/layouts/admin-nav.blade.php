@php
    $navItems = [
        [
            'route' => 'admin.dashboard',
            'label' => 'Dashboard',
            'icon'  => '<path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'route' => 'admin.users.index',
            'label' => 'Users',
            'icon'  => '<path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'route' => 'admin.tags.index',
            'label' => 'Tags',
            'icon'  => '<path d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" stroke-linecap="round" stroke-linejoin="round" /><path d="M6 6h.008v.008H6V6Z" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'route' => 'admin.faq.index',
            'label' => 'FAQ',
            'icon'  => '<path d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'route' => 'admin.posts.index',
            'label' => 'Posts',
            'icon'  => '<path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'route' => 'admin.reports.index',
            'label' => 'Reports',
            'icon'  => '<path d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" stroke-linecap="round" stroke-linejoin="round" />',
        ],
        [
            'route' => 'admin.feedback.index',
            'label' => 'Feedback',
            'icon'  => '<path d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" stroke-linecap="round" stroke-linejoin="round" />',
        ],
    ];
@endphp

<div class="flex h-16 shrink-0 items-center">
    <a href="{{ route('home') }}" class="flex items-center gap-x-2">
        <span class="flex size-8 items-center justify-center rounded-lg bg-indigo-600 text-xs font-bold text-white">H</span>
        <span class="text-sm font-semibold text-gray-900">HSPConnect <span class="text-gray-400 font-normal">Admin</span></span>
    </a>
</div>

<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach($navItems as $item)
                    @php $active = Route::has($item['route']) && request()->routeIs($item['route']); @endphp
                    <li>
                        @if(Route::has($item['route']))
                            <a href="{{ route($item['route']) }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold {{ $active ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"
                                     class="size-6 shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }}">
                                    {!! $item['icon'] !!}
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-300 cursor-not-allowed select-none">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0 text-gray-300">
                                    {!! $item['icon'] !!}
                                </svg>
                                {{ $item['label'] }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </li>

        <li class="mt-auto">
            <a href="{{ route('home') }}" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 hover:text-indigo-600">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 shrink-0 text-gray-400 group-hover:text-indigo-600">
                    <path d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Back to site
            </a>
        </li>
    </ul>
</nav>
