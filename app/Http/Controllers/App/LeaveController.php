<?php

namespace App\Http\Controllers\App;

use App\Models\Leavetype;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController as BaseController;

class LeaveController extends BaseController
{
    protected $companyId;
    //getData
    public function getData() 
    {
        $this->companyId = Auth::user()->company_id;
        return Leavetype::where('company_id', $this->companyId)->pluck('leaveType','leaveType');
        // return $this->leaveTypes;
    }

    // check events Exist or not
    public function checkLeave(){
        $eventCount = count($this->getData());
        if ($eventCount != 0) {return true;} else {return false;};
    }
}
