<?php

namespace App\UseCases;

use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoPostingUseCase
{
    public function execute($upload_photo)
    {
        $photo = new Photo();
        $photo->filename = $photo->id . '.' . $upload_photo->extension();
        Storage::cloud()->putFileAs('', $upload_photo, $photo->filename);

        DB::transaction(function () use ($photo) {
            Auth::user()->photos()->save($photo);
        });

        return $photo->id;
    }
}
