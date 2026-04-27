<?php

namespace App\GraphQL\Queries;

use App\Models\Pages;

class PageQuery
{
    public function pageBySlug($_, array $args): ?Pages
    {
        return Pages::query()
            ->where('slug', $args['slug'])
            ->where('published', true)
            ->first();
    }
}
