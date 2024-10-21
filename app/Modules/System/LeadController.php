<?php

namespace App\Modules\System;

use App\Http\Requests\ImporterFormRequest;
use App\Http\Requests\LeadFormRequest;
use App\Models\Area;
use App\Models\Lead;
use App\Models\LeadData;
use App\Models\PropertyType;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class LeadController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Lead::select([
                'id',
                'name',
                'created_by_staff_id',
                'created_at'
            ])
                ->with([
                    'staff'
                ]);



            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            whereBetween($eloquentData,'DATE(leads.created_at)',$request->created_at1,$request->created_at2);


            if($request->id){
                $eloquentData->where('leads.id',$request->id);
            }


            if($request->name){
                $eloquentData->where('leads.name','LIKE','%'.$request->name.'%');
            }


            if($request->created_by_staff_id){
                $eloquentData->where('leads.created_by_staff_id',$request->created_by_staff_id);
            }


            if(!staffCan('lead-manage-all')){
                $eloquentData->where('leads.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
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
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="'.route('system.lead.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Created At'),
                __('Created By'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Leads')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Leads');
            }else{
                $this->viewData['pageTitle'] = __('Leads');
            }

            return $this->view('lead.index',$this->viewData);
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
            'text'=> __('Leads'),
            'url'=> route('system.lead.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Leads'),
        ];

        $this->viewData['pageTitle'] = __('Create Leads');

        return $this->view('lead.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeadFormRequest $request){

        $file = $request->file('file')->store(setting('system_path').'/lead/'.date('Y/m/d'));


        $data = [
            'name'=> $request->name,
            'file'=> $file,
            'created_by_staff_id'=> Auth::id()
        ];


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

        $insertData = Lead::create($data);
        if($insertData){

            if($request->ignore_first_row == 'yes'){
                unset($spreadsheet[1]);
            }

            $i = 0;
            foreach ($spreadsheet as $key => $value){
                if(
                    !isset($value[strtoupper($request->columns_data_name)]) ||
                    !isset($value[strtoupper($request->columns_data_mobile)])
                ) continue;

                $name        = @$value[strtoupper($request->columns_data_name)];
                $mobile      = @$value[strtoupper($request->columns_data_mobile)];
                $email       = @$value[strtoupper($request->columns_data_email)];
                $description = @$value[strtoupper($request->columns_data_description)];

                LeadData::create([
                    'lead_id'=> $insertData->id,
                    'name'=> $name,
                    'mobile'=> $mobile,
                    'email'=> $email,
                    'description'=> $description
                ]);

                $i++;
            }

            if(!$i){
                $insertData->delete();
                return $this->response(
                    false,
                    11001,
                    __('corrupted XLS file')
                );
            }

            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.lead.show',$insertData->id)
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
    public function show(Lead $lead,Request $request){

        if(!staffCan('lead-manage-all') && $lead->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        if($request->isDataTable){

            $eloquentData = $lead->data();


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('name','{{$name}}')
                ->addColumn('mobile',function($data){
                    return '<a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })
                ->addColumn('email',function($data){
                    if(!$data->email) return '--';
                    return '<a href="mailto:'.$data->email.'">'.$data->email.'</a>';
                })
                ->addColumn('description',function($data){
                    if(!$data->description) return '--';
                    return $data->description;
                })
                ->escapeColumns([])
                ->make(false);
        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Leads'),
                    'url' => route('system.lead.index'),
                ],
                [
                    'text' => __('#ID: :id', ['id' => $lead->id]),
                ]
            ];

            $this->viewData['pageTitle'] = __('#ID: :id', ['id' => $lead->id]);

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Mobile'),
                __('E-mail'),
                __('Description')
            ];


            $this->viewData['result'] = $lead;
            return $this->view('lead.show', $this->viewData);
        }
    }


}