<?php

namespace App\UseCases;

use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Support\Facades\DB;

class PhotoAddCommentUseCase
{
    public function execute(int $user_id, String $photo_id, String $comment_text)
    {
        $arguments = [
            'user_id' => $user_id,
            'photo_id' => $photo_id,
            'comment_text' => $comment_text
        ];
        $comment = new Comment();

        DB::transaction(function () use ($comment, $arguments) {
            $comment->user_id = $arguments['user_id'];
            $comment->photo_id = $arguments['photo_id'];
            $comment->content = $arguments['comment_text'];
            $comment->save();

            $photo = Photo::where('id', $arguments['photo_id'])->first();
            $photo->comments()->save($comment);
        });

        return Comment::where('id', $comment->id)->with('user')->first();
    }
}
