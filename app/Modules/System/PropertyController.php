<?php

namespace App\Modules\System;

use App\Http\Requests\PropertyFormRequest;
use App\Http\Requests\PropertyUploadExcelFormRequest;
use App\Libs\AreasData;
use App\Libs\CheckKeyInArray;
use App\Models\Call;
use App\Models\Client;
use App\Models\DataSource;
use App\Models\Image;
use App\Models\ImporterData;
use App\Models\Parameter;
use App\Models\Property;
use App\Models\PropertyModel;
use App\Models\PropertyParameter;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Spatie\Activitylog\Models\Activity;

class PropertyController extends SystemController
{


    private function createEditData(){
        $this->viewData['property_types'] = PropertyType::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['purposes'] = Purpose::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['data_sources'] = DataSource::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['property_status'] = PropertyStatus::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        $this->viewData['property_model'] = PropertyModel::get([
            'id',
            'name_'.App::getLocale().' as name',
            'space'
        ]);


        $this->viewData['randKey'] = md5(rand().time());

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Property::select([
                'properties.id',
                'properties.property_type_id',
                'properties.purpose_id',
                'properties.data_source_id',
                'properties.client_id',
                'properties.area_id',
                'properties.property_status_id',
                'properties.name',
                'properties.address',
                'properties.payment_type',
                'properties.price',
                'properties.currency',
                'properties.space',
                'properties.space_type',
                'properties.sales_id',
                'properties.created_by_staff_id',
                'properties.created_at'
            ])
                ->join('clients','clients.id','=','properties.client_id')
                ->with([
                    'property_type',
                    'purpose',
                    'client',
                    'property_status',
                    'sales',
                    'area'
                ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            /*
            * Start handling filter
            */

            whereBetween($eloquentData,'DATE(properties.created_at)',$request->created_at1,$request->created_at2);

            if($request->investor_type){
                $eloquentData->where('clients.investor_type',$request->investor_type);
            }

            if($request->id){
                $eloquentData->where('properties.id',$request->id);
            }


            if($request->property_type_id){
                $eloquentData->where('properties.property_type_id',$request->property_type_id);
            }


            if($request->purpose_id){
                $eloquentData->where('properties.purpose_id',$request->purpose_id);
            }


            if($request->data_source_id){
                $eloquentData->where('properties.data_source_id',$request->data_source_id);
            }


            if($request->client_id){
                $eloquentData->where('properties.client_id',$request->client_id);
            }

            if($request->area_id){
                $eloquentData->whereIn('properties.area_id',AreasData::getAreasDown($request->area_id));
            }



            if($request->building_number){
                $eloquentData->where('properties.building_number',$request->building_number);
            }


            if($request->flat_number){
                $eloquentData->where('properties.flat_number',$request->flat_number);
            }


            if($request->property_status_id){
                $eloquentData->where('properties.property_status_id',$request->property_status_id);
            }

            if($request->property_model_id){
                $eloquentData->where('properties.property_model_id',$request->property_model_id);
            }

            if($request->name){
                $eloquentData->where('properties.name','LIKE','%'.$request->name.'%');
            }


            if($request->payment_type){
               $eloquentData->where('properties.payment_type',$request->payment_type);
            }

            whereBetween($eloquentData,'properties.years_of_installment',$request->years_of_installment1,$request->years_of_installment2);
            whereBetween($eloquentData,'properties.deposit',$request->deposit1,$request->deposit2);
            whereBetween($eloquentData,'properties.price',$request->price1,$request->price2);

            if($request->currency){
                $eloquentData->where('properties.currency',$request->currency);
            }

            if($request->negotiable){
                $eloquentData->where('properties.negotiable',$request->negotiable);
            }

            whereBetween($eloquentData,'properties.space',$request->space1,$request->space2);

            if($request->space_type){
                $eloquentData->where('properties.space_type',$request->space_type);
            }

            if($request->space){ // abdo
                $eloquentData->where('properties.space','LIKE','%'.$request->space.'%');
            }

            if($request->price){ //abdo
                $eloquentData->where('properties.price','LIKE','%'.$request->price.'%');
            }

            if($request->address){
                $eloquentData->where('properties.address','LIKE','%'.$request->address.'%');
            }


            whereBetween($eloquentData,'DATE(properties.call_update)',$request->call_update1,$request->call_update2);

            if($request->sales_id){
                $eloquentData->where('properties.sales_id',$request->sales_id);
            }

            if($request->created_by_staff_id){
                $eloquentData->where('properties.created_by_staff_id',$request->created_by_staff_id);
            }

            if($request->downloadExcel){
                return exportXLS(
                    __('Properties'),
                    [
                        __('ID'),
                        __('Investor'),
                        __('Mobile 1'),
                        __('Mobile 2'),
                        __('Type'),
                        __('Purpose'),
                        __('Data Source'),
                        __('Area'),
                        __('Status'),
                        __('Name'),
                        __('Description'),
                        __('Remarks'),
                        __('Payment Type'),
                        __('Years Of Installment'),
                        __('Deposit'),
                        __('Price'),
                        __('Negotiable'),
                        __('Space'),
                        __('Video URL'),
                        __('Sales'),
                        __('Created At')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'client'=> function($data){
                            return $data->client->name.'<br />('.$data->client->investor_type.')';
                        },
                        'mobile1'=> function($data){
                            if(!staffCan('property-manage-all') && (Auth::id() != $data->sales_id || Auth::id() != $data->created_by_staff_id)){
                                return '--';
                            }
                            return $data->client->mobile1;
                        },
                        'mobile2'=> function($data){
                            if(!staffCan('property-manage-all') && (Auth::id() != $data->sales_id || Auth::id() != $data->created_by_staff_id)){
                                return '--';
                            }
                            return $data->client->mobile2;
                        },

                        'property_type_id'=> function($data){
                            return $data->property_type->{'name_'.App::getLocale()};
                        },

                        'purpose_id'=> function($data){
                            return $data->purpose->{'name_'.App::getLocale()};
                        },

                        'data_source_id'=> function($data){
                            return $data->data_source->{'name_'.App::getLocale()};
                        },
                        'area'=> function($data){
                            if(!staffCan('property-manage-all') && (Auth::id() != $data->sales_id || Auth::id() != $data->created_by_staff_id)){
                                return $data->area->{'name_'.\App::getLocale()};
                            }
                            return $data->area->{'name_'.\App::getLocale()}.'<br /><small>'.$data->address.'</small>';
                        },

                        'property_status'=> function($data){
                            return $data->property_status->{'name_'.App::getLocale()};
                        },

                        'name'=> 'name',
                        'description'=> 'description',
                        'remarks'=> 'remarks',




                        'payment_type'=> function($data){
                            return $data->payment_type;
                        },
                        'years_of_installment'=> 'years_of_installment',
                        'deposit'=> function($data){
                            return number_format($data->deposit).' '.__($data->currency);
                        },
                        'price'=> function($data){
                            return number_format($data->price).' '.__($data->currency);
                        },

                        'negotiable'=> 'negotiable',
                        'space'=> function($data){
                            return number_format($data->space).' '.__($data->space_type);
                        },
                        'video_url'=> 'video_url',
                        'sales_id'=>  function($data){
                            return $data->sales->fullname;
                        },
                        'created_at'=> function($data){
                            return $data->created_at->format('Y-m-d h:i A');
                        }
                    ]
                );
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    if(!staffCan('property-manage-all') && (Auth::id() != $data->sales_id || Auth::id() != $data->created_by_staff_id)){
                        return '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a><br />('.$data->client->investor_type.')';
                    }
                    return '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a><br />('.$data->client->investor_type.')'.'<br /><a href="tel:'.$data->client->mobile1.'">'.$data->client->mobile1.'</a>';
                })
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.App::getLocale()};
                })
                ->addColumn('space',function($data){
                    return number_format($data->space).' '.__(ucfirst($data->space_type));
                })
                ->addColumn('price',function($data){
                    return number_format($data->price).' '.$data->currency;
                })
                ->addColumn('area',function($data){
                    if(!staffCan('property-manage-all') && (Auth::id() != $data->sales_id || Auth::id() != $data->created_by_staff_id)){
                        return $data->area->{'name_'.\App::getLocale()};
                    }
                    return $data->area->{'name_'.\App::getLocale()}.'<br /><small>'.$data->address.'</small>';
                })


                ->addColumn('property_status_id',function($data){
                    return $data->property_status->{'name_'.App::getLocale()};
                })
                ->addColumn('sales_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->sales_id).'" target="_blank">'.$data->sales->fullname.'</a>';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })

                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.property.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.property.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->

                            </div>
                        </span>';
                })
                ->whitelist(['properties.id','properties.name','clients.name','clients.mobile1'])
                ->escapeColumns([])
                ->make(false);

        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Investor'),
                __('Mobile'),
                __('Type'),
                __('Purpose'),
                __('Space'),
                __('Price'),
                __('Area'),
                __('Status'),
                __('Sales'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Properties')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Properties');
            }else{
                $this->viewData['pageTitle'] = __('Properties');
            }

            $this->createEditData();

            return $this->view('property.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){

        $this->viewData['importer_data'] = null;

        if($request->importer_data_id){
            $importerData = ImporterData::where('id',$request->importer_data_id)
                ->whereNull('property_id')
                ->firstOrFail();

            $this->viewData['importer_data'] = $importerData;
        }

        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property'),
            'url'=> route('system.property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Property'),
        ];

        $this->viewData['pageTitle'] = __('Create Property');

        $this->createEditData();
        return $this->view('property.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyFormRequest $request){

        if($request->importer_data_id){
            $importerData = ImporterData::where('id',$request->importer_data_id)
                ->whereNull('property_id')
                ->firstOrFail();

            $clientData = Client::where('mobile1',$importerData->mobile)
                ->orWhere('mobile2',$importerData->mobile)
                ->first();

            if(!$clientData){
                $clientData = Client::create([
                    'type'=> 'investor',
                    'investor_type'=> 'individual',
                    'name'=> $importerData->owner_name,
                    'mobile1'=> $importerData->mobile,
                    'created_by_staff_id'=> Auth::id(),
                ]);
            }


            $clientID = $clientData->id;
        }else{
            $clientID = $request->client_id;
        }

        $propertyDataInsert = [
            'property_type_id'=> $request->property_type_id,
            'purpose_id'=> $request->purpose_id,
            'data_source_id'=> $request->data_source_id,
            'client_id'=> $clientID,
            'area_id'=> $request->area_id,
            'building_number'=> $request->building_number,
            'flat_number'=> $request->flat_number,
            'property_status_id'=> $request->property_status_id,
            'property_model_id'=> $request->property_model_id,
            'name'=> $request->name,
            'description'=> $request->description,
            'remarks'=> $request->remarks,
            'payment_type'=> $request->payment_type,
            'years_of_installment'=> $request->years_of_installment,
            'deposit'=> $request->deposit,
            'price'=> $request->price,
            'currency'=> $request->currency,
            'negotiable'=> $request->negotiable,
            'space'=> $request->space,
            'space_type'=> $request->space_type,
            'address'=> $request->address,
            'latitude'=> $request->latitude,
            'longitude'=> $request->longitude,
            'video_url'=> $request->video_url,
            'sales_id'=> $request->sales_id,
            'created_by_staff_id'=> Auth::id(),
            'call_update'=> date('Y-m-d H:i:s')
        ];

        if(in_array($request->property_status_id,explode(',','archive_property_status'))){
            $propertyDataInsert['hold_until'] = $request->hold_until;
        }else{
            $propertyDataInsert['hold_until'] = null;
        }

        $insertData = Property::create($propertyDataInsert);
        if($insertData){

            $parametersData = Parameter::where('property_type_id',$request->property_type_id)
                ->get([
                    'column_name',
                    'type',
                    'options',
                    'required'
                ]);

            $parametersDataInsert = [
                'property_id'=> $insertData->id
            ];

            if($parametersData){
                foreach ($parametersData as $key => $value){
                    $parameterValue = $request->{'p_'.$value->column_name};
                    if($parameterValue){
                        $parametersDataInsert[$value->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                    }
                }
            }

            PropertyParameter::create($parametersDataInsert);


            // Images
            Image::where('custom_key',$request->key)->update([
                'property_id'=> $insertData->id
            ]);

            if($request->importer_data_id){
                ImporterData::where('id',$request->importer_data_id)
                    ->whereNull('property_id')
                    ->update([
                        'property_id'=> $insertData->id
                    ]);
            }

            // --- Notification
            $numRequests = $insertData->requests()->count();
            if($numRequests){
                $allStaffToNotify = array_column(
                    App\Models\Staff::get(['id'])->toArray(),
                    'id'
                );
                notifyStaff(
                    [
                        'type'  => 'staff',
                        'ids'   => $allStaffToNotify
                    ],
                    __('There are :number requests related to property',['number'=> $numRequests]),
                    __('There are :number requests related to property',['number'=> $numRequests]),
                    route('system.property.show',$insertData->id)
                );
            }
            // --- Notification

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.property.show',$insertData->id)
                ]
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not add the data')
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property,Request $request){

        /*if(!staffCan('property-manage-all') && ($property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id())){
            abort(401, 'Unauthorized.');
        }*/

        if($request->isDataTable == 'call'){
            $eloquentData = $property->calls()->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'description',
                'created_by_staff_id',
                'created_at'
            ])
                ->orderByDesc('id')
                ->with([
                    'client',
                    'call_purpose',
                    'call_status',
                    'staff'
                ]);

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            if(!staffCan('call-manage-all')){
                $eloquentData->where('calls.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('client_id',function($data){
                    return '<a href="'.route('system.client.show',$data->client->id).'" target="_blank">'.$data->client->name.'</a>';
                })
                ->addColumn('call_purpose_id',function($data){
                    return '<b style="color: '.$data->call_purpose->color.'">'.$data->call_purpose->{'name_'.\App::getLocale()}.'</b>';
                })
                ->addColumn('call_status_id',function($data){
                    return '<b style="color: '.$data->call_status->color.'">'.$data->call_status->{'name_'.\App::getLocale()}.'</b>';
                })
                ->addColumn('type',function($data){
                    return __(strtoupper($data->type));
                })
                ->addColumn('created_by_staff_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" target="_blank" href="'.route('system.call.index',['call_id'=>$data->id]).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.call.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }elseif($request->isDataTable == 'true'){

            $eloquentData = $property->requests()->select([
                'requests.id',
                'requests.property_type_id',
                'requests.purpose_id',
                'requests.client_id',
                'requests.area_ids',
                'requests.request_status_id',
                'requests.name',
                'requests.payment_type',
                'requests.price_from',
                'requests.price_to',
                'requests.currency',
                'requests.space_from',
                'requests.space_to',
                'requests.sales_id',
                'requests.created_at'
            ])
                ->with([
                    'property_type',
                    'purpose',
                    'client',
                    'request_status',
                    'sales'
                ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.App::getLocale()};
                })
                ->addColumn('client_id',function($data){
                    return '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a>';
                })

                ->addColumn('request_status_id',function($data){
                    return $data->request_status->{'name_'.App::getLocale()};
                })
                ->addColumn('sales_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->sales_id).'" target="_blank">'.$data->sales->fullname.'</a>';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })

                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.request.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'log'){
            $eloquentData = Activity::with(['subject','causer'])
                ->where('subject_type','App\Models\Property')
                ->where('subject_id',$property->id)
                ->select([
                    'id',
                    'log_name',
                    'description',
                    'subject_id',
                    'subject_type',
                    'causer_id',
                    'causer_type',
                    'created_at',
                    'updated_at'
                ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('causer',function($data){
                    return '<a target="_blank" href="'.route('system.staff.show',$data->causer->id).'">'.$data->causer->fullname.'</a>';
                })
                ->addColumn('created_at','{{$created_at}}')
                ->addColumn('action',function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Properties'),
                    'url' => route('system.property.index'),
                ],
                [
                    'text' => $property->name,
                ]
            ];

            $this->viewData['pageTitle'] = $property->name;

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Price'),
                __('Space'),
                __('Bed Rooms'),
                __('Bath Room'),
                __('Owner Name'),
                __('Action')
            ];

            $this->viewData['result'] = $property;
            return $this->view('property.show', $this->viewData);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property,Request $request){
        if(!staffCan('property-manage-all') && ($property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id())){
            abort(401, 'Unauthorized.');
        }
        // Main View Vars
        $this->viewData['importer_data'] = null;

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property'),
            'url'=> route('system.property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $property->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Property');
        $this->viewData['result'] = $property;

        $this->createEditData();
        return $this->view('property.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyFormRequest $request, Property $property)
    {
        if(!staffCan('property-manage-all') && ($property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id())){
            abort(401, 'Unauthorized.');
        }

        $propertyDataUpdate = [
            'property_type_id'=> $request->property_type_id,
            'purpose_id'=> $request->purpose_id,
            'data_source_id'=> $request->data_source_id,
            'client_id'=> $request->client_id,
            'area_id'=> $request->area_id,
            'building_number'=> $request->building_number,
            'flat_number'=> $request->flat_number,
            'property_status_id'=> $request->property_status_id,
            'property_model_id'=> $request->property_model_id,
            'name'=> $request->name,
            'description'=> $request->description,
            'remarks'=> $request->remarks,
            'payment_type'=> $request->payment_type,
            'years_of_installment'=> $request->years_of_installment,
            'deposit'=> $request->deposit,
            'price'=> $request->price,
            'currency'=> $request->currency,
            'negotiable'=> $request->negotiable,
            'space'=> $request->space,
            'space_type'=> $request->space_type,
            'address'=> $request->address,
            'latitude'=> $request->latitude,
            'longitude'=> $request->longitude,
            'video_url'=> $request->video_url,
            'sales_id'=> $request->sales_id,
            'created_by_staff_id'=> Auth::id()
        ];

        if(in_array($request->property_status_id,explode(',','archive_property_status'))){
            $propertyDataInsert['hold_until'] = $request->hold_until;
        }else{
            $propertyDataInsert['hold_until'] = null;
        }

        $updateData = $property->update($propertyDataUpdate);

        if($updateData){

            $parametersData = Parameter::where('property_type_id',$request->property_type_id)
                ->get([
                    'column_name',
                    'type',
                    'options',
                    'required'
                ]);

            $parametersDataUpdate = [];

            if($parametersData){
                foreach ($parametersData as $key => $value){
                    $parameterValue = $request->{'p_'.$value->column_name};
                    if($parameterValue){
                        $parametersDataUpdate[$value->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                    }
                }
            }
            if(!empty($parametersDataUpdate)){
                PropertyParameter::where('property_id',$property->id)
                    ->update($parametersDataUpdate);
            }

            // Images
            Image::where('custom_key',$request->key)->update([
                'property_id'=> $property->id
            ]);

            // --- Notification

            $numRequests = $property->requests()->count();
            if($numRequests){
                $allStaffToNotify = array_column(
                    App\Models\Staff::get(['id'])->toArray(),
                    'id'
                );
                notifyStaff(
                    [
                        'type'  => 'staff',
                        'ids'   => $allStaffToNotify
                    ],
                    __('There are :number requests related to property',['number'=> $numRequests]),
                    __('There are :number requests related to property',['number'=> $numRequests]),
                    route('system.property.show',$property->id)
                );
            }
            // --- Notification

            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.property.show',$property->id)
                ]
            );
        }else{
            return $this->response(
                false,
                11001,
                __('Sorry, we could not edit the data')
            );
        }

    }


    public function imageUpload(Request $request){
        $request->validate([
            'images.0' => 'required|image',
            'key'      => 'required|string'
        ]);

        $path = $request->file('images.0')->store(setting('system_path').'/'.date('Y/m/d'));

        if($path){
            $image = Image::create([
                'custom_key'=> $request->key,
                'path'=> $path,
                'image_name'=> $request->file('images.0')->getClientOriginalName()
            ]);

            return [
                'status'=> true,
                'id'=> $image->id
            ];
        }


        return [
            'status'=> false
        ];

    }

    public function removeImage(Request $request){
        $request->validate([
            'name' => 'required|string',
            'key'  => 'required|string'
        ]);

        $image = Image::where([
            'custom_key'=> $request->key,
            'image_name'=> $request->name
        ])->firstOrFail();

        unlink(storage_path('app/'.$image->path));

        $image->delete();


        return [
            'status'=> true
        ];

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Property $property)
    {

        if(!staffCan('property-manage-all') && ($property->created_by_staff_id != Auth::id() || $property->sales_id != Auth::id())){
            abort(401, 'Unauthorized.');
        }

        $message = __('Property Status deleted successfully');

        PropertyParameter::where('property_id',$property->id)->delete();
        $property->delete();

        return $this->response(true,200,$message);
    }


    public function uploadExcel(Request $request){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Property'),
            'url'=> route('system.property.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Upload Excel'),
        ];

        $this->viewData['pageTitle'] = __('Upload Excel');
        $this->viewData['importer_data'] = null;

        $this->createEditData();
        return $this->view('property.upload-excel',$this->viewData);
    }

    public function uploadExcelStore(PropertyUploadExcelFormRequest $request){


        $parametersData = Parameter::where('property_type_id',$request->property_type_id)
            ->get([
                'column_name',
                'name_ar',
                'name_en',
                'type',
                'options',
                'required'
            ]);

        // Start Handle XLS file

        $file = $request->file('excel_file')->store(setting('system_path').'/properties-excel/'.date('Y/m/d'));

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/'.$file))
            ->getActiveSheet()
            ->toArray(null,true,true,true);

        if(count($spreadsheet) < 2){
            return $this->response(
                false,
                11001,
                __('Empty XLS file')
            );
        }

        if($request->ignore_first_row == 'yes'){
            unset($spreadsheet[1]);
            $i = 2;
        }else{
            $i = 1;
        }


        $data   = [];
        $errors = [];

        foreach ($spreadsheet as $key => $value){
            CheckKeyInArray::setArray($value);
            $row = [];

            // Main Data
            $row['client_name']            = CheckKeyInArray::check(strtoupper($request->client_name));
            $row['client_mobile']          = CheckKeyInArray::check(strtoupper($request->client_mobile));
            $row['client_company_name']    = CheckKeyInArray::check(strtoupper($request->client_company_name)); // Not required

            $row['model']          = CheckKeyInArray::check(strtoupper($request->model)); // Not required
            $row['name']           = CheckKeyInArray::check(strtoupper($request->name)); // Not required
            $row['description']    = CheckKeyInArray::check(strtoupper($request->description)); // Not required
            $row['remarks']        = CheckKeyInArray::check(strtoupper($request->remarks)); // Not required

            $row['years_of_installment']   = CheckKeyInArray::check(strtoupper($request->years_of_installment)); // Not required
            $row['deposit']                = CheckKeyInArray::check(strtoupper($request->deposit)); // Not required

            $row['price']      = CheckKeyInArray::check(strtoupper($request->price));
            $row['space']      = CheckKeyInArray::check(strtoupper($request->space));
            $row['address']    = CheckKeyInArray::check(strtoupper($request->address));

            $row['building_number']    = CheckKeyInArray::check(strtoupper($request->building_number));
            $row['flat_number']    = CheckKeyInArray::check(strtoupper($request->flat_number));

            // Parameters

            $rowParameters = [];

            if($parametersData){
                foreach ($parametersData as $pKey => $pValue){
                    $parameterValue = $request->{'p_'.$pValue->column_name};
                    if($parameterValue){
                        switch ($pValue->type){

                            case 'text':
                            case 'textarea':
                            case 'number':

                                $rowParameters[$pValue->column_name] = CheckKeyInArray::check(strtoupper($parameterValue));

                                // check invalid parameter
                                if($pValue->required == 'yes' && !$rowParameters[$pValue->column_name]){
                                    $errors[$i][$pValue->column_name] = __('Invalid :name',['name'=> $pValue->{'name_'.\App::getLocale()}]);
                                }elseif($pValue->type == 'number' && !is_numeric($rowParameters[$pValue->column_name])){
                                    $errors[$i][$pValue->column_name] = __(':name should be numeric',['name'=> $pValue->{'name_'.\App::getLocale()}]);
                                }
                                // check invalid parameter
                                if($rowParameters[$pValue->column_name]){
                                    $rowParameters[$pValue->column_name] = (int)str_replace(',','',$rowParameters[$pValue->column_name]);
                                }
                                break;

                            default:
                                $rowParameters[$pValue->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                                break;

                        }

                    }
                }
            }


            // check invalid parameter
            if(!$row['client_name']){
                $errors[$i]['client_name'] = __('Invalid Client Name');
            }

            if(!$row['client_mobile']){
                $errors[$i]['client_mobile'] = __('Invalid Client Mobile');
            }

            if(!$row['price']){
                $errors[$i]['price'] = __('Invalid Price');
            }else{
                $row['price'] = (int)str_replace(',','',$row['price']);
            }

            if(!$row['space']){
                $errors[$i]['space'] = __('Invalid Space');
            }else{
                $row['space'] = (int)str_replace(',','',$row['space']);
            }

            if(!$row['address']){
                $errors[$i]['address'] = __('Invalid Address');
            }
            // check invalid parameter

            if(!isset($errors[$i])){
                $data[$i] = $row;
                $data[$i]['parameters'] = $rowParameters;
            }
            $i++;
        }

        if(!empty($errors)){
            return $this->response(
                false,
                11000,
                __('Excel Error'),
                $errors
            );
        }elseif (empty($data)){
            return $this->response(
                false,
                11001,
                __('There are no data to insert')
            );
        }

        foreach ($data as $key => $value){
            // Check Client
            $client = Client::where(function($query) use ($value) {
                $query->where('mobile1',$value['client_mobile'])
                    ->orWhere('mobile2',$value['client_mobile']);
            })->first();

            if(!$client){
                $client = Client::create([
                    'type'=> $request->client_type,
                    'investor_type'=> $request->client_investor_type ? $request->client_investor_type  : null,
                    'name'=> $value['client_name'],
                    'company_name'=> $request->client_company_name ? $request->client_company_name  : null,
                    'mobile1'=> $value['client_mobile'],
                    'created_by_staff_id'=> Auth::id()
                ]);
            }
            $propertyDataInsert = [
                'property_type_id'=> $request->property_type_id,
                'purpose_id'=> $request->purpose_id,
                'data_source_id'=> $request->data_source_id,
                'client_id'=> $client->id,
                'area_id'=> $request->area_id,
                'building_number'=> $value['building_number'],
                'flat_number'=> $value['flat_number'],
                'property_status_id'=> $request->property_status_id,
                'name'=> $value['name'],
                'description'=> $value['description'],
                'remarks'=> $value['remarks'],
                'payment_type'=> $request->payment_type,
                'years_of_installment'=> $value['years_of_installment'],
                'deposit'=> $value['deposit'],
                'price'=> $value['price'],
                'currency'=> $request->currency,
                'negotiable'=> $request->negotiable,
                'space'=> $value['space'],
                'space_type'=> 'meter',
                'address'=> $value['address'],
                'sales_id'=> $request->sales_id,
                'created_by_staff_id'=> Auth::id(),
                'call_update'=> date('Y-m-d H:i:s')
            ];
            $insertData = Property::create($propertyDataInsert);
            if($insertData){

                $parametersData = Parameter::where('property_type_id',$request->property_type_id)
                    ->get([
                        'column_name',
                        'type',
                        'options',
                        'required'
                    ]);

                $parametersDataInsert = [
                    'property_id'=> $insertData->id
                ];

                if($parametersData){
                    foreach ($parametersData as $pKey => $pValue){
                        $parameterValue = @$value['parameters'][$pValue->column_name];
                        if($parameterValue){
                            $parametersDataInsert[$pValue->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                        }
                    }
                }

                PropertyParameter::create($parametersDataInsert);

                if(setting('send_notification_on_upload_property_xls') == 'yes'){
                    // --- Notification
                    $numRequests = $insertData->requests()->count();
                    if($numRequests){
                        $allStaffToNotify = array_column(
                            App\Models\Staff::get(['id'])->toArray(),
                            'id'
                        );
                        notifyStaff(
                            [
                                'type'  => 'staff',
                                'ids'   => $allStaffToNotify
                            ],
                            __('There are :number requests related to property',['number'=> $numRequests]),
                            __('There are :number requests related to property',['number'=> $numRequests]),
                            route('system.property.show',$insertData->id)
                        );
                    }
                    // --- Notification
                }

            }

        }


        return $this->response(
            true,
            200,
            __(':num Properties added successfully',['num'=> count($data)]),
            [
                'url'=> route('system.property.upload-excel')
            ]
        );

    }


}