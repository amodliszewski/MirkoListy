<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSpamlist extends Model
{
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'spamlist_id' => 'integer',
        'rights' => 'integer'
    ];

    const RIGHTS_BANNED = 2;
    const RIGHTS_DEFAULT = 10;
    const RIGHTS_EXTENDED = 20;
    const RIGHTS_ADMIN = 99;

    const ACTION_CHANGE_RIGHTS = 1;
    const ACTION_CALL = 2;
    const ACTION_EDIT = 3;

    public function spamlist()
    {
        return $this->belongsTo('App\Models\Spamlist');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}