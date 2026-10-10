<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_toggle_theme_to_dark(): void
    {
        $response = $this->postJson('/toggle-theme', ['theme' => 'dark']);

        $response->assertOk()
            ->assertJson(['status' => 'success', 'theme' => 'dark']);

        $this->assertEquals('dark', session('theme'));
    }

    public function test_can_toggle_theme_to_light(): void
    {
        $response = $this->postJson('/toggle-theme', ['theme' => 'light']);

        $response->assertOk()
            ->assertJson(['status' => 'success', 'theme' => 'light']);

        $this->assertEquals('light', session('theme'));
    }

    public function test_theme_defaults_to_light_for_invalid_input(): void
    {
        $response = $this->postJson('/toggle-theme', ['theme' => 'invalid_theme']);

        $response->assertOk()
            ->assertJson(['status' => 'success', 'theme' => 'light']);

        $this->assertEquals('light', session('theme'));
    }

    public function test_login_page_renders_theme_toggle_button(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('data-theme-toggle', false)
            ->assertSee('theme-icon-light', false)
            ->assertSee('theme-icon-dark', false);
    }

    public function test_dashboard_renders_theme_toggle_button(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@admin.local')->firstOrFail();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk()
            ->assertSee('data-theme-toggle', false)
            ->assertSee('theme-icon-light', false)
            ->assertSee('theme-icon-dark', false);
    }
}
