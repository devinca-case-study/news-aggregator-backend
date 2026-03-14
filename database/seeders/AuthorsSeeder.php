<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            'John Smith',
            'Emma Johnson',
            'Michael Brown',
            'Sophia Lee',
            'David Wilson',
            'Olivia Martinez'
        ];

        foreach ($authors as $name) {
            Author::query()->create([
                'code' => Str::slug($name),
                'name' => $name
            ]);
        }
    }
}
