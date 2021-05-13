<?php

namespace App\UseCases;

use Illuminate\Support\Facades\Storage;

class PhotoDownloadUseCase
{
    public function execute(String $filename)
    {
        if (!Storage::cloud()->exists($filename)) {
            return null;
        }
        return Storage::cloud()->get($filename);
    }
}
