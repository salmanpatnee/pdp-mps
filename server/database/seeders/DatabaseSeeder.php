<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();


        // Ticket::factory(100)->recycle($users)->create();
        $this->call(JournalSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ArticleTypeSeeder::class);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role_id' => 1,
        ]);
    }
}
