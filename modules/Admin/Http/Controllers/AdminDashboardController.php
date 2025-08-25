<?php

namespace Modules\Admin\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContentTypes\Entities\Shippment;
use Modules\ContentTypes\Entities\TransporterContractInstance;

use Modules\SystemCore\Entities\Contracts\SystemConfigInterface;
use Modules\ContentManager\Entities\Contracts\SectionInterface;

class AdminDashboardController extends SystemCoreHttpController
{
    public function __construct(
        SectionInterface      $SectionInterface,
        SystemConfigInterface $SystemConfigInterface,
    )
    {
        $this->data['SystemConfig'] = $SystemConfigInterface;
        $this->data['Sections']     = $SectionInterface;

        $this->data['SystemConfig']->load();
        $this->data['Sections']->load();
        
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function index(Request $Request, ActionResponse $Response)
    {
        $this->data['progress'] = 10;
        if ( $Request->user()->company()->exists() ) {
            $this->data['progress'] = 35;
        }
        
        $this->data['clientShippmentCount'] = Shippment::where('company_user_id', $Request->user()->id)->count();

        $this->data['clientContractsAll']   = TransporterContractInstance::where('company_user_id', $Request->user()->id)
        ->count();

        $this->data['clientContractsNotActive']   = TransporterContractInstance::where('company_user_id', $Request->user()->id)
        ->OnlyNotActiveByStartDate()
        ->count();

        $this->data['clientContracts']      = TransporterContractInstance::where('company_user_id', $Request->user()->id)
        ->OnlyActiveByStartDate()
        ->count();

        if ($Request->user()->type == 'DEFAULT' &&  $this->data['clientContracts'] > 0) {
            $this->data['progress'] = 50;
        }

        if ($Request->user()->type == 'DEFAULT' &&  $this->data['clientShippmentCount'] > 0) {
            $this->data['progress'] = 100;
        }

        $this->data['transportarShippmentCount'] = Shippment::where('transporter_user_id', $Request->user()->id)->count();
        $this->data['transportarContracts']      = TransporterContractInstance::where('transporter_user_id', $Request->user()->id)
        ->OnlyActiveByStartDate()
        ->count();

        if ($Request->user()->type == 'TRANSPORTER' &&  $this->data['transportarShippmentCount'] > 0) {
            $this->data['progress'] = 50;
        }

        if ($Request->user()->type == 'TRANSPORTER' &&  $this->data['transportarContracts'] > 0) {
            $this->data['progress'] = 100;
        }

        return view('admin::dashboard', $this->data);
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
