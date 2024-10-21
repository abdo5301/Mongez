<div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

    @foreach($result as $key => $value)
        @switch($value->type)

            @case('text')
            <div class="form-group row">
                <div class="col-md-12">
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>
                    @if(isset($property))
                        {!! Form::text('p_'.$value->column_name,$property->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
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
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>
                    @if(isset($property))
                        {!! Form::textarea('p_'.$value->column_name,$property->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @else
                        {!! Form::textarea('p_'.$value->column_name,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @endif
                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break

            @case('number')
            <div class="form-group row">
                <div class="col-md-12">
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>
                    @if(isset($property))
                        {!! Form::text('p_'.$value->column_name,$property->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @else
                        {!! Form::text('p_'.$value->column_name,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @endif
                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break

            @case('select')
            <div class="form-group row">
                <div class="col-md-12">
                    @php
                        $selectData = array_column($value->options,'name_'.App::getLocale(),'value');
                    @endphp
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>

                    @if(isset($property))
                        {!! Form::select('p_'.$value->column_name,$selectData,$property->{$value->column_name},['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @else
                        {!! Form::select('p_'.$value->column_name,$selectData,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control','id'=>'p_'.$value->column_name.'-form-input','autocomplete'=>'off']) !!}
                    @endif

                    <div class="invalid-feedback" id="p_{{$value->column_name}}-form-error"></div>
                </div>
            </div>
            @break

            @case('multi_select')
            <div class="form-group row">
                <div class="col-md-12">
                    @php
                        $selectData = array_column($value->options,'name_'.App::getLocale(),'value');
                    @endphp
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>

                    @if(isset($property))
                        {!! Form::select('p_'.$value->column_name.'[]',$selectData,explode(',',$property->{$value->column_name}),['class'=>'form-control multiple-select2','id'=>'p_'.$value->column_name.'-form-input','multiple','autocomplete'=>'off']) !!}
                    @else
                        {!! Form::select('p_'.$value->column_name.'[]',$selectData,!empty($value->default_value) ? $value->default_value: null,['class'=>'form-control multiple-select2','id'=>'p_'.$value->column_name.'-form-input','multiple','autocomplete'=>'off']) !!}
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
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>

                    <div class="k-radio-inline">
                        @foreach($selectData as $radioKey => $radioValue)
                        <label class="k-radio">
                            @if(isset($property))
                                @php
                                $checkMark = explode(',',$property->{$value->column_name});
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
                    <label>{{$value->{'name_'.App::getLocale()} }} @if($value->required == 'yes') * @endif</label>

                    <div class="k-checkbox-inline">
                        @foreach($selectData as $radioKey => $radioValue)
                            <label class="k-checkbox">

                                @if(isset($property))
                                    @php
                                        $checkMark = explode(',',$property->{$value->column_name});
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