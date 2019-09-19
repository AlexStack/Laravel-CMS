<?php

namespace AlexStack\LaravelCms\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LaravelCmsInquiry.
 *
 * @version August 30, 2019, 10:36 am NZST
 *
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property \Illuminate\Database\Eloquent\Collection
 * @property int parent_id
 * @property int page_id
 * @property string first_name
 * @property string last_name
 * @property string company_name
 * @property string email
 * @property string phone
 * @property string mobile
 * @property string street
 * @property string address
 * @property string postal_code
 * @property string city
 * @property string state
 * @property string country
 * @property string website
 * @property string locale
 * @property string page_title
 * @property string category
 * @property string my_date
 * @property string ip
 * @property string subject
 * @property string message
 * @property string page_url
 * @property string admin_comment
 * @property string status
 * @property bool sort_value
 * @property string extra_data_1
 * @property string extra_data_2
 * @property string extra_data_3
 * @property string extra_data_4
 * @property string extra_data_5
 */
class LaravelCmsInquiry extends Model
{
    //use SoftDeletes;

    public $table = 'cms_inquiries';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'parent_id',
        'page_id',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'mobile',
        'street',
        'address',
        'postal_code',
        'city',
        'state',
        'country',
        'website',
        'locale',
        'page_title',
        'category',
        'my_date',
        'ip',
        'subject',
        'message',
        'page_url',
        'admin_comment',
        'status',
        'sort_value',
        'extra_data_1',
        'extra_data_2',
        'extra_data_3',
        'extra_data_4',
        'extra_data_5',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'parent_id'     => 'integer',
        'page_id'       => 'integer',
        'first_name'    => 'string',
        'last_name'     => 'string',
        'company_name'  => 'string',
        'email'         => 'string',
        'phone'         => 'string',
        'mobile'        => 'string',
        'street'        => 'string',
        'address'       => 'string',
        'postal_code'   => 'string',
        'city'          => 'string',
        'state'         => 'string',
        'country'       => 'string',
        'website'       => 'string',
        'locale'        => 'string',
        'page_title'    => 'string',
        'category'      => 'string',
        'my_date'       => 'string',
        'ip'            => 'string',
        'subject'       => 'string',
        'message'       => 'string',
        'page_url'      => 'string',
        'admin_comment' => 'string',
        'status'        => 'string',
        'sort_value'    => 'boolean',
        'extra_data_1'  => 'string',
        'extra_data_2'  => 'string',
        'extra_data_3'  => 'string',
        'extra_data_4'  => 'string',
        'extra_data_5'  => 'string',
    ];

    public function __construct()
    {
        $this->table = config('laravel-cms.table_name.inquiries') ?? 'cms_inquiries';
    }

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'id'         => 'required',
        'sort_value' => 'required',
    ];
}
