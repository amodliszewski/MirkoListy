<?php
namespace App\Models;

use WykoCommon\Models\User as Base;

class User extends Base
{
    public function spamlists()
    {
        return $this->belongsToMany('App\Models\Spamlist', 'user_spamlists');
    }

    public function scheduled()
    {
        return $this->hasMany('App\Models\ScheduledPost');
    }
}