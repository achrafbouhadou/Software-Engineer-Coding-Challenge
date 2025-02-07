<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/.*\.(?:png|jpg|jpeg))$/i'],
            'image_file'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories'  => 'nullable|array',
            'categories.*'=> 'exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => 'The product name is required.',
            'name.string'         => 'The product name must be a valid string.',
            'name.max'            => 'The product name cannot exceed 255 characters.',
            
            'description.string'  => 'The description must be a valid string.',

            'price.required'      => 'The price is required.',
            'price.numeric'       => 'The price must be a number.',
            'price.min'           => 'The price must be at least 0.',

            'image.regex'      => 'The image URL must be a valid URL ending with .png, .jpg, or .jpeg.',
            'image_file.image' => 'The uploaded file must be an image.',
            'image_file.mimes' => 'Only JPG and PNG images are allowed.',
            'image_file.max'   => 'The image must not exceed 2MB.',

            'categories.array'    => 'Categories must be an array.',
            'categories.*.exists' => 'One or more selected categories are invalid.',
        ];
    }
}
