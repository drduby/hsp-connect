<?php

use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $name_en = '';
    public string $color = '#0a6e7a';
    public bool $is_active = true;

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'name_en', 'color', 'is_active']);
        $this->color = '#0a6e7a';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $tag->id;
        $this->name = $tag->name;
        $this->name_en = $tag->name_en ?? '';
        $this->color = $tag->color;
        $this->is_active = (bool) $tag->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $this->name,
            'name_en' => $this->name_en ?: null,
            'color' => $this->color,
            'slug' => Str::slug($this->name),
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Tag::findOrFail($this->editingId)->update($data);
            $message = 'Tag updated.';
        } else {
            Tag::create($data);
            $message = 'Tag created.';
        }

        $this->showModal = false;
        session()->flash('success', $message);
    }

    public function delete(int $id): void
    {
        Tag::find($id)?->delete();
        session()->flash('success', 'Tag deleted.');
    }

    #[Computed]
    public function tags(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Tag::withCount(['posts' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('id')
            ->paginate(10);
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">{{ session('success') }}</div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Tags</h1>
        <button wire:click="openCreate"
                class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">+ New Tag</button>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Name (DE)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Name (EN)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Posts</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->tags as $tag)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-block h-5 w-5 rounded-full border border-gray-200"
                                  style="background-color: {{ $tag->color }}"></span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $tag->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $tag->name_en ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono whitespace-nowrap">{{ $tag->slug }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($tag->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $tag->posts_count }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute right-0 z-20 mt-1 w-40 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200">
                                    <button wire:click="openEdit({{ $tag->id }})" @click="open = false"
                                            class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.885L17.5 5.5a2.121 2.121 0 0 0-3-3L3.58 13.42a4 4 0 0 0-.885 1.343Z"/></svg>
                                        Edit
                                    </button>
                                    <div class="my-1 border-t border-gray-100"></div>
                                    <button wire:click="delete({{ $tag->id }})" wire:confirm="Are you sure you want to delete this tag?" @click="open = false"
                                            class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">No tags found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->tags->hasPages())
        <div class="mt-6">
            {{ $this->tags->links('admin.pagination') }}
        </div>
    @endif

    {{-- Create / Edit Modal --}}
    <div x-show="$wire.showModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6" @click.outside="$wire.showModal = false">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">
                {{ $editingId ? 'Edit Tag' : 'New Tag' }}
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (DE)</label>
                    <input wire:model="name" type="text"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (EN)</label>
                    <input wire:model="name_en" type="text"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('name_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <div class="flex items-center gap-x-3">
                        <input wire:model="color" type="color"
                               class="h-9 w-16 cursor-pointer rounded border border-gray-300 p-0.5">
                        <span class="text-sm text-gray-500 font-mono">{{ $color }}</span>
                    </div>
                    @error('color') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-x-2">
                    <input wire:model="is_active" type="checkbox" id="tag_is_active"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="tag_is_active" class="text-sm font-medium text-gray-700">Active</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-x-3">
                <button wire:click="$set('showModal', false)"
                        class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                <button wire:click="save"
                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
            </div>
        </div>
    </div>
</div>
