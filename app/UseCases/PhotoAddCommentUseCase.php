<?php

namespace App\UseCases;

use App\Models\Comment;
use App\Models\Photo;

class PhotoAddCommentUseCase
{
    public function execute(int $user_id, String $photo_id, String $comment_text)
    {
        $comment = new Comment();
        $comment->user_id = $user_id;
        $comment->photo_id = $photo_id;
        $comment->content = $comment_text;
        $comment->save();
        $photo = Photo::where('id', $photo_id)->first();
        $photo->comments()->save($comment);

        return Comment::where('id', $comment->id)->with('user')->first();
    }
}
