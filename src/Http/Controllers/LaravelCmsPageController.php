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

        if ('1' == $this->helper->s('allow_json_response') && 'json' == request()->response_type) {
            unset($data['helper']);
            unset($data['plugins']);

            return response()->json($data);
        }

        return view($this->helper->bladePath($data['page']->template_file, 'f'), $data);
    }
}
