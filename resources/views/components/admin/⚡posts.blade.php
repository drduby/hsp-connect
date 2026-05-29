<?php

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function togglePublish(int $id): void
    {
        $post = Post::find($id);
        if (! $post) {
            return;
        }
        $post->update([
            'is_published' => ! $post->is_published,
            'published_at' => ! $post->is_published ? now() : null,
        ]);
        session()->flash('success', 'Post updated.');
    }

    public function delete(int $id): void
    {
        Post::find($id)?->delete();
        session()->flash('success', 'Post deleted.');
    }

    public function restore(int $id): void
    {
        Post::withTrashed()->find($id)?->restore();
        session()->flash('success', 'Post restored.');
    }

    public function forceDelete(int $id): void
    {
        Post::withTrashed()->find($id)?->forceDelete();
        session()->flash('success', 'Post permanently deleted.');
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::withTrashed()
            ->with(['user', 'tags'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('type', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', fn ($q) => $q->where('nickname', 'like', '%'.$this->search.'%'))
                    ->orWhereHas('tags', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            }))
            ->when($this->filter === 'published', fn ($q) => $q->whereNull('deleted_at')->where('is_published', true))
            ->when($this->filter === 'deleted', fn ($q) => $q->onlyTrashed())
            ->when($this->filter === '', fn ($q) => $q->whereNull('deleted_at'))
            ->orderByDesc('created_at')
            ->paginate(10);
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">{{ session('success') }}</div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-x-3">
            <h1 class="text-2xl font-bold text-gray-900">Posts</h1>
            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                {{ $this->posts->total() }}
            </span>
        </div>
        <input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search by title, author or tag…"
            class="block w-72 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
    </div>

    {{-- Status filter tabs --}}
    <div class="mb-6 flex gap-x-1 border-b border-gray-200">
        @foreach(['' => 'Active', 'published' => 'Published', 'deleted' => 'Deleted'] as $value => $label)
            <button wire:click="$set('filter', '{{ $value }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                           {{ $filter === $value ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Tags</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->posts as $post)
                    <tr class="{{ $post->trashed() ? 'bg-red-50/40' : '' }}" wire:key="{{ $post->id }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs">
                            <span class="block truncate" title="{{ $post->title }}">{{ Str::limit($post->title, 55) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            {{ $post->user?->nickname ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($post->type?->value === 'experience')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Experience</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-700">Question</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $post->tags->pluck('name')->join(', ') ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($post->trashed())
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Deleted</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Published</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $post->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="relative inline-block text-left" x-data="{ open: false, up: false }" @click.outside="open = false">
                                <button @click="up = ($el.getBoundingClientRect().bottom + 120 > window.innerHeight); open = !open"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute right-0 z-20 w-44 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200" :class="up ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right'">
                                    @if($post->trashed())
                                        <button wire:click="restore({{ $post->id }})" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-indigo-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-indigo-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 0 1-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 0 1 0 10.75H10.75a.75.75 0 0 1 0-1.5h2.875a3.875 3.875 0 0 0 0-7.75H3.622l4.146 3.957a.75.75 0 0 1-1.036 1.085l-5.5-5.25a.75.75 0 0 1 0-1.085l5.5-5.25a.75.75 0 0 1 1.06.025Z" clip-rule="evenodd"/></svg>
                                            Restore
                                        </button>
                                        <div class="my-1 border-t border-gray-100"></div>
                                        <button wire:click="forceDelete({{ $post->id }})" wire:confirm="Permanently delete this post? This cannot be undone." @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                            Delete forever
                                        </button>
                                    @else
                                        <button wire:click="togglePublish({{ $post->id }})" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
                                            {{ $post->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                        <div class="my-1 border-t border-gray-100"></div>
                                        <button wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this post?" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">No posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->posts->hasPages())
        <div class="mt-6">
            {{ $this->posts->links('admin.pagination') }}
        </div>
    @endif
</div>
