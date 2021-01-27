<?php

namespace App\UseCases;

use Illuminate\Support\Facades\Storage;

class PhotoPostingUseCase
{
    public function execute($image_file)
    {
        Storage::cloud()->putFileAs('', $image_file, $image_file->name);
    }
}
