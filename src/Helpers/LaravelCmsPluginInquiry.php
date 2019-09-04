<?php

namespace AlexStack\LaravelCms\Helpers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsInquiry;
use AlexStack\LaravelCms\Models\LaravelCmsInquirySetting;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
use App\Http\Controllers\Controller;

class LaravelCmsPluginInquiry
{
    //private $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    // }

    // public function checkUser()
    // {
    //     // return true;
    //     $this->user = Auth::user();
    //     if (!$this->user) {
    //         exit('Can not get user info. Please logout and re-login again ');
    //     }

    //     if (!in_array($this->user->id, config('laravel-cms.admin_id_ary'))) {
    //         exit('Access denied for user id ' . $this->user->id);
    //     }
    // }

    // public function extraPageTabs()
    // {
    //     $app_view_dir = base_path('resources/views/vendor/laravel-cms') . '/plugins';

    //     if (!file_exists($app_view_dir)) {
    //         $app_view_dir = dirname(__FILE__, 3) . '/resources/views/plugins';
    //     }
    //     $dirs = glob($app_view_dir . "/page-tab-*");
    //     //LaravelCmsHelper::debug($dirs);
    //     $option_ary = [];
    //     foreach ($dirs as $d) {
    //         if (file_exists($d . '/config.php')) {
    //             $config_ary = include($d . '/config.php');
    //             if (isset($config_ary['blade_file']) && file_exists($d . '/' . $config_ary['blade_file']  . '.blade.php') && $config_ary['enabled']) {
    //                 $config_ary['blade_dir'] = basename($d);
    //                 $option_ary[] = $config_ary;
    //             }
    //         }
    //     }

    //     //LaravelCmsHelper::debug($option_ary);
    //     return $option_ary;
    // }

    // public function templateFileOption()
    // {
    //     $app_view_dir = base_path('resources/views/vendor/laravel-cms') . '/' . config('laravel-cms.template_frontend_dir');

    //     if (!file_exists($app_view_dir)) {
    //         $app_view_dir = dirname(__FILE__, 3) . '/resources/views/' . config('laravel-cms.template_frontend_dir');
    //     }
    //     $files = glob($app_view_dir . "/*.blade.php");
    //     foreach ($files as $f) {
    //         $k = str_replace('.blade.php', '', basename($f));
    //         $option_ary[$k] = ucwords(str_replace(['-', '_', '.'], ' ', $k));
    //     }
    //     if (file_exists($app_view_dir . '/config.php')) {
    //         $config_ary = include($app_view_dir . '/config.php');
    //         if (isset($config_ary['blade_files'])) {
    //             $option_ary = $config_ary['blade_files'] + $option_ary;
    //         }
    //     }

    //     return $option_ary;
    // }

    static public function displayForm($page)
    {
        $settings = LaravelCmsInquirySetting::where('page_id', $page->id)->first();
        // LaravelCmsHelper::debug($page);
        $data['page']           = $page;
        $data['settings']       = $settings;
        $data['dynamic_inputs'] = self::dynamicInputs($settings, $page);
        $data['gg_recaptcha']   = $settings->google_recaptcha_enabled ? GoogleRecaptcha::show(env('GOOGLE_RECAPTCHA_SITE_KEY'), 'message', 'no_debug', ($settings->google_recaptcha_css_class ?? 'invisible google-recaptcha'), ($settings->google_recaptcha_no_tick_msg ?? 'Please tick the I\'m not robot checkbox')) : '';


        return view('laravel-cms::plugins.page-tab-contact-us-form.' . ($settings->form_layout ?? 'frontend-form-001'), $data);
    }


