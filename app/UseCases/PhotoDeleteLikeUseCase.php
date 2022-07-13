<?php

namespace App\UseCases;

use App\Models\Photo;

class PhotoDeleteLikeUseCase
{
    public function execute(String $photo_id, int $auth_user_id)
    {
        $photo = Photo::where('id', $photo_id)->with('likes')->first();

        if (is_null($photo) || $photo->likes->count() === 0 && $photo->likes[0]->id !== $auth_user_id) {
            return null;
        }

        $photo->likes()->detach($auth_user_id);

        return $photo->id;
    }
}
