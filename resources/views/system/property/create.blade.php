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

                {!! Form::open(['route' => isset($result) ? ['system.property.update',$result->id]:'system.property.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                {!! Form::hidden('key',$randKey) !!}

                @if($importer_data)
                    {!! Form::hidden('importer_data_id',$importer_data->id) !!}
                @endif

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

                        @if($importer_data)
                            <div class="col-md-12">
                                <label>{{__('Client')}}*</label>
                                <input type="text" class="form-control" disabled="disabled" value="{{$importer_data->owner_name}}" />
                                <div class="invalid-feedback" id="client_id-form-error"></div>
                            </div>
                        @else

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
                                <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.client.create',['addClientFromProperty'=>'true','type'=>'investor'])}}');" class="form-control">
                                    <i class="la la-plus"></i>
                                </a>
                            </div>

                        @endif



                        </div>

                    <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Type')}}*</label>
                                    @php
                                    $typesData = [''=>__('Select Type')];
                                    foreach ($property_types as $key => $value){
                                        $typesData[$value->id] = $value->name;
                                    }

                                    if($importer_data){
                                        $typesDataValue = $importer_data->importer->property_type_id;
                                    }else{
                                        $typesDataValue = isset($result) ? $result->property_type_id: null;
                                    }

                                    @endphp
                                    {!! Form::select('property_type_id',$typesData,$typesDataValue,['class'=>'form-control','id'=>'property_type_id-form-input','onchange'=>'propertyType();','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="property_type_id-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Purpose')}}*</label>
                                    @php
                                        $purposesData = [''=>__('Select Purpose')];
                                        foreach ($purposes as $key => $value){
                                            $purposesData[$value->id] = $value->name;
                                        }

                                        if($importer_data){
                                            $purposesDataValue = $importer_data->importer->purpose_id;
                                        }else{
                                            $purposesDataValue = isset($result) ? $result->purpose_id: null;
                                        }
                                    @endphp
                                    {!! Form::select('purpose_id',$purposesData,$purposesDataValue,['class'=>'form-control','id'=>'purpose_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="purpose_id-form-error"></div>
                                </div>

                            </div>

                        <div class="form-group row">

                            <div class="col-md-12">
                                <label>{{__('Area')}}*</label>
                                @php
                                    $areaViewSelect = [''=> __('Select Area')];
                                    if(isset($result)){
                                        $areaViewSelect[$result->area_id] = implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true) );
                                    }

                                    if($importer_data){
                                        $areaValue = $importer_data->importer->area_id;
                                        $areaViewSelect[$areaValue] = implode(' -> ',\App\Libs\AreasData::getAreasUp($areaValue,true) );
                                    }else{
                                        $areaValue = isset($result) ? $result->area_id: null;
                                    }
                                @endphp
                                {!! Form::select('area_id',$areaViewSelect,$areaValue,['class'=>'form-control area-select','id'=>'area_id-form-input','onchange'=>'changeAreaEvent($(this).val())','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="area_id-form-error"></div>
                            </div>

                        </div>


                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Building Number')}}</label>
                                {!! Form::text('building_number',isset($result) ? $result->building_number: null,['class'=>'form-control','id'=>'building_number-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="building_number-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Flat Number')}}</label>
                                {!! Form::text('flat_number',isset($result) ? $result->flat_number: null,['class'=>'form-control','id'=>'flat_number-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="flat_number-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Address')}}*</label>
                                {!! Form::textarea('address',isset($result) ? $result->address: null,['class'=>'form-control','id'=>'address-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="address-form-error"></div>
                            </div>
                        </div>
                    </div>











                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

                    <div class="form-group row" id="property_model_div" @if(!(isset($result) && in_array($result->area_id,explode(',',setting('show_model_in_area_ids'))))) style="display:none" @endif>
                        <div class="col-md-12">
                            <label>{{__('Model')}}*</label>
                            @php
                               $propertyModelData = [''=>__('Select Model')];
                               foreach ($property_model as $key => $value){
                                   $propertyModelData[$value->id] = $value->name;
                               }
                               $propertyModelValue = isset($result) ? $result->property_model_id: null;

                            @endphp
                            {!! Form::select('property_model_id',$propertyModelData,$propertyModelValue,['class'=>'form-control','id'=>'property_model_id-form-input','onchange'=>'getPropertyModelSpace($(this).val());','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="property_model_id-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-8">
                            <label>{{__('Space')}}*</label>
                            @php
                                if($importer_data){
                                    $spaceValue = $importer_data->space;
                                }else{
                                    $spaceValue = isset($result) ? $result->space: null;
                                }
                            @endphp
                            {!! Form::text('space',$spaceValue,['class'=>'form-control','id'=>'space-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="space-form-error"></div>
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
                            <div class="col-md-4">
                                <label>{{__('Payment Type')}}*</label>
                                {!! Form::select('payment_type',['cash'=> __('Cash'),'installment'=> __('Installment'),'cash_installment'=> __('Cash or Installment')],isset($result) ? $result->payment_type: null,['class'=>'form-control','id'=>'payment_type-form-input','onchange'=>'paymentType();','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="payment_type-form-error"></div>
                            </div>

                            <div class="col-md-3" id="deposit_div" style="display: none;">
                                <label>{{__('Deposit')}}*</label>
                                {!! Form::text('deposit',isset($result) ? $result->deposit: null,['class'=>'form-control','id'=>'deposit-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="deposit-form-error"></div>
                            </div>

                            <div class="col-md-6" id="price_div">
                                <label>{{__('Price')}}*</label>
                                @php
                                    if($importer_data){
                                        $priceValue = $importer_data->price;
                                    }else{
                                        $priceValue = isset($result) ? $result->price: null;
                                    }
                                @endphp
                                {!! Form::text('price',$priceValue,['class'=>'form-control','id'=>'price-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price-form-error"></div>
                            </div>

                            <div class="col-md-2">
                                <label>{{__('Currency')}}*</label>
                                {!! Form::select('currency',['EGP'=>__('EGP'),'USD'=>__('USD')],isset($result) ? $result->currency: null,['class'=>'form-control','id'=>'currency-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="currency-form-error"></div>
                            </div>

                        </div>


                    <div class="form-group row" id="years_of_installment_div" style="display:none;">
                        <div class="col-md-12">
                            <label>{{__('Years Of Installment')}}*</label>
                            {!! Form::text('years_of_installment',isset($result) ? $result->years_of_installment: null,['class'=>'form-control','id'=>'years_of_installment-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="years_of_installment-form-error"></div>
                        </div>
                    </div>


                    <div class="form-group row">

                        <div class="col-md-12" id="price_div">
                            <label>{{__('Negotiable')}}*</label>
                            {!! Form::select('negotiable',['yes'=>__('Yes'),'no'=>__('No')],isset($result) ? $result->negotiable: null,['class'=>'form-control','id'=>'negotiable-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="negotiable-form-error"></div>
                        </div>

                    </div>


            </div>











                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Description')}}*</label>
                            @php
                                if($importer_data){
                                    $descriptionValue = $importer_data->description;
                                }else{
                                    $descriptionValue = isset($result) ? $result->description: null;
                                }
                            @endphp
                            {!! Form::textarea('description',$descriptionValue,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="description-form-error"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Remarks')}}</label>
                            {!! Form::textarea('remarks',isset($result) ? $result->remarks: null,['class'=>'form-control','id'=>'remarks-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="remarks-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Images')}}</label>
                            <input type="file" class="form-control" id="images-form-input" autocomplete="off" name="images" />
                            <div class="invalid-feedback" id="images-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Video URL')}}</label>
                            {!! Form::url('video_url',isset($result) ? $result->video_url: null,['class'=>'form-control','id'=>'video_url-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="video_url-form-error"></div>
                        </div>
                    </div>


                </div>

                <div id="parameters-html" style="display:none;"></div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">


                    <div class="form-group row">

                        <div class="col-md-6">
                            <label>{{__('Status')}}*</label>
                            @php
                                $propertyStatusData = [''=>__('Select Status')];
                                foreach ($property_status as $key => $value){
                                    $propertyStatusData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('property_status_id',$propertyStatusData,isset($result) ? $result->property_status_id: setting('default_property_status'),['class'=>'form-control','id'=>'property_status_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="property_status_id-form-error"></div>
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


                    <div class="form-group row" id="hold_until_div" style="display: none;">
                        <div class="col-md-12">
                            <label>{{__('Hold Until')}} *</label>
                            {!! Form::text('hold_until',isset($result) ? $result->hold_until: null,['class'=>'form-control k_datepicker_1','id'=>'hold_until-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="hold_until-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Property Name')}}</label>
                            @php
                                if($importer_data){
                                    $nameValue = $importer_data->name;
                                }else{
                                    $nameValue = isset($result) ? $result->name: null;
                                }
                            @endphp
                            {!! Form::text('name',$nameValue,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="name-form-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Data Source')}}*</label>
                            @php
                                $dataSourcesData = [''=>__('Select Data Source')];
                                foreach ($data_sources as $key => $value){
                                    $dataSourcesData[$value->id] = $value->name;
                                }

                                if($importer_data){
                                    if($importer_data->importer->connector == 'OLX'){
                                        $dataSourceValue = setting('olx_data_data_source_id');
                                    }elseif($importer_data->importer->connector == 'Aqarmap'){
                                        $dataSourceValue = setting('aqarmap_data_data_source_id');
                                    }

                                }else{
                                    $dataSourceValue = isset($result) ? $result->data_source_id: setting('default_data_source_id');
                                }
                            @endphp
                            {!! Form::select('data_source_id',$dataSourcesData,$dataSourceValue,['class'=>'form-control','id'=>'data_source_id-form-input','autocomplete'=>'off']) !!}
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
    <script src="{{asset('assets/uploader/jquery.fileuploader.min.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>

    <script type="text/javascript">

        ajaxSelect2('.client-select','investor');
        ajaxSelect2('.sales-select','sales');
        ajaxSelect2('.area-select','area',3);

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.property.update',$result->id):route('system.property.store')}}',
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
        function paymentType(){
            if($('#payment_type-form-input').val() == 'cash'){
                $('#price_div').attr('class','col-md-6');
                $('#deposit_div').hide();
                $('#years_of_installment_div').hide();
            }else{
                $('#price_div').attr('class','col-md-3');
                $('#deposit_div').show();
                $('#years_of_installment_div').show();
            }
        }
        function propertyType(){
            $('#parameters-html').hide();
            addLoading();
            $.get('{{route('system.misc.ajax')}}',{
                'type':'parameters-form',
                'property_type_id':$('#property_type_id-form-input').val(),
                @if(isset($result)) 'property_id': '{{$result->id}}', @endif
                @if($importer_data)
                    @foreach(explode(',',setting('bed_rooms_names')) as $key => $value)
                        'p_{{$value}}': '{{$importer_data->bed_rooms}}',
                    @endforeach
                    @foreach(explode(',',setting('bath_room_names')) as $key => $value)
                        'p_{{$value}}': '{{$importer_data->bath_room}}',
                    @endforeach
                @endif
            },function($data){
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

        $(document).ready(function() {
            changeStatus();
            // enable fileupload plugin
            $('#images-form-input').fileuploader({
                onSelect: function(item) {
                    // if (!item.html.find('.fileuploader-action-start').length)
                    //     item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-start" title="Upload"><i></i></a>');
                },
                upload: {
                    url: '{{route('system.property.image-upload')}}',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'key':  '{{$randKey}}'
                    },
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    start: true,
                    synchron: true,
                    onSuccess: function(result, item) {

                        console.log(result);

                        item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
                    },
                    onError: function(item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {

                        // console.log(jqXHR);


                        item.upload.status != 'cancelled' && item.html.find('.fileuploader-action-retry').length == 0 ? item.html.find('.column-actions').prepend(
                            '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
                        ) : null;
                    },
                    onComplete: null,
                },
                onRemove: function(item) {
                    // send POST request
                    $.post('{{route('system.property.remove-image')}}', {
                        'name': item.name,
                        '_token': '{{csrf_token()}}',
                        'key':  '{{$randKey}}'
                    });
                }
            });

        });

        $('#property_status_id-form-input').change(function(){
            changeStatus();
        });

        function changeStatus(){
            $value = $('#property_status_id-form-input').val();
            if(in_array($value,[{{setting('archive_property_status')}}])){
                $('#hold_until_div').show();
            }else{
                $('#hold_until_div').hide();
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
            // MODEL MODEL MODEL MODEL
            $mainData = new Array;
            @foreach(explode(',',area_has_models_ids()) as $key => $value)
                $mainData[{{$value}}] = '{{$value}}';
            @endforeach

            if(isset($mainData[$value])){
                $('#property_model_div').show();
            }else{
                $('#property_model_div').hide();
                $('#property_model_id-form-input').val('');
            }
            // MODEL MODEL MODEL MODEL
        }



    </script>
@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/uploader/font/font-fileuploader.css')}}" rel="stylesheet">
    <link href="{{asset('assets/uploader/jquery.fileuploader.min.css')}}" media="all" rel="stylesheet">
@endsection