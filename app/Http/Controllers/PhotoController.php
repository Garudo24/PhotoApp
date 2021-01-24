<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Models\Photo;

class PhotoController extends Controller
{
    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Request
     */
    public function create(StorePhoto $request)
    {
        return response('', 201);
    }
}
