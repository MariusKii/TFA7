<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EloquentAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_populate_five_records_each_and_preserve_data_on_rerun(): void
    {
        $this->seed();
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $customer = Customer::firstOrFail();
        $customer->update(['phone' => '09111111111']);
        $user = User::firstOrFail();
        $user->update(['password' => 'a-test-only-password']);
        $password = $user->password;

        $this->seed();

        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertSame('09111111111', $customer->fresh()->phone);
        $this->assertSame($password, $user->fresh()->password);
        $this->assertTrue(Hash::check('a-test-only-password', $user->fresh()->password));
    }

    public function test_customers_page_displays_eloquent_records_including_database_changes(): void
    {
        $customer = Customer::factory()->create(['full_name' => 'Database Customer']);
        $response = $this->get('/customers');
        $response->assertOk()->assertViewIs('customers');
        $this->assertInstanceOf(Collection::class, $response->viewData('customers'));
        foreach (['full_name', 'email', 'phone', 'status'] as $field) {
            $response->assertSee($customer->$field);
        }
        $customer->update(['full_name' => 'Updated Database Customer']);
        $this->get('/customers')->assertSee('Updated Database Customer');
    }

    public function test_users_page_displays_eloquent_records_including_username(): void
    {
        $user = User::factory()->create(['full_name' => 'Database Staff', 'username' => 'database.staff']);
        $response = $this->get('/users');
        $response->assertOk()->assertViewIs('users');
        $this->assertInstanceOf(Collection::class, $response->viewData('users'));
        foreach (['username', 'full_name', 'email', 'role', 'status'] as $field) {
            $response->assertSee($user->$field);
        }
        $user->update(['username' => 'updated.staff']);
        $this->get('/users')->assertSee('updated.staff');
        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function test_empty_tables_render_helpful_messages(): void
    {
        $this->get('/customers')->assertOk()->assertSee('No customers found.')->assertSee('0 records');
        $this->get('/users')->assertOk()->assertSee('No users found.')->assertSee('0 records');
    }

    public function test_database_values_are_escaped_in_both_views(): void
    {
        $payload = '<script>alert("test")</script>';
        Customer::factory()->create(['full_name' => $payload]);
        User::factory()->create(['full_name' => $payload]);
        foreach (['/customers', '/users'] as $path) {
            $this->get($path)->assertSee($payload)->assertDontSee($payload, false);
        }
    }

    public function test_customer_phone_may_be_null(): void
    {
        Customer::factory()->create(['phone' => null]);
        $this->get('/customers')->assertOk();
        $this->assertDatabaseHas('customers', ['phone' => null]);
    }

    public function test_username_is_unique_at_database_level(): void
    {
        User::factory()->create(['username' => 'unique.staff']);
        $this->expectException(QueryException::class);
        User::factory()->create(['username' => 'unique.staff']);
    }
}
