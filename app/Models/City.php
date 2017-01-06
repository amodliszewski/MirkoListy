<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $casts = [
        'id' => 'integer'
    ];

    public function spamlists() {
        return $this->hasMany('App\Models\Spamlist');
    }
}