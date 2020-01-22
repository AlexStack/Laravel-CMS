<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsPage;

class LaravelCmsPageRepository extends BaseRepository
{
    /**
     * Configure the Model.
     **/
    public function model()
    {
        return LaravelCmsPage::class;
    }

    /**
     * Controller methods.
     */
    public function index()
    {
        return true;
    }

    public function show($id)
    {
        $slug           = $id;
        $reserved_slugs = $this->helper->s('system.reserved_slugs');

        if (($reserved_slugs['search'] ?? 'Search-CMS.html') == $slug) {
            return $this->search($slug);
        } elseif (($reserved_slugs['tag'] ?? 'List-Tag.html') == $slug) {
            return $this->search($slug, 'tag');
        } elseif (($reserved_slugs['sitemap'] ?? 'sitemap.txt') == $slug) {
            return $this->sitemap('txt');
        } elseif (($reserved_slugs['redirect'] ?? 'redirect-link') == $slug) {
            return $this->goExternalLink();
        }

        $data['menus'] = $this->menus();
        if (is_numeric(str_replace('.html', '', $slug))) {
            $search_field = 'id';
            $slug         = str_replace('.html', '', $slug);
        } else {
            $search_field = 'slug';
            $slug         = trim($slug);
        }
        $data['page'] = LaravelCmsPage::with(['children' => function ($query) {
            return $query->take(120);
        }, 'parent:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled'])->where($search_field, $slug)->first();
        if (! $data['page']) {
            return abort(404);
        }
        if (! isset($data['page']->template_file) || '' == trim($data['page']->template_file)) {
            $data['page']->template_file = 'page-detail-default';
        }

        if (isset($data['page']->parent)) {
            $data['page']->parent_flat_ary = array_reverse($this->flattenParentArray($data['page']->parent->toArray(), 'parent'));
        } else {
            $data['page']->parent_flat_ary = [];
        }

        if ('' != trim($data['page']->tags)) {
            $data['page']->tags_ary = json_decode($data['page']->tags, true);
        } else {
            $data['page']->tags_ary = [];
        }

        $data['file_data'] = json_decode($data['page']->file_data);
        if (null == $data['file_data']) {
            $data['file_data'] = json_decode('{}');
        }
        $data['file_data']->file_dir = asset(''.$this->helper->s('file.upload_dir'));

        //$data['page']->file_data = $data['file_data'];
        $data['helper'] = $this->helper;

        $data['plugins'] = collect([]);
        $plugin_ary      = $this->helper->getPlugins('page-tab-');
        foreach ($plugin_ary as $plugin) {
            $plugin_class = trim($plugin['php_class'] ?? '');
            if ('' != $plugin_class && class_exists($plugin_class)) {
                $data['plugins']->put($plugin['blade_dir'], new $plugin_class());
            }
        }

        // override the $data by call_user_func
        if (strpos($data['page']->special_text, 'php_class') && $special_json = json_decode($data['page']->special_text)) {
            $user_class   = $special_json->php_class;
            $class_method = $special_json->class_method ?? 'index';
            $parameters   = $special_json->parameters ?? null;
            if ('' != $user_class && class_exists($user_class) && is_callable($user_class.'::'.$class_method)) {
                $data = call_user_func([new $user_class(), $class_method], $data, $parameters);
            } else {
                $data['call_user_func_error'] = $user_class.' class not exists or class_method not callable ';
            }
        }

        return $data;
    }

    public function create()
    {
        return true;
    }

    public function store($form_data)
    {
        return true;
    }

    public function update($form_data, $id)
    {
        return true;
    }

    public function edit($id)
    {
        return true;
    }

    public function destroy($id)
    {
        return true;
    }

    /**
     * Other methods.
     */
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
        $result = [];

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

    /**
     * For webmaster.google.com sitemap.
     */
    public function sitemap($type = 'txt')
    {
        $new_pages = LaravelCmsPage::where('status', 'publish')->orderBy('id', 'desc')->limit(2000)->get(['title', 'menu_title', 'id', 'parent_id', 'slug', 'redirect_url', 'menu_enabled']);
        if ('txt' == $type) {
            foreach ($new_pages as $page) {
                if ('' == trim($page->redirect_url)) {
                    echo $this->helper->url($page, true)."\n";
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

        //header('X-Robots-Tag: noindex, nofollow', true);

        //$this->helper->debug($s);

        return redirect($s, 301, ['X-Robots-Tag' => 'noindex, nofollow']);
    }

    public function search($slug, $search_type = 'content')
    {
        $keyword                = request()->keyword;
        if ($keyword || request()->tag) {
            if ('content' == $search_type) {
                $data['search_results'] = LaravelCmsPage::when($keyword, function ($query, $keyword) {
                    return $query->where(function ($query) use ($keyword) {
                        return $query->where('title', 'like', '%'.trim($keyword).'%')
                                ->orWhere('main_content', 'like', '%'.trim($keyword).'%')
                                ->orWhere('sub_content', 'like', '%'.trim($keyword).'%');
                    });
                })
                ->when(request()->tag, function ($query, $keyword) {
                    $query->where('tags', 'like', '%"'.trim(request()->tag).'"%');

                    // dd($query->toSql());

                    return $query;
                })
                ->orderBy('sort_value', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($this->helper->s('template.number_per_search') ?? 24);
            } elseif ('tag' == $search_type) {
                $data['search_results'] = LaravelCmsPage::when($keyword, function ($query, $keyword) {
                    return $query->where('tags', 'like', '%"'.trim($keyword).'"%');
                })
                ->orderBy('sort_value', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($this->helper->s('template.number_per_search') ?? 20);
            }
        } else {
            $data['search_results'] = [];
        }

        $data['helper'] = $this->helper;
        $data['menus']  = $this->menus();

        // simulate a page collection
        $data['page']                       = collect([]);
        $data['page']->template_file        = 'page-search-result';
        $data['page']->title                = ('tag' == $search_type) ? $this->helper->t('tag').' '.$keyword : $this->helper->t('search').' '.$keyword.(request()->tag ? ' - '.request()->tag : '');
        $data['page']->meta_title           = $data['page']->title.' ('.$this->helper->t('page_number', ['number'=>$_GET['page'] ?? 1]).')';
        $data['page']->slug                 = $slug;
        $data['page']->id                   = $slug;
        $data['page']->parent_flat_ary      = [];

        return $data;
    }
}
