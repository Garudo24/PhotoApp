<?php

namespace Tests\UseCases;

use App\Models\Photo;
use App\Models\User;
use App\UseCases\PhotoAddLikeUseCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PhotoAddLikeUseCaseTest extends TestCase
{
    use DatabaseTransactions;

    private $usecase;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->usecase = new PhotoAddLikeUseCase();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function 指定されたIDの写真にいいねをついかできる()
    {
        $photo = factory(Photo::class)->create();
        $this->actingAs($this->user);
        $this->usecase->execute((String)$photo->id, $this->user->id);

        $photo_with_like = Photo::with('likes')->first();
        $this->assertCount(1, $photo_with_like->likes);
    }

    /** @test */
    public function いいねを追加した写真のIDが返ってくる()
    {
        $photo = factory(Photo::class)->create();
        factory(Photo::class, 2)->create();
        $this->actingAs($this->user);
        $photo_id = $this->usecase->execute((String)$photo->id, $this->user->id);

        $this->assertCount(3, Photo::get());
        $this->assertEquals($photo->id, $photo_id);
    }

    /** @test */
    public function 写真が存在しない場合nullが返る()
    {
        $this->actingAs($this->user);
        $this->assertNull($this->usecase->execute('1', $this->user->id));
    }

    /** @test */
    public function すでにいいねを行っている場合nullが返る()
    {
        $photo = factory(Photo::class)->create();
        factory(Photo::class, 2)->create();
        $this->actingAs($this->user);
        $this->usecase->execute((String)$photo->id, $this->user->id);
        $photo_id = $this->usecase->execute((String)$photo->id, $this->user->id);

        $this->assertNull($photo_id);
    }
}
