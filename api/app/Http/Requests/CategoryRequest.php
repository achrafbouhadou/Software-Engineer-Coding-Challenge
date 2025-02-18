<?php

namespace App\Http\Requests;

use App\Rules\ParentCategoryHasNoChild;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => ['nullable', 'exists:categories,id', new ParentCategoryHasNoChild],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a valid text string.',
            'name.max' => 'The category name must not exceed 255 characters.',
            'name.unique' => 'A category with this name already exists.',

            'parent_id.exists' => 'The selected parent category does not exist.',
        ];
    }
}
