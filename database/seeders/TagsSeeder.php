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
            ['name' => 'Spastik',        'name_en' => 'Spasticity',     'color' => '#0a6e7a'],
            ['name' => 'Muskeln',        'name_en' => 'Muscles',        'color' => '#b8762a'],
            ['name' => 'Entspannung',    'name_en' => 'Relaxation',     'color' => '#2a8a52'],
            ['name' => 'Physiotherapie', 'name_en' => 'Physiotherapy',  'color' => '#1a5080'],
            ['name' => 'Hilfsmittel',    'name_en' => 'Assistive Aids', 'color' => '#7a4820'],
            ['name' => 'Alltag',         'name_en' => 'Daily Life',     'color' => '#486070'],
            ['name' => 'Ernährung',      'name_en' => 'Nutrition',      'color' => '#607020'],
            ['name' => 'Schlaf',         'name_en' => 'Sleep',          'color' => '#583878'],
            ['name' => 'Reha',           'name_en' => 'Rehabilitation', 'color' => '#7a3060'],
        ])->each(function (array $tag): void {
            Tag::query()->updateOrCreate(
                ['slug' => Str::slug($tag['name'])],
                [
                    'name' => $tag['name'],
                    'name_en' => $tag['name_en'],
                    'color' => $tag['color'],
                    'is_active' => true,
                ],
            );
        });
    }
}
