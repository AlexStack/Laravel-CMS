<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use Faker\Generator as Faker;

$factory->define(LaravelCmsSetting::class, function (Faker $faker) {
    return [
        'param_name'      => $faker->word,
        'page_id'         => null,
        'param_value'     => $faker->text,
        'input_attribute' => json_encode([$faker->word, $faker->word, $faker->word]),
        'abstract'        => $faker->text,
        'category'        => $faker->word,
        'enabled'         => 1,
        'sort_value'      => rand(0, 9999),
    ];
});
