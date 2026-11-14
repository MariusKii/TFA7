<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_forms_render_csrf_fields_edit_values_and_navigation(): void
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();
        foreach (['/customers/create', '/users/create', "/customers/{$customer->id}/edit", "/users/{$user->id}/edit"] as $url) {
            $response = $this->get($url)->assertOk()->assertSee('name="_token"', false);
            $this->assertSame(1, substr_count($response->getContent(), 'aria-current="page"'));
        }
        $this->get("/customers/{$customer->id}/edit")->assertSee($customer->full_name);
        $this->get("/users/{$user->id}/edit")->assertSee($user->username)->assertSee('multipart/form-data');
    }

    public function test_customer_creation_uses_only_validated_fields(): void
    {
        $this->post('/customers', ['full_name' => 'New Customer', 'email' => 'new@example.com', 'status' => 'Inactive'])
            ->assertRedirect(route('customers.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['full_name' => 'New Customer', 'status' => 'Active']);
        $this->get('/customers')->assertSee('Customer created successfully.');
    }

    public function test_customer_validation_preserves_old_input_and_displays_errors(): void
    {
        $this->get('/customers/create')->assertOk();
        $this->from('/customers/create')->post('/customers', ['full_name' => '', 'email' => 'bad-email'])
            ->assertRedirect('/customers/create')->assertSessionHasErrors(['full_name', 'email'])
            ->assertSessionHasInput('email', 'bad-email');
        $this->withCookie(session()->getName(), session()->getId());
        $response = $this->get('/customers/create');
        $response->assertSee('bad-email')->assertSee('field-error', false);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_customer_edit_updates_one_record_and_preserves_other_columns(): void
    {
        $customer = Customer::factory()->create(['phone' => '09123456789', 'status' => 'Inactive']);
        $this->put("/customers/{$customer->id}", ['full_name' => 'Updated Customer', 'email' => 'update@example.com'])
            ->assertRedirect(route('customers.index'))->assertSessionHas('success');
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Updated Customer', 'phone' => '09123456789', 'status' => 'Inactive']);
        $this->put("/customers/{$customer->id}", ['full_name' => '', 'email' => 'invalid'])->assertSessionHasErrors(['full_name', 'email']);
        $this->assertSame('Updated Customer', $customer->fresh()->full_name);
    }

    public function test_user_creation_handles_required_authentication_columns_and_hashes_password(): void
    {
        $this->post('/users', ['username' => 'new.user', 'full_name' => 'New User', 'email' => 'new@example.com',
            'password' => 'valid-password', 'password_confirmation' => 'valid-password', 'role' => 'Administrator'])
            ->assertRedirect(route('users.index'))->assertSessionHas('success');
        $user = User::sole();
        $this->assertSame('New User', $user->name);
        $this->assertSame('Staff', $user->role);
        $this->assertTrue(Hash::check('valid-password', $user->password));
    }

    public function test_user_creation_rejects_duplicates_and_missing_required_fields(): void
    {
        $user = User::factory()->create();
        $this->from('/users/create')->post('/users', ['username' => $user->username, 'full_name' => '', 'email' => $user->email, 'password' => 'short'])
            ->assertSessionHasErrors(['username', 'full_name', 'email', 'password'])
            ->assertSessionHasInput('username', $user->username);
        $this->post('/users', [])->assertSessionHasErrors(['username', 'full_name', 'email', 'password']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_edit_keeps_unchanged_username_credentials_role_and_avatar(): void
    {
        $user = User::factory()->create(['avatar' => 'avatars/existing.png']);
        $password = $user->password;
        $this->put("/users/{$user->id}", ['username' => $user->username, 'full_name' => 'Edited User', 'role' => 'Administrator', 'password' => 'tampered', 'avatar' => null])
            ->assertRedirect(route('users.index'))->assertSessionHas('success');
        $this->assertDatabaseCount('users', 1);
        $user->refresh();
        $this->assertSame('Edited User', $user->full_name);
        $this->assertSame('Edited User', $user->name);
        $this->assertSame($password, $user->password);
        $this->assertSame('Staff', $user->role);
        $this->assertSame('avatars/existing.png', $user->avatar);
    }

    public function test_user_edit_rejects_another_accounts_username(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->from("/users/{$user->id}/edit")->put("/users/{$user->id}", ['username' => $other->username, 'full_name' => 'Old input'])
            ->assertSessionHasErrors('username')->assertSessionHasInput('full_name', 'Old input');
        $this->assertNotSame($other->username, $user->fresh()->username);
    }

    public function test_valid_avatar_is_processed_to_thumbnail_and_stored_with_relative_path(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->put("/users/{$user->id}", ['username' => $user->username, 'full_name' => $user->full_name,
            'avatar' => UploadedFile::fake()->image('photo.jpg', 800, 400)->size(2048)])
            ->assertSessionHasNoErrors()->assertRedirect(route('users.index'));
        $path = $user->fresh()->avatar;
        $this->assertStringStartsWith('avatars/', $path);
        $this->assertStringEndsWith('.png', $path);
        Storage::disk('public')->assertExists($path);
        $size = getimagesize(Storage::disk('public')->path($path));
        $this->assertSame([256, 256], [$size[0], $size[1]]);
        $this->assertSame('image/png', $size['mime']);
        $this->get('/users')->assertSee(Storage::disk('public')->url($path), false);
        $this->put("/users/{$user->id}", ['username' => $user->username, 'full_name' => 'Text update'])->assertSessionHasNoErrors();
        $this->assertSame($path, $user->fresh()->avatar);
    }

    public function test_invalid_oversized_svg_and_excessive_dimensions_are_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => 'avatars/old.png']);
        $files = [UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
            UploadedFile::fake()->image('large.jpg')->size(2049),
            UploadedFile::fake()->createWithContent('image.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>'),
            UploadedFile::fake()->image('wide.png', 4097, 1)];
        foreach ($files as $file) {
            $this->put("/users/{$user->id}", ['username' => $user->username, 'full_name' => 'Rejected update', 'avatar' => $file])
                ->assertSessionHasErrors('avatar');
            $this->assertSame('avatars/old.png', $user->fresh()->avatar);
            $this->assertNotSame('Rejected update', $user->fresh()->full_name);
        }
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_storage_failure_does_not_update_user(): void
    {
        $user = User::factory()->create();
        Storage::shouldReceive('disk')->with('public')->once()->andReturnSelf();
        Storage::shouldReceive('put')->once()->andReturn(false);
        $this->put("/users/{$user->id}", ['username' => $user->username, 'full_name' => 'Unsaved', 'avatar' => UploadedFile::fake()->image('photo.png')])
            ->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
        $this->assertNotSame('Unsaved', $user->fresh()->full_name);
    }

    public function test_missing_avatars_use_placeholder_and_missing_records_return_404(): void
    {
        Storage::fake('public');
        User::factory()->create(['avatar' => null]);
        User::factory()->create(['avatar' => 'avatars/missing.png']);
        $response = $this->get('/users')->assertOk();
        $this->assertSame(2, substr_count($response->getContent(), 'images/avatar-placeholder.svg'));
        foreach (['customers', 'users'] as $resource) {
            $this->get("/{$resource}/999999/edit")->assertNotFound();
            $this->put("/{$resource}/999999", [])->assertNotFound();
        }
    }
}
