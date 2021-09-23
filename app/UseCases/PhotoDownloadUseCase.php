<?php

namespace App\UseCases;

use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoDownloadUseCase
{
    public function execute(String $photo_id)
    {
        $photo = Photo::firstWhere('id', $photo_id);
        if (is_null($photo)) {
            return null;
        }

        if (!Storage::cloud()->exists($photo->filename)) {
            return null;
        }

        return [
            'file' => Storage::cloud()->get($photo->filename),
            'file_name' => $photo->filename
        ];
    }
}
