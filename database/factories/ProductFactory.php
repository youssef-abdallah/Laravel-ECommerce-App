<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(4),
        'slug' => $faker->slug,
        'subtitle' => $faker->sentence(5),
        'description' => $faker->text,
        'price' => $faker->numberBetween(15, 300) * 100,
        'image' => 'https://via.placeholder.com/200x250'
    ];
});
