<?php

namespace Tests\Http\Controllers;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function create_ステータス201が返る()
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function index_ステータス200が返る()
    {
        $response = $this->actingAs($this->user)
            ->json('GET', route('photo.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function index_写真一覧のJSONデータが取得できる()
    {
        factory(Photo::class, 5)->create();

        $response = $this->json('GET', route('photo.index'));

        $response->assertJsonCount(5);
    }
}
