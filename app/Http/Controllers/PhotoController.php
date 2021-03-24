<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Models\Photo;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要
        $this->middleware('auth')->except(['index']);
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Request
     */
    public function create(StorePhoto $request)
    {
        return response('', 201);
    }

    /**
     * 写真一覧
     */
    public function index()
    {
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return $photos;
    }
}
