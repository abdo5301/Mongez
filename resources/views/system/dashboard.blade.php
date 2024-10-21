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

        <div class="row">
            <div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">

                <!--begin::Portlet-->
                <div class="k-portlet k-portlet--fit k-portlet--height-fluid">
                    <div class="k-portlet__body k-portlet__body--fluid">
                        <div class="k-widget-3 k-widget-3--brand">
                            <div class="k-widget-3__content">
                                <div class="k-widget-3__content-info">
                                    <div class="k-widget-3__content-section">
                                        <div class="k-widget-3__content-title">
                                            <i class="k-menu__link-icon flaticon-users"></i>
                                            {{__('Clients')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="k-widget-3__content-info">

                                    <div class="k-widget-3__content-section">
                                        <span class="k-widget-3__content-number">{{$clients}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Portlet-->
            </div>
            <div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">

                <!--begin::Portlet-->
                <div class="k-portlet k-portlet--fit k-portlet--height-fluid">
                    <div class="k-portlet__body k-portlet__body--fluid">
                        <div class="k-widget-3 k-widget-3--danger">
                            <div class="k-widget-3__content">
                                <div class="k-widget-3__content-info">
                                    <div class="k-widget-3__content-section">
                                        <div class="k-widget-3__content-title">
                                            <i class="k-menu__link-icon flaticon-users"></i>
                                            {{__('Investor')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="k-widget-3__content-info">

                                    <div class="k-widget-3__content-section">
                                        <span class="k-widget-3__content-number">{{$investor}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Portlet-->
            </div>
            <div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">

                <!--begin::Portlet-->
                <div class="k-portlet k-portlet--fit k-portlet--height-fluid">
                    <div class="k-portlet__body k-portlet__body--fluid">
                        <div class="k-widget-3 k-widget-3--success">
                            <div class="k-widget-3__content">
                                <div class="k-widget-3__content-info">
                                    <div class="k-widget-3__content-section">
                                        <div class="k-widget-3__content-title">
                                            <i class="k-menu__link-icon flaticon-search-1"></i>
                                            {{__('Requests')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="k-widget-3__content-info">

                                    <div class="k-widget-3__content-section">
                                        <span class="k-widget-3__content-number">{{$requests}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Portlet-->
            </div>

            <div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">

                <!--begin::Portlet-->
                <div class="k-portlet k-portlet--fit k-portlet--height-fluid">
                    <div class="k-portlet__body k-portlet__body--fluid">
                        <div class="k-widget-3 k-widget-3--dark">
                            <div class="k-widget-3__content">
                                <div class="k-widget-3__content-success">
                                    <div class="k-widget-3__content-section">
                                        <div class="k-widget-3__content-title">
                                            <i class="k-menu__link-icon flaticon-buildings"></i>
                                            {{__('Properties')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="k-widget-3__content-info">

                                    <div class="k-widget-3__content-section">
                                        <span class="k-widget-3__content-number">{{$properties}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end::Portlet-->
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12 col-xl-12 d-none d-sm-block">
                <div id="chart_div" style="height: 500px;"></div>
            </div>
        </div>


        <div style="padding-top: 20px;" class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
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
                            {{__("System Information")}}
                        </h3>
                    </div>
                </div>
                <div class="k-portlet__body">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td>{{__('System')}}</td>
                            <td>{{__('El-Mongez')}}</td>
                        </tr>
                        <tr>
                            <td>{{__('Website')}}</td>
                            <td>
                                <a href="http://mongez.org">Mongez.org</a>
                            </td>
                        </tr>
                        <tr>
                            <td>{{__('E-mail')}}</td>
                            <td>
                                <a href="mailto:info@mongez.org">info@mongez.org</a>
                            </td>
                        </tr>
                        <tr>
                            <td>{{__('Mobile')}}</td>
                            <td>
                                <a href="tel:01061119566">+201061119566</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawVisualization);

        function drawVisualization() {
            // Some raw data (not necessarily accurate)
            var data = google.visualization.arrayToDataTable([
                ['{{__('Month')}}', '{{__('Properties')}}', '{{__('Requests')}}', '{{__('Calls')}}'],
                @foreach(range(1,date('m')) as $key => $value)
                    @php
                        $month = (strlen($value) == 1) ? '0'.$value : $value;
                        $year  = date('Y');
                    @endphp
                    ['{{$year}}/{{$month}}', {{numProperties($year,$month)}}, {{numRequests($year,$month)}},  {{numCalls($year,$month)}}],
                @endforeach
             ]);

            var options = {
                title : '{{__('System Chart')}}',
                vAxis: {title: '{{__('Numbers')}}'},
                hAxis: {title: '{{__('Month')}}'},
                seriesType: 'bars',
                series: {5: {type: 'line'}}
            };

            var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
@endsection