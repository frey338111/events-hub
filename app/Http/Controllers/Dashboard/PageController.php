<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function showBySlug(string $slug)
    {
        $page = Pages::query()
            ->select(['title', 'slug', 'content'])
            ->where('slug', $slug)
            ->where('published', true)
            ->first();

        if (! $page) {
            return response()->json([
                'message' => 'Page not found.',
            ], 404);
        }

        return response()->json($page);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Pages::query()
            ->select(['id', 'title', 'slug', 'published', 'created_at'])
            ->latest()
            ->paginate(12);

        return view('dashboard.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published' => ['nullable', 'boolean'],
        ]);

        Pages::create([
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['slug'] ?: $validated['title']),
            'content' => $validated['content'],
            'published' => $request->boolean('published'),
        ]);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', 'Page created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pages $page)
    {
        return view('dashboard.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pages $page)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published' => ['nullable', 'boolean'],
        ]);

        $page->update([
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['slug'] ?: $validated['title'], $page->id),
            'content' => $validated['content'],
            'published' => $request->boolean('published'),
        ]);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', 'Page updated.');
    }

    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'upload' => ['required', 'image', 'max:5120'],
        ]);

        $path = $validated['upload']->store('pages/content', 'public');

        return response()->json([
            'url' => 'http://laravel.test'.Storage::url($path),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pages $page)
    {
        $page->delete();

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', 'Page deleted.');
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'page';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Pages::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
