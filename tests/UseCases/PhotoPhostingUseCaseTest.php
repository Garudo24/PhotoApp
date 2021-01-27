<?php

namespace Tests\UseCases;

use App\Models\Photo;
use App\UseCases\PhotoPostingUseCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
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
        $this->usecase->execute($this->image_file);
        Storage::cloud()->assertExists($this->image_file->name);
    }

    /** @test */
    public function photoテーブルにアップロードした画像情報を登録できる()
    {
        $this->usecase->execute($this->image_file);
        $this->assertCount(1, Photo::get());
    }
}
