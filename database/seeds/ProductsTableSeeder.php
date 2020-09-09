<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Product', 30)->create()->each(function ($product) {
            $product->categories()->attach([
                rand(1, 4),
                rand(1, 4),
            ]);
        });
    }
}
