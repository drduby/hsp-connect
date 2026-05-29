@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Dashboard</h1>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-200">
            <p class="text-sm font-medium text-gray-500">Total Users</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['users']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-200">
            <p class="text-sm font-medium text-gray-500">Online Now</p>
            <p class="mt-2 text-3xl font-bold {{ $stats['online'] > 0 ? 'text-green-600' : 'text-gray-900' }}">{{ $stats['online'] }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-200">
            <p class="text-sm font-medium text-gray-500">Published Posts</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['posts']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow ring-1 ring-gray-200">
            <p class="text-sm font-medium text-gray-500">Open Reports</p>
            <p class="mt-2 text-3xl font-bold {{ $stats['reports'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['reports'] }}</p>
        </div>
    </div>
@endsection
