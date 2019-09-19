<?php

namespace AlexStack\LaravelCms\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LaravelCmsSettings.
 *
 * @version September 5, 2019, 9:29 am NZST
 *
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property string param_name
 * @property int page_id
 * @property string param_value
 * @property string input_attribute
 * @property string abstract
 * @property string category
 * @property bool enabled
 */
class LaravelCmsSetting extends Model
{
    //use SoftDeletes;

    public $table = 'cms_settings';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'param_name',
        'page_id',
        'param_value',
        'input_attribute',
        'abstract',
        'category',
        'sort_value',
        'enabled',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'param_name'      => 'string',
        'page_id'         => 'integer',
        'param_value'     => 'string',
        'input_attribute' => 'string',
        'abstract'        => 'string',
        'category'        => 'string',
        'enabled'         => 'boolean',
        'sort_value'      => 'integer',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'id'         => 'required',
        'param_name' => 'required',
    ];

    public function __construct()
    {
        $this->table = config('laravel-cms.table_name.settings') ?? 'cms_settings';
    }
}
