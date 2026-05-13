<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_onboarding_is_redirected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('onboarding'));
    }

    public function test_user_with_completed_onboarding_can_access_home(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'disability_condition' => ['tunarungu'],
            'communication_preference' => ['full_teks'],
            'work_environment' => ['remote'],
            'skills' => ['Figma', 'Laravel'],
            'onboarding_completed' => true,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
