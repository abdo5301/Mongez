<div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

    @foreach($result as $key => $value)
        @switch($value->type)
            @case('text')
            <div class="form-group row">
                <div class="col-md-12">
                    <label>{{$value->{'name_'.App::getLocale()} }}*</label>
                    @if(isset($request_data))
                        {!! Form::text('p_'.$value->column_name,$request_data->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @else
                        {!! Form::text('p_'.$value->column_name,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @endif
                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break

            @case('textarea')
            <div class="form-group row">
                <div class="col-md-12">
                    <label>{{$value->{'name_'.App::getLocale()} }}*</label>
                    @if(isset($request_data))
                        {!! Form::textarea('p_'.$value->column_name,$request_data->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @else
                        {!! Form::textarea('p_'.$value->column_name,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @endif
                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break

            @case('number')
            <div class="form-group row">
                @if($value->between_request == 'no')
                    <div class="col-md-12">
                        <label>{{$value->{'name_'.App::getLocale()} }}</label>
                        @if(isset($request_data))
                            {!! Form::text('p_'.$value->column_name,$request_data->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                        @else
                            {!! Form::text('p_'.$value->column_name,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                        @endif
                        <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                    </div>
                @else
                    <div class="col-md-6">
                        <label>{{$value->{'name_'.App::getLocale()} }} {{__('From')}}</label>
                        @if(isset($request_data))
                            {!! Form::text('p_'.$value->column_name.'_from',$request_data->{$value->column_name.'_from'},['class'=>'form-control','id'=>'p_'.$value->column_name.'_from-form-input','autocomplete'=>'off']) !!}
                        @else
                            {!! Form::text('p_'.$value->column_name.'_from',!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'_from-form-input','autocomplete'=>'off']) !!}
                        @endif
                        <div class="invalid-feedback" id="p_{{$value->column_name}}_from-form-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label>{{$value->{'name_'.App::getLocale()} }} {{__('To')}}</label>
                        @if(isset($request_data))
                            {!! Form::text('p_'.$value->column_name.'_to',$request_data->{$value->column_name.'_to'},['class'=>'form-control','id'=>'p_'.$value->column_name.'_to-form-input','autocomplete'=>'off']) !!}
                        @else
                            {!! Form::text('p_'.$value->column_name.'_to',!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'_to-form-input','autocomplete'=>'off']) !!}
                        @endif
                        <div class="invalid-feedback" id="p_{{$value->column_name}}_to-form-error"></div>
                    </div>
                @endif
            </div>
            @break

            @case('select')
            @case('multi_select')
            <div class="form-group row">
                <div class="col-md-12">
                    @php
                        $selectData = array_column($value->options,'name_'.App::getLocale(),'value');
                    @endphp
                    <label>{{$value->{'name_'.App::getLocale()} }}</label>

                    @if($value->multi_request == 'yes')
                        @if(isset($request_data))
                            {!! Form::select('p_'.$value->column_name.'[]',$selectData,$request_data->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off','multiple'=> 'multiple']) !!}
                        @else
                            {!! Form::select('p_'.$value->column_name.'[]',$selectData,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off','multiple'=> 'multiple']) !!}
                        @endif
                    @else
                        @if(isset($request_data))
                            {!! Form::select('p_'.$value->column_name,$selectData,$request_data->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                        @else
                            {!! Form::select('p_'.$value->column_name,$selectData,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                        @endif
                    @endif


                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break

            @case('radio')
            <div class="form-group row">
                <div class="col-md-12">
                    @php
                        $selectData = array_column($value->options,'name_'.App::getLocale(),'value');
                    @endphp
                    <label>{{$value->{'name_'.App::getLocale()} }}</label>

                    <div class="k-radio-inline">
                        @foreach($selectData as $radioKey => $radioValue)
                        <label class="k-radio">
                            @if(isset($request_data))
                                @php
                                $checkMark = explode(',',$request_data->{$value->column_name});
                                @endphp
                                <input @if(in_array($radioKey,$checkMark)) checked @endif type="radio" name="p_{{$value->column_name}}" value="{{$radioKey}}"> {{$radioValue}}
                            @else
                                <input @if($value->default_value == $radioKey) checked @endif type="radio" name="p_{{$value->column_name}}" value="{{$radioKey}}"> {{$radioValue}}
                            @endif
                            <span></span>
                        </label>
                        @endforeach
                    </div>

                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break


            @case('checkbox')
            <div class="form-group row">
                <div class="col-md-12">
                    @php
                        $selectData = array_column($value->options,'name_'.App::getLocale(),'value');
                    @endphp
                    <label>{{$value->{'name_'.App::getLocale()} }}</label>

                    <div class="k-checkbox-inline">
                        @foreach($selectData as $radioKey => $radioValue)
                            <label class="k-checkbox">

                                @if(isset($request_data))
                                    @php
                                        $checkMark = explode(',',$request_data->{$value->column_name});
                                    @endphp
                                    <input @if(in_array($radioKey,$checkMark)) checked @endif type="checkbox" name="p_{{$value->column_name}}[]" value="{{$radioKey}}"> {{$radioValue}}
                                @else
                                    <input @if($value->default_value == $radioKey) checked @endif type="checkbox" name="p_{{$value->column_name}}[]" value="{{$radioKey}}"> {{$radioValue}}
                                @endif

                                <span></span>
                            </label>
                        @endforeach
                    </div>

                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break


        @endswitch

    @endforeach

</div>