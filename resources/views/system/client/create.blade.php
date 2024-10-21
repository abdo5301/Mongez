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
            <div class="k-portlet__body">

                {!! Form::open(['route' => isset($result) ? ['system.client.update',$result->id]:'system.client.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

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

                        @if(request('type') == 'client')
                            {!! Form::hidden('type','client',['id'=>'type-form-input','autocomplete'=>'off']) !!}
                        @elseif(request('type') == 'investor')
                            {!! Form::hidden('type','investor',['id'=>'type-form-input','autocomplete'=>'off']) !!}
                        @else
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Type')}}*</label>
                                    {!! Form::select('type',['client'=>__('Client'),'investor'=>__('Investor')],isset($result) ? $result->type : null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="type-form-error"></div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">


                                <div class="col-md-6">
                                    <label>{{__('Name')}}*</label>
                                    {!! Form::text('name',isset($result) ? $result->name : null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="name-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Mobile')}}*</label>
                                    {!! Form::text('mobile1',isset($result) ? $result->mobile1 : null,['class'=>'form-control','id'=>'mobile1-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="mobile1-form-error"></div>
                                </div>

                            </div>


                        <div id="investor-div" style="display: none;">

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Investor Type')}}*</label>
                                    {!! Form::select('investor_type',[''=>__('Select Investor Type'),'individual'=>__('Individual'),'company'=>__('Company'),'broker'=>__('Broker')],isset($result) ? $result->investor_type : null,['class'=>'form-control','id'=>'investor_type-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="investor_type-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row" id="company-name-div" style="display: none;">
                                <div class="col-md-12">
                                    <label>{{__('Company Name')}}*</label>
                                    {!! Form::text('company_name',isset($result) ? $result->company_name : null,['class'=>'form-control','id'=>'company_name-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="company_name-form-error"></div>
                                </div>
                            </div>

                        </div>


                        <div class="form-group row" id="more-option-button">
                            <div class="col-md-12">
                                <button type="button" onclick="$('#more-option-button').hide();$('#option-div').show();" class="btn btn-success col-md-12">{{__('More Option')}}</button>
                            </div>
                        </div>


                        <div id="option-div" style="display: none;">




                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Email')}}</label>
                                    {!! Form::email('email',isset($result) ? $result->email : null,['class'=>'form-control','id'=>'email-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="email-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Phone')}}</label>
                                    {!! Form::text('phone',isset($result) ? $result->phone : null,['class'=>'form-control','id'=>'phone-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="phone-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Mobile 2')}}</label>
                                    {!! Form::text('mobile2',isset($result) ? $result->mobile2 : null,['class'=>'form-control','id'=>'mobile2-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="mobile2-form-error"></div>
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Fax')}}</label>
                                    {!! Form::text('fax',isset($result) ? $result->fax : null,['class'=>'form-control','id'=>'fax-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="fax-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Website')}}</label>
                                    {!! Form::url('website',isset($result) ? $result->website : null,['class'=>'form-control','id'=>'website-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="website-form-error"></div>
                                </div>

                            </div>



                            <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Address')}}</label>
                                    {!! Form::textarea('address',isset($result) ? $result->address : null,['class'=>'form-control','id'=>'address-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="address-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Description')}}</label>
                                    {!! Form::textarea('description',isset($result) ? $result->description : null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="description-form-error"></div>
                                </div>

                            </div>



                            <div class="form-group row">

                                <div class="col-md-12">
                                    <label>{{__('Status')}}</label>
                                    {!! Form::select('status',['active'=> __('Active'),'in-active'=> __('In-Active')],isset($result) ? $result->status : null,['class'=>'form-control','id'=>'status-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="status-form-error"></div>
                                </div>

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
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.client.update',$result->id):route('system.client.store')}}',
                $('#main-form').serialize(),
                function ($data) {

                    @if(request('addClientFromProperty'))
                        $newHTML = "<div class=\"col-md-12\">\n" +
                        "<label>{{__('Client')}}*</label>\n" +
                        "<input type=\"text\" class=\"form-control\" disabled=\"disabled\" value=\""+$data.data.name+"\" />"+
                        "<input type=\"hidden\" name=\"client_id\" value=\""+$data.data.id+"\" />"+
                        "<div class=\"invalid-feedback\" id=\"client_id-form-error\"></div>\n" +
                        "</div>";
                    $("#client-select-information", parent.document.body).html($newHTML);
                    window.parent.closeModal();
                    @elseif(request('addClientFromCall'))
                        $newHTML = "<div class=\"col-md-12\">\n" +
                        "<label>{{__('Client')}}*</label>\n" +
                        "<input type=\"text\" class=\"form-control\" disabled=\"disabled\" value=\""+$data.data.name+"\" />"+
                        "<input type=\"hidden\" name=\"client_id\" value=\""+$data.data.id+"\" />"+
                        "<div class=\"invalid-feedback\" id=\"client_id-form-error\"></div>\n" +
                        "</div>";
                    $("#client-select-information", parent.document.body).html($newHTML);
                    window.parent.closeModal();
                    @else
                        window.location = $data.data.url;
                    @endif


                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }


        function investorDivEvent(){
            if($('#type-form-input').val() == 'investor'){
                $('#investor-div').show();
            }else{
                $('#investor-div').hide();
            }
        }

        function companyNameDivEvent(){
            if($('#investor_type-form-input').val() == 'company' || $('#investor_type-form-input').val() == 'broker'){
                $('#company-name-div').show();
            }else{
                $('#company-name-div').hide();
            }
        }


        $(document).ready(function(){
            investorDivEvent();
            companyNameDivEvent();
        });

        $('#type-form-input').change(function(){
            investorDivEvent();
        });


        $('#investor_type-form-input').change(function(){
            companyNameDivEvent();
        });



    </script>
@endsection