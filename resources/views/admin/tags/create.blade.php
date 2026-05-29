@extends('layouts.admin')

@section('title', 'New Tag')

@section('content')
    @if($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="mb-8 flex items-center gap-x-4">
        <a href="{{ route('admin.tags.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Tags</a>
    </div>

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Tag</h1>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
        <form method="POST" action="{{ route('admin.tags.store') }}" class="divide-y divide-gray-100">
            @csrf

            <div class="px-6 py-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (DE)</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (EN)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                           class="block h-10 w-20 rounded-md border border-gray-300 p-1 shadow-sm cursor-pointer focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="flex items-end gap-x-3 pb-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                </div>
            </div>

            <div class="px-6 py-4 flex items-center justify-end gap-x-3">
                <a href="{{ route('admin.tags.index') }}"
                   class="rounded-md px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">Cancel</a>
                <button type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create Tag</button>
            </div>
        </form>
    </div>
@endsection
