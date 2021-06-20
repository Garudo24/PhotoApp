<?php

namespace Tests\UseCases;

use App\Models\Photo;
use App\Models\User;
use App\UseCases\PhotoPostingUseCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoPostingUseCaseTest extends TestCase
{
    use DatabaseTransactions;

    private $usecase;
    private $image_file;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
        $this->usecase = new PhotoPostingUseCase();
        $this->image_file = UploadedFile::fake()->image('test_image.jpg');
    }


    /** @test */
    public function 渡されたイメージファイルをS3にアップロードできる()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $this->usecase->execute($this->image_file);

        Storage::cloud()->assertExists(Auth::user()->photos()->first()->filename);
    }

    /** @test */
    public function photoテーブルにアップロードした画像情報を登録できる()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $this->usecase->execute($this->image_file);
        $this->assertCount(1, Photo::get());
    }

    /** @test */
    public function photoテーブルに保存されるIDが12桁のランダムな文字列である()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $this->usecase->execute($this->image_file);
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', Photo::first()->id);
    }
}
