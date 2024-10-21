@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />
    <style>
        .pictures {
            list-style: none;
            margin: 0;
           /* max-width: 30rem;*/
            padding: 0;
        }

        .pictures > li {
            border: 1px solid transparent;
            float: left;
            height: 250px;
            margin: 0 -1px -1px 0;
            overflow: hidden;
            width: calc(100% / 3);
        }

        .pictures > li > img {
            cursor: zoom-in;
            width: 100%;
        }


        td:first-child {
            font-weight: bold
        }
    </style>

@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="k-content	k-grid__item k-grid__item--fluid k-grid k-grid--hor" id="k_content">

        <!-- begin:: Content Head -->
        <div class="k-content__head	k-grid__item">
            <div class="k-content__head-main">
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
                    <a href="whatsapp://send?text={{urlencode(implode("\n",propertyToText($result)))}}" data-action="share/whatsapp/share" class="btn btn-sm btn-success btn-brand" data-toggle="k-tooltip" title="{{__('Share on WhatsApp')}}" data-placement="left">
                        <i style="padding-left:0px !important;" class="flaticon-whatsapp k-padding-l-5 k-padding-r-0"></i>
                    </a>
                </div>

                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="javascript:void(0);" onclick="updateCallDate();" class="btn btn-sm btn-danger btn-brand" data-toggle="k-tooltip" title="{{__('Call Update :date',['date'=> $result->call_update])}}" data-placement="left">
                        <i style="padding-left:0px !important;" class="flaticon-refresh k-padding-l-5 k-padding-r-0"></i>
                    </a>
                </div>

                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="{{route('system.property.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Property')}}" data-placement="left">
                        <i class="la la-edit"></i>
                    </a>
                </div>

            </div>
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div class="k-portlet k-profile">
                <div class="k-profile__content">
                    <div class="row">
                        <div class="col-md-12 col-lg-5 col-xl-4">
                            <div class="k-profile__main">

                                <div class="k-profile__main-info">
                                    <div class="k-profile__main-info-name">
                                        @if($result->client->investor_type == 'company')
                                            <a target="_blank" href="{{route('system.client.show',$result->client->id)}}">{{$result->client->company_name}}</a>
                                            <small>{{$result->client->name}}</small>
                                        @else
                                            <a target="_blank" href="{{route('system.client.show',$result->client->id)}}">{{$result->client->name}}</a>
                                        @endif

                                    </div>
                                    <div class="k-profile__main-info-position">
                                        {{__(ucfirst($result->client->type))}}
                                        ( {{__(ucfirst($result->client->investor_type))}} )
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-4">
                            @if(!staffCan('property-manage-all') && ($result->created_by_staff_id != Auth::id() || $result->sales_id != Auth::id()))
                                <!-- CODE -->
                            @else
                            <div class="k-profile__contact">
                                @if($result->client->mobile1)
                                <a style="margin-bottom: 0.1rem;" href="tel:{{$result->client->mobile1}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->client->mobile1}}</span>
                                </a>
                                @endif
                                @if($result->client->mobile2)
                                <a style="margin-bottom: 0.1rem;" href="tel:{{$result->client->mobile2}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->client->mobile2}}</span>
                                </a>
                                @endif
                                @if($result->client->phone)
                                <a style="margin-bottom: 0.1rem;" href="tel:{{$result->client->phone}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->client->phone}}</span>
                                </a>
                                @endif
                                @if($result->client->email)
                                <a style="margin-bottom: 0.1rem;" href="mailto:{{$result->client->email}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->client->email}}</span>
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="col-md-12 col-lg-3 col-xl-4">
                            @if(!staffCan('property-manage-all') && ($result->created_by_staff_id != Auth::id() || $result->sales_id != Auth::id()))
                            <!-- CODE -->
                            @else
                            <div class="k-profile__stats">
                                @if($result->client->address)
                                    <div class="k-profile__stats-item">
                                        <div class="k-profile__stats-item-label">{{__('Address')}}</div>
                                        <div class="k-profile__stats-item-chart">
                                            {{$result->client->address}}
                                        </div>
                                    </div>
                                @endif
                                @if($result->client->description)
                                    <div class="k-profile__stats-item">
                                        <div class="k-profile__stats-item-label">{{__('Description')}}</div>
                                        <div class="k-profile__stats-item-chart">
                                            {{$result->client->description}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="k-profile__nav">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_1" role="tab">{{__('Property')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_2" role="tab">
                                {{__('Requests')}}
                                @php
                                    $requestsCount = $result->requests()->count();
                                @endphp
                                @if($requestsCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$requestsCount}}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_3" role="tab">
                                {{__('Calls')}}
                                @php
                                $callsCount = $result->calls()->count();
                                @endphp
                                @if($callsCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$callsCount}}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_4" role="tab">
                                {{__('Reminders')}}

                                @php
                                    $remindersCount = $result->reminders()->count();
                                @endphp
                                @if($remindersCount)
                                    <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{$remindersCount}}</span>
                                @endif

                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_5" role="tab">
                                {{__('Log')}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table table-striped">
                                      {{--  <thead>
                                        <tr>
                                            <th>{{__('Key')}}</th>
                                            <th>{{__('Value')}}</th>
                                        </tr>
                                        </thead>--}}
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Type')}}</td>
                                            <td>{{$result->property_type->{'name_'.\App::getLocale()} }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Purpose')}}</td>
                                            <td>{{$result->purpose->{'name_'.\App::getLocale()} }}</td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Area')}}</td>
                                            <td>{{implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true))}}</td>
                                        </tr>


                                        @if($result->building_number)
                                        <tr>
                                            <td>{{__('Building Number')}}</td>
                                            <td>{{$result->building_number }}</td>
                                        </tr>
                                        @endif

                                        @if($result->flat_number)
                                        <tr>
                                            <td>{{__('Flat Number')}}</td>
                                            <td>{{$result->flat_number }}</td>
                                        </tr>
                                        @endif

                                        @if(!staffCan('property-manage-all') && ($result->created_by_staff_id != Auth::id() || $result->sales_id != Auth::id()))
                                            <!-- CODE -->
                                        @else
                                        <tr>
                                            <td>{{__('Address')}}</td>
                                            <td>{{$result->address}}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Description')}}</td>
                                            <td>{{$result->description}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Remarks')}}</td>
                                            <td>{{$result->remarks}}</td>
                                        </tr>

                                        @if($result->property_model_id)
                                            <tr>
                                                <td>{{__('Model')}}</td>
                                                <td>{{$result->property_model->{'name_'.\App::getLocale()} }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Space')}}</td>
                                            <td>{{number_format($result->space)}} {{__(ucfirst($result->space_type))}}</td>
                                        </tr>

                                        @if($result->payment_type == 'cash')
                                        <tr>
                                            <td>{{__('Payment Type')}}</td>
                                            <td>{{__('Cash')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Price')}}</td>
                                            <td>{{number_format($result->price)}} {{$result->currency}}</td>
                                        </tr>
                                        @elseif($result->payment_type == 'installment')
                                            <tr>
                                                <td>{{__('Payment Type')}}</td>
                                                <td>{{__('Installment')}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Years Of Installment')}}</td>
                                                <td>{{$result->years_of_installment}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Deposit')}}</td>
                                                <td>{{number_format($result->deposit)}} {{$result->currency}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Price')}}</td>
                                                <td>{{number_format($result->price)}} {{$result->currency}}</td>
                                            </tr>
                                        @elseif($result->payment_type == 'cash_installment')
                                                <tr>
                                                    <td>{{__('Payment Type')}}</td>
                                                    <td>{{__('Cash & Installment')}}</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Years Of Installment')}}</td>
                                                    <td>{{$result->years_of_installment}}</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Deposit')}}</td>
                                                    <td>{{number_format($result->deposit)}} {{$result->currency}}</td>
                                                </tr>

                                                <tr>
                                                    <td>{{__('Price')}}</td>
                                                    <td>{{number_format($result->price)}} {{$result->currency}}</td>
                                                </tr>
                                        @endif
                                        <tr>
                                            <td>{{__('Negotiable')}}</td>
                                            <td>{{__(ucfirst($result->negotiable))}}</td>
                                        </tr>



                                        @if($result->hold_until)
                                        <tr>
                                            <td>{{__('Hold Until')}}</td>
                                            <td>{{$result->hold_until}}</td>
                                        </tr>
                                        @endif

                                        @if($result->latitude && $result->longitude)
                                        <tr>
                                            <td>{{__('Location')}}</td>
                                            <td>{{$result->latitude}}, {{$result->longitude}}</td>
                                        </tr>
                                        @endif
                                        @if(!$result->video_url)
                                        <tr>
                                            <td>{{__('Video')}}</td>
                                            <td>
                                                <a target="_blank" href="{{$result->video_url}}">{{$result->video_url}}</a>
                                            </td>
                                        </tr>
                                        @endif


                                        <tr>
                                            <td>{{__('Created By')}}</td>
                                            <td>
                                                <a target="_blank" href="{{route('system.staff.show',$result->created_by_staff->id)}}">{{$result->created_by_staff->fullname}}</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Sales')}}</td>
                                            <td>
                                                <a target="_blank" href="{{route('system.staff.show',$result->sales->id)}}">{{$result->sales->fullname}}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            <td>{{$result->property_status->{'name_'.\App::getLocale()} }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Data Source')}}</td>
                                            <td>{{$result->data_source->{'name_'.\App::getLocale()} }}</td>
                                        </tr>

                                        @if($result->hold_until)
                                        <tr class="table-danger">
                                            <td>{{__('Hold Until')}}</td>
                                            <td>
                                                {{$result->hold_until}}
                                            </td>
                                        </tr>
                                        @endif


                                        <tr>
                                            <td>{{__('Call Update')}}</td>
                                            <td>
                                                {{$result->updated_at->format('Y-m-d h:i A')}} ({{$result->updated_at->diffForHumans()}})
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Created At')}}</td>
                                            <td>
                                                {{$result->created_at->format('Y-m-d h:i A')}} ({{$result->created_at->diffForHumans()}})
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Last Update')}}</td>
                                            <td>
                                                {{$result->call_update->format('Y-m-d h:i A')}} ({{$result->call_update->diffForHumans()}})
                                            </td>
                                        </tr>

                                        @if($result->importer_data_id)
                                            <tr>
                                                <td>{{__('Importer')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.importer.show',$result->importer_data_id)}}">
                                                    {{__('#ID: :id',['id'=>$result->importer_data_id])}} {{$result->importer->connector}}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif


                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">
                                            {{__('Parameters')}}
                                        </h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <div class="tab-content">
                                        <table class="table table-striped">
                                            {{--<thead>
                                            <tr>
                                                <th>{{__('Key')}}</th>
                                                <th>{{__('Value')}}</th>
                                            </tr>
                                            </thead>--}}
                                            <tbody>
                                            {{-- @TODO NEED UPDATE  (SELECT / MULTI SELECT)--}}
                                            @foreach($result->main_paramaters as $key => $value)
                                                @if($result->paramaters->{$value->column_name})
                                                <tr>
                                                    <td>{{$value->{'name_'.\App::getLocale()} }}</td>
                                                    <td>


                                                        @switch($value->type)
                                                            @case('text')
                                                            @case('textarea')
                                                            {{ $result->paramaters->{$value->column_name} }}
                                                            @break

                                                            @case('number')
                                                            {{ number_format($result->paramaters->{$value->column_name}) }}
                                                            @break

                                                            @case('select')
                                                            @case('radio')
                                                            @if(is_array($value->options))
                                                                @foreach($value->options as $oValue)
                                                                    @if($oValue['value'] == $result->paramaters->{$value->column_name})
                                                                        {{$oValue['name_'.\App::getLocale() ]}}
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                {{ $result->paramaters->{$value->column_name} }}
                                                            @endif

                                                            @break

                                                            @default
                                                            {{ $result->paramaters->{$value->column_name} }}
                                                        @endswitch

                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                            {{-- @TODO NEED UPDATE  (SELECT / MULTI SELECT)--}}


                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        @if($result->images->isNotEmpty())
                        <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Images')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <ul id="image-view" class="pictures">
                                        @foreach($result->images as $key => $value)
                                            <li><img src="{{asset($value->path)}}" alt="{{$value->image_name}}"></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        @endif
                    </div>

                    <!--end::Row-->
                </div>
                <div class="tab-pane fade" id="k_tabs_1_2" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Requests')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Purpose')}}</th>
                                    <th>{{__('Client')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Sales')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Purpose')}}</th>
                                    <th>{{__('Client')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Sales')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="k_tabs_1_3" role="tabpanel">

                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Calls')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-call">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Client')}}</th>
                                    <th>{{__('Purpose')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Created By')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Client')}}</th>
                                    <th>{{__('Purpose')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Created By')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>



                    <!--end::Row-->
                </div>
                <div class="tab-pane fade" id="k_tabs_1_4" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Reminders')}}</h3>

                                <a href="{{route('system.calendar.index',['sign_type'=>'property','sign_id'=>$result->id])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand">
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Add')}}</span>
                                    <i class="flaticon2-plus-1 k-padding-l-5 k-padding-r-0"></i>
                                </a>
                            </div>

                        </div>
                        <div class="k-portlet__body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{__('ID')}}</th>
                                        <th>{{__('By')}}</th>
                                        <th>{{__('Date & Time')}}</th>
                                        <th>{{__('Comment')}}</th>
                                        <th>{{__('Created At')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result->reminders as $key => $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td>
                                            <a href="{{route('system.staff.show',$value->staff->id)}}" target="_blank">
                                                {{$value->staff->fullname}}
                                            </a>
                                        </td>
                                        <td>{{$value->date_time->format('Y-m-d h:i A')}}</td>
                                        <td>{{$value->comment}}</td>
                                        <td>{{$value->created_at->format('Y-m-d h:i A')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="k_tabs_1_5" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Log')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-log">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Staff')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Staff')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- end:: Content -->
@endsection
@section('footer')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.js"></script>

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

                $datatableLog = $('#datatable-log').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "log";
                        }
                    }
                });

                $datatableCall = $('#datatable-call').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "call";
                        }
                    }
                });


                window.addEventListener('DOMContentLoaded', function () {
                    var galley = document.getElementById('image-view');
                    var viewer = new Viewer(galley, {
                        url: 'data-original',
                        title: function (image) {
                            return image.alt + ' (' + (this.index + 1) + '/' + this.length + ')';
                        },
                    });
                });


                function updateCallDate(){
                    addLoading();
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'updateCallDate',
                            'id': {{$result->id}}
                        },
                        function(){
                            location.reload();
                        }
                    );
                }


            </script>
@endsection