<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    const TYPE_CALL = 1;
    const TYPE_JOINED_SELF = 10;
    const TYPE_JOINED_ADMIN = 11;
    const TYPE_LEFT_SELF = 15;
    const TYPE_LEFT_ADMIN = 16;
    const TYPE_RIGHTS_CHANGE = 20;
    const TYPE_RIGHTS_BANNED = 21;
    const TYPE_RIGHTS_CALL_OPTOUT_ON = 25;
    const TYPE_RIGHTS_CALL_OPTOUT_OFF = 26;
    const TYPE_CREATED = 30;
    const TYPE_DELETED = 31;
    const TYPE_EDITED = 40;
    const TYPE_SCHEDULED_CREATED = 45;
    const TYPE_SCHEDULED_EDITED = 46;
    const TYPE_SCHEDULED_DELETED = 47;
    const TYPE_SINGLE_CALL_VOTERS = 50;
    const TYPE_SINGLE_CALL_VOTERS_COMMENT = 51;
    const TYPE_SINGLE_CALL_COMMENTERS = 52;
    const TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS = 53;
    const TYPE_SINGLE_CALL_VOTERS_AND_COMMENTERS_COMMENT = 54;
    const TYPE_SINGLE_CALL_OWNERS = 60;
    const TYPE_SINGLE_CALL_LINK_DIGS = 70;
    const TYPE_SINGLE_CALL_LINK_BURIES = 71;
    const TYPE_SINGLE_CALL_LINK_COMMENTERS = 72;
    const TYPE_SINGLE_CALL_LINK_ALL = 73;
    const TYPE_SINGLE_LINK_CALL_VOTERS = 80;
    const TYPE_SINGLE_LINK_CALL_VOTERS_COMMENT = 81;
    const TYPE_SINGLE_LINK_CALL_COMMENTERS = 82;
    const TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS = 83;
    const TYPE_SINGLE_LINK_CALL_VOTERS_AND_COMMENTERS_COMMENT = 84;
    const TYPE_SINGLE_LINK_CALL_OWNERS = 90;
    const TYPE_SINGLE_LINK_CALL_LINK_DIGS = 95;
    const TYPE_SINGLE_LINK_CALL_LINK_BURIES = 96;
    const TYPE_SINGLE_LINK_CALL_LINK_COMMENTERS = 97;
    const TYPE_SINGLE_LINK_CALL_LINK_ALL = 98;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function subject()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function spamlist()
    {
        return $this->belongsTo('App\Models\Spamlist');
    }

    public function call()
    {
        return $this->belongsTo('App\Models\Call');
    }
}