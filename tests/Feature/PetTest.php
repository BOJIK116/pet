<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_pets(): void
    {
        $this->getJson('/api/pets')
            ->assertUnauthorized();
    }

    public function test_user_can_create_pet(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/pets', [
            'name' => 'Bonya',
            'species' => 'dog',
            'breed' => 'Labrador',
            'sex' => 'female',
            'birth_date' => '2025-11-12',
            'weight' => 24.5,
            'chronic_conditions' => 'Chicken allergy',
            'is_neutered' => false,
            'notes' => 'Afraid of veterinarians',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Bonya')
            ->assertJsonPath('data.species', 'dog')
            ->assertJsonPath('data.weight', 24.5);

        $this->assertDatabaseHas('pets', [
            'user_id' => $user->id,
            'name' => 'Bonya',
            'species' => 'dog',
        ]);
    }

    public function test_user_sees_only_own_pets(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Pet::factory()->for($user)->create([
            'name' => 'Bonya',
        ]);

        Pet::factory()->for($otherUser)->create([
            'name' => 'Secret Pet',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/pets');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Bonya')
            ->assertJsonMissing([
                'name' => 'Secret Pet',
            ]);
    }

    public function test_user_can_view_own_pet(): void
    {
        $user = User::factory()->create();

        $pet = Pet::factory()->for($user)->create([
            'name' => 'Bonya',
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/pets/{$pet->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $pet->id)
            ->assertJsonPath('data.name', 'Bonya');
    }

    public function test_user_cannot_view_another_users_pet(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = Pet::factory()->for($otherUser)->create();

        Sanctum::actingAs($user);

        $this->getJson("/api/pets/{$pet->id}")
            ->assertNotFound();
    }

    public function test_user_can_update_own_pet(): void
    {
        $user = User::factory()->create();

        $pet = Pet::factory()->for($user)->create([
            'name' => 'Old Name',
            'weight' => 20,
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/pets/{$pet->id}", [
            'name' => 'New Name',
            'weight' => 21.5,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.weight', 21.5);

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'New Name',
        ]);
    }

    public function test_user_cannot_update_another_users_pet(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = Pet::factory()->for($otherUser)->create([
            'name' => 'Original Name',
        ]);

        Sanctum::actingAs($user);

        $this->patchJson("/api/pets/{$pet->id}", [
            'name' => 'Hacked Name',
        ])->assertNotFound();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'Original Name',
        ]);
    }

    public function test_user_can_delete_own_pet(): void
    {
        $user = User::factory()->create();

        $pet = Pet::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $this->deleteJson("/api/pets/{$pet->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('pets', [
            'id' => $pet->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_pet(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $pet = Pet::factory()->for($otherUser)->create();

        Sanctum::actingAs($user);

        $this->deleteJson("/api/pets/{$pet->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
        ]);
    }

    public function test_pet_validation_works(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/pets', [
            'name' => '',
            'species' => 'dragon',
            'birth_date' => now()->addDay()->toDateString(),
            'weight' => -5,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'species',
                'birth_date',
                'weight',
            ]);
    }
}