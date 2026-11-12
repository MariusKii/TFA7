<?php

namespace Tests\Feature;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PagesTest extends TestCase
{
    public function test_all_pages_render_with_working_navigation_without_database_queries(): void
    {
        DB::listen(function (QueryExecuted $query): void {
            $this->fail('The four pages must not execute database queries.');
        });

        foreach (['home', 'about', 'customers.index', 'users.index'] as $name) {
            $response = $this->get(route($name));
            $response->assertOk();
            foreach (['home', 'about', 'customers.index', 'users.index'] as $destination) {
                $response->assertSee('href="'.route($destination).'"', false);
            }
            $this->assertSame(1, substr_count($response->getContent(), 'aria-current="page"'));
        }
    }

    public function test_customers_are_rendered_from_the_controller_array(): void
    {
        $response = $this->get(route('customers.index'));
        $response->assertOk()->assertViewIs('customers');
        $records = $response->viewData('customers');
        $this->assertIsArray($records);
        $this->assertGreaterThanOrEqual(5, count($records));
        $this->assertSame(count($records) + 1, substr_count($response->getContent(), '<tr>'));
        foreach ($records as $record) {
            foreach (['id', 'name', 'email', 'phone', 'status'] as $field) {
                $response->assertSee((string) $record[$field]);
            }
        }
        $this->assertStringContainsString('@foreach ($customers as $customer)', file_get_contents(resource_path('views/customers.blade.php')));
    }

    public function test_users_are_rendered_from_the_controller_array(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertOk()->assertViewIs('users');
        $records = $response->viewData('users');
        $this->assertIsArray($records);
        $this->assertGreaterThanOrEqual(5, count($records));
        $this->assertSame(count($records) + 1, substr_count($response->getContent(), '<tr>'));
        foreach ($records as $record) {
            foreach (['id', 'name', 'email', 'role', 'status'] as $field) {
                $response->assertSee((string) $record[$field]);
            }
        }
        $this->assertStringContainsString('@foreach ($users as $user)', file_get_contents(resource_path('views/users.blade.php')));
    }

    public function test_pages_work_when_the_database_file_does_not_exist(): void
    {
        config(['database.connections.sqlite.database' => '/nonexistent/counter-pos.sqlite']);
        DB::purge('sqlite');
        foreach (['/', '/about', '/customers', '/users'] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
