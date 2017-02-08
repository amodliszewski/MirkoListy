<?php
namespace App\Models;

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
}