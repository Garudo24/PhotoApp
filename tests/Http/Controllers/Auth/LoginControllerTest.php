<?php

namespace Tests\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    public $user;
    public $password;

    public function setUp(): void
    {
        parent::setUp();
        $this->password = 'pass1234@';
        $this->user = factory(User::class)->create(['password' => Hash::make($this->password)]);
    }

    /** @test */
    public function 登録済みのユーザーで認証できる()
    {
        $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);

        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function 認証後JSONを返却する()
    {
        $response = $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);
    }

    /** @test */
    public function 認証済みユーザーはログアウトできる()
    {
        $this->actingAs($this->user)->json('POST', route('logout'));

        $this->assertGuest();
    }

    /** @test */
    public function ログアウト後ステータス200が返る()
    {
        $response = $this->actingAs($this->user)->json('POST', route('logout'));

        $response->assertSuccessful();
    }
}
