<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getDates()
    {
        return array_merge(parent::getDates(), array('posted_at'));
    }
}