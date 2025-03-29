<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Tala',
            'email' => 'tala@gmail.com',
            'password' => Hash::make('password'),
            'bio' => 'this is tala',
            'image' => 'no image'
        ]);
        User::factory(10)->create();
        Project::factory(20)->create();
        Tag::factory(10)->create();
        Skill::factory(10)->create();
        Link::factory(20)->create();
        Service::factory(10)->create();
        Testimonial::factory(10)->create();
    }
}
