<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsFile;
use Auth;
use App\Http\Controllers\Controller;

class LaravelCmsPageAdminController extends Controller
{
    private $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth']); // TODO: must be admin
    }

    public function checkUser()
    {
        // return true;
        $this->user = Auth::user();
        if (!$this->user) {
            exit('Can not get user info. Please logout and re-login again ');
        }

        if (!in_array($this->user->id, config('laravel-cms.admin_id_ary'))) {
            exit('Access denied for user id ' . $this->user->id);
        }
    }

    public function templateFileOption()
    {
        $app_view_dir = base_path('resources/views/vendor/laravel-cms') . '/' . config('laravel-cms.template_frontend_dir');

        if (!file_exists($app_view_dir)) {
            $app_view_dir = dirname(__FILE__, 3) . '/resources/views/' . config('laravel-cms.template_frontend_dir');
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

        $data['controller'] = $this;

        return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-list', $data);
    }

    public function edit($id)
    {
        $this->checkUser();



        $data['page_model'] = LaravelCmsPage::find($id);
        //$data['parent_page_options'] = array_merge(array(null=>"Top Level"),  $this->parentPages()->pluck('title', 'id')->toArray());

        $data['parent_page_options'] = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();

        $data['file_data'] = json_decode($data['page_model']->file_data);
        if ($data['file_data'] == null) {
            $data['file_data'] = json_decode('{}');
        }

        // $this->debug($data['file_data'], 'exit');

        $data['file_data']->file_dir = asset('storage/' . config('laravel-cms.upload_dir'));

        $data['controller'] = $this;

        return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-edit', $data);
    }

    public function create()
    {
        $this->checkUser();

        $data['parent_page_options'] = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();

        return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-create', $data);
    }


    public function store(Request $request)
    {
        $this->checkUser();

        $form_data = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $all_file_data = [];
        $this->handleUpload($request, $form_data, $all_file_data);

        $rs = LaravelCmsPage::create($form_data);

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


        // $all_file_data = [];
        $all_file_data = json_decode($page->file_data, true); // json2array

        //$this->debug($all_file_data, 'exit');
        $this->handleUpload($request, $form_data, $all_file_data);

        unset($form_data['_method']);
        unset($form_data['_token']);


        $data['page_model'] = $page->update($form_data);

        return back()->withInput();
        //return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-edit', $data);

        //return redirect()->route('user.edit_pictures', ['model_name'=>'Property4rent', 'id'=>$property4rent->id]);

    }

    public function debug($data, $exit = 'exit')
    {
        echo '<pre>' . var_export($data, true) . '</pre>';
        echo '<hr>Debug Time: ' . date('Y-m-d H:i:s') . '<hr>';
        if ($exit != 'no_exit') {
            exit();
        }
    }


    public function flattenArray($elements, $depth = 0)
    {
        $result = array();

        foreach ($elements as $element) {
            $element['depth'] = $depth;

            if (isset($element['children'])) {
                $children = $element['children'];
                unset($element['children']);
            } else {
                $children = null;
            }

            $result[] = $element;

            if (isset($children)) {
                $result = array_merge($result, $this->flattenArray($children, $depth + 1));
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

        if ($request->hasFile('main_image')) {
            $all_file_data['main_image'] = $this->uploadFile($request->file('main_image'))->toArray();
            $form_data['main_image'] = $all_file_data['main_image']['id'];
        }
        if ($request->hasFile('main_banner')) {
            $all_file_data['main_banner'] = $this->uploadFile($request->file('main_banner'))->toArray();
            $form_data['main_banner'] = $all_file_data['main_banner']['id'];
        }
        if ($request->hasFile('extra_image_1')) {
            $all_file_data['extra_image_1'] = $this->uploadFile($request->file('extra_image_1'))->toArray();
            $form_data['extra_image_1'] = $all_file_data['extra_image_1']['id'];
        }
        if ($request->hasFile('extra_image_2')) {
            $all_file_data['extra_image_2'] = $this->uploadFile($request->file('extra_image_2'))->toArray();
            $form_data['extra_image_2'] = $all_file_data['extra_image_2']['id'];
        }
        if ($request->hasFile('extra_image_3')) {
            $all_file_data['extra_image_3'] = $this->uploadFile($request->file('extra_image_3'))->toArray();
            $form_data['extra_image_3'] = $all_file_data['extra_image_3']['id'];
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

        $f->storeAs(dirname('public/' . config('laravel-cms.upload_dir') . '/' . $file_data['path']), basename($file_data['path']));

        return $new_file;

        // echo '<pre>111:' . var_export($new_file, true) . '</pre>';
        // exit();
    }


    static public function imageUrl($img_obj, $width, $height = null, $resize_type = 'ratio')
    {
        if (!is_numeric($width)) {
            $width = null;
        }
        if (!is_numeric($height)) {
            $height = null;
        }
        if (config('laravel-cms.image_encode') == 'jpg') {
            $suffix = 'jpg';
        } else {
            $suffix = $img_obj->suffix;
        }

        $filename   = $img_obj->id . '_' . ($width ?? 'auto') . '_' . ($height ?? 'auto') . '_' . $resize_type . '.' . $suffix;

        $related_dir = 'storage/' . config('laravel-cms.upload_dir') . '/optimized/' . substr($img_obj->id, -2);

        $abs_real_dir = public_path($related_dir);
        $abs_real_path = $abs_real_dir . '/' . $filename;
        $web_url = '/' . $related_dir . '/' . $filename;

        if (file_exists($abs_real_path) && filemtime($abs_real_path) > time() - config('laravel-cms.image_reoptimize_time')) {
            return $web_url;
            //return $abs_real_path . ' - already exists - ' . $web_url;
        }

        if (!file_exists($abs_real_dir)) {
            mkdir($abs_real_dir, 0755, true);
        }

        $original_img = public_path('storage/' . config('laravel-cms.upload_dir') . '/' . $img_obj->path);

        //self::debug($original_img);

        // resize the image to a width of 800 and constrain aspect ratio (auto height)
        $new_img = \Intervention\Image\ImageManagerStatic::make($original_img)->orientate()->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        if ($suffix == 'jpg' || $suffix == 'jpeg') {
            $new_img->encode('jpg');
        }
        $new_img->save($abs_real_path, 75);

        return $web_url;
        // return $abs_real_path . ' optimized image created ' . $width;
    }

    public function url($page)
    {
        if (!$page->slug) {
            $page->slug = $page->id . '.html';
        }
        if ($page->slug == 'homepage') {
            return route('LaravelCmsPages.index');
        }
        if (trim($page->redirect_url) != '') {
            return trim($page->redirect_url);
        }
        return route('LaravelCmsPages.show', $page->slug);
    }
}
