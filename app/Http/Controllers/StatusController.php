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
                'login status' => [
                    100 => 'Successfully',
                    101 => 'Check admin to activate device id ' ,
                    102 => 'Unauthorised' 
                ],
                'check' => [
                    100 => 'You have successfully clocked in',
                    103 => 'You have been marked absent for today',
                    104 => 'Your attendance for today has already been marked',
                    105 => 'You have to clock in first'
                ]
            ],

            "parameter" => [
                "checkType" => ["checkIn","checkOut","checkInEvent", "checkOutEvent"],
            ]
           

        ];
    }

    public function index()
    {
        return $this->handleResponse($this->status, 'get Event successfully');
    }
}
