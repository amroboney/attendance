<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    public function findForPassport($username) {
      return $this->whereEmail($username)->first();
    }


    public function company()
    {
      return $this->belongsTo('App\Models\Company');
    }

    public function barnch()
    {
      return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function companyWithBranch()
    {
      return $this->belongsTo('App\Models\Company')->with('branch');
    }
    
}
