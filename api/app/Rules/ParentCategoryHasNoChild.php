<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ParentCategoryHasNoChild implements ValidationRule
{
    /**
     * Validate that the parent category does not already have a child.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value) {
            $parentCategory = Category::where('id', $value)->first();
            if ($parentCategory && $parentCategory->parent_id) {
                $fail('The selected parent category already has a parent.');
            }
        }
    }
}
