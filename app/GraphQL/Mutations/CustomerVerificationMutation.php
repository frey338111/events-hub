<?php

namespace App\GraphQL\Mutations;

use App\Events\CustomerVerificationRequested;
use App\Jobs\SendCustomerPasswordResetJob;
use App\Models\Customer;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerVerificationMutation
{
    /**
     * Resend a verification email to an existing customer.
     */
    public function resend($_, array $args): bool
    {
        $validator = Validator::make($args, [
            'email' => ['required', 'email'],
        ]);
        $validator->validate();

        $customer = Customer::where('email', $args['email'])->first();

        if (! $customer) {
            return false;
        }

        if ($customer->verified) {
            return true;
        }

        // create hashkey if not exists
        if (! $customer->hash_key) {
            $hashKey = hash('sha256', implode('|', [
                $customer->customer_id,
                $customer->email,
                $customer->name,
                $customer->created_at,
            ]));
            $customer->update(['hash_key' => $hashKey]);
        }

        event(new CustomerVerificationRequested($customer));

        return true;
    }

    /**
     * Verify a customer using their hash key.
     */
    public function verify($_, array $args): bool
    {
        $validator = Validator::make($args, [
            'hash_key' => ['required', 'string'],
        ]);
        $validator->validate();

        $customer = Customer::where('hash_key', $args['hash_key'])->first();

        if (! $customer) {
            return false;
        }

        if (! $customer->hash_key) {
            return false;
        }

        $customer->verified = true;
        $customer->save();

        return true;
    }

    /**
     * Request a password reset email for a customer.
     */
    public function resetPassword($_, array $args): bool
    {
        $validator = Validator::make($args, [
            'email' => ['required', 'email'],
        ]);
        $validator->validate();

        $customer = Customer::where('email', $args['email'])->first();
        if (! $customer) {
            return true; // avoid leaking existence
        }

        $token = Str::random(64);

        PasswordReset::where('customer_email', $customer->email)->delete();

        PasswordReset::create([
            'token' => $token,
            'customer_email' => $customer->email,
            'status' => 'new',
        ]);

        SendCustomerPasswordResetJob::dispatch($customer, $token);

        return true;
    }

    /**
     * Update a customer's password using a reset token.
     */
    public function changePassword($_, array $args): bool
    {
        $validator = Validator::make($args, [
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $validator->validate();

        $reset = PasswordReset::where('token', $args['token'])
            ->where('status', 'new')
            ->first();

        if (! $reset) {
            return false;
        }

        $customer = Customer::where('email', $reset->customer_email)->first();
        if (! $customer) {
            return false;
        }

        $customer->password = Hash::make($args['password']);
        $customer->save();

        $reset->status = 'completed';
        $reset->save();

        return true;
    }
}
