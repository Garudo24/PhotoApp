<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'filename',
    ];
    /** JSONに含める属性 */
    protected $visible = [
        'id', 'user', 'url', 'comments',
    ];

    /** JSONに含める属性 */
    protected $appends = [
        'url',
    ];

    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'users');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getUrlAttribute()
    {
        return Storage::cloud()->url($this->attributes['filename']);
    }

    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z'),
            ['-', '_']
        );

        $length = count($characters);
        $id = '';

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }
}
