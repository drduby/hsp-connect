@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    function statTrend(int $new, int $prev): array {
        $diff = $new - $prev;
        if ($diff > 0) return ['type' => 'up', 'value' => '+' . $diff, 'color' => 'text-green-600', 'icon_color' => 'text-green-500'];
        if ($diff < 0) return ['type' => 'down', 'value' => (string) $diff, 'color' => 'text-red-600', 'icon_color' => 'text-red-500'];
        return ['type' => 'neutral', 'value' => '0', 'color' => 'text-gray-500', 'icon_color' => 'text-gray-400'];
    }
@endphp

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Dashboard</h1>
    <p class="text-sm text-gray-500 mb-8">Compared to the previous 30 days</p>

    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Total Users --}}
        @php $trend = statTrend($stats['users_new'], $stats['users_prev']); @endphp
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-indigo-500 p-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-white">
                        <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Users</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['users']) }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold {{ $trend['color'] }}">
                    @if($trend['type'] === 'up')
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5 shrink-0 self-center {{ $trend['icon_color'] }}"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" /></svg>
                    @elseif($trend['type'] === 'down')
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5 shrink-0 self-center {{ $trend['icon_color'] }}"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" /></svg>
                    @endif
                    {{ $trend['value'] }} new
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.users.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">View all users</a>
                    </div>
                </div>
            </dd>
        </div>

        {{-- Online Now --}}
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-green-500 p-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-white">
                        <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Online Now</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['online'] }}</p>
                <p class="ml-2 text-sm text-gray-500">users active</p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.users.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">View all users</a>
                    </div>
                </div>
            </dd>
        </div>

        {{-- Published Posts --}}
        @php $trend = statTrend($stats['posts_new'], $stats['posts_prev']); @endphp
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-indigo-500 p-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-white">
                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Published Posts</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['posts']) }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold {{ $trend['color'] }}">
                    @if($trend['type'] === 'up')
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5 shrink-0 self-center {{ $trend['icon_color'] }}"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" /></svg>
                    @elseif($trend['type'] === 'down')
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5 shrink-0 self-center {{ $trend['icon_color'] }}"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" /></svg>
                    @endif
                    {{ $trend['value'] }} this month
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.posts.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">View all posts</a>
                    </div>
                </div>
            </dd>
        </div>

        {{-- Open Reports --}}
        @php $trend = statTrend($stats['reports_new'], $stats['reports_prev']); @endphp
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md p-3 {{ $stats['reports'] > 0 ? 'bg-red-500' : 'bg-indigo-500' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-white">
                        <path d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Open Reports</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                <p class="text-2xl font-semibold {{ $stats['reports'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['reports'] }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold {{ $trend['color'] }}">
                    @if($trend['type'] === 'up')
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5 shrink-0 self-center {{ $trend['icon_color'] }}"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" /></svg>
                    @elseif($trend['type'] === 'down')
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5 shrink-0 self-center {{ $trend['icon_color'] }}"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" /></svg>
                    @endif
                    {{ $trend['value'] }} this month
                </p>
                <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.reports.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">View all reports</a>
                    </div>
                </div>
            </dd>
        </div>

    </dl>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Recent Users --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Newest Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <ul role="list" class="divide-y divide-gray-100 px-6">
                @forelse($recentUsers as $user)
                    <li class="flex items-center justify-between gap-x-6 py-4">
                        <div class="flex min-w-0 items-center gap-x-4">
                            <span class="flex size-10 flex-none items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                                {{ strtoupper(mb_substr($user->nickname, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->nickname }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            @if($user->last_seen_at && $user->last_seen_at->gte(now()->subMinutes(5)))
                                <div class="flex items-center justify-end gap-x-1.5">
                                    <div class="flex-none rounded-full bg-emerald-500/20 p-1">
                                        <div class="size-1.5 rounded-full bg-emerald-500"></div>
                                    </div>
                                    <p class="text-xs text-gray-500">Online</p>
                                </div>
                            @else
                                <p class="text-xs text-gray-500">Joined {{ $user->created_at->diffForHumans() }}</p>
                            @endif
                            @if($user->is_admin)
                                <span class="mt-1 inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">Admin</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-500">No users yet.</li>
                @endforelse
            </ul>
        </div>

        {{-- Recent Posts --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Recent Posts</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <ul role="list" class="divide-y divide-gray-100 px-6">
                @forelse($recentPosts as $post)
                    <li class="flex items-center justify-between gap-x-6 py-4">
                        <div class="flex min-w-0 items-center gap-x-4">
                            <span class="flex size-10 flex-none items-center justify-center rounded-full text-sm font-bold text-white
                                {{ $post->type?->value === 'experience' ? 'bg-blue-500' : 'bg-purple-500' }}">
                                {{ $post->type?->value === 'experience' ? 'E' : 'Q' }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($post->title, 40) }}</p>
                                <p class="text-xs text-gray-500">by {{ $post->user?->nickname ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Published</span>
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-500">No posts yet.</li>
                @endforelse
            </ul>
        </div>

    </div>

    {{-- Today's Health --}}
    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Today's Activity</h2>
            <span class="text-xs text-gray-400">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="grid grid-cols-2 divide-x divide-y divide-gray-100 sm:grid-cols-4 lg:grid-cols-7">
            @php
                $healthItems = [
                    ['label' => 'Registrations',   'value' => $health['registrations_today'],  'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                    ['label' => 'Logins',           'value' => $health['logins_today'],         'color' => 'text-green-600',  'bg' => 'bg-green-50'],
                    ['label' => 'Failed Logins',    'value' => $health['failed_logins_today'],  'color' => $health['failed_logins_today'] > 0 ? 'text-red-600' : 'text-gray-400', 'bg' => $health['failed_logins_today'] > 0 ? 'bg-red-50' : 'bg-gray-50'],
                    ['label' => 'Posts',            'value' => $health['posts_today'],          'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
                    ['label' => 'Comments',         'value' => $health['comments_today'],       'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                    ['label' => 'Reports',          'value' => $health['reports_today'],        'color' => $health['reports_today'] > 0 ? 'text-orange-600' : 'text-gray-400', 'bg' => $health['reports_today'] > 0 ? 'bg-orange-50' : 'bg-gray-50'],
                    ['label' => 'Feedback',         'value' => $health['feedback_today'],       'color' => $health['feedback_today'] > 0 ? 'text-teal-600' : 'text-gray-400',   'bg' => $health['feedback_today'] > 0 ? 'bg-teal-50' : 'bg-gray-50'],
                ];
            @endphp
            @foreach($healthItems as $item)
                <div class="flex flex-col items-center justify-center px-4 py-5 {{ $item['bg'] }} gap-1">
                    <span class="text-2xl font-bold {{ $item['color'] }}">{{ $item['value'] }}</span>
                    <span class="text-xs font-medium text-gray-500">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
