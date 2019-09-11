<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use App\Http\Controllers\Controller;

class LaravelCmsPageController extends Controller
{
    public $helper;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->helper = new LaravelCmsHelper;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return $this->show('homepage');
    }

    public function show($slug)
    {
        if ($slug == 'sitemap.txt') {
            return $this->sitemap('txt');
        }
        if ($slug == 'redirect-link') {
            return $this->goExternalLink();
        }
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
        }, 'parent:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled'])->where($search_field, $slug)->first();
        if (!$data['page']) {
            return abort(404);
        }
        $template_file = $data['page']->template_file ?? 'page-detail-default';

        if (isset($data['page']->parent)) {
            $data['page']->parent_flat_ary = array_reverse($this->flattenParentArray($data['page']->parent->toArray(), 'parent'));
        } else {
            $data['page']->parent_flat_ary = [];
        }


        //$this->helper->debug($data['page']->parent->toArray(), 'no_exit');


        $data['file_data'] = json_decode($data['page']->file_data);
        if ($data['file_data'] == null) {
            $data['file_data'] = json_decode('{}');
        }
        $data['file_data']->file_dir = asset('storage/' . $this->helper->s('file.upload_dir'));

        //$data['page']->file_data = $data['file_data'];
        $data['helper'] = $this->helper;


        $data['plugins'] = collect([]);
        $plugin_ary = $this->helper->getPlugins('page-tab-');
        foreach ($plugin_ary as $plugin) {
            $plugin_class = trim($plugin['php_class'] ?? '');
            if ($plugin_class != '' && class_exists($plugin_class)) {
                $data['plugins']->put($plugin['blade_dir'], new $plugin_class);
            }
        }
        //$this->helper->debug($data['plugins']);

        return view('laravel-cms::' . $this->helper->s('template.frontend_dir') .  '.' . $template_file, $data);
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

    public function flattenParentArray($element, $name = 'parent', $depth = 0)
    {
        $result = array();


        $element['depth'] = $depth;

        if (isset($element[$name])) {
            $children = $element[$name];
            unset($element[$name]);
        } else {
            $children = null;
        }

        $result[] = $element;

        if (isset($children)) {
            $result = array_merge($result, $this->flattenParentArray($children, $name, $depth + 1));
        }

        return $result;
    }

    public function sitemap($type = 'txt')
    {
        $new_pages = LaravelCmsPage::where('status', 'publish')->orderBy('id', 'desc')->limit(2000)->get(['title', 'menu_title', 'id', 'parent_id', 'slug', 'redirect_url', 'menu_enabled']);
        if ($type == 'txt') {
            foreach ($new_pages as $page) {
                if (trim($page->redirect_url) == '') {
                    echo $this->helper->url($page, true) . "\n";
                }
            }
            exit();
        }

        //$this->helper->debug($sitemap);
        return true;
    }

    public function goExternalLink()
    {
        $s = request()->url;

        header("X-Robots-Tag: noindex, nofollow", true);

        //$this->helper->debug($s);
        return redirect($s, 301);
    }
}
