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
    private $user = null;


    public function __construct()
    {
        $this->helper = new LaravelCmsHelper;
    }

    public function index()
    {
        if (!$this->user) {
            $this->user = $this->helper->hasPermission();
        }
        $keyword = request()->keyword;
        $page_id = request()->page_id;
        $data['inquiries'] = LaravelCmsInquiry::when($page_id, function ($query) use ($page_id) {
            return $query->where('page_id', $page_id);
        })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->where('message', 'like', '%' . trim($keyword) . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%')
                        ->orWhere('first_name', 'like', '%' . trim($keyword) . '%')
                        ->orWhere('company_name', 'like', '%' . trim($keyword) . '%');
                });
            })
            ->orderBy('id', 'desc')
            //->toSql();
            ->paginate($this->helper->s('inquiry.number_per_page') ?? 20);
        //$this->helper->debug($data['inquiries']);

        $data['helper'] = $this->helper;


        //$this->helper->debug($data['inquiries']->toArray());

        return view('laravel-cms::plugins.page-tab-inquiry-form.inquiry-list', $data);
    }

    public function getFormSettings($page_id)
    {

        $settings = LaravelCmsInquirySetting::where('page_id', $page_id)->first();

        if (!isset($settings->form_enabled) || !$settings->form_enabled) {
            return null;
        }
        $default_setting_id = $settings->default_setting_id ?? $this->helper->s('inquiry.default_setting_id');
        if ($default_setting_id) {
            $default_settings = LaravelCmsInquirySetting::where('id', $default_setting_id)->first();
        }
        $new_settings = clone $settings;
        foreach ($settings->toArray() as $key => $value) {
            if ($new_settings[$key] === null) {
                $new_settings[$key] = $default_settings[$key];
            } else if ($key == 'success_content' && strlen(trim(strip_tags($new_settings[$key]))) < 4) {
                $new_settings[$key] = $default_settings[$key];
            }
        }

        //$this->helper->debug([$settings->toArray(), $default_settings->toArray(), $new_settings->toArray()]);

        return $new_settings;
    }

    public function displayForm($page)
    {
        $settings = $this->getFormSettings($page->id);
        // LaravelCmsHelper::debug($page);

        if (!$settings) {
            return '<!-- Inquiry form disabled for this page -->';
        }

        $data['page']           = $page;
        $data['settings']       = $settings;

        $data['dynamic_inputs'] = $this->dynamicInputs($settings, $page);
        $data['gg_recaptcha']   = (isset($settings->google_recaptcha_enabled) && $settings->google_recaptcha_enabled) ? GoogleRecaptcha::show($this->helper->s('google_recaptcha_site_key'), 'message', 'no_debug', ($settings->google_recaptcha_css_class ?? 'invisible google-recaptcha'), ($settings->google_recaptcha_no_tick_msg ?? 'Please tick the I\'m not robot checkbox')) : '';

        return view('laravel-cms::plugins.page-tab-inquiry-form.' . ($settings->form_layout ?? 'frontend-form-001'), $data);
    }


    public function dynamicInputs($settings, $page)
    {

        // $display_form_fields = (isset($settings->display_form_fields) && strpos($settings->display_form_fields, '|')) ? $settings->display_form_fields : 'first_name:' . $this->helper->t('your_name') . ':required | email:' . $this->helper->t('email') . ' | message:' . $this->helper->t('message') . ':required pattern="{5,5000}"  | submit:' . $this->helper->t('submit') . '';

        $fields_obj = json_decode($settings->display_form_fields);
        //$this->helper->debug($fields_obj);

        // $fields_ary = explode('|', $display_form_fields);
        $input_str = '<input type="hidden" name="page_id" value="' . $page->id . '" />
            <input type="hidden" name="page_title" value="' . $page->title . '" />';
        foreach ($fields_obj as $f) {
            // $f_ary = explode(':', trim($field));
            // $f_ary[0] = trim($f_ary[0]);
            // if (!isset($f_ary[1])) {
            //     $f_ary[1] = trim(ucwords(str_replace(['_', '-'], ' ', $f_ary[0])));
            // } else {
            //     $f_ary[1] = trim($f_ary[1]);
            // }
            // if (isset($f_ary[2])) {
            //     $attr = trim($f_ary[2]);
            // } else {
            //     $attr = '';
            // }
            $input_type = ($f->field == 'email') ? 'email' : 'text';

            if ($f->field == 'message') {
                $input_str .= '<div class="form-group">
                <label for="message" class="label-message">' . $f->text . '</label>
                    <textarea class="form-control input-message" name="message" cols="50" rows="10" id="message" ' . $f->attr  . '></textarea>
                </div>';
            } else if ($f->field == 'submit') {
                $input_str .= '<div id="laravel-cms-inquiry-form-results">
                        <div class="error_message"></div>
                        <button type="submit" class="btn btn-primary btn-submit">' . $f->text  . '</button>
                    </div>';
            } else {
                $input_str .= '<div class="form-group">
                <label for="' . $f->field . '" class="label-' . $f->field . '">' . $f->text  . '</label>
                    <input class="form-control input-' . $f->field . '" name="' . $f->field . '" type="' .  $input_type . '" id="' . $f->field . '" ' . $f->attr . '>
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
        $settings = $this->getFormSettings($form_data['page_id']);

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

    public function edit($page_id, $page = null)
    {
        $s = LaravelCmsInquirySetting::where('page_id', $page_id)->first();

        return $s;
    }


    public function store($form_data, $page = null)
    {
        return $this->update($form_data, $page);
    }

    public function update($form_data, $page = null)
    {
        $setting_data = $form_data;
        $setting_data['page_id']    = $page->id;
        $setting_data['id']         = null;

        if (trim($form_data['display_form_fields']) != '' && !$this->helper->correctJsonFormat($form_data['display_form_fields'], true)) {
            exit(sprintf('$this->wrong_json_format_str', 'Param Value'));
        }

        $s = LaravelCmsInquirySetting::updateOrCreate(
            ['page_id' => $page->id],
            $setting_data
        );

        return $s;
    }

    public function destroy($id)
    {

        $rs = LaravelCmsInquiry::where('id', $id)->delete();

        //LaravelCmsHelper::debug($rs);
        if (request()->result_type == 'json') {
            $result['success'] = $rs;
            $result['success_content'] = 'Inquire id ' . $id . ' deleted';
            $result['error_message'] = 'Delete inquire id ' . $id . ' failed!';

            return json_encode($result);
        }

        return $rs;
    }
}
