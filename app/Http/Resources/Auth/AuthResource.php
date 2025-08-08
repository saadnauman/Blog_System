<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class AuthResource extends JsonResource
{
    protected $token;

    public function __construct($resource, $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    public function toArray($request)
    {
        return [
            'data' => new UserResource($this->resource),
            'meta' => [
                'token'       => $this->token,
                'token_type'  => 'Bearer',
                'roles'       => $this->resource->getRoleNames(),
                'permissions' => $this->resource->getAllPermissions()->pluck('name')
            ]
        ];
    }
}
