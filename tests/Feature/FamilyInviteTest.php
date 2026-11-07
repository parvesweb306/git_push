<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\FamilyInviteLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamilyInviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_rotate_family_invite_link_and_old_link_is_revoked(): void
    {
        $user = User::factory()->create([
            'family_invite_code' => str_repeat('A', 32),
        ]);
        $device = Device::create([
            'user_id' => $user->id,
            'device_name' => 'Child phone',
        ]);

        $response = $this->actingAs($user)->post(route('family.rotate'));

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $newCode = $user->fresh()->family_invite_code;
        $this->assertNotSame(str_repeat('A', 32), $newCode);
        $this->assertSame($user->id, $device->fresh()->user_id);

        $this->get(route('family.install', str_repeat('A', 32)))
            ->assertNotFound();
    }

    public function test_guest_cannot_rotate_family_invite_link(): void
    {
        $this->post(route('family.rotate'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_create_and_revoke_a_family_link(): void
    {
        config(['services.play_store.package_id' => 'com.example.childguard']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('family-links.store'), ['label' => 'Living room'])
            ->assertRedirect(route('family-links.index'));

        $link = FamilyInviteLink::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Living room', $link->label);

        $this->get(route('family.install', $link->code))
            ->assertRedirect();

        $this->postJson('/api/devices/enroll-family', [
            'family_invite' => $link->code,
            'installation_id' => 'f9e01b01-55ca-472a-9bcb-0396c91b4d29',
            'device_name' => 'New family phone',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->delete(route('family-links.destroy', $link))
            ->assertRedirect(route('family-links.index'));

        $this->get(route('family.install', $link->code))
            ->assertNotFound();

        $this->postJson('/api/devices/enroll-family', [
            'family_invite' => $link->code,
            'installation_id' => 'a97dd9f2-4c2b-4acd-8300-b442661ddabd',
            'device_name' => 'Another phone',
        ])
            ->assertNotFound();

        $this->assertDatabaseCount('devices', 1);
    }

    public function test_user_cannot_revoke_another_users_family_link(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $link = FamilyInviteLink::create([
            'user_id' => $owner->id,
            'label' => 'Private link',
            'code' => str_repeat('B', 32),
        ]);

        $this->actingAs($otherUser)
            ->delete(route('family-links.destroy', $link))
            ->assertForbidden();

        $this->assertDatabaseHas('family_invite_links', ['id' => $link->id]);
    }

    public function test_admin_can_manage_and_delete_devices_owned_by_sub_users(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $subUser = User::factory()->create(['created_by' => $admin->id]);
        $device = Device::create([
            'user_id' => $subUser->id,
            'device_name' => 'Managed phone',
        ]);
        $familyLink = FamilyInviteLink::create([
            'user_id' => $subUser->id,
            'label' => 'Sub-user family',
            'code' => str_repeat('C', 32),
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Managed phone');

        $this->get(route('family-links.index'))
            ->assertOk()
            ->assertSee('Sub-user family');

        $this->delete(route('family-links.destroy', $familyLink))
            ->assertRedirect(route('family-links.index'));

        $this->assertDatabaseMissing('family_invite_links', ['id' => $familyLink->id]);

        $this->delete(route('devices.destroy', $device))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('devices', ['id' => $device->id]);
    }
}
