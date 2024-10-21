@extends('system.layout')
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
            <div class="k-portlet__body" style="background: #f7f7fb;">

                {!! Form::open(['route' => isset($result) ? ['system.request.update',$result->id]:'system.request.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body" style="background: #FFF;">

                        <div id="form-alert-message"></div>

                        {{--@if($errors->any())
                            <div class="alert alert-danger fade show" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">{{__('Some fields are invalid please fix them')}}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @elseif(Session::has('status'))
                            <div class="alert alert-{{Session::get('status')}} fade show" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">{{ Session::get('msg') }}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @endif--}}

                    <div class="form-group row" id="client-select-information">

                            <div class="col-md-10">
                                <label>{{__('Client')}}*</label>
                                @php
                                $clientViewSelect = [''=> __('Select Client')];
                                if(isset($result)){
                                    $clientViewSelect[$result->client_id] = $result->client->name;
                                }
                                @endphp
                                {!! Form::select('client_id',$clientViewSelect,isset($result) ? $result->client_id: null,['class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_id-form-error"></div>
                            </div>

                            <div class="col-md-2">
                                <label style="color: #FFF;">*</label>
                                <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.client.create',['addClientFromProperty'=>'true','type'=>'client'])}}');" class="form-control">
                                    <i class="la la-plus"></i>
                                </a>
                            </div>


                        </div>

                    <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Type')}}*</label>
                                    @php
                                    $typesData = [''=>__('Select Type')];
                                    foreach ($property_types as $key => $value){
                                        $typesData[$value->id] = $value->name;
                                    }
                                    @endphp
                                    {!! Form::select('property_type_id',$typesData,isset($result) ? $result->property_type_id: null,['class'=>'form-control','id'=>'property_type_id-form-input','onchange'=>'propertyType();','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="property_type_id-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Purpose')}}*</label>
                                    @php
                                        $purposesData = [''=>__('Select Purpose')];
                                        foreach ($purposes as $key => $value){
                                            $purposesData[$value->id] = $value->name;
                                        }
                                    @endphp
                                    {!! Form::select('purpose_id',$purposesData,isset($result) ? $result->purpose_id: null,['class'=>'form-control','id'=>'purpose_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="purpose_id-form-error"></div>
                                </div>

{{--
                                <div class="col-md-4">
                                    <label>{{__('Data Source')}}*</label>
                                    @php
                                        $dataSourcesData = [''=>__('Select Data Source')];
                                        foreach ($data_sources as $key => $value){
                                            $dataSourcesData[$value->id] = $value->name;
                                        }
                                    @endphp
                                    {!! Form::select('data_source_id',$dataSourcesData,isset($result) ? $result->data_source_id: null,['class'=>'form-control','id'=>'data_source_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="data_source_id-form-error"></div>
                                </div>--}}

                            </div>

                        <div class="form-group row">
                            @if(setting('request_area_type') == '1')
                            <div class="col-md-12">
                                <label>{{__('Areas')}}*</label>
                                @php
                                    $areaViewSelect = [];
                                    if(isset($result)){
                                        foreach(explode(',',$result->area_ids) as $key => $value){
                                            $areaViewSelect[$value] = implode(' -> ',\App\Libs\AreasData::getAreasUp($value,true) );
                                        }
                                    }
                                @endphp
                                {!! Form::select('area_ids[]',$areaViewSelect,isset($result) ? explode(',',$result->area_ids): null,['class'=>'form-control area-select','id'=>'area_ids-form-input','autocomplete'=>'off','onchange'=>'changeAreaEvent($(this).val())','multiple'=>'multiple']) !!}
                                <div class="invalid-feedback" id="area_ids-form-error"></div>
                            </div>
                            @else

                                <div class="form-group col-sm-6{!! formError($errors,'area_id',true) !!}">
                                    <fieldset egpay="select" class="form-group">
                                    <div class="row">

                                        <div class="col-md-10">
                                            {{ Form::label('area_id',$areas_data['type']->name) }}
                                            @php
                                                $arrayOfArea = $areas_data['areas']->toArray();
                                                if(!$arrayOfArea){
                                                    $arrayOfArea = [];
                                                }else{
                                                    $arrayOfArea = array_column($arrayOfArea,'name','id');
                                                }
                                            @endphp
                                            {!! Form::select('area_id[]',array_merge([0=>__('Select Area')],$arrayOfArea),null,['class'=>'form-control','id'=>'area_id','onchange'=>'getNextAreas($(this).val(),"'.$areas_data['type']->id.'",\'#nextAreasID\')']) !!}
                                            {!! formError($errors,'area_id') !!}
                                        </div>
                                        <div class="col-md-2" style="padding-top: 30px;">
                                            <a style="font-size: 18px;" onclick="ClickMove('#area_id','{{$areas_data['type']->name}}')" href="javascript:void(0)"> &gt;&gt; </a>
                                        </div>

                                    </div>
                                    </fieldset>
                                    <div id="nextAreasID">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Selected Areas')}}*</label>

                                    <select name="area_ids[]" class="form-control" id="area_ids-form-input" multiple="multiple" style="height: 200px;">
                                        @php
                                            if(isset($result)){
                                                foreach(\App\Models\Area::whereIn('id',explode(',',$result->area_ids))->get() as $key => $value){
                                                    echo '<option selected="selected" ondblclick="$(this).remove();"  id="CUC_'.$value->id.'" value="'.$value->id.'">'.$value->areaType->{'name_'.\App::getLocale()}.':'.$value->{'name_'.\App::getLocale()}.'</option>';
                                                }
                                            }
                                        @endphp
                                    </select>
                                    <div class="invalid-feedback" id="area_ids-form-error"></div>
                                </div>





                            @endif
                        </div>

                        <div class="form-group row">

                            <div class="col-md-12">
                                <label>{{__('Description')}}*</label>
                                {!! Form::textarea('description',isset($result) ? $result->description: null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="description-form-error"></div>
                            </div>

                        </div>

                    </div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <div class="form-group row" id="property_model_div" @if(!(isset($result) && !empty(array_intersect(explode(',',$result->area_ids), explode(',',area_has_models_ids())))))) style="display:none" @endif>
                        <div class="col-md-12">
                            <label>{{__('Model')}}*</label>
                            @php
                                $propertyModelData = [];//[''=>__('Select Model')];
                                $propertyModelValue = [];
                                foreach ($property_model as $key => $value){
                                    $propertyModelData[$value->id] = $value->name;

                                    if(isset($result) && in_array($value->id,explode(',', $result->property_model_id))){
                                   $propertyModelValue []= $value->id;
                                    }
                                 }


                            @endphp
                            {!! Form::select('property_model_id[]',$propertyModelData,$propertyModelValue,['class'=>'form-control multiple-select2','id'=>'property_model_id-form-input','onchange'=>'getPropertyModelSpace($(this).val());','autocomplete'=>'off','multiple'=>'multiple','style'=>'width:100%']) !!}
                            <div class="invalid-feedback" id="property_model_id-form-error"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>{{__('Space From')}}*</label>
                            {!! Form::text('space_from',isset($result) ? $result->space_from: null,['class'=>'form-control','id'=>'space_from-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="space_from-form-error"></div>
                        </div>

                        <div class="col-md-4">
                            <label>{{__('Space To')}}*</label>
                            {!! Form::text('space_to',isset($result) ? $result->space_to: null,['class'=>'form-control','id'=>'space_to-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="space_to-form-error"></div>
                        </div>

                        <div class="col-md-4">
                            <label>{{__('Space Type')}}*</label>
                            {!! Form::select('space_type',['meter'=> __('Meter'),'carat'=> __('Carat'),'acre'=> __('Acre')],isset($result) ? $result->space_type: null,['class'=>'form-control','id'=>'space_type-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="space_type-form-error"></div>
                        </div>

                    </div>

                </div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Payment Type')}}*</label>
                                {!! Form::select('payment_type',['cash'=> __('Cash'),'installment'=> __('Installment'),'cash_installment'=> __('Cash or Installment')],isset($result) ? $result->payment_type: null,['class'=>'form-control','id'=>'payment_type-form-input','onchange'=>'paymentType();','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="payment_type-form-error"></div>
                            </div>
                        </div>

                    <div class="form-group row" id="deposit_div" style="display: none;">
                        <div class="col-md-6">
                            <label>{{__('Deposit From')}}*</label>
                            {!! Form::text('deposit_from',isset($result) ? $result->deposit_from: null,['class'=>'form-control','id'=>'deposit_from-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="deposit_from-form-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Deposit To')}}*</label>
                            {!! Form::text('deposit_to',isset($result) ? $result->deposit_to: null,['class'=>'form-control','id'=>'deposit_to-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="deposit_to-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row" id="price_div">
                            <div class="col-md-5">
                                <label>{{__('Price From')}}*</label>
                                {!! Form::text('price_from',isset($result) ? $result->price_from: null,['class'=>'form-control','id'=>'price_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price_from-form-error"></div>
                            </div>

                            <div class="col-md-5">
                                <label>{{__('Price To')}}*</label>
                                {!! Form::text('price_to',isset($result) ? $result->price_to: null,['class'=>'form-control','id'=>'price_to-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price_to-form-error"></div>
                            </div>

                            <div class="col-md-2 price_div">
                                <label>{{__('Currency')}}*</label>
                                {!! Form::select('currency',['EGP'=>__('EGP'),'USD'=>__('USD')],isset($result) ? $result->currency: null,['class'=>'form-control','id'=>'currency-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="currency-form-error"></div>
                            </div>
                    </div>

            </div>



                <div id="parameters-html" style="display:none;"></div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Status')}}*</label>
                            @php
                                $requestStatusData = [''=>__('Select Status')];
                                foreach ($request_status as $key => $value){
                                    $requestStatusData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('request_status_id',$requestStatusData,isset($result) ? $result->request_status_id: null,['class'=>'form-control','id'=>'request_status_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="request_status_id-form-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Sales')}}*</label>
                            @php
                                $salesViewSelect = [''=> __('Select Sales')];
                                $salesViewSelect = $salesViewSelect+array_column(getSales()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('sales_id',$salesViewSelect,isset($result) ? $result->sales_id: null,['class'=>'form-control','id'=>'sales_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="sales_id-form-error"></div>
                        </div>



                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Request Name')}}</label>
                            {!! Form::text('name',isset($result) ? $result->name: null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="name-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Data Source')}}*</label>
                            @php
                                $dataSourcesData = [''=>__('Select Data Source')];
                                foreach ($data_sources as $key => $value){
                                    $dataSourcesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('data_source_id',$dataSourcesData,isset($result) ? $result->data_source_id: null,['class'=>'form-control','id'=>'data_source_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="data_source_id-form-error"></div>
                        </div>
                    </div>

                    <div class="k-portlet__foot">
                        <div class="k-form__actions">
                            <div class="row" style="float: right;">
                                <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                            </div>
                        </div>
                    </div>

                </div>

                {!! Form::close() !!}
        </div>
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        ajaxSelect2('.client-select','client');
        ajaxSelect2('.sales-select','sales');
        @if(setting('request_area_type') == '1')
        ajaxSelect2('.area-select','area',3);
        @else
        @php
            $startWorkWithArea = getLastNotEmptyItem(old('area_id'));
            if($startWorkWithArea){
                $areaData = \App\Libs\AreasData::getAreasUp($startWorkWithArea);
                echo '$runAreaLoop = true;$areaLoopData = [];';
                if($areaData){
                    foreach ($areaData as $key => $value){
                        echo '$areaLoopData['.$key.'] = '.$value.';';
                    }
                    echo '$(\'#area_id\').val(next($areaLoopData)).change();';
                }
            }
        @endphp

        @endif

        function submitMainForm(){
            @if(setting('request_area_type') != '1')
            $('#area_ids-form-input option').prop('selected', true);
            @endif
                if( $('#payment_type-form-input').val() != 'cash' &&
                    ( $('#deposit_from-form-input').val() == '' || $('#deposit_to-form-input').val() == '' ) ){
                if (confirm("<?= __('Deposite is empty,are you sure you want to let it empty ?') ?>") == true) {
                    formSubmit(
                        '{{isset($result) ? route('system.request.update',$result->id):route('system.request.store')}}',
                        $('#main-form').serialize(),
                        function ($data) {
                            window.location = $data.data.url;
                        },
                        function ($data){
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            pageAlert('#form-alert-message','error',$data.message);
                        }
                    );
                } else {
                    return false;
                }
            }else{
                formSubmit(
                    '{{isset($result) ? route('system.request.update',$result->id):route('system.request.store')}}',
                    $('#main-form').serialize(),
                    function ($data) {
                        window.location = $data.data.url;
                    },
                    function ($data){
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',$data.message);
                    }
                );
            }


        }

        function getPropertyModelSpace($value) {
            $mainData = new Array;
            @foreach($property_model as $key => $value)
                $mainData[{{$value->id}}] = '{{$value->space}}';
            @endforeach

            if(isset($mainData[$value])){
                $('#space-form-input').val($mainData[$value]);
            }
        }

        function changeAreaEvent($value){
            console.log($value);
            // MODEL MODEL MODEL MODEL
            $mainData = new Array;
            @foreach(explode(',',area_has_models_ids()) as $key => $value)
                $mainData[{{$value}}] = '{{$value}}';
            @endforeach
var show = 0;
                foreach($mainData,function (key,val) {
                    console.log(val);
                if($value.includes(val)){
                    show = 1;
                }
            })

            if(show == 1){
                $('#property_model_div').show();
            }else{
                $('#property_model_div').hide();
                $('#property_model_id-form-input').val('');
            }
            // MODEL MODEL MODEL MODEL
        }

        function paymentType(){
            if($('#payment_type-form-input').val() == 'cash'){
                $('#deposit_div').hide();
            }else{
                $('#deposit_div').show();
            }
        }



        function propertyType(){
            $('#parameters-html').hide();
            addLoading();
            $.get('{{route('system.misc.ajax')}}',{'type':'parameters-request-form','property_type_id':$('#property_type_id-form-input').val() @if(isset($result)) ,'request_id': '{{$result->id}}' @endif},function($data){
                removeLoading();
                if($data == false) return false;
                $('#parameters-html').show().html($data);
                $('.multiple-select2').select2();
            });
        }

        $(document).ready(function(){
            paymentType();
            propertyType();
            $('.multiple-select2').select2();

        });

        window.closeModal = function(){
            $('#modal-iframe').modal('hide');
        };

    </script>
@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
@endsection