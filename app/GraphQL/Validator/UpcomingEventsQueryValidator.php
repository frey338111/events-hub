<?php

namespace App\GraphQL\Validator;

use Nuwave\Lighthouse\Validation\Validator;

class UpcomingEventsQueryValidator extends Validator
{
    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'numeric'],
        ];
    }
}
