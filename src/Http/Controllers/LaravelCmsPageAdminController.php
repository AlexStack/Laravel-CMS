<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use Auth;
use App\Http\Controllers\Controller;
use DB;

class LaravelCmsPageAdminController extends Controller
{
    private $user = null;
    public $helper;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth']); // TODO: must be admin

        $this->helper = new LaravelCmsHelper;
    }

    public function checkUser()
    {
        // return true;
        if (!$this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    public function extraPageTabs($action = 'return_options', $form_data = null, $page = null)
    {

        $option_ary = $this->helper->getPlugins('page-tab-');

        //$this->helper->debug($option_ary);

        if ($action == 'return_options') {
            return $option_ary;
        } else if (in_array($action, ['edit', 'store', 'update', 'destroy'])) {
            $callback_ary = collect([]);
            foreach ($option_ary as $plugin) {
                $plugin_class = trim($plugin['php_class'] ?? '');
                if ($plugin_class != '' && class_exists($plugin_class) && is_callable($plugin_class . '::' . $action)) {
                    //echo $plugin_class . '::' . $action . '  --- ';
                    $s = call_user_func($plugin_class . '::' . $action, $form_data, $page);
                    $callback_ary->put($plugin['blade_file'], $s);
                    //$this->helper->debug($s->toArray());
                } else {
                    $callback_ary->put($plugin['blade_file'], null);
                }
            }
            //$this->helper->debug($callback_ary);
            return $callback_ary;
        }

        //$this->helper->debug($option_ary);
        return $option_ary;
    }

    public function templateFileOption()
    {
        $app_view_dir = base_path('resources/views/vendor/laravel-cms') . '/' . $this->helper->getCmsSetting('template_frontend_dir');

        if (!file_exists($app_view_dir)) {
            $app_view_dir = dirname(__FILE__, 3) . '/resources/views/' . $this->helper->getCmsSetting('template_frontend_dir');
        }
        $files = glob($app_view_dir . "/*.blade.php");
        foreach ($files as $f) {
            $k = str_replace('.blade.php', '', basename($f));
            $option_ary[$k] = ucwords(str_replace(['-', '_', '.'], ' ', $k));
        }
        if (file_exists($app_view_dir . '/config.php')) {
            $config_ary = include($app_view_dir . '/config.php');
            if (isset($config_ary['blade_files'])) {
                $option_ary = $config_ary['blade_files'] + $option_ary;
            }
        }

        return $option_ary;
    }

    public function dashboard()
    {
        $this->checkUser();
        return redirect()->route('LaravelCmsAdminPages.index');
    }

    public function index()
    {
        $this->checkUser();
        //$data['all_pages'] = LaravelCmsPage::orderBy('id','desc')->get();

        $all_page_ary = $this->flattenArray($this->parentPages('all')->toArray());

        $data['all_pages']  = json_decode(json_encode($all_page_ary), FALSE);

        $data['helper'] = $this->helper;

        return view('laravel-cms::' . $this->helper->getCmsSetting('template_backend_dir') .  '.page-list', $data);
    }

    public function edit($id)
    {
        $this->checkUser();

        $data['page_tab_blades'] = $this->extraPageTabs();

        $data['page'] = LaravelCmsPage::find($id);
        //$data['parent_page_options'] = array_merge(array(null=>"Top Level"),  $this->parentPages()->pluck('title', 'id')->toArray());

        $data['parent_page_options'] = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();

        $data['file_data'] = json_decode($data['page']->file_data);
        if ($data['file_data'] == null) {
            $data['file_data'] = json_decode('{}');
        }

        // $this->debug($data['file_data'], 'exit');

        $data['file_data']->file_dir = asset('storage/' . $this->helper->getCmsSetting('upload_dir'));

        $data['helper'] = $this->helper;

        $data['plugins'] = $this->extraPageTabs('edit', $id, $data['page']);

        //$this->helper->debug($data['plugins'], 'no_exit22');

        return view('laravel-cms::' . $this->helper->getCmsSetting('template_backend_dir') .  '.page-edit', $data);
    }

    public function create()
    {
        $this->checkUser();

        $data['parent_page_options'] = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();
        $data['helper'] = $this->helper;
        $data['page_tab_blades'] = $this->extraPageTabs();

        return view('laravel-cms::' . $this->helper->getCmsSetting('template_backend_dir') .  '.page-create', $data);
    }


    public function store(Request $request)
    {
        $this->checkUser();

        $form_data = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $all_file_data = [];
        $this->handleUpload($request, $form_data, $all_file_data);
        //$this->helper->debug($form_data, 'no_exit');

        $form_data['slug'] = $this->getSlug($form_data);
        // DB::enableQueryLog();

        // $rs = LaravelCmsPage::create($form_data);  // create() not working ???

        $rs = new LaravelCmsPage;
        foreach ($rs->fillable as $field) {
            if (isset($form_data[$field])) {
                $rs->$field = trim($form_data[$field]);
            }
        }
        $rs->save();
        //$this->helper->debug($rs);

        // $sql = DB::getQueryLog();
        // $this->helper->debug($sql);
        if ($rs->slug == null || trim($rs->slug) == '') {
            $rs->save(['slug' => $this->generateSlug('', $rs->id)]);
        }

        $this->extraPageTabs('store', $form_data, $rs);

        if ($form_data['return_to_the_list']) {
            return redirect()->route(
                'LaravelCmsAdminPages.index'
            );
        }

        return redirect()->route(
            'LaravelCmsAdminPages.edit',
            ['id' => $rs->id]
        );
    }

    public function update(Request $request)
    {
        $this->checkUser();

        $form_data = $request->all();
        $form_data['id'] = $request->page;

        $page = LaravelCmsPage::find($form_data['id']);
        if (!$page->user_id && isset($this->user->id)) {
            $form_data['user_id'] = $this->user->id;
        }

        $form_data['slug'] = $this->getSlug($form_data);

        $all_file_data = json_decode($page->file_data, true); // json2array

        //$this->debug($all_file_data, 'exit');
        $this->handleUpload($request, $form_data, $all_file_data);

        unset($form_data['_method']);
        unset($form_data['_token']);


        $data['page'] = $page->update($form_data);

        $this->extraPageTabs('update', $form_data, $page);

        if ($form_data['return_to_the_list']) {
            return redirect()->route(
                'LaravelCmsAdminPages.index'
            );
        }
        return back()->withInput();
    }

    public function destroy(Request $request, $id)
    {
        $this->checkUser();
        $rs = LaravelCmsPage::find($id)->delete();

        //$this->helper->debug($rs);

        $this->extraPageTabs('destroy', $id);

        return redirect()->route(
            'LaravelCmsAdminPages.index'
        );
    }

    public function flattenArray($elements, $name = 'children', $depth = 0)
    {
        $result = array();

        foreach ($elements as $element) {
            $element['depth'] = $depth;

            if (isset($element[$name])) {
                $children = $element[$name];
                unset($element[$name]);
            } else {
                $children = null;
            }

            $result[] = $element;

            if (isset($children)) {
                $result = array_merge($result, $this->flattenArray($children, $name, $depth + 1));
            }
        }

        return $result;
    }

    public function parentPages($action = 'get_select_options')
    {
        $data['children'] = LaravelCmsPage::with('children:title,menu_title,id,parent_id,menu_enabled,slug,redirect_url,sort_value,status')
            ->whereNull('parent_id')
            ->orderBy('sort_value', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        //var_dump($data['children']->toArray());

        if ($action == 'get_select_options') {
            $options = [null => 'Top Level'];
            $flat_ary = $this->flattenArray($data['children']->toArray());

            //var_dump($flat_ary);

            foreach ($flat_ary as $item) {
                $title = $item['menu_title'] ?? $item['title'];
                $options[$item['id']] = '-' . str_repeat("--", $item['depth']) . ' ' . $title;
            }
            return json_decode(json_encode($options), FALSE); // return object
            //return $options;
        }

        return $data['children'];
    }


    private function handleUpload($request, &$form_data, &$all_file_data = [])
    {

        $file_ary = ['main_image', 'main_banner', 'extra_image_1', 'extra_image_2', 'extra_image_3'];
        foreach ($file_ary as $field_name) {

            $field_name_delete = $field_name . '_delete';

            if ($request->hasFile($field_name)) {

                $all_file_data[$field_name] = $this->uploadFile($request->file($field_name))->toArray();
                $form_data[$field_name]     = $all_file_data[$field_name]['id'];
            } else if ($request->$field_name_delete) {

                $all_file_data[$field_name] = null;
                $form_data[$field_name]     = null;
            }
        }
        $form_data['file_data'] = json_encode($all_file_data);
    }

    public function uploadFile($f)
    {

        // $file_data['user_id'] = $user->id;
        $file_data['mimetype']  = $f->getMimeType();
        $file_data['suffix']    = $f->getClientOriginalExtension();
        $file_data['filename']  = $f->getClientOriginalName();
        $file_data['title']     = $file_data['filename'];
        $file_data['filesize']  = $f->getSize();
        if (strpos($file_data['mimetype'], 'image/') !== false) {
            $file_data['is_image']  = 1;
        }
        if (strpos($file_data['mimetype'], 'video/') !== false) {
            $file_data['is_video']  = 1;
        }
        $file_data['filehash']  = sha1_file($f->path());

        $file_data['path']  = substr($file_data['filehash'], -2) . '/' . $file_data['filehash'] . '.' . $file_data['suffix'];

        // $abs_real_path = public_path('laravel-cms-uploads/' . $file_data['path']);

        // if (!file_exists(dirname($abs_real_path))) {
        //     mkdir(dirname($abs_real_path), 0755, true);
        // }

        $new_file = LaravelCmsFile::updateOrCreate(
            ['filehash' => $file_data['filehash']],
            $file_data
        );

        $f->storeAs(dirname('public/' . $this->helper->getCmsSetting('upload_dir') . '/' . $file_data['path']), basename($file_data['path']));

        return $new_file;

        // echo '<pre>111:' . var_export($new_file, true) . '</pre>';
        // exit();
    }


    public function generateSlug($slug, $def = null, $separate = '-')
    {
        $slug_format = $this->helper->getCmsSetting('slug_format');
        $slug_suffix = $this->helper->getCmsSetting('slug_suffix');
        $separate    = $this->helper->getCmsSetting('slug_separate') ?? $separate;

        if ($this->helper->getCmsSetting('template_language') == 'cn') {
            if ($slug_format == 'from_title') {
                $slug_format    = 'pinyin';
            }
            $normalizeChars = [];
        } else {
            $normalizeChars = array(
                'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
                'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
                'Ï' => 'I', 'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
                'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
                'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
                'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
                'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
                'ă' => 'a', 'î' => 'i', 'â' => 'a', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Î' => 'I', 'Â' => 'A', 'Ș' => 'S', 'Ț' => 'T',
            );
        }


        if ($slug || trim($slug) != '') {
            $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, trim(strtr($slug, $normalizeChars)));
            if (strpos($slug, '.') === false && strpos($slug_suffix, '.') !== false && $slug != 'homepage') {
                return $slug . $slug_suffix;
            }
            return $slug;
        } else if ($slug_format == 'pinyin') {
            $pinyin = new \Overtrue\Pinyin\Pinyin('\Overtrue\Pinyin\GeneratorFileDictLoader');
            $slug = strtr($pinyin->permalink(trim($def), '-'), $normalizeChars);

            $slug = ucwords($slug, '-');
            //exit($slug); // some Chinese can not convert
            $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, $slug);

            if ($separate != '-') {
                $slug = str_replace('-', $separate, $slug);
            }
        } else if ($slug_format == 'from_title') {
            $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, trim(strtr($def, $normalizeChars)));
        } else {
            if (!$def) {
                return '';
            }
            $slug = trim($def);
        }
        if (strlen($slug) > (190 - strlen($slug_suffix))) {
            $slug = substr($slug, 0, (190 - strlen($slug_suffix)));
        }
        return $slug . $slug_suffix;
    }

    public function getSlug($form_data)
    {
        $slug_format = $this->helper->getCmsSetting('slug_format');
        $slug_suffix = $this->helper->getCmsSetting('slug_suffix');

        $default_slug = $slug_format == 'id' ? $form_data['id'] : ($form_data['menu_title'] ?? $form_data['title']);
        $new_slug = $this->generateSlug($form_data['slug'], $default_slug);
        $rs = LaravelCmsPage::where('slug', $new_slug)->first();
        if (isset($rs->id) && $rs->id != $form_data['id']) {
            if ($slug_suffix != '') {
                $new_slug = str_replace($slug_suffix, '', $new_slug) . '-' . uniqid() . $slug_suffix;
            } else {
                $new_slug .= '-' . uniqid();
            }
        }
        return $new_slug;
    }
}
