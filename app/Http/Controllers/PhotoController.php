<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComment;
use App\Http\Requests\StorePhoto;
use App\Models\Photo;
use App\UseCases\PhotoAddCommentUseCase;
use App\UseCases\PhotoAddLikeUseCase;
use App\UseCases\PhotoDownloadUseCase;
use App\UseCases\PhotoPostingUseCase;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    /**
     * 写真一覧
     */
    public function index()
    {
        $photos = Photo::with(['user', 'likes'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return $photos;
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @param PhotoPostingUseCase $usecase
     * @return \Illuminate\Http\Request
     */
    public function create(StorePhoto $request, PhotoPostingUseCase $usecase)
    {
        $uploaded_file = $request->file('photo');
        $add_photo_id = $usecase->execute($uploaded_file);
        return response(['add_photo_id' => $add_photo_id], 201);
    }

    /**
     * 写真ダウンロード
     * @param String $photo_id
     * @param PhotoDownloadUseCase $usecase
     * @return \Illuminate\Http\Response
     */
    public function download(String $photo_id, PhotoDownloadUseCase $usecase)
    {
        $download_file = $usecase->execute($photo_id);
        // 写真の存在チェック
        if (is_null($download_file)) {
            abort(404);
        }

        $disposition = 'attachment; filename="' . $download_file['file_name'] . '"';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => $disposition,
        ];

        return response($download_file['file'], 200, $headers);
    }

    /**
     * 写真詳細
     * @param String $photo_id
     * @return App\Models\Photo
     */
    public function show(String $photo_id)
    {
        return Photo::where('id', $photo_id)->with(['user', 'comments.user', 'likes'])->firstOrFail();
    }

    /**
     * コメント追加
     * @param String $photo_id
     * @param StoreComment $request
     * @param PhotoDownloadUseCase $usecase
     * @return \Illuminate\Http\Response
     */
    public function addComment(String $photo_id, StoreComment $request, PhotoAddCommentUseCase $usecase)
    {
        return response($usecase->execute(Auth::id(), $photo_id, $request->input('content')), 201);
    }

    /**
     * いいね
     * @param string $photo_id
     * @return array
     */
    public function like(string $photo_id, PhotoAddLikeUseCase $usecase)
    {
        return response($usecase->execute($photo_id, Auth::id()));
    }

    /**
     * いいね解除
     * @param string $id
     * @return array
     */
    public function unlike(string $photo_id)
    {
        return;
    }
}
