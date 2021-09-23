<?php

namespace Tests\UseCases;

use App\Models\Comment;
use App\Models\Photo;
use App\Models\User;
use App\UseCases\PhotoAddCommentUseCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PhotoAddCommentUseCaseTest extends TestCase
{
    use DatabaseTransactions;

    private $photo;
    private $user;
    private $usecase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->photo = $this->user->photos()->save(factory(Photo::class)->make([
            'user_id' => $this->user->id
        ]));
        $this->usecase = new PhotoAddCommentUseCase();
    }

    /** @test */
    public function コメントを追加できる()
    {
        $this->usecase->execute($this->user->id, $this->photo->id, $comment = 'テストコメント');
        $this->assertCount(1, Comment::get());
    }
}
