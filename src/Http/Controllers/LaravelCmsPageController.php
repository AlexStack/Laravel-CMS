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

        //$this->helper->debug($data['page']->template_file);

        if ('1' == $this->helper->s('system.allow_json_response') && 'json' == request()->response_type) {
            unset($data['helper']);
            unset($data['plugins']);

            $rs = response()->json($data);

            if ($this->helper->s('system.allow_access_origin')) {
                $rs->header('Access-Control-Allow-Origin', $this->helper->s('system.allow_access_origin'));
                if ($this->helper->s('system.allow_access_methods')) {
                    $rs->header('Access-Control-Allow-Methods', $this->helper->s('system.allow_access_methods'));
                } else {
                    $rs->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                }
            }

            return $rs;
        }
        if (is_array($data)) {
            if (isset($data['page']->template_file)) {
                return view($this->helper->bladePath($data['page']->template_file, 'f'), $data);
            } else {
                return 'Retrieve page data error';
            }
        }

        return $data;
    }
}
