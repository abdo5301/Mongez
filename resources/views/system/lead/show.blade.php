@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

    <!-- begin:: Content -->
    <div class="k-content	k-grid__item k-grid__item--fluid k-grid k-grid--hor" id="k_content">

        <!-- begin:: Content Head -->
        <div class="k-content__head	k-grid__item">
            <div class="k-content__head-main">
                <h3 class="k-content__head-title">{{$pageTitle}}</h3>
                <div class="k-content__head-breadcrumbs">
                    <a href="{{route('system.dashboard')}}" class="k-content__head-breadcrumb-home"><i class="flaticon2-shelter"></i></a>

                    @foreach($breadcrumb as $key => $value)
                        <span class="k-content__head-breadcrumb-separator"></span>
                        @if(isset($value['url']))
                            <a href="{{$value['url']}}" class="k-content__head-breadcrumb-link">{{$value['text']}}</a>
                        @else
                            <span class="k-content__head-breadcrumb-link k-content__head-breadcrumb-link--active">{{$value['text']}}</span>
                        @endif
                    @endforeach

                </div>
            </div>
            <div class="k-content__head-toolbar">
                <div class="k-content__head-wrapper">
                    <a href="#" data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">
                        <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Filter')}}</span>
                        <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>
                    </a>

                </div>
            </div>
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-4 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{__('Key')}}</th>
                                            <th>{{__('Value')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Name')}}</td>
                                            <td>{{$result->name}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('File')}}</td>
                                            <td><a href="{{asset($result->file)}}">{{__('Download')}}</a></td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Created By')}}</td>
                                            <td>
                                                <a href="{{route('system.staff.show',$result->created_by_staff_id)}}" target="_blank">
                                                    {{$result->staff->fullname}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Created At')}}</td>
                                            <td>{!! $result->created_at->format('Y-m-d h:iA') . '<br /> ('.$result->created_at->diffForHumans().')' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Last Update')}}</td>
                                            <td>{!! $result->updated_at->format('Y-m-d h:iA') . '<br /> ('.$result->updated_at->diffForHumans().')' !!}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        <div class="col-lg-8 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">
                                            {{__('Data')}}
                                        </h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <div class="tab-content">
                                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                                            <thead>
                                            <tr>
                                                @foreach($tableColumns as $key => $value)
                                                    <th>{{$value}}</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                @foreach($tableColumns as $key => $value)
                                                    <th>{{$value}}</th>
                                                @endforeach
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                    </div>

                    <!--end::Row-->
                </div>
            </div>
        </div>
        <!-- end:: Content -->
        @endsection
        @section('footer')
            <script type="text/javascript">
                $datatable = $('#datatable-main').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "true";
                        }
                    }
                });

                function filterFunction($this,downloadExcel = false){

                    if(downloadExcel == false) {
                        $url = '{{url()->full()}}?is_total=true&'+$this.serialize();
                        $datatable.ajax.url($url).load();
                        $('#filter-modal').modal('hide');
                    }else{
                        $url = '{{url()->full()}}?is_total=true&isDataTable=true&'+$this.serialize()+'&downloadExcel='+downloadExcel;
                        location = $url;
                    }

                }

                function viewProperty($id){
                    $('#property-modal').modal('hide');
                    addLoading();
                    $.get('{{route('system.importer.show',$result->id)}}',{
                        'propertyData': $id,
                        'mobile': $('[name="mobile"]').val(),
                        'count_from': $('[name="count_from"]').val(),
                        'count_to': $('[name=count_to]').val()
                    },function($data){
                        removeLoading();
                        if(!$data.status){
                            return false;
                        }


                        if($data.next){
                            $('#property-modal-next').attr('onclick','viewProperty('+$data.next+')').show();
                        }else{
                            $('#property-modal-next').hide();
                        }

                        if($data.previous){
                            $('#property-modal-previous').attr('onclick','viewProperty('+$data.previous+')').show();
                        }else{
                            $('#property-modal-previous').hide();
                        }

                        if(!$data.property_id){
                            $('#property-modal-property_id').text('{{__('Approve')}}').attr('onclick','approveProperty('+$id+')').show();
                        }else{
                            $('#property-modal-property_id').text('{{__('View')}}').attr('onclick','viewRealProperty('+$data.property_id+')').show();
                        }


                        $('#property-modal-data').html($data.table);
                        $('#property-modal').modal('show');
                    });
                }


                function approveProperty($id){
                    window.open('{{route('system.property.create')}}?importer_data_id='+$id, '_blank').focus();
                }

                function viewRealProperty($id){
                    window.open('{{route('system.property.index')}}/'+$id, '_blank').focus();
                }

            </script>
@endsection