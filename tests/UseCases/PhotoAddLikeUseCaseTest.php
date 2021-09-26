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

        $photo = Photo::with('likes')->get();
        $this->assertCount(1, $photo->likes);
    }
}
