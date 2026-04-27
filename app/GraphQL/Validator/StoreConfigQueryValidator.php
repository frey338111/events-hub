<?php

namespace App\GraphQL\Validator;

use Nuwave\Lighthouse\Validation\Validator;

class StoreConfigQueryValidator extends Validator
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'regex:/^[A-Za-z0-9-]+$/'],
            'names' => ['sometimes', 'array'],
            'names.*' => ['required', 'regex:/^[A-Za-z0-9-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The config name may only contain letters, numbers, and dashes.',
            'names.*.regex' => 'Config names may only contain letters, numbers, and dashes.',
        ];
    }
}
