<?php

namespace App\Modules\System;

use App\Http\Requests\PropertyFormRequest;
use App\Http\Requests\RequestFormRequest;
use App\Models\AreaType;
use App\Models\DataSource;
use App\Models\ImporterData;
use App\Models\Parameter;
use App\Models\Property;
use App\Models\PropertyParameter;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Purpose;
use App\Models\Request as RequestModal;
use App\Models\RequestParameter;
use App\Models\RequestStatus;
use App\Models\PropertyModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;


class RequestController extends SystemController
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

        $this->viewData['request_status'] = RequestStatus::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        $this->viewData['data_sources'] = DataSource::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        if(setting('request_area_type') != '1'){
            $this->viewData['areas_data'] = AreaType::getFirstArea(\App::getLocale());
        }

        $this->viewData['property_model'] = PropertyModel::get([
            'id',
            'name_'.App::getLocale().' as name',
            'space'
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = RequestModal::select([
                'requests.id',
                'clients.mobile1',
                'clients.mobile2',
                'requests.name',
                'requests.property_type_id',
                'requests.purpose_id',
                'requests.data_source_id',
                'requests.client_id',
                'requests.area_ids',
                'requests.request_status_id',
                'requests.payment_type',
                'requests.price_from',
                'requests.price_to',
                'requests.currency',
                'requests.property_model_id',
                'requests.space_from',
                'requests.space_to',
                'requests.space_type',
                'requests.sales_id',
                'requests.created_at'

            ])
                ->join('request_parameters','request_parameters.request_id','=','requests.id')
                ->join('clients','requests.client_id','=','clients.id')
                ->with([
                    'property_type',
                    'purpose',
                    'client',
                    'request_status',
                    'sales',
                    'data_source'
                ]);


            /*
             * Start handling filter
             */

            whereBetween($eloquentData,'DATE(requests.created_at)',$request->created_at1,$request->created_at2);


            if($request->id){
                $eloquentData->where('requests.id',$request->id);
            }

            if($request->property_model_id){
                $eloquentData->whereRaw('FIND_IN_SET(?,requests.property_model_id)',[$request->property_model_id]);
            }

            if($request->property_type_id){
                $eloquentData->where('requests.property_type_id',$request->property_type_id);

                /*
                 * Parameters
                 */
/*
                $parametersData = App\Models\Parameter::where('property_type_id',$request->property_type_id)
                    ->where('show_in_request','yes')
                    ->get();

                if($parametersData->isNotEmpty()){
                    foreach ($parametersData as $key => $value){
                        switch ($value->type){
                            case 'text':
                            case 'textarea':
                                if($request->{'p_'.$value->column_name}){
                                    $eloquentData->where('request_parameters.'.$value->column_name,'LIKE','%'.$request->{'p_'.$value->column_name}.'%');
                                }
                                break;

                            case 'number':
                                if($value->between_request == 'yes'){
                                    whereBetween2Column($eloquentData,'request_parameters.'.$value->column_name,$request->{'p_'.$value->column_name.'_from'},$request->{'p_'.$value->column_name.'_to'});
                                }else{
                                    if($request->{'p_'.$value->column_name}){
                                        whereBetween($eloquentData,'request_parameters.'.$value->column_name,$request->{'p_'.$value->column_name.'_from'},$request->{'p_'.$value->column_name.'_to'});
                                    }
                                }
                                break;

                            case 'select':
                            case 'radio':
                                if($request->{'p_'.$value->column_name}){
                                    $eloquentData->where('request_parameters.'.$value->column_name,'=',$request->{'p_'.$value->column_name});
                                }
                                break;

                            case 'multi_select':
                            case 'checkbox':
                                if($request->{'p_'.$value->column_name} && is_array($request->{'p_'.$value->column_name})){
                                    $eloquentData->whereRaw('CONCAT(",", request_parameters.'.$value->column_name.', ",") REGEXP ",('.implode('|',$request->{'p_'.$value->column_name}).'),"');
                                }
                                break;
                        }
                    }
                }
*/
            }

            if($request->purpose_id){
                $eloquentData->where('requests.purpose_id',$request->purpose_id);
            }



            if($request->data_source_id){
                $eloquentData->where('requests.data_source_id',$request->data_source_id);
            }

            if($request->client_id){
                $eloquentData->where('requests.client_id',$request->client_id);
            }

            if($request->area_ids){
                $eloquentData->whereRaw('FIND_IN_SET(?,requests.area_ids)',[$request->area_ids]);
            }

            if($request->request_status_id){
                $eloquentData->where('requests.request_status_id',$request->request_status_id);
            }

            if($request->name){
                $eloquentData->where('requests.name','LIKE','%'.$request->name.'%');
            }

            if($request->payment_type){
                $eloquentData->where('requests.payment_type',$request->payment_type);
            }



            whereBetween2Column($eloquentData,'requests.deposit_from',$request->deposit_from1,$request->deposit_from2);
            whereBetween2Column($eloquentData,'requests.deposit_to',$request->deposit_to1,$request->deposit_to2);

            whereBetween2Column($eloquentData,'requests.price_from',$request->price_from1,$request->price_from2);
            whereBetween2Column($eloquentData,'requests.price_to',$request->price_to1,$request->price_to2);

            if($request->currency){
                $eloquentData->where('requests.currency',$request->currency);
            }


            whereBetween2Column($eloquentData,'requests.space_from',$request->space_from1,$request->space_from2);
            whereBetween2Column($eloquentData,'requests.space_to',$request->space_to1,$request->space_to2);

            if($request->space_type){
                $eloquentData->where('requests.space_type',$request->space_type);
            }

            if(staffCan('request-manage-all')){
                if($request->sales_id){
                    $eloquentData->where('requests.sales_id',$request->sales_id);
                }

                if($request->created_by_staff_id){
                    $eloquentData->where('requests.created_by_staff_id',$request->created_by_staff_id);
                }
            }else{
                $eloquentData->where(function($query){
                    $query->where('requests.sales_id',Auth::id())
                        ->orWhere('requests.created_by_staff_id',Auth::id());
                });
            }


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            if($request->downloadExcel){
                return exportXLS(
                    __('Requests'),
                    [
                        __('ID'),
                        __('Client'),
                        __('Mobile 1'),
                        __('Mobile 2'),
                        __('Type'),
                        __('Purpose'),
                        __('Data Source'),
                        __('Status'),
                        __('Name'),
                        __('Description'),
                        __('Payment Type'),
                        __('Deposit'),
                        __('Price'),
                        __('Space'),
                        __('Sales'),
                        __('Created At')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'client'=> function($data){
                            return $data->client->name;
                        },
                        'mobile1'=> function($data){
                            return $data->client->mobile1;
                        },
                        'mobile2'=> function($data){
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

                        'request_status'=> function($data){
                            return $data->request_status->{'name_'.App::getLocale()};
                        },

                        'name'=> 'name',
                        'description'=> 'description',

                        'payment_type'=> function($data){
                            return $data->payment_type;
                        },
                        'deposit'=> function($data){
                            return number_format($data->deposit_from).' '.__($data->currency).' : '.number_format($data->deposit_to).' '.__($data->currency);
                        },
                        'price'=> function($data){
                            return number_format($data->price_from).' '.__($data->currency).' : '.number_format($data->price_to).' '.__($data->currency);
                        },
                        'space'=> function($data){
                            return number_format($data->space_from).' '.__($data->space_type).' : '.number_format($data->space_to).' '.__($data->space_type);
                        },
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
                    return '<a href="'.route('system.client.show',$data->client_id).'" target="_blank">'.$data->client->name.'</a>';
                })
                ->addColumn('mobile1',function($data){
                    return '<a href="tel:'.$data->client->mobile1.'">'.$data->client->mobile1.'</a>';
                })
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.App::getLocale()};
                })
                ->addColumn('request_status_id',function($data){
                    return '<b style="color:'.$data->request_status->color.'">'.$data->request_status->{'name_'.App::getLocale()}.'</b>';
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
                                <a class="dropdown-item" href="'.route('system.request.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.request.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->whitelist(['requests.id','clients.name','clients.mobile1'])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Client'),
                __('Mobile'),
                __('Type'),
                __('Purpose'),
                __('Status'),
                __('Sales'),
                __('Created At'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Requests')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Requests');
            }else{
                $this->viewData['pageTitle'] = __('Requests');
            }

            $this->createEditData();


            return $this->view('request.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Requests'),
            'url'=> route('system.request.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Request'),
        ];

        $this->viewData['pageTitle'] = __('Create Request');

        $this->createEditData();
        return $this->view('request.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestFormRequest $request){
        $requestDataInsert = [
            'property_type_id'=> $request->property_type_id,
            'purpose_id'=> $request->purpose_id,
            'data_source_id'=> $request->data_source_id,
            'client_id'=> $request->client_id,
            'area_ids'=> implode(',',$request->area_ids),
            'request_status_id'=> $request->request_status_id,
            'name'=> $request->name,
            'description'=> $request->description,
            'payment_type'=> $request->payment_type,
            'deposit_from'=> $request->deposit_from,
            'deposit_to'=> $request->deposit_to,
            'price_from'=> $request->price_from,
            'price_to'=> $request->price_to,
            'currency'=> $request->currency,
            'property_model_id'=> implode(',',$request->property_model_id),
            'space_from'=> $request->space_from,
            'space_to'=> $request->space_to,
            'space_type'=> $request->space_type,
            'sales_id'=> $request->sales_id,
            'created_by_staff_id'=> Auth::id()
        ];

        $insertData = RequestModal::create($requestDataInsert);
        if($insertData){

            $parametersData = Parameter::where('property_type_id',$request->property_type_id)
                ->where('show_in_request','yes')
                ->get([
                    'column_name',
                    'type',
                    'options',
                    'required'
                ]);

            $parametersDataInsert = [
                'request_id'=> $insertData->id
            ];

            if($parametersData){
                foreach ($parametersData as $key => $value){
                    if($value->between_request == 'yes' && $value->type == 'number'){
                        $parameterValueFrom = $request->{'p_'.$value->column_name.'_from'};
                        $parameterValueTo   = $request->{'p_'.$value->column_name.'_to'};

                        if($parameterValueFrom){
                            $parametersDataInsert[$value->column_name.'_from'] = $parameterValueFrom;
                        }

                        if($parameterValueTo){
                            $parametersDataInsert[$value->column_name.'_to'] = $parameterValueTo;
                        }

                    }else{
                        $parameterValue = $request->{'p_'.$value->column_name};
                        if($parameterValue){
                            $parametersDataInsert[$value->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                        }
                    }

                }
            }

            RequestParameter::create($parametersDataInsert);


            // --- Notification
            $numProperties = $insertData->property()->count();

            if($numProperties){
                $allStaffToNotify = array_column(
                    App\Models\Staff::get(['id'])->toArray(),
                    'id'
                );
                notifyStaff(
                    [
                        'type'  => 'staff',
                        'ids'   => $allStaffToNotify
                    ],
                    __('There are :number properties related to request',['number'=> $numProperties]),
                    __('There are :number properties related to request',['number'=> $numProperties]),
                    route('system.request.show',$insertData->id)
                );
            }
            // --- Notification

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.request.show',$insertData->id)
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
    public function show(RequestModal $request,Request $HTTPrequest){
        list($requestModal,$request) = [$request,$HTTPrequest];

        if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }


        $this->viewData['importerCount'] = ImporterData::requests($requestModal->property_type_id,$requestModal->purpose_id,explode(',',$requestModal->area_ids),$requestModal->space_from,$requestModal->space_to,$requestModal->price_from,$requestModal->price_to)
            ->count();

        if($request->isDataTable == 'importer'){
            $eloquentData = ImporterData::requests($requestModal->property_type_id,$requestModal->purpose_id,explode(',',$requestModal->area_ids),$requestModal->space_from,$requestModal->space_to,$requestModal->price_from,$requestModal->price_to);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('price',function($data){
                    return amount($data->price,true);
                })
                ->addColumn('space','{{$space}}')
                ->addColumn('bed_rooms',function($data){
                    return $data->bed_rooms;
                })
                ->addColumn('bath_room',function($data){
                    return $data->bath_room;
                })
                ->addColumn('owner_name',function($data){
                    return $data->owner_name.'<br/> <a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })

                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" target="_blank" href="'.route('system.importer.show',$data->importer_id).'?id='.$data->id.'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'call'){
            $eloquentData = $requestModal->calls()->select([
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

            $eloquentData = $requestModal->property()->select([
                'properties.id',
                'properties.property_type_id',
                'properties.purpose_id',
                'properties.client_id',
                'properties.property_status_id',
                'properties.name',
                'properties.payment_type',
                'properties.price',
                'properties.currency',
                'properties.space',
                'properties.sales_id',
                'properties.created_at',
                \DB::raw("(SELECT views FROM `property_views` WHERE `request_id` = '$requestModal->id' AND `property_id` = properties.id) as views")
            ])
                ->with([
                    'property_type',
                    'purpose',
                    'client',
                    'property_status',
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
                ->addColumn('property_status_id',function($data){
                    return $data->property_status->{'name_'.App::getLocale()};
                })
                ->addColumn('sales_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->sales_id).'" target="_blank">'.$data->sales->fullname.'</a>';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('views', function($data){
                    if(!$data->views) return '--';

                    return number_format($data->views);
                })

                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'log'){
            $eloquentData = Activity::with(['subject','causer'])
                ->where('subject_type','App\Models\Request')
                ->where('subject_id',$requestModal->id)
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
                    'text' => __('Requests'),
                    'url' => route('system.request.index'),
                ],
                [
                    'text' => $requestModal->name,
                ]
            ];

            $this->viewData['pageTitle'] = $requestModal->name;

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

            $this->viewData['result'] = $requestModal;
            $this->viewData['result']->property_model_array = PropertyModel::select('id','name_'.App::getLocale().' as name')->whereIn('id',explode(',',$requestModal->property_model_id))->get();

            return $this->view('request.show', $this->viewData);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestModal $request,Request $HTTPrequest){
        list($requestModal,$request) = [$request,$HTTPrequest];

        if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Request'),
            'url'=> route('system.request.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $requestModal->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Request');
        $this->viewData['result'] = $requestModal;

        $this->createEditData();
        return $this->view('request.create',$this->viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(RequestModal $request,RequestFormRequest $HTTPrequest)
    {
        list($requestModal,$request) = [$request,$HTTPrequest];
        if(!staffCan('request-manage-all') && ( $requestModal->created_by_staff_id != Auth::id() || $requestModal->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }

        $requestDataInsert = [
            'property_type_id'=> $request->property_type_id,
            'purpose_id'=> $request->purpose_id,
            'data_source_id'=> $request->data_source_id,
            'client_id'=> $request->client_id,
            'area_ids'=> implode(',',$request->area_ids),
            'request_status_id'=> $request->request_status_id,
            'name'=> $request->name,
            'description'=> $request->description,
            'payment_type'=> $request->payment_type,
            'deposit_from'=> $request->deposit_from,
            'deposit_to'=> $request->deposit_to,
            'price_from'=> $request->price_from,
            'price_to'=> $request->price_to,
            'currency'=> $request->currency,
            'property_model_id'=> implode(',',$request->property_model_id),
            'space_from'=> $request->space_from,
            'space_to'=> $request->space_to,
            'space_type'=> $request->space_type,
            'sales_id'=> $request->sales_id,
            'created_by_staff_id'=> Auth::id()
        ];
        $updateData = $requestModal->update($requestDataInsert);

        if($updateData){

            $parametersData = Parameter::where('property_type_id',$request->property_type_id)
                ->where('show_in_request','yes')
                ->get([
                    'column_name',
                    'type',
                    'options',
                    'required'
                ]);

            $parametersDataUpdate = [];


            if($parametersData){
                foreach ($parametersData as $key => $value){
                    if($value->between_request == 'yes' && $value->type == 'number'){
                        $parameterValueFrom = $request->{'p_'.$value->column_name.'_from'};
                        $parameterValueTo   = $request->{'p_'.$value->column_name.'_to'};

                        if($parameterValueFrom){
                            $parametersDataUpdate[$value->column_name.'_from'] = $parameterValueFrom;
                        }

                        if($parameterValueTo){
                            $parametersDataUpdate[$value->column_name.'_to'] = $parameterValueTo;
                        }

                    }else{
                        $parameterValue = $request->{'p_'.$value->column_name};
                        if($parameterValue){
                            $parametersDataUpdate[$value->column_name] = (is_array($parameterValue)) ? implode(',',$parameterValue) : $parameterValue;
                        }
                    }
                }
            }

            if(!empty($parametersDataUpdate)){
                RequestParameter::where('request_id',$requestModal->id)
                    ->update($parametersDataUpdate);
            }



            // --- Notification
            $numProperties = $requestModal->property()->count();

            if($numProperties){
                $allStaffToNotify = array_column(
                    App\Models\Staff::get(['id'])->toArray(),
                    'id'
                );
                notifyStaff(
                    [
                        'type'  => 'staff',
                        'ids'   => $allStaffToNotify
                    ],
                    __('There are :number properties related to request',['number'=> $numProperties]),
                    __('There are :number properties related to request',['number'=> $numProperties]),
                    route('system.request.show',$requestModal->id)
                );
            }
            // --- Notification


            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.request.show',$requestModal->id)
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




    public function share(Request $request){
        $requestData = RequestModal::findOrFail($request->id);

        if(!staffCan('request-manage-all') && ( $requestData->created_by_staff_id != Auth::id() || $requestData->sales_id != Auth::id() )){
            abort(401, 'Unauthorized.');
        }

        $hours = (int) $request->hours;
        if(!$hours){
            return [
                'status'=> false,
                'message'=> __('Please select valid hours')
            ];
        }

        $dataArray = [
            'sharing_until'=> Carbon::now()->addHours($hours)->format('Y-m-d H:i:s'),
            'sharing_staff_id'=> Auth::id()
        ];

        if(!$requestData->sharing_slug){
            $dataArray['sharing_slug'] = Str::random(5).$requestData->id.Str::random(5);
        }

        $requestData->update($dataArray);

        return [
            'status'=> true,
            'message'=> __('Done')
        ];

    }
    public function closeShare(Request $request){
        $requestData = RequestModal::findOrFail($request->id);

        $requestData->update([
            'sharing_slug'      => null,
            'sharing_until'     => null,
            'sharing_staff_id'  => null
        ]);

        return [
            'status'=> true,
            'message'=> __('Done')
        ];

    }












    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestModal $requestModal)
    {
        $message = __('Request deleted successfully');

        RequestParameter::where('request_id',$requestModal->id)->delete();
        $requestModal->delete();

        return $this->response(true,200,$message);
    }

}
