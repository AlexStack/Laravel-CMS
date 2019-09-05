<?php

namespace AlexStack\LaravelCms\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LaravelCmsSettings
 * @package App\Models
 * @version September 5, 2019, 9:29 am NZST
 *
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property string param_name
 * @property integer page_id
 * @property string param_value
 * @property string input_type
 * @property string abstract
 * @property string category
 * @property boolean enabled
 */
class LaravelCmsSetting extends Model
{
    //use SoftDeletes;

    public $table = 'laravelcms_settings';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'param_name',
        'page_id',
        'param_value',
        'input_type',
        'abstract',
        'category',
        'enabled'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'param_name' => 'string',
        'page_id' => 'integer',
        'param_value' => 'string',
        'input_type' => 'string',
        'abstract' => 'string',
        'category' => 'string',
        'enabled' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
        'enabled' => 'required'
    ];

    public function __construct()
    {
        $this->table = config('laravel-cms.table_name.settings') ?? 'cms_settings';
    }
}
