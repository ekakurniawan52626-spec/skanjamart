<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin SKANJAMart',
            'email' => 'admin@skanjamart.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pelanggan Contoh',
            'email' => 'user@skanjamart.test',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $categories = ['Elektronik', 'Fashion', 'Makanan & Minuman', 'Kesehatan & Kecantikan'];
        foreach ($categories as $name) {
            $cat = Category::create(['name' => $name, 'slug' => Str::slug($name)]);

            for ($i = 1; $i <= 4; $i++) {
                Product::create([
                    'category_id' => $cat->id,
                    'name' => $name . ' Produk ' . $i,
                    'slug' => Str::slug($name . ' Produk ' . $i) . '-' . Str::random(4),
                    'description' => 'Deskripsi contoh untuk ' . $name . ' Produk ' . $i . '.',
                    'price' => rand(15, 500) * 1000,
                    'stock' => rand(5, 50),
                    'is_active' => true,
                ]);
            }
        }

        $this->call(FaqSeeder::class);
    }
}
