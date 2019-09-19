<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsPageRepository;
use App\Http\Controllers\Controller;

class LaravelCmsPageController extends Controller
{
    private $helper;
    private $repo;

    public function __construct(LaravelCmsPageRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo   = $repo;
        $this->helper = $helper;

        $this->repo->setHelper($helper);
    }

    public function index()
    {
        return $this->show('homepage');
    }

    public function show($slug)
    {
        $data = $this->repo->show($slug);

        return view('laravel-cms::'.$this->helper->s('template.frontend_dir').'.'.$data['page']->template_file, $data);
    }
}
