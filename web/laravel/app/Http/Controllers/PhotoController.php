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
        $this->middleware('auth')->except(['index', 'download']);
    }

    public function index()
    {
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();
    
        return $photos;
    }

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

    public function download(Photo $photo)
    {
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
