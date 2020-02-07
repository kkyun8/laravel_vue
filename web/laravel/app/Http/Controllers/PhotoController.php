<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要
        $this->middleware('auth');
    }

   /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */…
    public function create(StorePhoto $request)
    {
        // 投稿写真の拡張子を取得する
        $extension = $request->photo->extension();

        $photo = new Photo();

        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせてファイル名とする
        $photo->filename = $photo->id . '.' . $extension;

        // S3にファイルを保存する
        // 第三引数の'public'はファイルを公開状態で保存するため
        Storage::cloud()
            ->putFileAs('', $request->photo, $photo->filename, 'public');

        DB::beginTransaction();

        try {
          Auth::user()->photos()->save($photo);
          DB::commit();
        } catch (\Exception $exception) {
          DB::rollBack();
          Storage::cloud()->delete($photo->filename);
          throw $exception;
        }
        //status>>201 created
        return response($photo, 201);
    }
}
