<?php

namespace App\GraphQL\Queries;

use App\Models\StoreConfig;

class StoreConfigQuery
{
    public function all(): \Illuminate\Support\Collection
    {
        return StoreConfig::query()->orderBy('name')->get();
    }

    public function byName($_, array $args): ?StoreConfig
    {
        return StoreConfig::where('name', $args['name'])->first();
    }

    public function byNames($_, array $args): \Illuminate\Support\Collection
    {
        $names = $args['names'] ?? [];

        return StoreConfig::whereIn('name', $names)->get();
    }
}
