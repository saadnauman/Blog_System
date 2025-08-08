<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');
        // Admins can edit all posts, users only their own
        return $this->user()->hasRole('admin') || auth()->id() === $post->user_id;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|exists:categories,id',
            'title'       => 'sometimes|string|max:255',
            'body'        => 'sometimes|string',
            //'is_published'=> 'boolean'
        ];
    }
}
