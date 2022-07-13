<?php

namespace Tests\UseCases;

use App\Models\Photo;
use App\Models\User;
use App\UseCases\PhotoDeleteLikeUseCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PhotoDeleteLikeUseCaseTest extends TestCase
{
    use DatabaseTransactions;

    private $usecase;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->usecase = new PhotoDeleteLikeUseCase();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function 指定した写真のいいねを削除できる()
    {
        $photo = factory(Photo::class)->create();
        $this->actingAs($this->user);
        $photo->likes()->attach($this->user->id);

        $this->usecase->execute((String)$photo->id, $this->user->id);

        $photo_with_like = Photo::with('likes')->first();
        $this->assertCount(0, $photo_with_like->likes);
    }

    /** @test */
    public function 自分以外のいいねは削除できない()
    {
        $photo = factory(Photo::class)->create();
        $photo->likes()->attach($this->user->id);

        $user2 = factory(User::class)->create();
        $this->actingAs($user2);

        $this->usecase->execute((String)$photo->id, $user2->id);

        $photo_with_like = Photo::with('likes')->firstWhere('id', $photo->id);
        $this->assertCount(1, $photo_with_like->likes);
    }
}
