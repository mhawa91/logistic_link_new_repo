<?php

namespace Modules\SystemCore\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'                 => (int)    $this->id,
            'name'               => (string) $this->name,
            'username'           => (string) $this->username,
            'phone_number'       => (string) $this->phone_number,
            'email'              => (string) $this->email,
            'avatar'             => (string) $this->avatar,
            'completed_at'       => (string) $this->completed_at,
            'reviewd_at'         => (string) $this->reviewd_at,
            'sys_status'         => (string) $this->sys_status,
            // 'created_by_user_id' => (int)    $this->created_by_user_id,
            // 'updated_by_user_id' => (int)    $this->updated_by_user_id,
        ];
    }
}
