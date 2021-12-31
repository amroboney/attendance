<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\EmployeeEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController as BaseController;

class EventAttendanceController extends BaseController
{
    public $employeeID;
    public $company;

    public function checkIn(Request $request)
    {
        $this->employeeID = Auth::user()->id;
        $this->company = Company::find(Auth::user()->company_id);

        $today = Carbon::now();
        $cur_time = $today->format('H:i:s');
        $time = Carbon::now()->timezone($this->company->timezone)->format('h:i A');
        $date_time = Carbon::now()->timezone($this->company->timezone)->format("Y-m-d H:i:s");

        $employeeEvent = EmployeeEvent::where('employee_id', $this->employeeID)
                        ->where('event_id', $request->event_id)->first();

        if ($employeeEvent->clock_in != null) {
            return $this->handleResponse('','Your already been check in');
        }else{
            $employeeEvent->date = $today->format("Y-m-d");
            $employeeEvent->clock_in = $cur_time;
            $employeeEvent->notes = $request->notes;
            $employeeEvent->update();
        }
            $data = ['time' => $time, 'timeDiff' => $today->diffForHumans(), 'time_date' => $date_time];

            return $this->handleResponse($data, 'You have successfully clocked in');
    }

    function checkOut(Request $request)
    {
        // get the employee id ang company data
        $this->employeeID = Auth::user()->id;
        $this->company = Company::find(Auth::user()->company_id);
        
        $today = Carbon::now();
        $cur_time = $today->format('H:i:s');
        $time = Carbon::now()->timezone($this->company->timezone)->format('h:i A');
        $date_time = Carbon::now()->timezone($this->company->timezone)->format("Y-m-d H:i:s");


        $employeeEvent = EmployeeEvent::where('employee_id', $this->employeeID)
                        ->where('event_id', $request->event_id)->first();

        if ($employeeEvent->clock_out != null) {
            return $this->handleResponse('','Your already been check out');
        }else{
            $employeeEvent->date = $today->format("Y-m-d");
            $employeeEvent->clock_out = $cur_time;
            $employeeEvent->notes = $request->notes;
            $employeeEvent->update();
        }
            $data = ['time' => $time, 'timeDiff' => $today->diffForHumans(), 'time_date' => $date_time];

            return $this->handleResponse($data, 'You have successfully clocked in');

    }


}
