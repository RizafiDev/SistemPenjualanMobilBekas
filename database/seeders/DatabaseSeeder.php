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
        // User::factory(10)->create();

        $this->call(MerekSeeder::class);
        $this->call(KategoriSeeder::class);
        $this->call(MobilSeeder::class);
        $this->call(VarianSeeder::class);
        $this->call(StokMobilSeeder::class);
        $this->call(RiwayatServisSeeder::class);
        $this->call(ArticleSeeder::class);
    }
}
