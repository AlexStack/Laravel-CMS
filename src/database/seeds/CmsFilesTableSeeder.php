<?php

namespace AlexStack\LaravelCms;

use Illuminate\Database\Seeder;

class CmsFilesTableSeeder extends Seeder
{
    private $config;
    private $table_name;

    public function __construct()
    {
        $this->config     = include base_path('config/laravel-cms.php');
        $this->table_name = $this->config['table_name']['files'];
    }

    public function run()
    {
        \DB::table($this->table_name)->delete();

        \DB::table($this->table_name)->insert([
            0 => [
                'id'          => 2,
                'user_id'     => null,
                'title'       => 'extra_image_1_300x200.png',
                'description' => '2019-09-28 07:07:19',
                'suffix'      => 'png',
                'path'        => 'dc/be767a55ebb67fa64a9b4efb0ddd424c10df7cdc.png',
                'filename'    => 'extra_image_1_300x200.png',
                'mimetype'    => 'image/png',
                'is_image'    => 1,
                'is_video'    => null,
                'filesize'    => 3546,
                'filehash'    => 'be767a55ebb67fa64a9b4efb0ddd424c10df7cdc',
                'url'         => null,
                'created_at'  => '2019-09-28 07:07:19',
                'updated_at'  => '2019-09-28 07:07:19',
                'deleted_at'  => null,
            ],
            1 => [
                'id'          => 3,
                'user_id'     => null,
                'title'       => 'main_image_300x200.png',
                'description' => '2019-09-28 07:12:02',
                'suffix'      => 'png',
                'path'        => '0b/ee0680114797c407d31cef43ba650b0c4a3e780b.png',
                'filename'    => 'main_image_300x200.png',
                'mimetype'    => 'image/png',
                'is_image'    => 1,
                'is_video'    => null,
                'filesize'    => 3359,
                'filehash'    => 'ee0680114797c407d31cef43ba650b0c4a3e780b',
                'url'         => null,
                'created_at'  => '2019-09-28 07:12:02',
                'updated_at'  => '2019-09-28 07:12:02',
                'deleted_at'  => null,
            ],
            2 => [
                'id'          => 4,
                'user_id'     => null,
                'title'       => 'extra_image_3_300x200.png',
                'description' => '2019-09-28 07:12:02',
                'suffix'      => 'png',
                'path'        => 'fb/b1500e1fdcd1320924f05fdca6bd99630a1037fb.png',
                'filename'    => 'extra_image_3_300x200.png',
                'mimetype'    => 'image/png',
                'is_image'    => 1,
                'is_video'    => null,
                'filesize'    => 4039,
                'filehash'    => 'b1500e1fdcd1320924f05fdca6bd99630a1037fb',
                'url'         => null,
                'created_at'  => '2019-09-28 07:12:02',
                'updated_at'  => '2019-09-28 07:12:02',
                'deleted_at'  => null,
            ],
            3 => [
                'id'          => 5,
                'user_id'     => null,
                'title'       => 'extra_image_2_300x200.png',
                'description' => '2019-09-28 07:12:02',
                'suffix'      => 'png',
                'path'        => '3b/4946510cda4730ff1516bb1ffd81a5dfeddd003b.png',
                'filename'    => 'extra_image_2_300x200.png',
                'mimetype'    => 'image/png',
                'is_image'    => 1,
                'is_video'    => null,
                'filesize'    => 3921,
                'filehash'    => '4946510cda4730ff1516bb1ffd81a5dfeddd003b',
                'url'         => null,
                'created_at'  => '2019-09-28 07:12:02',
                'updated_at'  => '2019-09-28 07:12:02',
                'deleted_at'  => null,
            ],
            4 => [
                'id'          => 8,
                'user_id'     => null,
                'title'       => 'main_banner_1000x100.png',
                'description' => '2019-09-28 07:37:04',
                'suffix'      => 'png',
                'path'        => '8d/b03ddf1c616a8832d9fc79f17597caa9e36e758d.png',
                'filename'    => 'main_banner_1000x100.png',
                'mimetype'    => 'image/png',
                'is_image'    => 1,
                'is_video'    => null,
                'filesize'    => 8013,
                'filehash'    => 'b03ddf1c616a8832d9fc79f17597caa9e36e758d',
                'url'         => null,
                'created_at'  => '2019-09-28 07:37:04',
                'updated_at'  => '2019-09-28 07:37:04',
                'deleted_at'  => null,
            ],
        ]);
    }
}
