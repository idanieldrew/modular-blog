<?php

namespace Module\User\tests\Feature\CrudUser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Module\User\Models\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function super_can_update_user()
    {
        $this->CreateUser('super');
        $user = User::factory()->create();

        $this->patch(
            route('user.update',$user->id),
            ['email' => 'test@test.co'])
            ->assertStatus(200);

        $this->assertDatabaseHas('users',['email' => 'test@test.co']);
    }

    /** @test */
    public function user_can_not_update_other_user()
    {
        $firstUser = $this->CreateUser('user');
        $secondUser = User::factory()->create();

        $this->patch(
            route('user.update',$secondUser->id),
            ['email' => 'test@test.co'])
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_update_own_information()
    {
        $user = $this->CreateUser('user');

        $this->patch(
            route('user.update',$user->id),
            ['email' => 'test@test.co'])
            ->assertStatus(200);

        $this->assertDatabaseHas('users',['email' => 'test@test.co']);
    }
}
