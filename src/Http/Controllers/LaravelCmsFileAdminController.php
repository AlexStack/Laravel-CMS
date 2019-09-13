<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use Auth;
use App\Http\Controllers\Controller;
use DB;

class LaravelCmsFileAdminController extends Controller
{
    private $user = null;

    public $helper;

    private $wrong_json_format_str = '%s is NOT a Correct Json Format string! <hr> Please input a correct json format string. eg. use \\\\ instead of \, use " instead of \' , no comma for the last property<hr>Please make  { not at the begging or make  } not at the end if the input is not a json string';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth']); // TODO: must be admin
        $this->helper = new LaravelCmsHelper;
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
        $keyword = request()->keyword;
        $data['files'] = LaravelCmsFile::when($keyword, function ($query, $keyword) {
            return $query->where('title', 'like', '%' . trim($keyword) . '%');
        })
            ->orderBy('updated_at', 'desc')
            ->paginate($this->helper->s('file.number_per_page') ?? 12);;

        $data['helper'] = $this->helper;


        //$this->helper->debug($data['categories']);

        return view('laravel-cms::' . config('laravel-cms.template.backend_dir') .  '.file-list', $data);
    }

    public function show($id)
    {
        $this->checkUser();

        if (request()->generate_image && request()->width) {
            $file = LaravelCmsFile::find($id);
            $url = $this->helper->imageUrl($file, request()->width, request()->height);
            if (request()->return_url) {
                return $url;
            }
            return redirect()->to($url);
        }
    }
    public function edit($id)
    {
        $this->checkUser();


        $data['setting'] = LaravelCmsSetting::find($id);

        $data['helper'] = $this->helper;

        return view('laravel-cms::' . config('laravel-cms.template.backend_dir') .  '.setting-edit', $data);
    }

    public function create()
    {
        $this->checkUser();


        $data['helper'] = $this->helper;

        return view('laravel-cms::' . config('laravel-cms.template.backend_dir') .  '.setting-create', $data);
    }


    public function store(Request $request)
    {
        $this->checkUser();


        $form_data = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $all_file_data = [];
        $this->handleUpload($request, $form_data, $all_file_data);

        //$this->helper->debug($all_file_data);


        return redirect()->route(
            'LaravelCmsAdminFiles.index',
            ['editor_id' => $request->editor_id]
        );
    }

    public function update(Request $request)
    {
        $this->checkUser();
    }

    public function destroy(Request $request, $id)
    {
        $this->checkUser();

        $file = LaravelCmsFile::find($id);

        $original_file_path = public_path($this->helper->imageUrl($file));
        if (file_exists($original_file_path)) {
            unlink($original_file_path);
        }
        if ($file->is_image) {
            $small_img_path = public_path($this->helper->imageUrl($file, $this->helper->s('file.small_image_width')));

            $all_images = glob(dirname($small_img_path) . "/" . $id . "_*");

            //$this->helper->debug($all_images);
            array_map('unlink', $all_images);
        }

        $file->delete();

        return redirect()->route(
            'LaravelCmsAdminFiles.index'
        );
    }



    private function handleUpload($request, &$form_data, &$all_file_data = [])
    {

        $files = $request->file('files');

        if ($request->hasFile('files')) {
            foreach ($files as $file) {
                $all_file_data[] = $this->helper->uploadFile($file)->toArray();
            }
            return true;
        }
        return false;
    }

    // public function uploadFile($f)
    // {

    //     // $file_data['user_id'] = $user->id;
    //     $file_data['mimetype']  = $f->getMimeType();
    //     $file_data['suffix']    = $f->getClientOriginalExtension();
    //     $file_data['filename']  = $f->getClientOriginalName();
    //     $file_data['title']     = $file_data['filename'];
    //     $file_data['filesize']  = $f->getSize();
    //     if (strpos($file_data['mimetype'], 'image/') !== false) {
    //         $file_data['is_image']  = 1;
    //     }
    //     if (strpos($file_data['mimetype'], 'video/') !== false) {
    //         $file_data['is_video']  = 1;
    //     }
    //     $file_data['filehash']  = sha1_file($f->path());

    //     $file_data['path']  = substr($file_data['filehash'], -2) . '/' . $file_data['filehash'] . '.' . $file_data['suffix'];

    //     // $abs_real_path = public_path('laravel-cms-uploads/' . $file_data['path']);

    //     // if (!file_exists(dirname($abs_real_path))) {
    //     //     mkdir(dirname($abs_real_path), 0755, true);
    //     // }

    //     $file_data['description']  = date('Y-m-d H:i:s'); // make some different otherwise the updated_at will not update
    //     $new_file = LaravelCmsFile::updateOrCreate(
    //         ['filehash' => $file_data['filehash']],
    //         $file_data
    //     );

    //     //$f->storeAs(dirname('public/' . $this->helper->s('file.upload_dir') . '/' . $file_data['path']), basename($file_data['path']));

    //     $file_store_dir = public_path(dirname($this->helper->s('file.upload_dir') . '/' . $file_data['path']));

    //     //$this->helper->debug($file_store_dir);

    //     $f->move($file_store_dir, basename($file_data['path']));

    //     return $new_file;

    //     // echo '<pre>111:' . var_export($new_file, true) . '</pre>';
    //     // exit();
    // }
}
