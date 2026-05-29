<?php

use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
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
    public function tags(): \Illuminate\Database\Eloquent\Collection
    {
        return Tag::withCount(['posts' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('id')
            ->get();
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

    <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
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
                            <div class="flex items-center justify-end gap-x-3">
                                <button wire:click="openEdit({{ $tag->id }})"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit</button>
                                <button wire:click="delete({{ $tag->id }})"
                                        wire:confirm="Are you sure you want to delete this tag?"
                                        class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
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
