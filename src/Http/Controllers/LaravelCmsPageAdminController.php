<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
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

        $data['helper'] = new LaravelCmsHelper;

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

        $data['helper'] = new LaravelCmsHelper;

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
        //LaravelCmsHelper::debug($form_data);

        $form_data2['title'] = 'test' . date('Y-m-d H:i:s');

        //LaravelCmsHelper::debug($form_data2);

        // $model = new LaravelCmsPage();
        // $rs = $model->save($form_data);

        $rs = LaravelCmsPage::create($form_data2);

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

    public function destroy(Request $request, $id)
    {
        $this->checkUser();
        $rs = LaravelCmsPage::find($id)->delete();

        //LaravelCmsHelper::debug($rs);

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

        $f->storeAs(dirname('public/' . config('laravel-cms.upload_dir') . '/' . $file_data['path']), basename($file_data['path']));

        return $new_file;

        // echo '<pre>111:' . var_export($new_file, true) . '</pre>';
        // exit();
    }
}
