<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => '36'],
            ['name' => '37'],
            ['name' => '38'],
            ['name' => '39'],
            ['name' => '40'],
            ['name' => '41'],
            ['name' => '42'],
            ['name' => '43'],
            ['name' => '44'],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
} 