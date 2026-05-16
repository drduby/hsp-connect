<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Collection;

class TagService
{
    public function activeTags(): Collection
    {
        return Tag::where('is_active', true)->get();
    }
}
