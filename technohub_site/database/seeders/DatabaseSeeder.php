<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AboutQwasar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Clear the table before seeding to start fresh
        AboutQwasar::truncate();
        
        AboutQwasar::create([
            'title' => 'Default Qwasar Title',
            'description' => 'This is a default description for the About Qwasar section.',
            'video' => null, // This field is nullable
        ]);
    }
}