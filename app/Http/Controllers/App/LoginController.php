<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController as BaseController;

class LoginController extends BaseController
{
    public $employee;
    public $company;
    public $branch;

    //login funciton
    public function login(Request $request)
    {
        try {
            if(auth()->attempt(['email' => $request->email, 'password' => $request->password])){ 
                $auth = Auth::user();
                $this->employee = $auth;
                
                $this->checkDeviceId($request->device_id);
                $this->getCompany();
                $this->getBranch();
                
                if ($this->employee->device_id != $request->device_id || $this->employee->device_status == 0) {
                    return $this->handleResponse('', 'Please send to admin to activate device id!', 101);
                }
                
                $success['name']    =  $this->employee->full_name;
                $success['gender']  =  $this->employee->gender;
                $success['status']  =  $this->employee->status;
                $success['cmopany_id']    =  $this->company->id;
                $success['company_name']    =  $this->company->company_name;
                $success['branch_id']    =  $this->branch->id;
                $success['branch_name']    =  $this->branch->name;
                $success['token']   =  $auth->createToken('Token Name')->accessToken; 
                return $this->handleResponse($success, 'User logged-in!', 100);
            } 
            else{ 
                return $this->handleResponse('Unauthorised.', 'Unauthorised', 102);
            } 
        } catch (\Throwable $th) {
            $this->setLog('login funciton', $th);
            return $this->handleError('System Error', '', 400);
        }
        
    }

    // get company
    public function getCompany() : void
    {
        $this->company = Company::find($this->employee->company_id);
    }

    // get branch
    public function getBranch() : void
    {
        $this->branch = Branch::find($this->employee->branch_id);
    }

    // check the device id exist or not and update the new
    public function checkDeviceId($deviceId) : void
    {
        if ($this->employee->device_id == null) {
            $employee = Employee::find($this->employee->id);
            $employee->device_id = $deviceId;
            $employee->device_status = 0;
            $employee->update();
        }
    }
}
