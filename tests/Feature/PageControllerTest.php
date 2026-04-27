<?php

namespace Tests\Feature;

use App\Models\Pages;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard.pages.create'));

        $response->assertOk();
    }

    public function test_page_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('dashboard.pages.store'), [
                'title' => 'About Us',
                'slug' => '',
                'content' => 'Simple page body.',
                'published' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard.pages.index'));

        $this->assertDatabaseHas('pages', [
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => 'Simple page body.',
            'published' => true,
        ]);
    }

    public function test_duplicate_slug_gets_incremented(): void
    {
        $user = User::factory()->create();

        Pages::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => 'Existing page.',
            'published' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('dashboard.pages.store'), [
                'title' => 'About Us',
                'content' => 'Second page body.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard.pages.index'));

        $this->assertDatabaseHas('pages', [
            'title' => 'About Us',
            'slug' => 'about-us-2',
            'content' => 'Second page body.',
            'published' => false,
        ]);
    }

    public function test_published_page_can_be_fetched_by_slug(): void
    {
        Pages::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => '<p>Simple page body.</p>',
            'published' => true,
        ]);

        $response = $this->getJson(route('api.pages.show', ['slug' => 'about-us']));

        $response
            ->assertOk()
            ->assertJson([
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<p>Simple page body.</p>',
            ]);
    }

    public function test_unpublished_page_cannot_be_fetched_by_slug(): void
    {
        Pages::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'content' => '<p>Draft content.</p>',
            'published' => false,
        ]);

        $response = $this->getJson(route('api.pages.show', ['slug' => 'draft-page']));

        $response
            ->assertNotFound()
            ->assertJson([
                'message' => 'Page not found.',
            ]);
    }
}
