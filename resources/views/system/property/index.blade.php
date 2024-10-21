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
                            {!! Form::select('area_id',[''=> __('Select Area')],null,['class'=>'form-control area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Building Number')}}</label>
                            {!! Form::text('building_number',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Flat Number')}}</label>
                            {!! Form::text('flat_number',null,['class'=>'form-control']) !!}
                        </div>
                    </div>



                    <div class="form-group row mb1">

                        <div class="col-md-12">
                            <label>{{__('Status')}}</label>
                            @php
                                $propertyStatusData = [''=>__('Select Status')];
                                foreach ($property_status as $key => $value){
                                    $propertyStatusData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('property_status_id',$propertyStatusData, null,['class'=>'form-control','id'=>'property_status_id-form-input','autocomplete'=>'off']) !!}
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
                            <label>{{__('Years Of Installment From')}}</label>
                            {!! Form::number('years_of_installment1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Years Of Installment To')}}</label>
                            {!! Form::number('years_of_installment2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Deposit From')}}</label>
                            {!! Form::number('deposit1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Deposit To')}}</label>
                            {!! Form::number('deposit2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Price From')}}</label>
                            {!! Form::number('price1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Price To')}}</label>
                            {!! Form::number('price2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>



                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Currency')}}</label>
                            {!! Form::select('currency',[''=> __('Select Currency'),'EGP'=> __('EGP'),'USD'=> __('USD')], null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Negotiable')}}</label>
                            {!! Form::select('negotiable',[''=> __('Select Negotiable'),'yes'=> __('Yes'),'no'=> __('No')], null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>

                    </div>


                    <div class="form-group row mb1">

                        <div class="col-md-12">
                            <label>{{__('Model')}}</label>
                            @php
                                $propertyModelData = [''=>__('Select Model')];
                                foreach ($property_model as $key => $value){
                                    $propertyModelData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('property_model_id',$propertyModelData, null,['class'=>'form-control','id'=>'property_model_id-form-input','autocomplete'=>'off']) !!}
                        </div>

                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-4">
                            <label>{{__('Space From')}}</label>
                            {!! Form::number('space1',null,['class'=>'form-control']) !!}
                        </div>

                        <div class="col-md-4">
                            <label>{{__('Space To')}}</label>
                            {!! Form::number('space2',null,['class'=>'form-control']) !!}
                        </div>

                        <div class="col-md-4">
                            <label>{{__('Space Type')}}</label>
                            {!! Form::select('space_type',[''=>__('Select Space Type'),'meter'=> __('Meter'),'carat'=> __('Carat'),'acre'=> __('Acre')],null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Address')}}</label>
                            {!! Form::text('address',null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group mb1">
                        <label>{{__('Call Update')}}</label>
                        <div class="input-daterange input-group" id="k_datepicker_6">
                            {!! Form::text('call_update1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('call_update2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>

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
                            <th>{{__('ID')}}</th>
                            <th>
                            <div class="dropdown">
                                <a id="th_investor_type_a" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="false">
                                    {{__('Investor')}}  <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a onclick="$('#th_investor_type').val('').change();" href="javascript:void(0);">{{__('Select')}}</a></li>

                                    <li><a onclick="$('#th_investor_type').val('individual').change();" href="javascript:void(0);">{{__('Individual')}}</a></li>
                                    <li><a onclick="$('#th_investor_type').val('company').change();" href="javascript:void(0);">{{__('Company')}}</a></li>
                                    <li><a onclick="$('#th_investor_type').val('broker').change();" href="javascript:void(0);">{{__('Broker')}}</a></li>

                                </ul>
                            </div>
                            <input id="th_investor_type" type="hidden" />
                          </th>

                            <th>
                            <div class="dropdown">
                                <a id="th_property_type_id_a" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="false">
                                    {{__('Type')}}  <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a onclick="$('#th_property_type_id').val('').change();" href="javascript:void(0);">{{__('Select')}}</a></li>

                                    @foreach(getPropertyType() as $key => $value)
                                        <li><a onclick="$('#th_property_type_id').val('{{$value->id}}').change();" href="javascript:void(0);">{{$value->name}}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                            <input id="th_property_type_id" type="hidden" />
                          </th>
                            <th>
                            <div class="dropdown">
                                <a id="th_purpose_id_a" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="false">
                                    {{__('Purpose')}}  <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a onclick="$('#th_purpose_id').val('').change();" href="javascript:void(0);">{{__('Select')}}</a></li>

                                    @foreach(getPurpose() as $key => $value)
                                        <li><a onclick="$('#th_purpose_id').val('{{$value->id}}').change();" href="javascript:void(0);">{{$value->name}}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                            <input id="th_purpose_id" type="hidden" />
                           </th>
                                <th>{{__('Space')}}
                                    <input type="text" id="th_space_header_id" class="form-control form-control-sm" placeholder="{{__('Search in').' '.__('Space') }}" />
                                </th>
                                <th>{{__('Price')}}
                                    <input type="text" id="th_price_header_id" class="form-control form-control-sm" placeholder="{{__('Search in').' '.__('Price') }}" />
                                </th>

                        <th>
                            <div class="dropdown">
                                <a id="th_area_header_a" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="false">
                                    {{__('Area')}}  <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu" id="dropdown_menu_id">
                                    <li>
                                        @php
                                        $firstArea = getFirstArea();
                                        @endphp
                                        <a onclick="dropdownMenuArea({{$firstArea->id}});" href="javascript:void(0);">{{$firstArea->name}}</a>
                                    </li>
                                </ul>
                            </div>
                            <input id="th_area_header" type="hidden" />
                            <input type="text" id="th_address_header_id" class="form-control form-control-sm" placeholder="{{__('Search in').' '.__('Address') }}" />

                            </th>
                            <th>
                            <div class="dropdown">
                                <a id="th_property_status_id_a" href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="false">
                                    {{__('Status')}}  <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a onclick="$('#th_property_status_id').val('').change();" href="javascript:void(0);">{{__('Select')}}</a></li>

                                    @foreach(getPropertyStatus() as $key => $value)
                                        <li><a onclick="$('#th_property_status_id').val('{{$value->id}}').change();" href="javascript:void(0);">{{$value->name}}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                            <input id="th_property_status_id" type="hidden" />

                        </th>
                            <th>{{__('Sales')}}</th>
                            <th>{{__('Created At')}}</th>
                            <th>{{__('Action')}}</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Investor')}}</th>
                        <th>{{__('Type')}}</th>
                        <th>{{__('Purpose')}}</th>
                        <th>{{__('Space')}}</th>
                        <th>{{__('Price')}}</th>
                        <th>{{__('Area')}}</th>
                        <th>{{__('Status')}}</th>
                        <th>{{__('Sales')}}</th>
                        <th>{{__('Created At')}}</th>
                        <th>{{__('Action')}}</th>
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
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>



    <script type="text/javascript">

        ajaxSelect2('.client-select','investor');
        ajaxSelect2('.area-select','area',3);
        ajaxSelect2('.sales-select','sales');
        ajaxSelect2('.created-by-select','sales');




        $datatable = $('#datatable-main').DataTable({
            "iDisplayLength": 25,
            //abdo edit start
            //naming columns to catch them from setting
            columns: [
                { name: 'id' },
                { name: 'client_id' },
                { name: 'property_type_id' },
                { name: 'purpose_id' },
                { name: 'space' },
                { name: 'price' },
                { name: 'area' },
                { name: 'property_status_id' },
                { name: 'sales_id' },
                { name: 'created_at' },
                { name: 'action' },
            ],
            //abdo edit end

            "columnDefs": [
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": false, "targets": 3 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },
                { "orderable": false, "targets": 6 },
                { "orderable": false, "targets": 7 },
                { "orderable": false, "targets": 8 },
                { "orderable": false, "targets": 9 },
                { "orderable": false, "targets": 10 }
            ],
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

        $('#th_property_type_id,#th_purpose_id,#th_property_status_id,#th_investor_type,#th_area_header').change(function(){
            if(!empty($(this).val())){
                $('#'+$(this).attr('id')+"_a").css('color','red');
            }else{
                $('#'+$(this).attr('id')+"_a").css('color','');
            }

            $WHERE = [];

            if(!empty($('#th_property_type_id').val())){
                $WHERE.push("property_type_id="+$('#th_property_type_id').val());
            }

            if(!empty($('#th_purpose_id').val())){
                $WHERE.push("purpose_id="+$('#th_purpose_id').val());
            }

            if(!empty($('#th_property_status_id').val())){
                $WHERE.push("property_status_id="+$('#th_property_status_id').val());
            }

            if(!empty($('#th_investor_type').val())){
                $WHERE.push("investor_type="+$('#th_investor_type').val());
            }

            if(!empty($('#th_area_header').val())){
                $WHERE.push("area_id="+$('#th_area_header').val());
            }


            var URL = $WHERE.join("&");

            if("{{url()->full()}}".includes('?')){
                $sign = '&';
            }else{
                $sign = '?';
            }

            $datatable.ajax.url("{{url()->full()}}"+$sign+URL).load();
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


        function dropdownMenuArea($id){
            $back = 0;
            if($id == 0){
                $id = $('#th_area_header').val();
                $back = 1;
            }

            $('#th_area_header').val($id).change();
            $.ajax({
                url: "{{route('system.misc.ajax',['type'=>'dropdownMenuArea'])}}",
                method:"GET",
                data: {
                    'id':$id,
                    'back':$back
                },
                cache: false
            }).done(function( data ) {
                if(empty(data)){
                    return false;
                }

                var $return = '';

                if(data.area_type_id != 1){
                    $return += '<li><a onclick="dropdownMenuArea(0);" href="javascript:void(0);">{{__('>')}}</a></li>';
                }



                $.each(data.areas,function(key,value){
                    $return += '<li><a onclick="dropdownMenuArea('+value.id+');" href="javascript:void(0);">'+value.name+'</a></li>';
                });

                $('#dropdown_menu_id').html($return);

            });
        }


        //abdo edit for <thead> fast searching or filtering columns from settings

        //fast search server side
        $('#th_space_header_id,#th_price_header_id,#th_address_header_id').keyup(function(){

            $WHERE = [];

            if(!empty($('#th_space_header_id').val())){
                $WHERE.push("space="+$('#th_space_header_id').val());
            }

            if(!empty($('#th_price_header_id').val())){
                $WHERE.push("price="+$('#th_price_header_id').val());
            }

            if(!empty($('#th_address_header_id').val())){
                $WHERE.push("address="+$('#th_address_header_id').val());
            }

            var URL = $WHERE.join("&");

            if("{{url()->full()}}".includes('?')){
                $sign = '&';
            }else{
                $sign = '?';
            }

            $datatable.ajax.url("{{url()->full()}}"+$sign+URL).load();
        });


            //setting filter
            @if(setting('table_prop_id') === 'no')
            $datatable.column( 'id:name' ).visible(false);
            @endif

            @if(setting('table_prop_client') === 'no')
            $datatable.column( 'client_id:name' ).visible(false);
            @endif

            @if(setting('table_prop_status') === 'no')
            $datatable.column( 'property_status_id:name' ).visible(false);
            @endif

            @if(setting('table_prop_type') === 'no')
            $datatable.column( 'property_type_id:name' ).visible(false);
            @endif

            @if(setting('table_prop_purpose') === 'no')
            $datatable.column( 'purpose_id:name' ).visible(false);
            @endif

            @if(setting('table_prop_price') === 'no')
            $datatable.column( 'price:name' ).visible(false);
            @endif

            @if(setting('table_prop_space') === 'no')
            $datatable.column( 'space:name' ).visible(false);
            @endif

            @if(setting('table_prop_area') === 'no')
            $datatable.column( 'area:name' ).visible(false);
            @endif

            @if(setting('table_prop_sales') === 'no')
            $datatable.column( 'sales_id:name' ).visible(false);
            @endif

            @if(setting('table_prop_created_at') === 'no')
            $datatable.column( 'created_at:name' ).visible(false);
            @endif

            @if(setting('table_prop_action') === 'no')
            $datatable.column( 'action:name' ).visible(false);
            @endif


        //fast search in shown column only { not server side }
        {{--$('#datatable-main thead tr').clone(true).appendTo( '#datatable-main thead' );--}}
        {{--$('#datatable-main thead tr:eq(1) th').each( function (i) {--}}
        {{--        var title = $(this).text();--}}
        {{--        if(title.split(' ').length < 4 && title !== '{{__('Action')}}' ){ // check number of words inside <th> to cancel the dropdown select <th> from the loop--}}
        {{--             //  alert(title);--}}
        {{--            $(this).html( '<input type="text" id="fast_prop_search_'+title+'" class="form-control form-control-md" placeholder="{{__('Search')}}" />' );--}}
        {{--        }else{--}}
        {{--            $(this).html( '' );--}}
        {{--        }--}}


        // $( 'input', this ).on( 'keyup change', function () {
        //     if ( $datatable.column(i).search() !== this.value ) {
        //         $datatable
        //             .column(i)
        //             .search( this.value )
        //             .draw();
        //     }
        // } );
        // } );


            //end abdo edit



    </script>




@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
@endsection