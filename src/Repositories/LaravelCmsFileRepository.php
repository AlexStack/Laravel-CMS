<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Repositories\BaseRepository;

/**
 * Class LaravelCmsFileRepository
 * @package App\Repositories
 * @version September 18, 2019, 4:56 am UTC
 */

class LaravelCmsFileRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'title',
        'description',
        'suffix',
        'path',
        'filename',
        'mimetype',
        'is_image',
        'is_video',
        'filesize',
        'filehash',
        'url'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LaravelCmsFile::class;
    }
}