    static public function dynamicInputs($settings, $page)
    {

        $display_form_fields = strpos($settings->display_form_fields, '|') ? $settings->display_form_fields : 'first_name:Your Name:required | email | message:Message:required pattern="{5,5000}"';
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

    static public function submitForm(Request $request)
    {
        //
        $form_data = $request->all();
        $form_data['ip'] = $request->ip();

        //LaravelCmsHelper::debug($form_data);

        $settings = LaravelCmsInquirySetting::where('page_id', $form_data['page_id'])->first();

        if ($settings->google_recaptcha_enabled && !GoogleRecaptcha::verify(env('GOOGLE_RECAPTCHA_SECRET_KEY'), null)) {
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
        } else {
            $result['success'] = false;
        }
        return json_encode($result);




        // return view('laravel-cms::plugins.page-tab-contact-us-form.' . ($settings->form_layout ?? 'frontend-form-001'), $page);

        // return 'displayForm displayForm ' . $page->id . ' - ' . route('LaravelCmsPluginInquiry.add');
    }

    // public function index()
    // {
    //     $this->checkUser();
    //     //$data['all_pages'] = LaravelCmsPage::orderBy('id','desc')->get();

    //     $all_page_ary = $this->flattenArray($this->parentPages('all')->toArray());

    //     $data['all_pages']  = json_decode(json_encode($all_page_ary), FALSE);

    //     $data['helper'] = new LaravelCmsHelper;

    //     return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-list', $data);
    // }

    static public function search(Request $request)
    {
        $user = LaravelCmsHelper::hasPermission();

        $form_data = $request->all();
        $form_data['ip'] = $request->ip();
        $form_data['success'] = true;

        $form_data['user'] = $user->id;
        return json_encode($form_data);

        //LaravelCmsHelper::debug($form_data);
    }

    static public function edit($page_id, $page = null)
    {
        $s = LaravelCmsInquirySetting::where('page_id', $page_id)->first();
        //$s->onetime_api_key = LaravelCmsHelper::onetimeApiToken();
        //LaravelCmsHelper::debug($s);
        return $s;
    }

    // public function create()
    // {
    //     $this->checkUser();

    //     $data['parent_page_options'] = $this->parentPages();
    //     $data['template_file_options'] = $this->templateFileOption();
    //     $data['helper'] = new LaravelCmsHelper;
    //     $data['page_tab_blades'] = $this->extraPageTabs();

    //     return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-create', $data);
    // }


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

    // public function flattenArray($elements, $name = 'children', $depth = 0)
    // {
    //     $result = array();

    //     foreach ($elements as $element) {
    //         $element['depth'] = $depth;

    //         if (isset($element[$name])) {
    //             $children = $element[$name];
    //             unset($element[$name]);
    //         } else {
    //             $children = null;
    //         }

    //         $result[] = $element;

    //         if (isset($children)) {
    //             $result = array_merge($result, $this->flattenArray($children, $name, $depth + 1));
    //         }
    //     }

    //     return $result;
    // }

    // public function parentPages($action = 'get_select_options')
    // {
    //     $data['children'] = LaravelCmsPage::with('children:title,menu_title,id,parent_id,menu_enabled,slug,redirect_url,sort_value,status')
    //         ->whereNull('parent_id')
    //         ->orderBy('sort_value', 'desc')
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     //var_dump($data['children']->toArray());

    //     if ($action == 'get_select_options') {
    //         $options = [null => 'Top Level'];
    //         $flat_ary = $this->flattenArray($data['children']->toArray());

    //         //var_dump($flat_ary);

    //         foreach ($flat_ary as $item) {
    //             $title = $item['menu_title'] ?? $item['title'];
    //             $options[$item['id']] = '-' . str_repeat("--", $item['depth']) . ' ' . $title;
    //         }
    //         return json_decode(json_encode($options), FALSE); // return object
    //         //return $options;
    //     }

    //     return $data['children'];
    // }


    // private function handleUpload($request, &$form_data, &$all_file_data = [])
    // {

    //     $file_ary = ['main_image', 'main_banner', 'extra_image_1', 'extra_image_2', 'extra_image_3'];
    //     foreach ($file_ary as $field_name) {

    //         $field_name_delete = $field_name . '_delete';

    //         if ($request->hasFile($field_name)) {

    //             $all_file_data[$field_name] = $this->uploadFile($request->file($field_name))->toArray();
    //             $form_data[$field_name]     = $all_file_data[$field_name]['id'];
    //         } else if ($request->$field_name_delete) {

    //             $all_file_data[$field_name] = null;
    //             $form_data[$field_name]     = null;
    //         }
    //     }
    //     $form_data['file_data'] = json_encode($all_file_data);
    // }

    // public function uploadFile($f)
    // {

    //     // $file_data['user_id'] = $user->id;
    //     $file_data['mimetype']  = $f->getMimeType();
    //     $file_data['suffix']    = $f->getClientOriginalExtension();
    //     $file_data['filename']  = $f->getClientOriginalName();
    //     $file_data['title']     = $file_data['filename'];
    //     $file_data['filesize']  = $f->getSize();
    //     if (strpos($file_data['mimetype'], 'image/') !== false) {
    //         $file_data['is_image']  = 1;
    //     }
    //     if (strpos($file_data['mimetype'], 'video/') !== false) {
    //         $file_data['is_video']  = 1;
    //     }
    //     $file_data['filehash']  = sha1_file($f->path());

    //     $file_data['path']  = substr($file_data['filehash'], -2) . '/' . $file_data['filehash'] . '.' . $file_data['suffix'];

    //     // $abs_real_path = public_path('laravel-cms-uploads/' . $file_data['path']);

    //     // if (!file_exists(dirname($abs_real_path))) {
    //     //     mkdir(dirname($abs_real_path), 0755, true);
    //     // }

    //     $new_file = LaravelCmsFile::updateOrCreate(
    //         ['filehash' => $file_data['filehash']],
    //         $file_data
    //     );

    //     $f->storeAs(dirname('public/' . config('laravel-cms.upload_dir') . '/' . $file_data['path']), basename($file_data['path']));

    //     return $new_file;

    //     // echo '<pre>111:' . var_export($new_file, true) . '</pre>';
    //     // exit();
    // }


    // public function generateSlug($slug, $def = null, $separate = '-')
    // {
    //     $slug_format = config('laravel-cms.slug_format');
    //     $slug_suffix = config('laravel-cms.slug_suffix');
    //     $separate    = config('laravel-cms.slug_separate') ?? $separate;

    //     if (config('laravel-cms.template_language') == 'cn') {
    //         if ($slug_format == 'from_title') {
    //             $slug_format    = 'pinyin';
    //         }
    //         $normalizeChars = [];
    //     } else {
    //         $normalizeChars = array(
    //             'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
    //             'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
    //             'Ï' => 'I', 'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
    //             'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
    //             'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
    //             'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
    //             'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
    //             'ă' => 'a', 'î' => 'i', 'â' => 'a', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Î' => 'I', 'Â' => 'A', 'Ș' => 'S', 'Ț' => 'T',
    //         );
    //     }


    //     if ($slug || trim($slug) != '') {
    //         $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, trim(strtr($slug, $normalizeChars)));
    //         if (strpos($slug, '.') === false && strpos($slug_suffix, '.') !== false && $slug != 'homepage') {
    //             return $slug . $slug_suffix;
    //         }
    //         return $slug;
    //     } else if ($slug_format == 'pinyin') {
    //         $pinyin = new \Overtrue\Pinyin\Pinyin('\Overtrue\Pinyin\GeneratorFileDictLoader');
    //         $slug = strtr($pinyin->permalink(trim($def), '-'), $normalizeChars);

    //         $slug = ucwords($slug, '-');
    //         //exit($slug); // some Chinese can not convert
    //         $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, $slug);

    //         if ($separate != '-') {
    //             $slug = str_replace('-', $separate, $slug);
    //         }
    //     } else if ($slug_format == 'from_title') {
    //         $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, trim(strtr($def, $normalizeChars)));
    //     } else {
    //         if (!$def) {
    //             return '';
    //         }
    //         $slug = trim($def);
    //     }
    //     if (strlen($slug) > (190 - strlen($slug_suffix))) {
    //         $slug = substr($slug, 0, (190 - strlen($slug_suffix)));
    //     }
    //     return $slug . $slug_suffix;
    // }

    // public function getSlug($form_data)
    // {
    //     $slug_format = config('laravel-cms.slug_format');
    //     $slug_suffix = config('laravel-cms.slug_suffix');

    //     $default_slug = $slug_format == 'id' ? $form_data['id'] : ($form_data['menu_title'] ?? $form_data['title']);
    //     $new_slug = $this->generateSlug($form_data['slug'], $default_slug);
    //     $rs = LaravelCmsPage::where('slug', $new_slug)->first();
    //     if (isset($rs->id) && $rs->id != $form_data['id']) {
    //         if ($slug_suffix != '') {
    //             $new_slug = str_replace($slug_suffix, '', $new_slug) . '-' . uniqid() . $slug_suffix;
    //         } else {
    //             $new_slug .= '-' . uniqid();
    //         }
    //     }
    //     return $new_slug;
    // }
}
