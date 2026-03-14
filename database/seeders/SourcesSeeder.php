<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'BBC News',
            'CNN',
            'The Guardian',
            'The New York Times',
            'Reuters',
            'Bloomberg',
            'Al Jazeera',
            'TechCrunch',
            'The Verge',
            'Associated Press'
        ];

        foreach ($sources as $name) {
            Source::query()->updateOrCreate(
                ['code' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
