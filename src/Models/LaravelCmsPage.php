<?php

namespace AlexStack\LaravelCms\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LaravelCmsPage.
 *
 * @version August 13, 2019, 10:57 am NZST
 *
 * @property \AlexStack\LaravelCms\Models\LaravelCmsFile mainImage
 * @property \AlexStack\LaravelCms\Models\LaravelCmsFile mainBanner
 * @property \AlexStack\LaravelCms\Models\LaravelCmsFile extraImage
 * @property \AlexStack\LaravelCms\Models\LaravelCmsFile extraImage2
 * @property \App\Models\User user
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property int user_id
 * @property int parent_id
 * @property bool menu_enabled
 * @property string status
 * @property string title
 * @property string menu_title
 * @property string slug
 * @property string template_file
 * @property string meta_title
 * @property string meta_keywords
 * @property string meta_description
 * @property string abstract
 * @property int main_banner
 * @property int main_image
 * @property string sub_content
 * @property string main_content
 * @property int sort_value
 * @property int view_counts
 * @property string tags
 * @property int extra_image
 * @property string extra_text
 * @property string extra_content
 * @property int extra_image_2
 * @property string extra_text_2
 * @property string extra_content_2
 */
class LaravelCmsPage extends Model
{
    //use SoftDeletes;

    public $table = 'cms_pages';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'user_id',
        'parent_id',
        'menu_enabled',
        'status',
        'title',
        'menu_title',
        'slug',
        'template_file',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'abstract',
        'main_banner',
        'main_image',
        'sub_content',
        'main_content',
        'sort_value',
        'view_counts',
        'tags',
        'extra_image_1',
        'extra_text_1',
        'extra_content_1',
        'extra_image_2',
        'extra_text_2',
        'extra_content_2',
        'extra_image_3',
        'extra_text_3',
        'extra_content_3',
        'redirect_url',
        'file_data',
        'special_text',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'user_id'          => 'integer',
        'parent_id'        => 'integer',
        'menu_enabled'     => 'boolean',
        'status'           => 'string',
        'title'            => 'string',
        'menu_title'       => 'string',
        'slug'             => 'string',
        'template_file'    => 'string',
        'meta_title'       => 'string',
        'meta_keywords'    => 'string',
        'meta_description' => 'string',
        'abstract'         => 'string',
        'main_banner'      => 'integer',
        'main_image'       => 'integer',
        'sub_content'      => 'string',
        'main_content'     => 'string',
        'sort_value'       => 'integer',
        'view_counts'      => 'integer',
        'tags'             => 'string',
        'extra_image_1'    => 'integer',
        'extra_text_1'     => 'string',
        'extra_content_1'  => 'string',
        'extra_image_2'    => 'integer',
        'extra_text_2'     => 'string',
        'extra_content_2'  => 'string',
        'extra_image_3'    => 'integer',
        'extra_text_3'     => 'string',
        'extra_content_3'  => 'string',
        'redirect_url'     => 'string',
        'file_data'        => 'string',
        'special_text'     => 'string',
    ];

    public function __construct()
    {
        $this->table = config('laravel-cms.table_name.pages') ?? 'cms_pages';
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function mainImage()
    {
        return $this->belongsTo(\AlexStack\LaravelCms\Models\LaravelCmsFile::class, 'main_image');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function mainBanner()
    {
        return $this->belongsTo(\AlexStack\LaravelCms\Models\LaravelCmsFile::class, 'main_banner');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function extraImage1()
    {
        return $this->belongsTo(\AlexStack\LaravelCms\Models\LaravelCmsFile::class, 'extra_image_1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function extraImage2()
    {
        return $this->belongsTo(\AlexStack\LaravelCms\Models\LaravelCmsFile::class, 'extra_image_2');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function extraImage3()
    {
        return $this->belongsTo(\AlexStack\LaravelCms\Models\LaravelCmsFile::class, 'extra_image_3');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(\AlexStack\LaravelCms\Models\LaravelCmsPage::class, 'parent_id')->with('parent:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled');
    }

    public function children()
    {
        return $this->hasMany(\AlexStack\LaravelCms\Models\LaravelCmsPage::class, 'parent_id', 'id')->with('children')->orderBy('sort_value', 'desc')->orderBy('id', 'desc');
    }

    public function children4list()
    {
        return $this->hasMany(\AlexStack\LaravelCms\Models\LaravelCmsPage::class, 'parent_id', 'id')->with('children4list:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled')->orderBy('sort_value', 'desc')->orderBy('id', 'desc');
    }

    public function menus()
    {
        return $this->hasMany(\AlexStack\LaravelCms\Models\LaravelCmsPage::class, 'parent_id', 'id')->where('menu_enabled', 1)->with('menus:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled')->orderBy('sort_value', 'desc')->orderBy('id', 'desc');
    }
}
