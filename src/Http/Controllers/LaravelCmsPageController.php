<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use App\Http\Controllers\Controller;

class LaravelCmsPageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return 'front-end cms';
        // return view('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.page-list');
        return $this->show('homepage');
    }

    public function show($slug)
    {
        $data['menus'] = $this->menus();
        if (is_numeric(str_replace('.html', '', $slug))) {
            $search_field = 'id';
            $slug = str_replace('.html', '', $slug);
        } else {
            $search_field = 'slug';
            $slug = trim($slug);
        }
        $data['page']  = LaravelCmsPage::with(['children' => function ($query) {
            return $query->take(120);
        }])->where($search_field, $slug)->first();
        if (!$data['page']) {
            return abort(404);
        }
        $template_file = $data['page']->template_file ?? 'page-detail-default';
        //$this->debug($data['page']->toArray());


        $data['file_data'] = json_decode($data['page']->file_data);
        if ($data['file_data'] == null) {
            $data['file_data'] = json_decode('{}');
        }
        $data['file_data']->file_dir = asset('storage/' . config('laravel-cms.upload_dir'));

        $data['controller'] = $this;
        $data['cms_helper'] = new LaravelCmsHelper;

        return view('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.' . $template_file, $data);
    }

    static public function imageUrl($img_obj, $width = null, $height = null, $resize_type = 'ratio')
    {
        if (!is_numeric($width)) {
            $width = null;
        }
        if (!is_numeric($height)) {
            $height = null;
        }

        if ($img_obj->suffix == 'svg' || ($width == null && $height == null)) {
            $original_img_url = '/storage/' . config('laravel-cms.upload_dir') . '/' . $img_obj->path;
            return $original_img_url;
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


    static public function debug($data, $exit = 'exit')
    {
        if (is_a($data, 'Illuminate\Database\Eloquent\Collection')) {
            $data = $data->toArray();
        }
        echo '<pre>' . var_export($data, true) . '</pre>';
        echo '<hr>Debug Time: ' . date('Y-m-d H:i:s') . '<hr>';
        if ($exit != 'no_exit') {
            exit();
        }
    }

    public function menus()
    {
        $data['menus'] = LaravelCmsPage::with('menus:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled')
            ->whereNull('parent_id')
            ->where('menu_enabled', 1)
            ->orderBy('sort_value', 'desc')
            ->orderBy('id', 'desc')
            ->get(['title', 'menu_title', 'id', 'parent_id', 'slug', 'redirect_url', 'menu_enabled']);

        //var_dump($data['menus']->toArray());
        //$this->debug($data['menus']);

        return $data['menus'];
    }


    public function url($page)
    {
        if (!$page->slug) {
            $page->slug = $page->id . '.html';
        }
        if (trim($page->redirect_url) != '') {
            return trim($page->redirect_url);
        }
        if ($page->slug == 'homepage') {
            return route('LaravelCmsPages.index');
        }
        return route('LaravelCmsPages.show', $page->slug);
    }
}
