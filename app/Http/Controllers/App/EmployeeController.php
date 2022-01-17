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
use App\Http\Controllers\App\FrontBaseController;

class EmployeeController extends BaseController
{
    public $employee;
    public $setting;
    public $branch;
    public $events;

    
 
    public function getUserData()
    {
        
        $this->today = $this->today->format("Y-m-d");
        try {
            $data = [];
            $data['today'] = $this->today;
            $this->employee = Auth::user();
            $this->getBranch();
            $this->getEvents();
            
            $data['today_attendance'] = null;
            $attendance = Attendance::where('employee_id', $this->employee->id)->whereDate('created_at', $this->today)->exists();
            if ($attendance) {
                $attendances = Attendance::where('employee_id', $this->employee->id)->whereDate('created_at', $this->today)->first();
                $data['today_attendance'] = $attendances;
            }
            $data['status_attendance'] = $attendance;
            $this->attendanceActive = 'active';        

            $timeZone = Company::find(Auth::user()->company_id)->timezone;
            $data['timeZone'] = $timeZone;
            $local_time = Carbon::now(new \DateTimeZone($timeZone));
            $data['local_time'] = $local_time;

            $data['branch_id'] = $this->branch->id;
            $data['branch_name'] = $this->branch->name;
            $data['area'] = $this->branch->area;
            $data['branch_coordinate'] = $this->branch->lat. ','. $this->branch->lng;
            $data['event_status'] = $this->checkEvent();
            $data['events'] = $this->events;

            $ip_address = $_SERVER['REMOTE_ADDR'];
            return $this->handleResponse($data, 'attendance data');
        } catch (\Throwable $th) {
            $this->setLog('login funciton', $th);
            return $this->handleError('System Error', '', 400);
        }
        
    }


    // get branch
    public function getBranch() : void
    {
        $this->branch =  Branch::find($this->employee->branch_id);
    }
    
    // get attendance 
    public function getAttendance()
    {
        $this->employee = Auth::user();
        $attendance = Attendance::where('created_at',  '>', now()->subDays(30)->endOfDay())
            ->where('employee_id', '=', $this->employee->id)
            ->orderBy('date')
            ->get();
        return $this->handleResponse($attendance, 'attendance data');
    }

    // get active event
    public function getEvents() : void
    {
        $this->events = EmployeeEvent::with('events')->whereHas('events', function($event) {
            $event->whereDate('date', '>=', $this->today);
        })->where('employee_id', $this->employee->id)->get();
    }

    public function checkEvent(){
        $eventCount = count($this->events);
        if ($eventCount != 0) {return true;} else {return false;};
    }


}
