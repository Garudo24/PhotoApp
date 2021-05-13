<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Models\Photo;
use App\UseCases\PhotoPostingUseCase;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    /**
     * 写真一覧
     */
    public function index()
    {
        $photos = Photo::with(['user'])
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
        $usecase->execute($uploaded_file);
        return response('', 201);
    }

    /**
 * 写真ダウンロード
 * @param Photo $photo
 * @return \Illuminate\Http\Response
 */
    public function download(Photo $photo)
    {
        // 写真の存在チェック
        if (! Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $disposition = 'attachment; filename="' . $photo->filename . '"';
        $headers = [
        'Content-Type' => 'application/octet-stream',
        'Content-Disposition' => $disposition,
    ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }
}
