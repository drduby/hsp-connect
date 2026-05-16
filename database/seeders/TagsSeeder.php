<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            'Spastik' => '#0a6e7a',
            'Muskeln' => '#b8762a',
            'Entspannung' => '#2a8a52',
            'Physiotherapie' => '#1a5080',
            'Hilfsmittel' => '#7a4820',
            'Alltag' => '#486070',
            'Ernährung' => '#607020',
            'Schlaf' => '#583878',
            'Reha' => '#7a3060',
        ])->each(function (string $color, string $name): void {
            Tag::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'color' => $color,
                    'is_active' => true,
                ],
            );
        });
    }
}
