<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model {

    protected $table = 'history';

    protected $fillable = [
        'reference_table',
        'reference_id',
        'actor_id',
        'body',
    ];
}
