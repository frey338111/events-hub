<?php

namespace App\GraphQL\Validator;

use Nuwave\Lighthouse\Validation\Validator;

class PageQueryValidator extends Validator
{
    public function rules(): array
    {
        return [
            'slug' => ['required', 'regex:/^[A-Za-z0-9-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain letters, numbers, and dashes.',
        ];
    }
}
