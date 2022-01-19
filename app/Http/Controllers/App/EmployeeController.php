<?php

namespace App\Http\Controllers\App;


use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\EmployeeEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController;
use App\Http\Controllers\App\EventController;
use App\Http\Controllers\App\LeaveController;
use App\Http\Controllers\App\BranchController;
use App\Http\Controllers\App\FrontBaseController;

class EmployeeController extends BaseController
{
    public $employee;
    // public $branch;    
 
    public function getUserData()
    {
        $this->today = $this->today->format("Y-m-d");
        // try {
            // $this->today = $this->today;
            $this->employee = Auth::user();

            // get Branch data
            $branch = new BranchController();
            $newBranch = $branch->getBranch($this->employee->branch_id);

            $this->branch_id            = $newBranch->id;
            $this->branch_name          = $newBranch->name;
            $this->area                 = $newBranch->area;
            $this->branch_coordinate    = $newBranch->lat. ','. $newBranch->lng;

            // return today attedance
            $attendance = new AttendanceController();
            $this->status_attendance = $attendance->checkTodayAttendance($this->employee->id);
            $this->today_attendance  = $attendance->getTodayAttendance($this->employee->id);
            
            // return time zone and local time
            $timeZone = Company::find(Auth::user()->company_id)->timezone;
            $this->timeZone = $timeZone;
            $local_time = Carbon::now(new \DateTimeZone($timeZone));
            $this->local_time = $local_time;

            // get events 
            $event = new EventController();
            $this->events = $event->getActiveEvents($this->employee->id);
            $this->event_status = $event->checkEvent($this->employee->id);
            
            // get leave data 
            $leaveType = new LeaveController();
            $this->leaveTypes = $leaveType->getData();
            $this->leave_statue = $leaveType->checkLeave();

            $ip_address = $_SERVER['REMOTE_ADDR'];
            return $this->handleResponse($this->data, 'attendance data');
        // } catch (\Throwable $th) {
        //     $this->setLog('login funciton', $th);
        //     return $this->handleError('System Error', '', 400);
        // }
        
    }


    
    
    

    

    


}
