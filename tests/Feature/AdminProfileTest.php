<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_and_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
        ]);
        $user->assignRole('staff');

        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->assertOk()
            ->set('data.name', 'Updated Name')
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('staff@example.com', $user->email);
    }

    public function test_profile_page_is_registered_for_admin_panel(): void
    {
        $this->assertTrue(filament()->getCurrentPanel()?->hasProfile() ?? filament()->getDefaultPanel()->hasProfile());
        $this->assertSame(EditProfile::class, filament()->getDefaultPanel()->getProfilePage());
    }
}
