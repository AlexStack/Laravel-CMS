<?php

namespace AlexStack\LaravelCms;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use App;
use Illuminate\Database\Seeder;

class CmsInquirySettingsTableSeeder extends Seeder
{
    public $helper;
    private $config;
    private $table_name;

    public function __construct()
    {
        $this->config     = include base_path('config/laravel-cms.php');
        $this->table_name = $this->config['table_name']['inquiry_settings'];
        $this->helper     = new LaravelCmsHelper();
        App::setLocale($this->config['template']['backend_language']);
    }

    public function run()
    {
        \DB::table($this->table_name)->delete();

        \DB::table($this->table_name)->insert([
            0 => [
                'form_layout'          => 'frontend-form-001',
                'page_id'              => 1,
                'default_setting_id'   => null,
                'form_layout_filename' => null,
                'display_form_fields'  => '[
{ "field" : "first_name",  "text" : "'.$this->helper->t('your,name').'", "attr" :"required"},
{ "field" : "email",  "text" : "'.$this->helper->t('email').'", "attr" :""},
{"field" : "message",  "text" : "'.$this->helper->t('message').'", "attr" :"required"},
{"field" : "submit",  "text" : "'.$this->helper->t('submit').'", "attr" :""}
]',
                'mail_from'                    => null,
                'mail_to'                      => config('mail.from.address'),
                'mail_subject'                 => null,
                'success_title'                => '',
                'success_content'              => '',
                'google_recaptcha_site_key'    => null,
                'google_recaptcha_secret_key'  => null,
                'google_recaptcha_css_class'   => 'form-group mb-4 show-inline-badge',
                'google_recaptcha_no_tick_msg' => 'v3',
                'google_recaptcha_enabled'     => 0,
                'form_enabled'                 => 0,
            ],
            1 => [
                'form_layout'                  => null,
                'page_id'                      => 2,
                'default_setting_id'           => null,
                'form_layout_filename'         => null,
                'display_form_fields'          => null,
                'mail_from'                    => null,
                'mail_to'                      => null,
                'mail_subject'                 => null,
                'success_title'                => '',
                'success_content'              => '',
                'google_recaptcha_site_key'    => null,
                'google_recaptcha_secret_key'  => null,
                'google_recaptcha_css_class'   => null,
                'google_recaptcha_no_tick_msg' => null,
                'google_recaptcha_enabled'     => null,
                'form_enabled'                 => 1,
            ],
        ]);
    }
}
