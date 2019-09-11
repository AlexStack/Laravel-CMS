<?php

namespace AlexStack\LaravelCms\Helpers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsInquiry;
use AlexStack\LaravelCms\Models\LaravelCmsInquirySetting;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;

class LaravelCmsPluginInquiry
{
    public $helper;


    public function __construct()
    {
        $this->helper = new LaravelCmsHelper;
    }

    public function displayForm($page)
    {
        $settings = LaravelCmsInquirySetting::where('page_id', $page->id)->first();
        // LaravelCmsHelper::debug($page);
        $data['page']           = $page;
        $data['settings']       = $settings;
        if (!isset($settings->form_enabled) || !$settings->form_enabled) {
            return '<!-- Inquiry form disabled for this page -->';
        }
        $data['dynamic_inputs'] = self::dynamicInputs($settings, $page);
        $data['gg_recaptcha']   = (isset($settings->google_recaptcha_enabled) && $settings->google_recaptcha_enabled) ? GoogleRecaptcha::show($this->helper->s('google_recaptcha_site_key'), 'message', 'no_debug', ($settings->google_recaptcha_css_class ?? 'invisible google-recaptcha'), ($settings->google_recaptcha_no_tick_msg ?? 'Please tick the I\'m not robot checkbox')) : '';

        return view('laravel-cms::plugins.page-tab-inquiry-form.' . ($settings->form_layout ?? 'frontend-form-001'), $data);
    }


    static public function dynamicInputs($settings, $page)
    {

        $display_form_fields = (isset($settings->display_form_fields) && strpos($settings->display_form_fields, '|')) ? $settings->display_form_fields : 'first_name:Your Name:required | email | message:Message:required pattern="{5,5000}"  | submit';
        $fields_ary = explode('|', $display_form_fields);
        $input_str = '<input type="hidden" name="page_id" value="' . $page->id . '" />
            <input type="hidden" name="page_title" value="' . $page->title . '" />';
        foreach ($fields_ary as $field) {
            $f_ary = explode(':', trim($field));
            $f_ary[0] = trim($f_ary[0]);
            if (!isset($f_ary[1])) {
                $f_ary[1] = trim(ucwords(str_replace(['_', '-'], ' ', $f_ary[0])));
            } else {
                $f_ary[1] = trim($f_ary[1]);
            }
            if (isset($f_ary[2])) {
                $attr = trim($f_ary[2]);
            } else {
                $attr = '';
            }
            $input_type = ($f_ary[0] == 'email') ? 'email' : 'text';

            if ($f_ary[0] == 'message') {
                $input_str .= '<div class="form-group">
                <label for="message" class="label-message">' . $f_ary[1] . '</label>
                    <textarea class="form-control input-message" name="message" cols="50" rows="10" id="message" ' . $attr . '></textarea>
                </div>';
            } else if ($f_ary[0] == 'submit') {
                $input_str .= '<div id="laravel-cms-inquiry-form-results">
                        <div class="error_message"></div>
                        <button type="submit" class="btn btn-primary btn-submit">' . $f_ary[1] . '</button>
                    </div>';
            } else {
                $input_str .= '<div class="form-group">
                <label for="' . $f_ary[0] . '" class="label-' . $f_ary[0] . '">' . $f_ary[1] . '</label>
                    <input class="form-control input-' . $f_ary[0] . '" name="' . $f_ary[0] . '" type="' .  $input_type . '" id="' . $f_ary[0] . '" ' . $attr . '>
                </div>';
            }
        }
        return $input_str;
    }

    public function submitForm(Request $request)
    {
        //
        $form_data = $request->all();
        $form_data['ip'] = $request->ip();

        //LaravelCmsHelper::debug($form_data);

        $settings = LaravelCmsInquirySetting::where('page_id', $form_data['page_id'])->first();

        if ($settings->google_recaptcha_enabled && !GoogleRecaptcha::verify($this->helper->s('google_recaptcha_secret_key'), null)) {
            $result['success'] = false;
            $result['error_message'] = 'Verify Google Recaptcha failed';
            return json_encode($result);
        }

        $inquiry = new LaravelCmsInquiry;
        foreach ($inquiry->fillable as $field) {
            if (isset($form_data[$field])) {
                $inquiry->$field = trim($form_data[$field]);
            }
        }
        $inquiry->save();

        if ($inquiry) {
            $result['success'] = true;
            $result['success_content'] = $settings->success_content;
            $result['form_data'] = $form_data;
            if (trim(strip_tags($result['success_content'])) == '') {
                $result['success_content'] = '<b>Thank you for submit the inquiry, we will get back to you ASAP.</b>';
            }
        } else {
            $result['success'] = false;
        }
        return json_encode($result);
    }



    public function search(Request $request)
    {
        $user = $this->helper->hasPermission();

        $form_data = $request->all();
        $form_data['html_content'] = $request->ip();
        $form_data['success'] = true;

        $form_data['user'] = $user->id;
        return json_encode($form_data);

        //LaravelCmsHelper::debug($form_data);
    }

    static public function edit($page_id, $page = null)
    {
        $s = LaravelCmsInquirySetting::where('page_id', $page_id)->first();

        return $s;
    }


    static public function store($form_data, $page = null)
    {
        return self::update($form_data, $page);
    }

    static public function update($form_data, $page = null)
    {
        $setting_data = $form_data;
        $setting_data['page_id']    = $page->id;
        $setting_data['id']         = null;

        $s = LaravelCmsInquirySetting::updateOrCreate(
            ['page_id' => $page->id],
            $setting_data
        );

        return $s;
    }

    static public function destroy($page_id)
    {

        $rs = LaravelCmsInquirySetting::where('page_id', $page_id)->delete();

        //LaravelCmsHelper::debug($rs);

        return $rs;
    }
}
