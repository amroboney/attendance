<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\EmployeeEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController as BaseController;

class EventController extends BaseController
{
    public $employeeID;

    //get all active event
    public function index()
    {
        $this->employeeID = Auth::user()->id;        
        $events = EmployeeEvent::with('events')
            ->where('date',  '>', now()->subDays(30)->endOfDay())
            ->where('employee_id', $this->employeeID)->get();
        
        $mapEvents =$this->mapingEvents($events);
        return $this->handleResponse($mapEvents, 'get Event successfully');
    }


    public function getEvents()
    {
        $this->employeeID = Auth::user()->id;
        $events = EmployeeEvent::with('events')->whereHas('events', function($event) use ($today){
            $event->whereDate('date', '>=', $today);
        })
        ->where('date',  '>', now()->subDays(30)->endOfDay())
        ->where('employee_id', $this->employeeID)->get();

        return $this->handleResponse($attendance, 'attendance data');
    }


    // get active event 
    public function getActiveEvents($empId)
    {
        $activeEvents =  EmployeeEvent::with('events')->whereHas('events', function($event) {
            $event->whereDate('date', '>=', $this->today);
        })->where('employee_id', $empId)->get();
        return $this->mapingEvents($activeEvents);
    }



    public function mapingEvents($activeEvents)
    {
        $events = [];
        foreach ($activeEvents as $key => $event) {
            $arrEvent['id']            = $event->id;
            $arrEvent['event_id']      = $event->event_id;
            $arrEvent['name']          = $event->events->description;
            $arrEvent['description']   = $event->events->description;
            $arrEvent['date']          = $event->events->date;
            $arrEvent['coordiante']    = $event->events->lat .','. $event->events->lng;
            $arrEvent['area']          = $event->events->area;
            $arrEvent['start_time']    = $event->events->start_time;
            $arrEvent['end_time']      = $event->events->end_time;
            $arrEvent['clock_in']      = $event->clock_in;
            $arrEvent['clock_out']     = $event->clock_out;
            $arrEvent['event_id']      = $event->event_id;
            $events[] = $arrEvent;
        }
        return $events;
    }

    // check events Exist or not
    public function checkEvent($empId){
        $eventCount = count($this->getActiveEvents($empId));
        if ($eventCount != 0) {return true;} else {return false;};
    }

}
