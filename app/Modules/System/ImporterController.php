<?php

namespace App\Modules\System;

use App\Http\Requests\ImporterFormRequest;
use App\Models\Area;
use App\Models\Importer;
use App\Models\ImporterData;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;

class ImporterController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        if($request->isDataTable){

            $eloquentData = Importer::select([
                'id',
                'connector',
                'area_id',
                'property_type_id',
                'purpose_id',
                'status',
                'success',
                'created_by_staff_id',
                'created_at'
            ])
                ->with([
                    'property_type',
                    'purpose',
                    'staff'
                ]);



            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }

            if(!staffCan('importer-manage-all')){
                $eloquentData->where('calls.created_by_staff_id',Auth::id());
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('connector','{{$connector}}')
                ->addColumn('area_id',function($data){
                    return implode(' -> ',\App\Libs\AreasData::getAreasUp($data->area_id,true));
                })
                ->addColumn('property_type_id',function($data){
                    return $data->property_type->{'name_'.App::getLocale()};
                })
                ->addColumn('purpose_id',function($data){
                    return $data->purpose->{'name_'.App::getLocale()};
                })
                ->addColumn('status','{{$status}}')
                ->addColumn('success','{{$success}}')

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
                                <a class="dropdown-item" href="'.route('system.importer.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Connector'),
                __('Area'),
                __('Type'),
                __('Purpose'),
                __('Status'),
                __('Success'),
                __('Created At'),
                __('Created By'),
                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Importer')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Importer');
            }else{
                $this->viewData['pageTitle'] = __('Importer');
            }

            return $this->view('importer.index',$this->viewData);
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
            'text'=> __('Importer'),
            'url'=> route('system.importer.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Import Data'),
        ];

        $this->viewData['pageTitle'] = __('Import Data');

        $this->viewData['property_types'] = PropertyType::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);
        $this->viewData['purposes'] = Purpose::get([
            'id',
            'name_'.App::getLocale().' as name'
        ]);

        return $this->view('importer.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ImporterFormRequest $request){
        $requestData = $request->all();
        $requestData['created_by_staff_id'] = Auth::id();


        switch ($requestData['connector']){
            case 'OLX':
                $areaInfo = Area::where('id',$request->area_id)
                    ->whereNotNull('olx_id')
                    ->first();

                $propertyTypeInfo = PropertyType::where('id',$request->property_type_id)
                    ->whereNotNull('olx_id')
                    ->first();

                $purposeInfo = Purpose::where('id',$request->purpose_id)
                    ->whereNotNull('olx_id')
                    ->first();

                if(!$areaInfo || !$propertyTypeInfo || !$purposeInfo){
                    return $this->response(
                        false,
                        11001,
                        __('Sorry, we could import date from OLX')
                    );
                }

                break;

            case 'Aqarmap':

                $areaInfo = Area::where('id',$request->area_id)
                    ->whereNotNull('aqarmap_id')
                    ->first();

                $propertyTypeInfo = PropertyType::where('id',$request->property_type_id)
                    ->whereNotNull('aqarmap_id')
                    ->first();

                $purposeInfo = Purpose::where('id',$request->purpose_id)
                    ->whereNotNull('aqarmap_id')
                    ->first();

                if(!$areaInfo || !$propertyTypeInfo || !$purposeInfo){
                    return $this->response(
                        false,
                        11001,
                        __('Sorry, we could import date from Aqarmap')
                    );
                }

                break;
        }

        $insertData = Importer::create($requestData);
        if($insertData){
            return $this->response(
                true,
                200,
                __('Data added successfully'),
                [
                    'url'=> route('system.importer.show',$insertData->id)
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
    public function show(Importer $importer,Request $request){

        if(!staffCan('importer-manage-all') && $importer->created_by_staff_id != Auth::id()){
            abort(401, 'Unauthorized.');
        }

        if($request->isDataTable){

            $eloquentData = $importer->data()->select('*',\DB::raw('COUNT(*) as `counter`' ))->groupBy('mobile')
                ->orderBy('counter','ASC');

            if($request->id){
                $eloquentData->where('id',$request->id);
            }

            if($request->mobile){
                $eloquentData->where('mobile',$request->mobile);
            }

            if($request->count_from && $request->count_to){
                $countWhere = $importer->data()->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $eloquentData->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }


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
                    return '('.$data->counter.')'.$data->owner_name.'<br/> <a href="tel:'.$data->mobile.'">'.$data->mobile.'</a>';
                })

                ->addColumn('action', function($data){
                    return '<span class="dropdown">
                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                              <i class="la la-gear"></i>
                            </a>
                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
                                <a class="dropdown-item" href="javascript:viewProperty('.$data->id.')"><i class="la la-search-plus"></i> '.__('View').'</a>
                            </div>
                        </span>';
                })
                ->escapeColumns([])
                ->make(false);
        }elseif($request->propertyData){
            $propertyDataID = $request->propertyData;

            $propertyData = ImporterData::where([
                ['id',$propertyDataID],
                ['importer_id',$importer->id]
            ])->first();

            if(!$propertyData) return ['status'=> false];

            $next = ImporterData::where([
                ['id','>',$propertyDataID],
                ['importer_id',$importer->id]
            ])
                ->orderBy('id','ASC');


            if($request->mobile){
                $next->where('mobile',$request->mobile);
            }
            if($request->count_from && $request->count_to){
                $countWhere = $importer->data()->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $next->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            $next = $next->first(['id']);


            $previous = ImporterData::where([
                ['id','<',$propertyDataID],
                ['importer_id',$importer->id]
            ])
                ->orderBy('id','DESC');

            if($request->mobile){
                $previous->where('mobile',$request->mobile);
            }
            if($request->count_from && $request->count_to){
                $countWhere = $importer->data()->select([
                    'mobile',
                    \DB::raw('COUNT(*) as `count`')
                ])
                    ->groupBy('mobile')
                    ->havingRaw("`count` BETWEEN ? AND ?",[$request->count_from,$request->count_to])
                    ->get();

                if($countWhere->isNotEmpty()){
                    $previous->whereIn('mobile',array_column($countWhere->toArray(),'mobile'));
                }
            }

            $previous = $previous->first(['id']);

            $systemProperty = '';
            if($propertyData->property_id){
                $systemProperty = ' <tr>
                        <td>'.__('System Property').'</td>
                        <td><a href="'.route('system.property.show',$propertyData->property_id).'" target="_blank">#ID: '.$propertyData->property_id.'</a></td>
                    </tr>';
            }


            $table = '<table class="table">
                <thead>
                    <tr>
                        <th>'.__('Key').'</th>
                        <th>'.__('Value').'</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>'.__('ID').'</td>
                        <td>'.$propertyData->id.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Connector').'</td>
                        <td>'.$propertyData->connector_id.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Name').'</td>
                        <td>'.$propertyData->name.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Description').'</td>
                        <td>'.$propertyData->description.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Price').'</td>
                        <td>'.amount($propertyData->price,true).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Space').'</td>
                        <td>'.number_format($propertyData->space).'</td>
                    </tr>
                    <tr>
                        <td>'.__('Bed Rooms').'</td>
                        <td>'.$propertyData->bed_rooms.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Bath Room').'</td>
                        <td>'.$propertyData->bath_room.'</td>
                    </tr>
                    <tr>
                        <td>'.__('Owner').'</td>
                        <td>'.$propertyData->owner_name.' ( <a href="tel:'.$propertyData->mobile.'">'.$propertyData->mobile.'</a> )</td>
                    </tr>
                    '.$systemProperty.'
                </tbody>
            </table>';

            return [
                'status'    => true,
                'table'     => $table,
                'next'      => ($next) ? $next->id : false,
                'previous'  => ($previous) ? $previous->id : false,
                'property_id'=> $propertyData->property_id
            ];

        }else{

            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Importer'),
                    'url' => route('system.importer.index'),
                ],
                [
                    'text' => __('#ID: :id', ['id' => $importer->id]),
                ]
            ];

            $this->viewData['pageTitle'] = __('#ID: :id', ['id' => $importer->id]);

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


            $ownersData = $importer->data()->select([
                'owner_name',
                'mobile',
                \DB::raw('COUNT(*) as `count`')
            ])
                ->groupBy('mobile')
                ->orderBy('count','ASC')
                ->get();

            $this->viewData['ownersData'] = $ownersData;

            $this->viewData['result'] = $importer;
            return $this->view('importer.show', $this->viewData);
        }
    }


}