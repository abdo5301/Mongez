<?php

namespace App\Modules\System;

use App\Libs\AreasData;
use App\Models\{Area, Client, PropertyParameter, RequestParameter, Staff, PermissionGroup};
use Illuminate\Http\Request;
use App\Http\Requests\StaffFormRequest;
use Form;
use Auth;
use App;

class AjaxController extends SystemController{

    public function index(Request $request){

        switch ($request->type){

            case 'readNotification':
                foreach (Auth::user()->unreadNotifications as $notification) {
                    $notification->markAsRead();
                }
                break;

            case 'getNextAreas':
                return AreasData::getNextAreas($request->id);
                break;

            case 'updateCallDate':
                App\Models\Property::where('id',$request->id)
                    ->update([
                        'call_update'=> date('Y-m-d H:i:s')
                    ]);
                break;

            case 'dropdownMenuArea':
                $id = $request->id;


                if($id == 0){
                    return [
                        'area_type_id'=> 1,
                        'areas'=> Area::where('area_type_id',1)->get(['id','name_'.\App::getLocale().' as name'])
                    ];
                }

                $data = AreasData::getNextAreas($id);

                $returnData = [];
                if(!empty($data['areas'])){
                    foreach ($data['areas'] as $key => $value){
                        $returnData[]= [
                            'id'=> $value['id'],
                            'name'=> $value['name_'.\App::getLocale()]
                        ];
                    }

                    return [
                        'area_type_id'=> $data['type']->id,
                        'areas'=> $returnData
                    ];
                }

                return [];

                break;

            case 'investor':
                $word = $request->word;

                return Client::where('status','active')
                    //->where('type','investor')
                    ->where(function($query) use ($word) {
                        $query->where('name','LIKE','%'.$word.'%')
                            ->orWhere('company_name','LIKE','%'.$word.'%')
                            ->orWhere('mobile1','LIKE','%'.$word.'%')
                            ->orWhere('mobile2','LIKE','%'.$word.'%');
                    })
                    ->get([
                        'id',
                        \DB::raw('CONCAT(name," (",mobile1,") ") as value')
                    ]);

                break;

            case 'client':
                $word = $request->word;

                return Client::where('status','active')
                  //  ->where('type','client')
                    ->where(function($query) use ($word) {
                        $query->where('name','LIKE','%'.$word.'%')
                            ->orWhere('company_name','LIKE','%'.$word.'%')
                            ->orWhere('mobile1','LIKE','%'.$word.'%')
                            ->orWhere('mobile2','LIKE','%'.$word.'%');
                    })
                    ->get([
                        'id',
                        \DB::raw('CONCAT(name," (",mobile1,") ") as value')
                    ]);

                break;

            case 'investor-client':
                $word = $request->word;

                $data = Client::where('status','active')
                    ->where(function($query) use ($word) {
                        $query->where('name','LIKE','%'.$word.'%')
                            ->orWhere('company_name','LIKE','%'.$word.'%')
                            ->orWhere('mobile1','LIKE','%'.$word.'%')
                            ->orWhere('mobile2','LIKE','%'.$word.'%');
                    })
                    ->get([
                        'id',
                        'name',
                        'type',
                        'mobile1'
                    ]);

                if(!$data) return [];

                $returnData = [];
                foreach ($data as $key => $value){
                    $returnData[] = [
                        'id'=> $value->id,
                        'value'=> $value->name.' ( '.__(ucfirst($value->type)).' ) ( '.$value->mobile1.' )'
                    ];
                }

                return $returnData;

                break;
            case 'sales':
                $word = $request->word;

                return Staff::whereIn('permission_group_id',explode(',',setting('sales_group')))
                    ->where('status','active')
                    ->where(function($query) use ($word) {
                        $query->where('firstname','LIKE','%'.$word.'%')
                            ->orWhere('lastname','LIKE','%'.$word.'%');
                    })
                    ->get([
                        'id',
                        \DB::raw('CONCAT(firstname,\' \',lastname) as value')
                    ]);

                break;
            case 'area':
                $word = $request->word;

                $data = Area::where(function($query) use ($word) {
                    $query->where('name_ar','LIKE','%'.$word.'%')
                        ->orWhere('name_en','LIKE','%'.$word.'%');
                })->get([
                    'id'
                ]);

                if($data->isEmpty()){
                    return [];
                }

                $result = [];

                foreach ($data as $key => $value){
                    $result[] = [
                        'id'=> $value->id,
                        'value'=> str_replace($word,'<b>'.$word.'</b>',implode(' -> ',AreasData::getAreasUp($value->id,true) ))
                    ];
                }

                return $result;

                break;
            case 'parameters-form':

                $propertyTypeID = $request->property_type_id;
                $propertyID     = $request->property_id;


                $data = App\Models\Parameter::where('property_type_id',$propertyTypeID)
                    ->orderBy('position','ASC')
                    ->get();

                if($data->isEmpty()){
                    return;
                }


                if($propertyID){
                    $property = PropertyParameter::where('property_id',$propertyID)->first();
                    if($property){
                        $this->viewData['property'] = $property;
                    }
                }

                $this->viewData['result'] = $data;

                return $this->view('property.parameters-form',$this->viewData);

                break;
            case 'parameters-request-form':

                $propertyTypeID = $request->property_type_id;
                $requestID      = $request->request_id;

                $data = App\Models\Parameter::where('property_type_id',$propertyTypeID)
                    ->where('show_in_request','yes')
                    ->orderBy('position','ASC')
                    ->get();

                if($data->isEmpty()){
                    return;
                }


                if($requestID){
                    $requestData = RequestParameter::where('request_id',$requestID)->first();
                    if($requestData){
                        $this->viewData['request_data'] = $requestData;
                    }
                }


                $this->viewData['result'] = $data;

                return $this->view('request.parameters-form',$this->viewData);

                break;

        }

    }

}
