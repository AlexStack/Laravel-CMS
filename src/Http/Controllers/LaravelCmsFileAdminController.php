<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsFileAdminRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaravelCmsFileAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsFileAdminRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo   = $repo;
        $this->helper = $helper;

        $this->repo->setHelper($helper);
    }

    public function checkUser()
    {
        // return true;
        if (! $this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    public function index()
    {
        $this->checkUser();

        if (0 === strpos(request()->download_file, 'http')) {
            return $this->repo->downloadFile(request()->download_file);
        } elseif (false !== strpos(request()->extract_file, '/temp/')) {
            return $this->repo->extractFile(request()->extract_file);
        } elseif (false !== strpos(request()->install_package, '/temp/')) {
            return $this->repo->installPackage(request()->install_package);
        } elseif (request()->new_version && request()->old_version) {
            return $this->repo->upgradeCmsViaBrowser(request()->new_version, request()->old_version);
        }

        $data = $this->repo->index();

        return view($this->helper->bladePath('file-list', 'b'), $data);
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
    }

    public function create()
    {
        $this->checkUser();
    }

    public function store(Request $request)
    {
        $this->checkUser();

        $form_data            = $request->all();
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

        if ('json' == request()->response_type) {
            $result['success']         = $rs;
            $result['success_content'] = 'Id '.$id.' deleted';
            $result['error_message']   = 'Delete id '.$id.' failed!';

            return json_encode($result);
        }

        return redirect()->route('LaravelCmsAdminFiles.index');
    }
}
