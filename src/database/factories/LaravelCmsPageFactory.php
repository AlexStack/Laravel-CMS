<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use Faker\Generator as Faker;

$factory->define(LaravelCmsPage::class, function (Faker $faker) {
    return [
        //'user_id' => $faker->word,
        //'parent_id' => $faker->randomDigitNotNull,
        'menu_enabled' => 0,
        //'status' => $faker->word,
        'title'      => $faker->word,
        'menu_title' => $faker->word,
        'slug'       => null,
        //'template_file' => $faker->word,
        'meta_title'       => $faker->word,
        'meta_keywords'    => $faker->word,
        'meta_description' => $faker->text,
        'abstract'         => $faker->text,
        //'main_banner' => $faker->randomDigitNotNull,
        //'main_image' => $faker->randomDigitNotNull,
        'sub_content'  => $faker->text,
        'main_content' => $faker->text,
        'sort_value'   => rand(0, 9999),
        'view_counts'  => rand(0, 9999),
        'tags'         => $faker->word,
        //'extra_image_1' => $faker->randomDigitNotNull,
        'extra_text_1'    => $faker->text,
        'extra_content_1' => $faker->text,
        //'extra_image_2' => $faker->randomDigitNotNull,
        'extra_text_2'    => $faker->text,
        'extra_content_2' => $faker->text,
        //'extra_image_3' => $faker->randomDigitNotNull,
        'extra_text_3'    => $faker->text,
        'extra_content_3' => $faker->text,
        'special_text'    => $faker->text,
        //'file_data' => $faker->text,
        //'redirect_url' => $faker->word,
        //'deleted_at' => $faker->date('Y-m-d H:i:s'),
        //'created_at' => $faker->date('Y-m-d H:i:s'),
        //'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
