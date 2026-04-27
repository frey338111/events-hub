<?php

namespace App\GraphQL\Validator;

use Nuwave\Lighthouse\Validation\Validator;

class TicketQueryValidator extends Validator
{
    public function rules(): array
    {
        return [
            'ticket_id' => ['required', 'numeric'],
            'customer_id' => ['required', 'numeric'],
            'hash_key' => ['required', 'regex:/^[A-Za-z0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'hash_key.regex' => 'The hash key may only contain letters and numbers.',
        ];
    }
}
