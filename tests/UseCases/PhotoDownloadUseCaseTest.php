<?php

namespace Tests\UseCases;

use App\Models\Photo;
use App\Models\User;
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

        $this->download_file = UploadedFile::fake()->image('test_image.jpg');
        $photo = new Photo();
        $photo->user_id = factory(User::class)->create()->id;
        $photo->filename = $photo->id . $this->download_file->extension();
        $photo->save();

        $this->photo = Photo::first();
        $this->usecase = new PhotoDownloadUseCase();
    }

    /** @test */
    public function DBに登録されたphoto_idでS3からファイルをダウンロードできる()
    {
        Storage::cloud()->putFileAs('', $this->download_file, $this->photo->filename);
        $download_file = $this->usecase->execute($this->photo->id);
        $this->assertNotNull($download_file);
    }

    /** @test */
    public function 値が返される場合ファイル名も返される()
    {
        Storage::cloud()->putFileAs('', $this->download_file, $this->photo->filename);
        $download_file = $this->usecase->execute($this->photo->id);
        $this->assertEquals($this->photo->filename, $download_file['file_name']);
    }

    /** @test */
    public function DBに登録されたidがS3にない場合nullを返す()
    {
        $download_file = $this->usecase->execute($this->photo->id);
        $this->assertNull($download_file);
    }
}
