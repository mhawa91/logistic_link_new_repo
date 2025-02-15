<?php
namespace Modules\SystemCore\Http\Responses;

use Exception;
use Illuminate\Contracts\Support\Responsable;

class ActionResponse implements Responsable
{
    protected string $module = 'SystemCore';

    protected bool $status   = true;

    protected string $code   = '200';

    protected array $data    = [];

    protected array $onlyWeb = [];

    protected $exception     = null;

    /**
     * Create new instances for dependencies.
     *
     * @param NULL
     */
    public function __construct()
    {
    }
    
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return request()->ajax() || $request->is('api/*') ? $this->apiResponse($request) : $this->webResponse($request);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiResponse($request)
    {
        $Response = [
            'status'  => $this->status,
            'code'    => $this->code,
            'title'   => $this->getTitle(),
            'message' => $this->getMessage(),
            'hint'    => $this->getHint(),
            'result'  => $this->getResult(),
            'errors'  => $this->getErrors(),
        ];
        if ($request->header('platform', null) == 'WEB') {
            $Response = array_merge($Response, $this->onlyWeb);
            if (isset($Response['redirect_url']) || isset($Response['redirect'])) {
                session()->flash('system_response', $Response);
            }
        }
        return response()->json($Response, 200);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function webResponse($request)
    {
        $Response = [
            'status'  => $this->status,
            'code'    => $this->code,
            'title'   => $this->getTitle(),
            'message' => $this->getMessage(),
            'hint'    => $this->getHint(),
            'result'  => $this->getResult(),
            'errors'  => $this->getErrors(),
        ];
        $Response = array_merge($Response, $this->onlyWeb);
        if (isset($Response['redirect_url']) || isset($Response['redirect'])) {
            return redirect()->to( $Response['redirect_url'] ?? $Response['redirect'] )->withInput()->with('system_response', $Response);
        }
        return redirect()->back()->withInput()->with('system_response', $Response);
    }

    /**
     * set module name to use for translations.
     *
     * @param  array  $module
     * @return ActionResponse
     */
    public function module(string $module)
    {
        $this->module = strtolower($module);
        return $this;
    }

    /**
     * set module name to use for translations.
     *
     * @param  array  $code
     * @return ActionResponse
     */
    public function code(string $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Add an HTTP data that represents the object.
     *
     * @param  array  $data
     * @return ActionResponse
     */
    public function with(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Add an HTTP data only to requests that has platform = WEB HEADER.
     *
     * @param  array  $data
     * @return ActionResponse
     */
    public function withOnlyToAjaxWeb(array $onlyWeb)
    {
        $this->onlyWeb = array_merge($this->onlyWeb, $onlyWeb);
        return $this;
    }

    /**
     * Add an exception data and return with the response if the debug is enabled.
     *
     * @param  Exception $e
     * @return ActionResponse
     */
    public function exception(Exception $e)
    {
        $this->exception = $e;
        return $this;
    }

    /**
     * Create an HTTP success response.
     *
     * @return ActionResponse
     */
    public function success()
    {
        $this->status = true;
        return $this;
    }

    /**
     * Create an HTTP fail response.
     *
     * @return ActionResponse
     */
    public function fail()
    {
        $this->status = false;
        return $this;
    }

    public function getTitle()
    {
        return __($this->module . '::api.responses.' . $this->code . '.title');
    }

    public function getMessage()
    {
        return __($this->module . '::api.responses.' . $this->code . '.message');
    }

    public function getHint()
    {
        return __($this->module . '::api.responses.' . $this->code . '.hint');
    }

    public function getResult()
    {
        return ! $this->status ? null : $this->data;
    }

    public function getErrors()
    {
        return ! $this->status ? $this->data : null;
    }
}