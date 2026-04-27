<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\StoreConfig;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = json_decode(
            (string) StoreConfig::query()->where('name', 'site-nav')->value('value'),
            true
        );

        return view('dashboard.menu.index', [
            'menuItems' => is_array($menuItems) ? $menuItems : [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_json' => ['required', 'string'],
        ]);

        StoreConfig::query()->updateOrCreate(
            ['name' => 'site-nav'],
            ['value' => $validated['menu_json']]
        );

        return redirect()
            ->route('dashboard.menu.index')
            ->with('success', 'Menu saved.');
    }
}
