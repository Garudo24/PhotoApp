<?php

namespace Tests\Http\Controllers\Auth;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function 登録済みのユーザーで認証できる()
    {
        $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => $this->user->password,
        ]);

        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function 認証後JSONを返却する()
    {
        $response = $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => $this->user->password,
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);
    }
}
