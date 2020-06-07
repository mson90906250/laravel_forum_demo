<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use RecordsActivity;

    protected $fillable = ['favorited_id', 'user_id', 'favorited_type'];

    public function favorited()
    {
        return $this->morphTo();
    }
}
