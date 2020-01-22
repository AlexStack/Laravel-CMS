<?php

namespace AlexStack\LaravelCms\Helpers;

use AlexStack\LaravelCms\Models\LaravelCmsInquiry;
use AlexStack\LaravelCms\Models\LaravelCmsInquirySetting;
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LaravelCmsPluginInquiry
{
    public $helper;
    private $user = null;

    public function __construct()
    {
        $this->helper = new LaravelCmsHelper();
    }

    public function index()
    {
        if (! $this->user) {
            $this->user = $this->helper->hasPermission();
        }
        $keyword           = request()->keyword;
        $page_id           = request()->page_id;
        $data['inquiries'] = LaravelCmsInquiry::when($page_id, function ($query) use ($page_id) {
            return $query->where('page_id', $page_id);
        })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->where('message', 'like', '%'.trim($keyword).'%')
                        ->orWhere('last_name', 'like', '%'.$keyword.'%')
                        ->orWhere('first_name', 'like', '%'.trim($keyword).'%')
                        ->orWhere('company_name', 'like', '%'.trim($keyword).'%');
                });
            })
            ->orderBy('id', 'desc')
            //->toSql();
            ->paginate($this->helper->s('inquiry.number_per_page') ?? 20);
        //$this->helper->debug($data['inquiries']);

        $data['helper'] = $this->helper;

        //$this->helper->debug($data['inquiries']->toArray());

        return view($this->helper->bladePath('page-tab-inquiry-form.inquiry-list', 'plugins'), $data);
    }

    public function getFormSettings($page_id)
    {
        $settings = LaravelCmsInquirySetting::where('page_id', $page_id)->first();

        if (isset($settings->form_enabled) && ! $settings->form_enabled) {
            return null;
        }
        $default_setting_id = isset($settings->default_setting_id) ? $settings->default_setting_id : $this->helper->s('inquiry.default_setting_id');
        if ($default_setting_id) {
            $default_settings = LaravelCmsInquirySetting::where('id', $default_setting_id)->first();
            if (! $default_settings) {
                return null;
            } elseif (! $settings) {
                // in case there is no record in the inquires table
                $settings = $default_settings;
            }
        }
        // dd($settings);
        $new_settings = clone $settings;
        foreach ($settings->toArray() as $key => $value) {
            if (null === $new_settings[$key]) {
                $new_settings[$key] = $default_settings[$key];
            } elseif ('success_content' == $key && strlen(trim(strip_tags($new_settings[$key]))) < 4) {
                $new_settings[$key] = $default_settings[$key];
            }
        }

        return $new_settings;
    }

    public function displayForm($page)
    {
        $settings = $this->getFormSettings($page->id);

        if (! $settings) {
            return '<!-- Inquiry form disabled for this page -->';
        }

        $data['page']     = $page;
        $data['settings'] = $settings;

        $data['dynamic_inputs'] = $this->dynamicInputs($settings, $page);
        $data['gg_recaptcha']   = (isset($settings->google_recaptcha_enabled) && $settings->google_recaptcha_enabled) ? GoogleRecaptcha::show($this->helper->s('inquiry.google_recaptcha_site_key'), 'message', 'no_debug', ($settings->google_recaptcha_css_class ?? 'form-group google-recaptcha'), ($settings->google_recaptcha_no_tick_msg ?? 'Please tick the I\'m not robot checkbox')) : '';

        return view($this->helper->bladePath('page-tab-inquiry-form.'.($settings->form_layout ?? 'frontend-form-001'), 'plugins'), $data);
    }

    public function dynamicInputs($settings, $page)
    {
        $fields_obj = json_decode($settings->display_form_fields);

        $input_str = '<input type="hidden" name="page_id" value="'.$page->id.'" />
            <input type="hidden" name="page_title" value="'.$page->title.'" />';
        foreach ($fields_obj as $f) {
            $input_type = ('email' == $f->field) ? 'email' : 'text';

            if ('message' == $f->field) {
                $input_str .= '<div class="form-group">
                <label for="message" class="label-message">'.$f->text.'</label>
                    <textarea class="form-control input-message" name="message" cols="50" rows="10" id="message" '.$f->attr.'></textarea>
                </div>';
            } elseif ('submit' == $f->field) {
                $input_str .= '<div id="laravel-cms-inquiry-form-results">
                        <div class="error_message"></div>
                        <button type="submit" class="btn btn-primary btn-submit">'.$f->text.'</button>
                    </div>';
            } else {
                $input_str .= '<div class="form-group">
                <label for="'.$f->field.'" class="label-'.$f->field.'">'.$f->text.'</label>
                    <input class="form-control input-'.$f->field.'" name="'.$f->field.'" type="'.$input_type.'" id="'.$f->field.'" '.$f->attr.'>
                </div>';
            }
        }

        return $input_str;
    }

    public function submitForm(Request $request)
    {
        $form_data       = $request->all();
        $form_data['ip'] = $request->ip();

        //LaravelCmsHelper::debug($form_data);
        $settings = $this->getFormSettings($form_data['page_id']);

        if ($settings->google_recaptcha_enabled && ! GoogleRecaptcha::verify($this->helper->s('inquiry.google_recaptcha_secret_key'), null)) {
            $result['success']       = false;
            $result['error_message'] = 'Verify Google Recaptcha failed.';

            return json_encode($result);
        }

        // inquiry_verify_str for basic spam check
        if (! isset($form_data['inquiry_verify_str']) || ! strpos($form_data['inquiry_verify_str'], '-')) {
            $result['success']       = false;
            $result['error_message'] = 'Verify inquiry_verify_str failed.';

            return json_encode($result);
        } else {
            $verify_str_ary = explode('-', $form_data['inquiry_verify_str']);
            if (3 != count($verify_str_ary) || $verify_str_ary[0] != $form_data['page_id'] || $verify_str_ary[1] < 5 || $verify_str_ary[2] < 4) {
                $result['success']       = false;
                $result['error_message'] = 'Verify inquiry_verify_str failed! Message too short?';

                return json_encode($result);
            }
        }

        $inquiry = new LaravelCmsInquiry();
        foreach ($inquiry->fillable as $field) {
            if (isset($form_data[$field])) {
                $inquiry->$field = trim($form_data[$field]);
            }
        }
        $inquiry->save();

        if ($inquiry) {
            $result['success']         = true;
            $result['success_content'] = $settings->success_content;
            $result['form_data']       = $form_data;
            if ('' == trim(strip_tags($result['success_content']))) {
                $result['success_content'] = $this->helper->t('submit_success_content') ?? '<b>Thank you for submit the inquiry, we will get back to you ASAP.</b>';
            }
        } else {
            $result['success'] = false;
        }

        // send email
        if (strpos($form_data['email'], '@') && strpos($settings->mail_to, '@') && '' != trim($settings->mail_subject) && $from_email = config('mail.from.address')) {
            // send email to the inquiry submitter
            $submitter_name = $inquiry->first_name.' '.$inquiry->last_name;
            $this->sendNow([
                'to'        => trim($form_data['email']),
                'to_name'   => $submitter_name,
                'reply_to'  => $settings->mail_to,
                'from'      => $from_email,
                'from_name' => $settings->mail_to,
                'subject'   => $inquiry->first_name.','.$settings->mail_subject,
                'body'      => $result['success_content'].'
                <hr/>
                '.nl2br($form_data['message']),
                //'language'  => 'en',
            ]);
            // send notification email to the admin

            $this->sendNow([
                'to'        => $settings->mail_to,
                'to_name'   => $settings->mail_to,
                'reply_to'  => trim($form_data['email']),
                'from'      => $from_email,
                'from_name' => $submitter_name,
                'subject'   => $this->helper->t('got_inquiry_from', ['name'=>$submitter_name]).'('.$this->helper->s('site_name').')',
                'body'      => $this->helper->t('got_inquiry_from', ['name'=>$submitter_name]).'
                <hr/>
                '.nl2br($form_data['message']),
                //'language'  => 'en',
            ]);
        }

        return json_encode($result);
    }

    // public function search(Request $request)
    // {
    //     $user = $this->helper->hasPermission();

    //     $form_data                 = $request->all();
    //     $form_data['html_content'] = $request->ip();
    //     $form_data['success']      = true;
    //     $form_data['user']         = $user->id;

    //     return json_encode($form_data);
    // }

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
        $setting_data            = $form_data;
        $setting_data['page_id'] = $page->id;
        $setting_data['id']      = null;

        if (isset($form_data['display_form_fields']) && '' != trim($form_data['display_form_fields']) && ! $this->helper->correctJsonFormat($form_data['display_form_fields'], true)) {
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

        if ('json' == request()->response_type) {
            $result['success']         = $rs;
            $result['success_content'] = 'Inquire id '.$id.' deleted';
            $result['error_message']   = 'Delete inquire id '.$id.' failed!';

            return response()->json($result);
        }

        return $rs;
    }

    public function sendNow($data)
    {
        //$this->helper->debug($data);
        //return false; // uncomment for fast debug without send email
        try {
            $mail_html_tpl  = $this->helper->bladePath('page-tab-inquiry-form.email_notification', 'plugins');
            $mail_plain_tpl = $this->helper->bladePath('page-tab-inquiry-form.email_notification_plaintext', 'plugins');

            //$this->helper->debug([$mail_html_tpl, $this->helper]);

            Mail::send(
                [$mail_html_tpl, $mail_plain_tpl],
                $data,
                function ($m) use ($data) {
                    if (isset($data['reply_to'])) {
                        $m->to($data['to'])
                            ->from($data['from'], $data['from_name'])
                            ->replyTo($data['reply_to'])
                            ->subject($data['subject']);
                    } else {
                        $m->to($data['to'])
                            ->subject($data['subject']);
                    }
                    //$m->later(now()->addSeconds(5));
                }
            );

            return true;
        } catch (\Exception $e) {
            echo $e->getMessage().' sent-to:'.$data['to'];

            return false;
        }

        return false;
    }

    public function show($id)
    {
        if ('yes' == request()->go_setting_section) {
            $setting = LaravelCmsInquirySetting::find($id);

            return redirect()->route('LaravelCmsAdminPages.edit', [
                'page'                  => $setting->page_id,
                'switch_nav_tab'        => 'inquiry',
                'show_advanced_settings'=> 'yes',
            ]);
        }
    }
}
