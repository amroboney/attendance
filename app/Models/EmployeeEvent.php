<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEvent extends Model
{
    use HasFactory;

    public function events()
    {
        return $this->belongsTo('App\Models\Event', 'event_id', 'id');
    }
}
