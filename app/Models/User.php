<?php
namespace App\Models;

use Ramsey\Uuid\Uuid;
use WykoCommon\Models\User as Base;

class User extends Base
{
    protected $casts = [
        'id' => 'integer',
        'rights' => 'integer',
        'color' => 'integer',
        'sex' => 'integer',
        'call_optout' => 'integer',
        'created_count' => 'integer',
        'joined_count' => 'integer',
        'called_count' => 'integer'
    ];

    public function spamlists()
    {
        return $this->belongsToMany('App\Models\Spamlist', 'user_spamlists');
    }

    public function scheduled()
    {
        return $this->hasMany('App\Models\ScheduledPost');
    }

    /**
     * Boot extension for handling facade usage
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->api_key = Uuid::uuid4()->toString();
        });
    }

    /**
     * Extended constructor that adds new uuid
     *
     * @param array $attributes
     * @throws \Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (empty($this->api_key)) {
            $this->api_key = Uuid::uuid4()->toString();
        }
    }
}
