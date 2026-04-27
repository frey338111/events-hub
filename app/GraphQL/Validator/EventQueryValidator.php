<?php

namespace App\GraphQL\Validator;

use Nuwave\Lighthouse\Validation\Validator;

class EventQueryValidator extends Validator
{
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'numeric'],
            'first' => ['nullable', 'numeric'],
            'search' => ['nullable', 'regex:/^[A-Za-z0-9-]+$/'],
            'type' => ['nullable', 'numeric'],
            'location' => ['nullable', 'numeric'],
            'monthStart' => ['nullable', 'date'],
            'url_key' => ['nullable', 'regex:/^[A-Za-z0-9_-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.regex' => 'Search may only contain letters, numbers, and dashes.',
            'url_key.regex' => 'The event URL key may only contain letters, numbers, dashes, and underscores.',
        ];
    }
}
