<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsPageRepository;
use App\Http\Controllers\Controller;


class LaravelCmsPageAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsPageRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo     = $repo;
        $this->helper   = $helper;

        $this->repo->setHelper($helper);
    }

    public function checkUser()
    {
        // return true;
        if (!$this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }


    public function index()
    {
        $this->checkUser();

        $data = $this->repo->index();

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.page-list', $data);
    }

    public function create()
    {
        $this->checkUser();

        $data['parent_page_options'] = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();
        $data['helper'] = $this->helper;
        $data['page_tab_blades'] = $this->extraPageTabs();

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.page-create', $data);
    }


    public function store(Request $request)
    {
        $this->checkUser();

        $form_data = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $rs = $this->repo->store($form_data);

        if ($form_data['return_to_the_list']) {
            return redirect()->route(
                'LaravelCmsAdminPages.index'
            );
        }

        return redirect()->route(
            'LaravelCmsAdminPages.edit',
            ['page' => $rs->id]
        );
    }

    public function edit($id)
    {
        $this->checkUser();

        $data = $this->repo->edit($id);

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.page-edit', $data);
    }

    public function update(Request $request, $page)
    {
        $this->checkUser();
        $page_id = $page;

        $form_data = $request->all();
        $form_data['user_id'] = $form_data['user_id'] ?? $this->user->id;

        $rs = $this->repo->update($form_data, $page_id);

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
        $rs = $this->repo->destroy($id);

        return redirect()->route(
            'LaravelCmsAdminPages.index'
        );
    }
}
