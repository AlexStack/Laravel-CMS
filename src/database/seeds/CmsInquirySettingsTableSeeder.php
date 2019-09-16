<?php

namespace AlexStack\LaravelCms;

use Illuminate\Database\Seeder;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use App;

class CmsInquirySettingsTableSeeder extends Seeder
{
    public $helper;
    private $config;
    private $table_name;
    public function __construct()
    {
        $this->config = include(base_path('config/laravel-cms.php'));
        $this->table_name = $this->config['table_name']['inquiry_settings'];
        $this->helper = new LaravelCmsHelper;
        App::setLocale($this->config['template']['backend_language']);
    }


    public function run()
    {


        \DB::table($this->table_name)->delete();

        \DB::table($this->table_name)->insert(array(
            0 =>
            array(
                'id' => 1,
                'form_layout' => NULL,
                'page_id' => 1,
                'default_setting_id' => NULL,
                'form_layout_filename' => NULL,
                'display_form_fields' => '[
{ "field" : "first_name",  "text" : "' . $this->helper->t('your,name') . '", "attr" :"required"},
{ "field" : "email",  "text" : "' . $this->helper->t('email') . '", "attr" :""},
{"field" : "message",  "text" : "' . $this->helper->t('message') . '", "attr" :"required"},
{"field" : "submit",  "text" : "' . $this->helper->t('submit') . '", "attr" :""}
]',
                'mail_from' => NULL,
                'mail_to' => NULL,
                'mail_subject' => NULL,
                'success_title' => '',
                'success_content' => '',
                'google_recaptcha_site_key' => NULL,
                'google_recaptcha_secret_key' => NULL,
                'google_recaptcha_css_class' => 'form-group mb-4 show-inline-badge',
                'google_recaptcha_no_tick_msg' => 'v3',
                'google_recaptcha_enabled' => 0,
                'form_enabled' => 0,
                'created_at' => '2019-09-14 12:01:26',
                'updated_at' => '2019-09-16 10:11:46',
                'deleted_at' => NULL,
            ),
        ));
    }
}
