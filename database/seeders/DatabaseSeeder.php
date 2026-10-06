<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // AKun admin (id_user = 1) sudah ada di db_auth dan tidak boleh
        // disentuh oleh seeder.

        if (User::query()->where('username', 'admin')->exists()) {
            $this->command?->info('Akun admin sudah ada, seeder dilewati.');

            return;
        }

        User::factory()->create([
            'username' => 'operator',
            'nama_lengkap' => 'Operator Aceh',
            'email' => 'operator@test.com',
        ]);
    }
}
