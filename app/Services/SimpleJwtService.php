<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Str;

class SimpleJwtService
{
    protected string $secret;

    protected int $ttl;

    public function __construct(?string $secret = null, ?int $ttlSeconds = null)
    {
        $key = $secret ?? config('app.key');
        $this->secret = Str::startsWith($key, 'base64:')
            ? base64_decode(substr($key, 7))
            : $key;

        $this->ttl = $ttlSeconds ?? 3600; // default 1 hour
    }

    public function ttl(): int
    {
        return $this->ttl;
    }

    public function createToken(Customer $customer, ?int $ttlSeconds = null): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $ttl = $ttlSeconds ?? $this->ttl;
        $now = time();
        $payload = [
            'sub' => $customer->id,
            'email' => $customer->email,
            'iat' => $now,
            'exp' => $now + $ttl,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];

        $signature = $this->sign(implode('.', $segments));
        $segments[] = $signature;

        return implode('.', $segments);
    }

    public function decode(string $token): ?array
    {
        $segments = explode('.', $token);
        if (count($segments) !== 3) {
            return null;
        }

        [$header64, $payload64, $signature] = $segments;
        $signed = $header64.'.'.$payload64;

        if (! hash_equals($this->sign($signed), $signature)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payload64), true);
        if (! $payload || ! is_array($payload)) {
            return null;
        }

        if (! empty($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    protected function sign(string $data): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->secret, true));
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}
