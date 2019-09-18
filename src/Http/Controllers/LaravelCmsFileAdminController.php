<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsFileAdminRepository;

class LaravelCmsFileAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsFileAdminRepository $repo, LaravelCmsHelper $helper)
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

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.file-list', $data);
    }

    public function show($id)
    {
        $this->checkUser();

        $rs = $this->repo->show($id);

        return $rs;
    }


    public function edit($id)
    {
        $this->checkUser();

        //$data = $this->repo->edit($id);

        // return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.file-edit', $data);
    }

    public function create()
    {
        $this->checkUser();


        //$data = $this->repo->create();

        // return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.file-create', $data);
    }


    public function store(Request $request)
    {
        $this->checkUser();

        $form_data = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $rs = $this->repo->store($form_data);


        return redirect()->route('LaravelCmsAdminFiles.index', ['editor_id' => $request->editor_id]);
    }

    public function update(Request $request)
    {
        //$this->checkUser();
    }

    public function destroy(Request $request, $id)
    {
        $this->checkUser();

        $rs = $this->repo->destroy($id);

        if (request()->result_type == 'json') {
            $result['success'] = $rs;
            $result['success_content'] = 'Id ' . $id . ' deleted';
            $result['error_message'] = 'Delete id ' . $id . ' failed!';

            return json_encode($result);
        }

        return redirect()->route('LaravelCmsAdminFiles.index');
    }
}
