<?php

namespace App\Modules\System;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\ClientFormRequest;
use Form;
use Auth;
use Spatie\Activitylog\Models\Activity;

class ClientController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Client::select([
                'id',
                'type',
                'investor_type',
                'name',
                'company_name',
                'mobile1',
                'status',
                'created_at',
                'created_by_staff_id'
            ])
                ->with('staff');

            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            /*
             * Start handling filter
             */

            whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('id','=',$request->id);
            }

            if($request->type){
                $eloquentData->where('type','=',$request->type);
            }

            if($request->investor_type){
                $eloquentData->where('investor_type','=',$request->investor_type);
            }

            if($request->name){
                $eloquentData->where('name','LIKE','%'.$request->name.'%');
            }

            if($request->company_name){
                $eloquentData->where('company_name','LIKE','%'.$request->company_name.'%');
            }

            if($request->email){
                $eloquentData->where('email','LIKE','%'.$request->email.'%');
            }

            if($request->phone){
                $eloquentData->where('phone','LIKE','%'.$request->phone.'%');
            }

            if($request->mobile){
                $eloquentData->where(function($query) use ($request){
                    $query->where('mobile1','LIKE','%'.$request->mobile.'%')
                        ->orWhere('mobile2','LIKE','%'.$request->mobile.'%');
                });
            }

            if($request->status){
                $eloquentData->where('status','=',$request->status);
            }

            if(staffCan('client-manage-all')){
                if($request->created_by_staff_id){
                    $eloquentData->where('created_by_staff_id','=',$request->created_by_staff_id);
                }
            }else{
                $eloquentData->where('created_by_staff_id','=',Auth::id());
            }

            if($request->downloadExcel){
                return exportXLS(
                    __('Clients'),
                    [
                        __('ID'),
                        __('Type'),
                        __('Name'),
                        __('Mobile 1'),
                        __('Mobile 2'),
                        __('Phone'),
                        __('Fax'),
                        __('E-Mail'),
                        __('Website'),
                        __('Address'),
                        __('Description'),
                        __('Status'),
                        __('Created By'),
                        __('Created At')
                    ],
                    $eloquentData->get(),
                    [
                        'id'=> 'id',
                        'type'=> function($data){return ucfirst($data->type);},
                        'name'=> function($data){
                            if($data->type == 'client') {
                                return $data->name;
                            }

                            return $data->company_name.' ('.$data->name.') <br /> ('.__(ucfirst($data->investor_type)).')';
                        },
                        'mobile1'=> 'mobile1',
                        'mobile2'=> 'mobile2',
                        'phone'=> 'phone',
                        'fax'=> 'fax',
                        'email'=> 'email',
                        'website'=> 'website',
                        'address'=> 'address',
                        'description'=> 'description',
                        'status'=>  function($data){
                            if($data->status == 'active'){
                                return __('Active');
                            }
                            return __('In-Active');
                        },
                        'created_by_staff_id'=> function($data){
                            return '#ID:'.$data->staff->id.' - '.$data->staff->fullname;
                        },
                        'created_at'=> function($data){
                            return $data->created_at->format('Y-m-d h:i A');
                        }
                    ]
                );
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('type',function($data){
                    if($data->type == 'client'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Client').'</span>';
                    }else{
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('investor').'</span>';
                    }
                })
                ->addColumn('name', function($data){
                    if($data->type == 'client') {
                        return $data->name;
                    }

                    return $data->company_name.' ('.$data->name.') <br /> ('.__(ucfirst($data->investor_type)).')';
                })
                ->addColumn('mobile1', function($data){
                    return '<a href="tel:'.$data->mobile1.'">'.$data->mobile1.'</a>';
                })
                ->addColumn('status', function($data){
                    if($data->status == 'active'){
                        return '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__('Active').'</span>';
                    }
                    return '<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">'.__('In-Active').'</span>';
                })
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
                ->addColumn('created_by_staff_id', function($data){
                    return '<a href="'.route('system.staff.show',$data->staff->id).'" target="_blank">'.$data->staff->fullname.'</a>';
                })
                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.client.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                                <a class="dropdown-item" href="'.route('system.client.edit',$data->id).'"><i class="la la-edit"></i> '.__('Edit').'</a>
                               <!--  <a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord(\''.route('system.client.destroy',$data->id).'\')"><i class="la la-trash-o"></i> '.__('Delete').'</a> -->
                            </div>
                        </span>';
                })
                ->whitelist(['id','name','mobile1'])
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Type'),
                __('Name'),
                __('Mobile'),
                __('Status'),
                __('Created At'),
                __('Created By'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Clients')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Clients');
            }else{
                $this->viewData['pageTitle'] = __('Clients');
            }

            return $this->view('client.index',$this->viewData);
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
            'text'=> __('Clients'),
            'url'=> route('system.client.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Client'),
        ];

        $this->viewData['pageTitle'] = __('Create Client');

        return $this->view('client.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();

        $insertData = Client::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'id'=> $insertData->id,
                    'name'=> $insertData->name,
                    'url'=> route('system.client.show',$insertData->id)
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
    public function show(Client $client,Request $request){

        if(!staffCan('client-manage-all') && $client->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        if($request->isDataTable == 'call'){
            $eloquentData = $client->calls()->select([
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

        }elseif($request->isDataTable == 'property'){

            $eloquentData = $client->property()->select([
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
                'properties.created_at'
            ])
                ->with([
                    'property_type',
                    'purpose',
                    'property_status',
                    'sales'
                ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.\App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.\App::getLocale()};
                })
                ->addColumn('property_status_id',function($data){
                    return $data->property_status->{'name_'.\App::getLocale()};
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
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.property.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'log'){
            $eloquentData = Activity::with(['subject','causer'])
                ->where('subject_type','App\Models\Client')
                ->where('subject_id',$client->id)
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
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:urlIframe(\''.route('system.activity-log.show',$data->id).'\')"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }elseif($request->isDataTable == 'request'){

            $eloquentData = $client->requests()->select([
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
                    'request_status',
                    'sales'
                ]);

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.\App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.\App::getLocale()};
                })

                ->addColumn('request_status_id',function($data){
                    return $data->request_status->{'name_'.\App::getLocale()};
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
                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.request.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text'=> __('Clients'),
                    'url'=> route('system.client.index'),
                ],
                [
                    'text'=> $client->name,
                ]
            ];

            $this->viewData['pageTitle'] = __('Client Profile');

            $this->viewData['result'] = $client;
            return $this->view('client.show',$this->viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client,Request $request){

        if(!staffCan('client-manage-all') && $client->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Clients'),
            'url'=> route('system.client.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Edit (:name)',['name'=> $client->name]),
        ];

        $this->viewData['pageTitle'] = __('Edit Client');
        $this->viewData['result'] = $client;

        return $this->view('client.create',$this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ClientFormRequest $request, Client $client)
    {
        if(!staffCan('client-manage-all') && $client->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        $requestData = $request->all();

        $updateData = $client->update($requestData);

        if($updateData){
            return $this->response(
                true,
                200,
                __('Data modified successfully'),
                [
                    'url'=> route('system.client.show',$client->id)
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client,Request $request)
    {
        if(!staffCan('client-manage-all') && $client->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        $message = __('Client deleted successfully');
        $client->delete();
        return $this->response(true,200,$message);
    }

}
