<?php

namespace AlexStack\LaravelCms\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LaravelCmsInquirySetting.
 *
 * @version August 30, 2019, 10:35 am NZST
 *
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property string form_layout
 * @property int page_id
 * @property int default_setting_id
 * @property string form_layout_filename
 * @property string display_form_fields
 * @property string mail_from
 * @property string mail_to
 * @property string mail_subject
 * @property string success_title
 * @property string success_content
 * @property string google_recaptcha_site_key
 * @property string google_recaptcha_secret_key
 * @property string google_recaptcha_css_class
 * @property string google_recaptcha_no_tick_msg
 * @property bool google_recaptcha_enabled
 * @property bool form_enabled
 */
class LaravelCmsInquirySetting extends Model
{
    //use SoftDeletes;

    public $table = 'cms_inquiry_settings';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'form_layout',
        'page_id',
        'default_setting_id',
        'form_layout_filename',
        'display_form_fields',
        'mail_from',
        'mail_to',
        'mail_subject',
        'success_title',
        'success_content',
        'google_recaptcha_site_key',
        'google_recaptcha_secret_key',
        'google_recaptcha_css_class',
        'google_recaptcha_no_tick_msg',
        'google_recaptcha_enabled',
        'form_enabled',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                           => 'integer',
        'form_layout'                  => 'string',
        'page_id'                      => 'integer',
        'default_setting_id'           => 'integer',
        'form_layout_filename'         => 'string',
        'display_form_fields'          => 'string',
        'mail_from'                    => 'string',
        'mail_to'                      => 'string',
        'mail_subject'                 => 'string',
        'success_title'                => 'string',
        'success_content'              => 'string',
        'google_recaptcha_site_key'    => 'string',
        'google_recaptcha_secret_key'  => 'string',
        'google_recaptcha_css_class'   => 'string',
        'google_recaptcha_no_tick_msg' => 'string',
        'google_recaptcha_enabled'     => 'boolean',
        'form_enabled'                 => 'boolean',
    ];

    public function __construct()
    {
        $this->table = config('laravel-cms.table_name.inquiry_settings') ?? 'cms_inquiry_settings';
    }

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'id'                       => 'required',
        'google_recaptcha_enabled' => 'required',
        'form_enabled'             => 'required',
    ];
}
