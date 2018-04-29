<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledPost extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function deletedBy() {
        return $this->belongsTo('App\Models\User', 'deleted_by');
    }

    public function getDates()
    {
        return array_merge(parent::getDates(), array('post_at'));
    }
}