<?php

namespace App\Http\Controllers;

use App\Events\CustomerVerificationRequested;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerRegisterController extends Controller
{
    public function store(Request $request)
    {
        // Validate fields
        $validator = \Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'password' => ['required', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Create customer
        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

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

        return response()->json([
            'message' => 'Registration successful',
            'customer' => $customer,
        ]);
    }
}
