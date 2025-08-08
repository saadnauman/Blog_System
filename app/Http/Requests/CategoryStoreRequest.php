<?php

namespace App\Http\Requests;
use App\Models\User;
use App\Models\Post;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Category;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name'
        ];
    }
}
