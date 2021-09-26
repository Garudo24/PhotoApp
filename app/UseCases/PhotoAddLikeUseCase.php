<?php

namespace App\UseCases;

use App\Models\Photo;

class PhotoAddLikeUseCase
{
    public function execute(String $photo_id, int $auth_user_id)
    {
        $photo = Photo::where('id', $photo_id)->with('likes')->first();

        if (is_null($photo) || $photo->likes->contains('user_id', $auth_user_id) !== false) {
            return null;
        }

        $photo->likes()->attach($auth_user_id);

        return $photo->id;
    }
}
