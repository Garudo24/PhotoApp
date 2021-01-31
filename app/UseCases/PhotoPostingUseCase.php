<?php

namespace App\UseCases;

use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoPostingUseCase
{
    public function execute($user_id, $image_file)
    {
        Storage::cloud()->putFileAs('', $image_file, $image_file->name);

        DB::transaction(function () use ($user_id, $image_file) {
            Photo::create([
                'user_id' => $user_id,
                'filename' => $image_file->name
            ]);
        });
    }
}
