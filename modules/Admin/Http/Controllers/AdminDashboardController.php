<?php

namespace Modules\Admin\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;

class AdminDashboardController extends SystemCoreHttpController
{
    public function __construct()
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function index(Request $Request, ActionResponse $Response)
    {
        return view('admin::dashboard');
    }

    public function editorUpload(Request $Request, ActionResponse $Response)
    {
        if (! $Request->hasFile('file')) {
            return [
                'status'  => 'FAIL',
                'message' => 'no_file_to_save',
            ];
        }
        // $PATH = $Request->file->store('editor', 'graph');
        $FILE = app('GraphManager')->process('editor', $Request->file);
        $PATH = app('GraphManager')->src('original', 'editor', $FILE);
        return [
            'status'   => 'OK',
            'location' => $PATH,
        ];
    }
}
