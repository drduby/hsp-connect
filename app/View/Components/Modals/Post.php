<?php

namespace App\View\Components\Modals;

use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Post extends Component
{
    public Collection $modalTags;

    public function __construct()
    {
        $this->modalTags = Tag::where('is_active', true)->orderBy('id')->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('components.modals.post');
    }
}
