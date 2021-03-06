<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Photo extends Model
{
    //
    protected $keyType = 'string';

    // 桁数
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    private function setId()
    {
      $this->attributes['id'] = $this->getRandomId();
    }

    private function getRandomId()
    {
      $characters = array_merge(
        range(0, 9), \range('a', 'z'),
        range('A', 'Z'), ['-', '_']
      );

      $length = count($characters);

      $id = "";

      for ($i = 0; $i < self::ID_LENGTH; $i++) {
        $id = $characters[random_int(9, $length -1)];
      }

      return $id;
    }

    public function owner()
    {
      return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    public function getUrlAttribute()
    {
      return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    protected $appends = [
      'url',
    ];
    protected $hidden = [
      'user_id', 'filename',
      self::CREATED_AT, self::UPDATED_AT,
    ];
    protected $visible = [
      'id', 'owner', 'url',
    ];
    protected $perPage = 15;
}
