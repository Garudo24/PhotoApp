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
    public function create_追加された写真のIDがjsonデータに含まれている()
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        $this->assertNotNull($response->json()['add_photo_id']);
    }

    /** @test */
    public function index_ステータス200が返る()
    {
        $response = $this->json('GET', route('photo.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function index_写真一覧のJSONデータが取得できる()
    {
        $this->user->photos()->saveMany(factory(Photo::class, 5)->make());
        $response = $this->json('GET', route('photo.index'));
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function download_対象ファイルが見つからない場合は404を返す()
    {
        Storage::fake('s3');
        factory(Photo::class)->create();
        $photo = Photo::first();

        $response = $this->actingAs($this->user)
            ->json('GET', "/photos/$photo->id/download");

        $response->assertStatus(404);
    }

    /** @test */
    public function download_ファイルをダウンロードできた場合はステータス200を返す()
    {
        Storage::fake('s3');
        $download_file = UploadedFile::fake()->image('test_image.jpg');
        $this->user->photos()->save(factory(Photo::class)->make());
        $photo = Photo::first();
        $photo->filename = $photo->id . $download_file->extension();
        $photo->save();
        Storage::cloud()->putFileAs('', $download_file, $photo->filename);

        $response = $this->actingAs($this->user)
            ->json('GET', "/photos/$photo->id/download");

        $response->assertStatus(200);
    }

    /** @test */
    public function should_ステータスコード200が返る()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();
        $response = $this->actingAs($this->user)->json('GET', route('photo.show', [
            'photo_id' => $photo->id,
        ]));

        $response->assertStatus(200);
    }

    /** @test */
    public function should_詳細写真のJSONを返却する()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();
        $response = $this->actingAs($this->user)->json('GET', route('photo.show', [
            'photo_id' => $photo->id,
        ]));

        $response->assertJsonFragment(
            [
                'id' => $photo->id,
                'url' => $photo->url,
                'user' => [
                    'name' => $photo->user->name,
                ],
            ]
        );
    }

    /** @test */
    public function addComment_ステータスコード201が返る()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();
        $content = 'テストコメント';
        $response = $this->actingAs($this->user)->json('POST', route('photo.comment', [
            'photo_id' => $photo->id,
        ]), compact('content'));

        $response->assertStatus(201);
    }

    /** @test */
    public function like_ステータスコード200が返る()
    {
        $photo = factory(Photo::class)->create();
        $param = ['photo_id' => $photo->id];
        $response = $this->actingAs($this->user)->json('PUT', route('photo.like', $param));

        $response->assertStatus(200);
    }

    /** @test */
    public function unlike_ステータスコード200が返る()
    {
        $photo = factory(Photo::class)->create();
        $param = ['photo_id' => $photo->id];
        $response = $this->actingAs($this->user)->json('PUT', route('photo.unlike', $param));

        $response->assertStatus(200);
    }
}
