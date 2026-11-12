<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExistingUserMigrationTest extends TestCase
{
    public function test_upgrade_backfills_existing_users_without_changing_credentials(): void
    {
        $original = DB::getDefaultConnection();
        config(['database.connections.upgrade_test' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);
        DB::setDefaultConnection('upgrade_test');

        try {
            $initial = require database_path('migrations/0001_01_01_000000_create_users_table.php');
            $initial->up();
            $id = DB::table('users')->insertGetId([
                'name' => 'Existing Account',
                'email' => 'existing@example.com',
                'password' => 'existing-hash-preserved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $upgrade = require database_path('migrations/2026_11_12_134921_add_pos_fields_to_users_table.php');
            $upgrade->up();
            $account = DB::table('users')->find($id);
            $this->assertSame('legacy_'.$id, $account->username);
            $this->assertSame('Existing Account', $account->full_name);
            $this->assertSame('existing@example.com', $account->email);
            $this->assertSame('existing-hash-preserved', $account->password);
            $this->assertSame(1, DB::table('users')->count());

            $upgrade->down();
            $this->assertFalse(Schema::hasColumn('users', 'username'));
            $this->assertSame('existing-hash-preserved', DB::table('users')->find($id)->password);
        } finally {
            DB::setDefaultConnection($original);
            DB::purge('upgrade_test');
        }
    }
}
