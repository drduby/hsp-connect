<?php

use App\Enums\PostType;
use App\Models\Post;
use Illuminate\Support\Str;
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
    public function posts(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Post::withTrashed()
            ->with(['user', 'tags'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn ($q) => $q->where('nickname', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('tags', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->filter === 'published', fn ($q) => $q->whereNull('deleted_at')->where('is_published', true))
            ->when($this->filter === 'draft', fn ($q) => $q->whereNull('deleted_at')->where('is_published', false))
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
        @foreach(['' => 'Active', 'published' => 'Published', 'draft' => 'Draft', 'deleted' => 'Deleted'] as $value => $label)
            <button wire:click="$set('filter', '{{ $value }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                           {{ $filter === $value ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
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
                            @elseif($post->is_published)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Published</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $post->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-x-3">
                                @if($post->trashed())
                                    <button wire:click="restore({{ $post->id }})"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Restore</button>
                                    <button wire:click="forceDelete({{ $post->id }})"
                                            wire:confirm="Permanently delete this post? This cannot be undone."
                                            class="text-sm font-medium text-red-600 hover:text-red-800">Delete Forever</button>
                                @else
                                    <button wire:click="togglePublish({{ $post->id }})"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-800">
                                        {{ $post->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                    <button wire:click="delete({{ $post->id }})"
                                            wire:confirm="Are you sure you want to delete this post?"
                                            class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                @endif
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
