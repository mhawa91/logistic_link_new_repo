<?php

namespace Modules\SystemCore\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthLoginRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'driver'       => $this->driver,
            'key'          => $this->key,
            'code'         => $this->code,
            'requested_at' => $this->requested_at,
        ];
    }
}
