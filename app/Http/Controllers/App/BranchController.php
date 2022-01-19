<?php

namespace App\Http\Controllers\App;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\App\BaseController as BaseController;

class BranchController extends BaseController 
{
    // get branch
    public function getBranch($branchId) 
    {
        
        return  Branch::find($branchId);
        $this->branch_id            =  $branch->id;
        $this->branch_name          =  $branch->name;
        $this->area                 =  $branch->area;
        $this->branch_coordinate    =  $branch->lat. ','. $branch->lng;
        return $this->data;
    }
}
