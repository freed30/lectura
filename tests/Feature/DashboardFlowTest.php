<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_readers_to_their_profile_dashboard(): void
    {
        $reader = User::factory()->create([
            'role' => 'reader',
        ]);

        $response = $this->actingAs($reader)->get(route('dashboard'));

        $response->assertRedirect(route('profile.edit'));
    }

    public function test_dashboard_redirects_admins_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
