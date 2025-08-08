<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'post',
            'id'   => (string) $this->id,
            'attributes' => [
                'title'      => $this->title,
                'body'       => $this->body,
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'author' => [
                    'id'   => (string) $this->user_id,
                    'name' => $this->user?->name,
                ],
                'category' => [
                    'id'   => (string) $this->category_id,
                    'name' => $this->category?->name,
                ],
                'tagged_users' => $this->taggedUsers->map(function ($user) {
                    return [
                        'id'   => (string) $user->id,
                        'name' => $user->name,
                    ];
                }),
            ],
        ];
    }
}
