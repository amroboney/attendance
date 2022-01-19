<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\App\BaseController as BaseController;

class StatusController extends BaseController
{
    public $status;
    public function __construct()
    {
        $this->status = [
            "status" => [
                100 => 'Successfully',
                107 => 'Inpute Validation Error',
                'login status' => [
                    100 => 'Successfully',
                    101 => 'Check admin to activate device id ' ,
                    102 => 'Unauthorised' 
                ],
                'check' => [
                    100 => 'You have successfully clocked in',
                    103 => 'You have been marked absent for today',
                    104 => 'Your attendance for today has already been marked',
                    105 => 'You have to clock in first',
                    106 => 'you dont have attendance for today'
                ]
            ],

            "parameter" => [
                "checkType" => ["checkIn","checkOut","checkInEvent", "checkOutEvent"],
                "leaveformType" => ["single_leaves","date_range"],
                "halfleaveType" => ["yes","no"],
                "application_status" => ['pending','approved','rejected'],
            ]
           

        ];
    }

    public function index()
    {
        return $this->handleResponse($this->status, 'get Event successfully');
    }
}
