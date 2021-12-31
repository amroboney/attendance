<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;


    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }

    public function companyWithBranch()
    {
        return $this->belongsTo('App\Models\Company')->with('branch');
    }


}
