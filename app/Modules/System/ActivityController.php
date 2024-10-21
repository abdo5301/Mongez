<?php

namespace App\Modules\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Auth;
use Jenssegers\Agent\Agent;

class ActivityController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){
            $eloquentData = Activity::with(['subject','causer'])
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

            whereBetween($eloquentData,'DATE(created_at)',$request->created_at1,$request->created_at2);

            if($request->id){
                $eloquentData->where('id', '=',$request->id);
            }

            if($request->description){
                $eloquentData->where('description', '=',$request->description);
            }

            if($request->subject_type){
                $eloquentData->where('subject_type', '=',$request->subject_type);
            }

            if($request->subject_id){
                $eloquentData->where('subject_id', '=',$request->subject_id);
            }

            if($request->causer_type){
                $eloquentData->where('causer_type', '=',$request->causer_type);
            }

            if($request->causer_id){
                $eloquentData->where('causer_id', '=',$request->causer_id);
            }



            if ($request->downloadExcel == "true") {
                if (staffCan('download.activity-log.excel')) {
                    $excelData = $eloquentData;
                    $excelData = $excelData->get();
                    exportXLS(__('Activity Log'),
                        [
                            'ID',
                            'Status',
                            'Model',
                            'User',
                            'Created At'

                        ],
                        $excelData,
                        [
                            'id' => 'id',
                            'description' => 'description',
                            'subject' => function($data){
                                return $data->subject_type.' ('.$data->subject_id.')';
                            },
                            'causer'=>function($data){
                                return $data->causer_type.' ('.$data->causer_id.')';
                            },
                            'created_at' =>'created_at',
                        ]
                    );
                }
            }


            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('description','{{$description}}')
                ->addColumn('causer',function($data){
                    return '<a target="_blank" href="'.route('system.staff.show',$data->causer->id).'">'.$data->causer->fullname.'</a>';
                })
                ->addColumn('subject',function($data){
                    return last(explode('\\',$data->subject_type)).' ('.$data->subject_id.')';
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
        }else{

            // View Data
            $this->viewData['tableColumns'] = ['ID','Status','Staff','Model','Created At','Action'];
            $this->viewData['breadcrumb'][] = [
                'text'=> __('Activity Log')
            ];
            $this->viewData['pageTitle'] = __('Activity Log');


            return $this->view('activity-log.index',$this->viewData);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($ID){
        $result = Activity::findOrFail($ID);

        $agent = new Agent();
        $agent->setUserAgent($result->user_agent);
        $result->agent = $agent;

        $location = @json_decode(file_get_contents('http://ip-api.com/json/'.$result->ip));
        if($location->status!='fail')
            $result->location = $location;


        $this->viewData['result'] = $result;
        return $this->view('activity-log.show',$this->viewData);
    }

}
