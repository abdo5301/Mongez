@extends('system.layout')
@section('content')

    <div class="modal fade" id="filter-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'filterForm','onsubmit'=>'filterFunction($(this));return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Filter')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <div class="form-group mb1">
                        <label>{{__('Created At')}}</label>
                        <div class="input-daterange input-group" id="k_datepicker_5">
                            {!! Form::text('created_at1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('created_at2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>

                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('ID')}}</label>
                            {!! Form::number('id',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">

                        <div class="col-md-6">
                            <label>{{__('Type')}}*</label>
                            @php
                                $typesData = [''=>__('Select Type')];
                                foreach ($property_types as $key => $value){
                                    $typesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('property_type_id',$typesData,null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Purpose')}}*</label>
                            @php
                                $purposesData = [''=>__('Select Purpose')];
                                foreach ($purposes as $key => $value){
                                    $purposesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('purpose_id',$purposesData,null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>

                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Data Source')}}</label>
                            @php
                                $dataSourcesData = [''=>__('Select Data Source')];
                                foreach ($data_sources as $key => $value){
                                    $dataSourcesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('data_source_id',$dataSourcesData,null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>

                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Client')}}</label>
                            {!! Form::select('client_id',[''=> __('Select Client')],null,['class'=>'form-control client-select','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Area')}}</label>
                            {!! Form::select('area_ids',[''=> __('Select Area')],null,['class'=>'form-control area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Status')}}</label>
                            @php
                                $requestStatusData = [''=>__('Select Status')];
                                foreach ($request_status as $key => $value){
                                    $requestStatusData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('request_status_id',$requestStatusData,null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>

                    </div>



                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Name')}}</label>
                            {!! Form::text('name',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Payment Type')}}</label>
                            {!! Form::select('payment_type',[''=>__('Select Payment Type'),'cash'=> __('Cash'),'installment'=> __('Installment'),'cash_installment'=> __('Cash or Installment')], null,['class'=>'form-control','id'=>'payment_type-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Deposit From (From)')}}</label>
                            {!! Form::number('deposit_from1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Deposit From (To)')}}</label>
                            {!! Form::number('deposit_from2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Deposit To (From)')}}</label>
                            {!! Form::number('deposit_to1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Deposit To (To)')}}</label>
                            {!! Form::number('deposit_to2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Price From (From)')}}</label>
                            {!! Form::number('price_from1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Price From (To)')}}</label>
                            {!! Form::number('price_from2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Price To (From)')}}</label>
                            {!! Form::number('price_to1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Price To (To)')}}</label>
                            {!! Form::number('price_to2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Currency')}}</label>
                            {!! Form::select('currency',[''=> __('Select Currency'),'EGP'=> __('EGP'),'USD'=> __('USD')], null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Model')}}</label>
                            @php
                                $requestModel = [];
                                foreach ($property_model as $key => $value){
                                    $requestModel[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('property_model_id[]',$requestModel, null,['class'=>'form-control multiple-select2','autocomplete'=>'off','multiple'=>'multiple','style'=>'width:100%']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Space From (From)')}}</label>
                            {!! Form::number('space_from1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Space From (To)')}}</label>
                            {!! Form::number('space_from2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Space To (From)')}}</label>
                            {!! Form::number('space_to1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Space To (To)')}}</label>
                            {!! Form::number('space_to2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>



                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Space Type')}}</label>
                            {!! Form::select('space_type',[''=>__('Select Space Type'),'meter'=> __('Meter'),'carat'=> __('Carat'),'acre'=> __('Acre')],null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Sales')}}</label>
                            @php
                                $salesViewSelect = [''=> __('Select Sales')];
                                $salesViewSelect = $salesViewSelect+array_column(getSales()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('sales_id',$salesViewSelect, null,['class'=>'form-control','id'=>'sales_id-form-input','autocomplete'=>'off']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Created By')}}</label>
                            @php
                                $staffViewSelect = [''=> __('Select Creator')];
                                $staffViewSelect  = $staffViewSelect +array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('created_by_staff_id',$staffViewSelect, null,['class'=>'form-control','id'=>'created-by-select-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>



                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Filter')}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

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
                <a href="javascript:filterFunction($('#filterForm'),true)" class="btn btn-sm btn-elevate btn-brand">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Download Excel')}}</span>
                    <i class="flaticon-download k-padding-l-5 k-padding-r-0"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- end:: Content Head -->

    <!-- begin:: Content Body -->
    <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
       {{-- <div class="alert alert-light alert-elevate" role="alert">
            <div class="alert-icon"><i class="flaticon-warning k-font-brand"></i></div>
            <div class="alert-text">
                With server-side processing enabled, all paging, searching, ordering actions that DataTables performs are handed off to a server where an SQL engine (or similar) can perform these actions on the large data set.
                See official documentation <a class="k-link k-font-bold" href="https://datatables.net/examples/data_sources/server_side.html" target="_blank">here</a>.
            </div>
        </div>--}}
        <div class="k-portlet k-portlet--mobile">
            <div class="k-portlet__head">
                <div class="k-portlet__head-label">
                    <h3 class="k-portlet__head-title">
                        {{$pageTitle}}{{__("'s data")}}
                    </h3>
                </div>
            </div>
            <div class="k-portlet__body table-responsive">

                <!--begin: Datatable -->
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

                <!--end: Datatable -->
            </div>
        </div>
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

    <script type="text/javascript">
        ajaxSelect2('.client-select','client');
        ajaxSelect2('.area-select','area',3);
        ajaxSelect2('.sales-select','sales');
        ajaxSelect2('.created-by-select','sales');

        $datatable = $('#datatable-main').DataTable({
            "iDisplayLength": 25,
            processing: true,
            orderCellsTop: true,  // abdo edit
            fixedHeader: true,  // abdo edit
            serverSide: true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": "{{url()->full()}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
            /*,
            "fnPreDrawCallback": function(oSettings) {
                for (var i = 0, iLen = oSettings.aoData.length; i < iLen; i++) {
                    if(oSettings.aoData[i]._aData[6] != ''){
                        oSettings.aoData[i].nTr.className = oSettings.aoData[i]._aData[6];
                    }
                }
            }*/
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
        $('.multiple-select2').select2();


        //abdo edit for <thead> fast searching or filtering

        $('#datatable-main thead tr').clone(true).appendTo( '#datatable-main thead' );
        $('#datatable-main thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            if(title.split(' ').length < 4){ // check number of words inside <th> to cancel the dropdown select <th> from the loop
                //  alert(title);
                $(this).html( '<input type="text" class="form-control form-control-md" placeholder="{{__('Search')}}" />' );
            }else{
                $(this).html( '' );
            }

            $( 'input', this ).on( 'keyup change', function () {
                if ( $datatable.column(i).search() !== this.value ) {
                    $datatable
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            } );
        } );


        //end abdo edit

    </script>

@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
@endsection