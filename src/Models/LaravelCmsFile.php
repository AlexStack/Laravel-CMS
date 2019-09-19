<?php

namespace AlexStack\LaravelCms\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LaravelCmsFile.
 *
 * @version August 13, 2019, 10:57 am NZST
 *
 * @property \Illuminate\Database\Eloquent\Collection cmsPages
 * @property \Illuminate\Database\Eloquent\Collection cmsPages
 * @property \Illuminate\Database\Eloquent\Collection cmsPages
 * @property \Illuminate\Database\Eloquent\Collection cmsPages
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property int user_id
 * @property string title
 * @property string description
 * @property string suffix
 * @property string path
 * @property string filename
 * @property string mimetype
 * @property bool is_image
 * @property bool is_video
 * @property int filesize
 * @property string url
 */
class LaravelCmsFile extends Model
{
    //use SoftDeletes;

    public $table = 'cms_files';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
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
        'url',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'title'       => 'string',
        'description' => 'string',
        'suffix'      => 'string',
        'path'        => 'string',
        'filename'    => 'string',
        'mimetype'    => 'string',
        'is_image'    => 'boolean',
        'is_video'    => 'boolean',
        'filesize'    => 'integer',
        'filehash'    => 'string',
        'url'         => 'string',
    ];

    public function __construct()
    {
        $this->table = config('laravel-cms.table_name.files') ?? 'cms_files';
    }

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function pages()
    {
        return $this->hasMany(\AlexStack\LaravelCms\Models\LaravelCmsPage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
