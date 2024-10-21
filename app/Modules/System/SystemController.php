<?php

namespace App\Modules\System;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Importer;
use App\Models\ImporterData;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\Request;
use App\Models\RequestStatus;
use App\Notifications\General;

class SystemController extends Controller{

    protected $viewData = [
        'breadcrumb'=> []
    ];

    public function __construct(){

        $this->middleware(['auth:staff']);
        $this->viewData['property_status_menu'] = PropertyStatus::get([
            'id',
            'name_ar',
            'name_en'
        ]);

        $this->viewData['request_status_menu'] = RequestStatus::get([
            'id',
            'name_ar',
            'name_en'
        ]);

    }

    protected function view($file,array $data = []){
        return view('system.'.$file,$data);
    }

    protected function response($status,$code = '200',$message = 'Done',$data = []): array {
        return [
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ];
    }

    public function dashboard(){

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Dashboard')
        ];

        $this->viewData['pageTitle'] = __('Dashboard');

        $this->viewData['clients'] = number_format(Client::where('type','client')->count());
        $this->viewData['investor'] = number_format(Client::where('type','investor')->count());

        $this->viewData['properties'] = number_format(Property::count());
        $this->viewData['requests'] = number_format(Request::count());

        return $this->view('dashboard',$this->viewData);
    }

}