<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spamlist extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'called_count' => 'integer',
        'joined_count' => 'integer',
        'city_id' => 'integer',
        'category_id' => 'integer'
    ];

    public function users() {
        return $this->hasMany('App\Models\User');
    }

    public function calls() {
        return $this->hasMany('App\Models\Call');
    }

    public function logs() {
        return $this->hasMany('App\Models\Log');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function deletedBy() {
        return $this->belongsTo('App\Models\User', 'deleted_by');
    }

    public function city() {
        return $this->belongsTo('App\Models\City');
    }

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }

    public function getDates() {
        return array_merge(parent::getDates(), array('last_called_at'));
    }
}