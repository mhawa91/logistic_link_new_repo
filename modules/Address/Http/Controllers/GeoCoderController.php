<?php

namespace Modules\Address\Http\Controllers;

use DB;
use Validator;
use Exception;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\SystemCore\Entities;
use Modules\Address\Entities\LocationService;

class GeoCoderController extends SystemCoreHttpController
{
    public function __construct()
    {
        // $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));
    }

    public function search(Request $Request, ActionResponse $Response, LocationService $location)
    {
        $validator = Validator::make($Request->all(), [
            'address' => 'required|max:2500',
        ]);
        $validator->validate();
        try {
            $location->search($Request->address);
            DB::transaction(function() use($Request, $location) {
                $this->data['models'] = $location->firstOrCreate();
            });
        } 
        catch (Exception $e) {
            return $Response->module('Address')->code('200')->fail()->with(
                $this->response([], $location)
            );
        }
        return $Response->module('Address')->code('200')->success()->with(
            $this->response($this->data['models'], $location)
        );
    }

    public function reverse(Request $Request, ActionResponse $Response, LocationService $location)
    {
        $validator = Validator::make($Request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);
        $validator->validate();
        try {
            $location->geo($Request->lat, $Request->lng);
            DB::transaction(function() use($Request, $location) {
                $this->data['models'] = $location->firstOrCreate();
            });
        } 
        catch (Exception $e) {
            return $Response->module('Address')->code('200')->fail()->with(
                $this->response([], $location)
            );
        }
        return $Response->module('Address')->code('200')->success()->with(
            $this->response($this->data['models'], $location)
        );
    }

    private function response(array $models, LocationService $location)
    {
        $this->data['locale'] = in_array(request()->locale ?? app()->getLocale(), ['ar', 'en']) ? (request()->locale ?? app()->getLocale()) : 'ar';
        return [
            'lat'          => $location->data($this->data['locale'], 'lat'),
            'lng'          => $location->data($this->data['locale'], 'lng'),
            'postal_code'  => $location->data($this->data['locale'], 'postal_code'),
            'street'       => ! empty($street = $location->data($this->data['locale'], 'street')) ? $street['long_name'] : null,
            'full_address' => $location->data($this->data['locale'], 'full_address'),
            'country'      => empty($models) || ! $models['country'] ? null : [
                'id'   => $models['country']->id,
                'text' => $models['country']->{'name_' . $this->data['locale']},
                'data' => $models['country']->toArray(),
            ],
            'city'         => empty($models) || ! $models['city'] ? null : [
                'id'   => $models['city']->id,
                'text' => $models['city']->{'name_' . $this->data['locale']},
                'data' => $models['city']->toArray(),
            ],
            'municipality' => empty($models) || ! $models['municipality'] ? null : [
                'id'   => $models['municipality']->id,
                'text' => $models['municipality']->{'name_' . $this->data['locale']},
                'data' => $models['municipality']->toArray(),
            ],
            'neighborhood' => empty($models) || ! $models['neighborhood'] ? null : [
                'id'   => $models['neighborhood']->id,
                'text' => $models['neighborhood']->{'name_' . $this->data['locale']},
                'data' => $models['neighborhood']->toArray(),
            ],
        ];
    }
}
