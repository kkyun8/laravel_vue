<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Log;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Photo;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ファイルアップロード()
    {
        // S3ではなくテスト用のストレージを使用する
        // → storage/framework/testing
        Storage::fake('s3');
        Log::debug('$this');
        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                // ダミーファイルを作成して送信している
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが201(CREATED)であること
        $response->assertStatus(201);

        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // DBに挿入されたファイル名のファイルがストレージに保存されていること
        Storage::cloud()->assertExists($photo->filename);
    }

    /**
     * @test
     */
    public function should_DBエラーの場合、ファイル保存しない()
    {
      // DBエラー起こす
      Schema::drop('photos');

      Storage::fake('s3');

      $response = $this->actingAs($this->user)
          ->json('POST', route('photo.create'), [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
          ]);

      $response->assertStatus(500);

      $this->assertEquals(0, count(Storage::cloud()->files()));

    }

    /**
     * @test
     */
    public function should_ファイル保存エラーの場合、DB更新しない()
    {
        // エラーを起こす
        Storage::shouldReceive('cloud')
          ->once()
          ->andReturnNull();
      
        $response = $this->actingAs($this->user)
          ->json('POST', route('photo.create'), [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
          ]);

          $response->assertStatus(500);
          $this->assertEmpty(Photo::all());
    }

}
