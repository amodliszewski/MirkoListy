<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledPost extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getDates()
    {
        return array_merge(parent::getDates(), array('post_at'));
    }
}