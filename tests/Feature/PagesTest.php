<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render_with_working_navigation(): void
    {
        $this->seed();

        foreach (['home', 'about', 'customers.index', 'users.index'] as $name) {
            $response = $this->get(route($name));
            $response->assertOk();

            foreach (['home', 'about', 'customers.index', 'users.index'] as $destination) {
                $response->assertSee('href="'.route($destination).'"', false);
            }

            $this->assertSame(1, substr_count($response->getContent(), 'aria-current="page"'));
        }
    }
}
