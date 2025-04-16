<?php

namespace Modules\Website\Http\Controllers;

use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\ContactUs\Entities\ContactUsService;
use DB;

class WebsiteController extends SystemCoreHttpController
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('website::index');
    }

    public function postContactRequest(Request $Request, ActionResponse $Response, ContactUsService $ContactUsService)
    {
        $ContactUsService->unguard();
        $this->data['Validator'] = $ContactUsService->validator('create', $Request->all());
        $this->data['Validator']->validate();

        if (
                str_contains($Request->phone_number, '+5556660606') ||
                str_contains($Request->email, 'sample@email.tst') ||
                str_contains($Request->name, 'ARzZfuRY') ||
                str_contains($Request->subject, 'XOR(if(n') ||
                str_contains($Request->subject, 'select(0') ||
                str_contains($Request->message, 'select(0') ||
                str_contains($Request->phone_number, '5556660606') ||
                str_contains($Request->email, 'testing@example.com') ||
                str_contains($Request->name, 'QPbmCRVM') || 
                str_contains($Request->subject, '(SELECT') ||
                str_contains($Request->message, '(SELECT') 
                
            ) 
            { 
                return $Response->module('Products')->code('200')->success()->withOnlyToAjaxWeb([
                    'toastr' => [
                        'type'        => 'success',
                        'title'       => 'عذراً لك يكتمل الطلب',
                        'description' => ' لايمكنك التقديم في الوقت الحالي',
                    ],
                ]);
            }
        try {
            DB::transaction(function() use($Request, $ContactUsService) {
                $ContactUsService->create($Request->all());
            });
        } 
        catch (Exception $e) {
            throw $e;
        }
        return $Response->module('Website')->code('200')->success()->withOnlyToAjaxWeb([
            // 'toastr' => [
            //     'type'        => 'success',
            //     'title'       => 'لقد تم ارسال الطلب بنجاح',
            //     'description' => 'لقد تم ارسال طلبك للتواصل بنجاح سيتم التواصل معكم بأقرب وقت ممكن',
            // ],
            'redirect' => route('WebsiteController@index')
        ]);
    }
}
