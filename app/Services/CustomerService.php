<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class CustomerService
{
    public function findOrCreateFromOrder(Order $order): Customer
    {
        if ($order->customer_id !== null) {
            return $order->customer;
        }

        $order->loadMissing('shippingAddress');
        $address = $order->shippingAddress;
        $meta = $order->metadata ?? [];

        $displayName = trim(collect([
            $address?->first_name,
            $address?->last_name,
        ])->filter()->implode(' '));

        if ($displayName === '') {
            $displayName = 'Walk-in customer';
        }

        $email = $meta['email'] ?? null;
        $phone = $address?->phone ?? ($meta['phone'] ?? null);

        $existing = Customer::query()
            ->when(filled($email), fn ($q) => $q->where('email', $email))
            ->when(blank($email) && filled($phone), fn ($q) => $q->where('phone', $phone))
            ->first();

        if ($existing !== null) {
            $order->update(['customer_id' => $existing->id]);

            return $existing;
        }

        $customer = Customer::query()->create([
            'display_name' => $displayName,
            'email' => $email,
            'phone' => $phone,
            'billing_address_line1' => $address?->street_address,
            'billing_city' => $address?->city,
            'billing_postal_code' => $address?->postal_code,
            'is_active' => true,
        ]);

        $order->update(['customer_id' => $customer->id]);

        return $customer;
    }

    public function createFromAddress(array $data): Customer
    {
        return Customer::query()->create([
            'display_name' => trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')) ?: 'Customer',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address_line1' => $data['street_address'] ?? null,
            'billing_city' => $data['city'] ?? null,
            'billing_postal_code' => $data['postal_code'] ?? null,
            'is_active' => true,
        ]);
    }
}
