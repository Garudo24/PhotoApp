<?php

namespace Tests\Http\Controllers\Auth;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function JSONで送信したら、新しくユーザーを作成する()
    {
        $data = [
            'name' => 'test_user',
            'email' => 'example@example.com',
            'password' => 'pass1234@',
            'password_confirmation' => 'pass1234@',
        ];

        $response = $this->json('POST', route('register'), $data);
        $user = User::first();
        $this->assertEquals($data['name'], $user->name);
    }

    /** @test */
    public function 新しいユーザーをJSON形式で返却する()
    {
        $data = [
            'name' => 'test_user',
            'email' => 'example@example.com',
            'password' => 'pass1234@',
            'password_confirmation' => 'pass1234@',
        ];

        $response = $this->json('POST', route('register'), $data);
        $user = User::first();

        $response->assertStatus(201)
            ->assertJson(['name' => $user->name]);
    }
}
