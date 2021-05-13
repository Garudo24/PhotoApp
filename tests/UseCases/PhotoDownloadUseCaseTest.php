<?php

namespace Tests\UseCases;

use App\Models\Photo;
use App\UseCases\PhotoDownloadUseCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoDownloadUseCaseTest extends TestCase
{
    use DatabaseTransactions;

    private $photo;
    private $download_file;
    private $usecase;

    // テスト用にファイルをストレージにアップロードできる状態で始める
    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
        $this->download_file = UploadedFile::fake()->image('test_image.jpg');
        $this->photo = new Photo();
        $this->photo->filename = $this->photo->id . $this->download_file->extension();
        $this->usecase = new PhotoDownloadUseCase();
    }

    /** @test */
    public function DBに登録されたfilenameでS3からファイルをダウンロードできる()
    {
        Storage::cloud()->putFileAs('', $this->download_file, $this->photo->filename);
        $download_file = $this->usecase->execute($this->photo->filename);
        $this->assertNotNull($download_file);
    }

    /** @test */
    public function DBに登録されたfilenameがS3にない場合nullを返す()
    {
        $download_file = $this->usecase->execute($this->photo->filename);
        $this->assertNull($download_file);
    }
}
