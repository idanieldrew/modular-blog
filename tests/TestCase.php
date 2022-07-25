<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Module\Role\Models\Role;
use Module\User\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function CreateUser($type = 'writer')
    {
        // Create role
        $role = Role::create(['name' => $type]);
        // Create new user with type admin
        $user = User::factory()->create();

        $user->assignRole($type);
        // actingAs
        Sanctum::actingAs($user);

        return [$user->id, $role->id];
    }
}
