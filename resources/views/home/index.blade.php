@extends('layouts.app')
@section('title', __('home.home'))
<link rel="stylesheet" type="text/css" href="{{ asset('usama_css/style.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('usama_css/jquery.mCustomScrollbar.css') }}">
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header-custom">
   <div class="row">
      <div class="col-md-6 mr-left">
         <h1>{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
         </h1>
      </div>
      <div class="col-md-6 mr-right">
         <div class="form-group pull-right">
            <div class="input-group">
               <button type="button" class="btn btn-primary" id="dashboard_date_filter">
               <span>
               <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
               </span>
               <i class="fa fa-caret-down"></i>
               </button>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- Main content -->
<section class="content content-custom no-print">
   <br>
   @if(auth()->user()->can('dashboard.data'))
   @if($is_admin)
   <div class="row">
      <div class="col-md-4 col-xs-12">
         @if(count($all_locations) > 1)
         {!! Form::select('dashboard_location', $all_locations, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.select_location'), 'id' => 'dashboard_location']); !!}
         @endif
      </div>
      <div class="col-md-8 col-xs-12">
      </div>
   </div>
   <br>
   <div class="row row-custom">
      <div class="col-md-3 col-sm-6 col-xs-12 col-custom col-data">
         <div class="info-box info-box-new-style">
            <div class="info-box-content">
               <span class="info-box-number total_purchase"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
               <span class="info-box-text">{{ __('home.total_purchase') }}</span>
            </div>
            <span class="info-box-icon bg-aqua"><i class="feather icon-bar-chart f-28"></i></span>
            <!-- /.info-box-content -->
         </div>
         <div class="card-footer bg-c-yellow">
            <div class="row align-items-center">
               <div class="col-9">
                  <p class="text-white m-b-0">% change</p>
               </div>
               <div class="col-3 text-right">
                  <i class="feather icon-trending-up text-white f-16"></i>
               </div>
            </div>
         </div>
         <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-md-3 col-sm-6 col-xs-12 col-custom col-data">
         <div class="info-box info-box-new-style">
            <div class="info-box-content">
               <span class="info-box-number total_sell"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
               <span class="info-box-text">{{ __('home.total_sell') }}</span>
            </div>
            <span class="info-box-icon bg-aqua"><i class="feather icon-file-text f-28"></i></span>
            <!-- /.info-box-content -->
         </div>
         <div class="card-footer bg-c-green">
            <div class="row align-items-center">
               <div class="col-9">
                  <p class="text-white m-b-0">% change</p>
               </div>
               <div class="col-3 text-right">
                  <i class="feather icon-trending-up text-white f-16"></i>
               </div>
            </div>
         </div>
         <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-md-3 col-sm-6 col-xs-12 col-custom col-data">
         <div class="info-box info-box-new-style">
            <div class="info-box-content">
               <span class="info-box-number purchase_due"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
               <span class="info-box-text">{{ __('home.purchase_due') }}</span>
            </div>
            <span class="info-box-icon bg-yellow">
            <i class="feather icon-calendar f-28"></i>
            </span>
            <!-- /.info-box-content -->
         </div>
         <div class="card-footer bg-c-pink">
            <div class="row align-items-center">
               <div class="col-9">
                  <p class="text-white m-b-0">% change</p>
               </div>
               <div class="col-3 text-right">
                  <i class="feather icon-trending-up text-white f-16"></i>
               </div>
            </div>
         </div>
         <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <!-- fix for small devices only -->
      <!-- <div class="clearfix visible-sm-block"></div> -->
      <div class="col-md-3 col-sm-6 col-xs-12 col-custom col-data">
         <div class="info-box info-box-new-style">
            <div class="info-box-content">
               <span class="info-box-number invoice_due"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
               <span class="info-box-text">{{ __('home.invoice_due') }}</span>
            </div>
            <span class="info-box-icon bg-yellow">
            <i class="feather icon-download f-28"></i>
            </span>
            <!-- /.info-box-content -->
         </div>
         <div class="card-footer bg-c-blue">
            <div class="row align-items-center">
               <div class="col-9">
                  <p class="text-white m-b-0">% change</p>
               </div>
               <div class="col-3 text-right">
                  <i class="feather icon-trending-up text-white f-16"></i>
               </div>
            </div>
         </div>
         <!-- /.info-box -->
      </div>
      <!-- /.col -->
   </div>
   <div class="row row-custom mn-dis">
      <!-- expense -->
      <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
         <div class="info-box info-box-new-style">
            <span class="info-box-icon bg-red">
            <i class="fas fa-minus-circle"></i>
            </span>
            <div class="info-box-content">
               <span class="info-box-text">
               {{ __('lang_v1.expense') }}
               </span>
               <span class="info-box-number total_expense"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
            </div>
            <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
      </div>
   </div>
   @if(!empty($widgets['after_sale_purchase_totals']))
   @foreach($widgets['after_sale_purchase_totals'] as $widget)
   {!! $widget !!}
   @endforeach
   @endif
   @endif 
   <!-- end is_admin check -->
   @if(auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
   @if(!empty($all_locations))
   <!-- sales chart start -->
   <div class="row">
      <div class="col-md-8 col-sm-12">
         <div class="card update-chart">
            <div class="card-header">
               <h5>Visitors</h5>
               <span class="text-muted">For more details about usage, please
               refer <a href="https://www.amcharts.com/online-store/" target="_blank">amCharts</a> licences.</span>
               <div class="card-header-right">
                  <ul class="list-unstyled card-option">
                     <li><i class="feather icon-maximize full-card" onclick="javascript:toggleFullScreen()"></i></li>
                     <li><i class="feather icon-minus minimize-card"></i></li>
                     <li><i class="feather icon-trash-2 close-card"></i></li>
                  </ul>
               </div>
            </div>
            <div class="card-block">
               <div id="visitor" style="height: 300px; overflow: hidden; text-align: left;">
                  <div class="amcharts-main-div" style="position: relative; width: 100%; height: 100%;">
                     <div class="amChartsLegend amcharts-legend-div" style="overflow: hidden; position: relative; text-align: left; width: 794px; height: 76px; cursor: default;">
                        <svg version="1.1" style="position: absolute; width: 794px; height: 76px;">
                           <desc>JavaScript chart by amCharts 3.21.5</desc>
                           <g transform="translate(81,10)">
                              <path cs="100,100" d="M0.5,0.5 L644.5,0.5 L644.5,65.5 L0.5,65.5 Z" fill="#FFFFFF" stroke="#000000" fill-opacity="0" stroke-width="1" stroke-opacity="0"></path>
                              <g transform="translate(0,11)">
                                 <g cursor="pointer" aria-label="old Visitor" transform="translate(0,0)">
                                    <path cs="100,100" d="M-15.5,8.5 L16.5,8.5 L16.5,-7.5 L-15.5,-7.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9" transform="translate(16,8)"></path>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(37,7)">
                                       <tspan y="6" x="0">old Visitor</tspan>
                                    </text>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(193,7)"> </text>
                                    <rect x="32" y="0" width="161.03076171875" height="18" rx="0" ry="0" stroke-width="0" stroke="none" fill="#fff" fill-opacity="0.005"></rect>
                                 </g>
                                 <g cursor="pointer" aria-label="New visitor" transform="translate(208,0)">
                                    <path cs="100,100" d="M-15.5,8.5 L16.5,8.5 L16.5,-7.5 L-15.5,-7.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9" transform="translate(16,8)"></path>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(37,7)">
                                       <tspan y="6" x="0">New visitor</tspan>
                                    </text>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(193,7)"> </text>
                                    <rect x="32" y="0" width="161.03076171875" height="18" rx="0" ry="0" stroke-width="0" stroke="none" fill="#fff" fill-opacity="0.005"></rect>
                                 </g>
                                 <g cursor="pointer" aria-label="Last Month Visitor" transform="translate(415,0)">
                                    <g>
                                       <path cs="100,100" d="M0.5,8.5 L32.5,8.5" fill="none" stroke-width="2" stroke-opacity="0.9" stroke="#0df3a3"></path>
                                       <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(17,8)"></circle>
                                    </g>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(37,7)">
                                       <tspan y="6" x="0">Last Month Visitor</tspan>
                                    </text>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(193,7)"> </text>
                                    <rect x="32" y="0" width="161.03076171875" height="18" rx="0" ry="0" stroke-width="0" stroke="none" fill="#fff" fill-opacity="0.005"></rect>
                                 </g>
                                 <g cursor="pointer" aria-label="Average Visitor" transform="translate(0,28)">
                                    <g>
                                       <path cs="100,100" d="M0.5,8.5 L32.5,8.5" fill="none" stroke-width="2" stroke-dasharray="5" stroke-opacity="0.9" stroke="#fe5d70"></path>
                                       <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(17,8)"></circle>
                                    </g>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(37,7)">
                                       <tspan y="6" x="0">Average Visitor</tspan>
                                    </text>
                                    <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(193,7)"> </text>
                                    <rect x="32" y="0" width="161.03076171875" height="18" rx="0" ry="0" stroke-width="0" stroke="none" fill="#fff" fill-opacity="0.005"></rect>
                                 </g>
                              </g>
                           </g>
                        </svg>
                     </div>
                     <div class="amcharts-chart-div" style="overflow: hidden; position: relative; text-align: left; width: 794px; height: 224px; padding: 0px; cursor: default; touch-action: none;">
                        <svg version="1.1" style="position: absolute; width: 794px; height: 224px; top: -0.109375px; left: -0.203125px;">
                           <desc>JavaScript chart by amCharts 3.21.5</desc>
                           <g>
                              <path cs="100,100" d="M0.5,0.5 L793.5,0.5 L793.5,223.5 L0.5,223.5 Z" fill="#FFFFFF" stroke="#000000" fill-opacity="0" stroke-width="1" stroke-opacity="0"></path>
                              <path cs="100,100" d="M0.5,0.5 L644.5,0.5 L644.5,173.5 L0.5,173.5 L0.5,0.5 Z" fill="#FFFFFF" stroke="#000000" fill-opacity="0" stroke-width="1" stroke-opacity="0" transform="translate(81,20)"></path>
                           </g>
                           <g>
                              <g transform="translate(81,20)">
                                 <g>
                                    <path cs="100,100" d="M0.5,0.5 L0.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M0.5,173.5 L0.5,173.5 L0.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M43.5,173.5 L43.5,173.5 L43.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M86.5,0.5 L86.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M86.5,173.5 L86.5,173.5 L86.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M129.5,173.5 L129.5,173.5 L129.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M172.5,0.5 L172.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M172.5,173.5 L172.5,173.5 L172.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M215.5,173.5 L215.5,173.5 L215.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M258.5,0.5 L258.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M258.5,173.5 L258.5,173.5 L258.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M301.5,173.5 L301.5,173.5 L301.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M343.5,0.5 L343.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M343.5,173.5 L343.5,173.5 L343.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M386.5,173.5 L386.5,173.5 L386.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M429.5,0.5 L429.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M429.5,173.5 L429.5,173.5 L429.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M472.5,173.5 L472.5,173.5 L472.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M515.5,0.5 L515.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M515.5,173.5 L515.5,173.5 L515.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M558.5,173.5 L558.5,173.5 L558.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M601.5,0.5 L601.5,5.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(0,173)"></path>
                                    <path cs="100,100" d="M601.5,173.5 L601.5,173.5 L601.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M644.5,173.5 L644.5,173.5 L644.5,0.5" fill="none" stroke-width="1" stroke-dasharray="1" stroke-opacity="0.07" stroke="#000000"></path>
                                 </g>
                              </g>
                              <g transform="translate(81,20)" visibility="visible">
                                 <g>
                                    <path cs="100,100" d="M0.5,173.5 L6.5,173.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,173.5 L0.5,173.5 L644.5,173.5" fill="none" stroke-width="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,138.5 L6.5,138.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,138.5 L0.5,138.5 L644.5,138.5" fill="none" stroke-width="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,104.5 L6.5,104.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,104.5 L0.5,104.5 L644.5,104.5" fill="none" stroke-width="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,69.5 L6.5,69.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,69.5 L0.5,69.5 L644.5,69.5" fill="none" stroke-width="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,35.5 L6.5,35.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,35.5 L0.5,35.5 L644.5,35.5" fill="none" stroke-width="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,0.5 L6.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,0.5 L0.5,0.5 L644.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.1" stroke="#000000"></path>
                                 </g>
                              </g>
                              <g transform="translate(81,20)" visibility="visible">
                                 <g>
                                    <path cs="100,100" d="M0.5,173.5 L6.5,173.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(644,0)"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,138.5 L6.5,138.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(644,0)"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,104.5 L6.5,104.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(644,0)"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,69.5 L6.5,69.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(644,0)"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,35.5 L6.5,35.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(644,0)"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,0.5 L6.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(644,0)"></path>
                                 </g>
                              </g>
                           </g>
                           <g transform="translate(81,20)" clip-path="url(#AmChartsEl-3)">
                              <g visibility="hidden"></g>
                           </g>
                           <g></g>
                           <g></g>
                           <g></g>
                           <g>
                              <g transform="translate(81,20)">
                                 <g>
                                    <g transform="translate(10,173)" aria-label="old Visitor Jan 16, 2013 8.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-137.5 L21.5,-137.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(53,173)" aria-label="old Visitor Jan 17, 2013 6.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-103.5 L21.5,-103.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(96,173)" aria-label="old Visitor Jan 18, 2013 2.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-34.5 L21.5,-34.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(139,173)" aria-label="old Visitor Jan 19, 2013 9.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-155.5 L21.5,-155.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(182,173)" aria-label="old Visitor Jan 20, 2013 6.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-103.5 L21.5,-103.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(225,173)" aria-label="old Visitor Jan 21, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L21.5,-85.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(268,173)" aria-label="old Visitor Jan 22, 2013 7.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-120.5 L21.5,-120.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(311,173)" aria-label="old Visitor Jan 23, 2013 6.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-103.5 L21.5,-103.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(354,173)" aria-label="old Visitor Jan 24, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L21.5,-85.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(397,173)" aria-label="old Visitor Jan 25, 2013 8.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-137.5 L21.5,-137.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(440,173)" aria-label="old Visitor Jan 26, 2013 8.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-137.5 L21.5,-137.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(483,173)" aria-label="old Visitor Jan 27, 2013 4.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-68.5 L21.5,-68.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(526,173)" aria-label="old Visitor Jan 28, 2013 7.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-120.5 L21.5,-120.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(569,173)" aria-label="old Visitor Jan 29, 2013 8.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-137.5 L21.5,-137.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(612,173)" aria-label="old Visitor Jan 30, 2013 7.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-120.5 L21.5,-120.5 L21.5,0.5 L0.5,0.5 Z" fill="#feb798" stroke="#feb798" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                 </g>
                              </g>
                              <g transform="translate(81,20)">
                                 <g>
                                    <g transform="translate(15,173)" aria-label="New visitor Jan 16, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L13.5,-85.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(58,173)" aria-label="New visitor Jan 17, 2013 4.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-68.5 L13.5,-68.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(101,173)" aria-label="New visitor Jan 18, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L13.5,-85.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(144,173)" aria-label="New visitor Jan 19, 2013 8.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-137.5 L13.5,-137.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(187,173)" aria-label="New visitor Jan 20, 2013 9.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-155.5 L13.5,-155.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(230,173)" aria-label="New visitor Jan 21, 2013 3.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-51.5 L13.5,-51.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(273,173)" aria-label="New visitor Jan 22, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L13.5,-85.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(316,173)" aria-label="New visitor Jan 23, 2013 7.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-120.5 L13.5,-120.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(359,173)" aria-label="New visitor Jan 24, 2013 9.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-155.5 L13.5,-155.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(402,173)" aria-label="New visitor Jan 25, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L13.5,-85.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(445,173)" aria-label="New visitor Jan 26, 2013 4.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-68.5 L13.5,-68.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(488,173)" aria-label="New visitor Jan 27, 2013 3.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-51.5 L13.5,-51.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(531,173)" aria-label="New visitor Jan 28, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L13.5,-85.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(574,173)" aria-label="New visitor Jan 29, 2013 5.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-85.5 L13.5,-85.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                    <g transform="translate(617,173)" aria-label="New visitor Jan 30, 2013 4.00">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-68.5 L13.5,-68.5 L13.5,0.5 L0.5,0.5 Z" fill="#fe9365" stroke="#fe9365" fill-opacity="1" stroke-width="1" stroke-opacity="0.9"></path>
                                    </g>
                                 </g>
                              </g>
                              <g transform="translate(81,20)">
                                 <g></g>
                                 <g clip-path="url(#AmChartsEl-5)">
                                    <path cs="100,100" d="M21,166 C32,165,43,148,64,145 C86,143,86,121,107,118 C129,114,129,71,150,69 C172,68,172,89,193,90 C215,91,215,85,236,83 C258,81,258,49,279,48 C301,48,301,68,322,69 C344,70,344,67,365,69 C387,71,387,104,408,104 C430,103,430,57,451,55 C473,54,473,75,494,76 C516,78,516,83,537,83 C559,83,559,75,580,76 C602,77,612,96,623,97 M0,0 L0,0" fill="none" fill-opacity="0" stroke-width="2" stroke-opacity="0.9" stroke="#0df3a3"></path>
                                 </g>
                                 <clipPath id="AmChartsEl-5">
                                    <rect x="0" y="0" width="644" height="173" rx="0" ry="0" stroke-width="0"></rect>
                                 </clipPath>
                                 <g></g>
                              </g>
                              <g transform="translate(81,20)">
                                 <g></g>
                                 <g clip-path="url(#AmChartsEl-6)">
                                    <path cs="100,100" d="M21.5,138.9 L64.5,118.14 L107.5,48.94 L150.5,42.02 L193.5,42.02 L236.5,69.7 L279.5,21.26 L322.5,35.1 L365.5,28.18 L408.5,76.62 L451.5,21.26 L494.5,55.86 L537.5,48.94 L580.5,55.86 L623.5,69.7 M0,0 L0,0" fill="none" stroke-width="2" stroke-dasharray="5" stroke-opacity="0.9" stroke="#fe5d70" stroke-linejoin="round"></path>
                                 </g>
                                 <clipPath id="AmChartsEl-6">
                                    <rect x="0" y="0" width="644" height="173" rx="0" ry="0" stroke-width="0"></rect>
                                 </clipPath>
                                 <g></g>
                              </g>
                           </g>
                           <g></g>
                           <g>
                              <path cs="100,100" d="M0.5,173.5 L644.5,173.5 L644.5,173.5" fill="none" stroke-width="1" stroke-opacity="0.2" stroke="#000000" transform="translate(81,20)"></path>
                              <g>
                                 <path cs="100,100" d="M0.5,0.5 L644.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(81,193)"></path>
                              </g>
                              <g>
                                 <path cs="100,100" d="M0.5,0.5 L0.5,173.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(81,20)" visibility="visible"></path>
                              </g>
                              <g>
                                 <path cs="100,100" d="M0.5,0.5 L0.5,173.5 L0.5,173.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(725,20)" visibility="visible"></path>
                              </g>
                           </g>
                           <g>
                              <g transform="translate(81,20)" clip-path="url(#AmChartsEl-4)" style="pointer-events: none;">
                                 <path cs="100,100" d="M0.5,0.5 L0.5,0.5 L0.5,173.5" fill="none" stroke-width="1" stroke-opacity="0" stroke="#000000" visibility="hidden" transform="translate(150,0)"></path>
                                 <path cs="100,100" d="M0.5,0.5 L644.5,0.5 L644.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.2" stroke="#000000" visibility="hidden" transform="translate(0,19)"></path>
                              </g>
                              <clipPath id="AmChartsEl-4">
                                 <rect x="0" y="0" width="644" height="173" rx="0" ry="0" stroke-width="0"></rect>
                              </clipPath>
                           </g>
                           <g></g>
                           <g>
                              <g transform="translate(81,20)"></g>
                              <g transform="translate(81,20)"></g>
                              <g transform="translate(81,20)">
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(21,166)" aria-label="Last Month Visitor Jan 16, 2013 71.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(64,145) scale(1)" aria-label="Last Month Visitor Jan 17, 2013 74.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(107,118) scale(1)" aria-label="Last Month Visitor Jan 18, 2013 78.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(150,69) scale(1)" aria-label="Last Month Visitor Jan 19, 2013 85.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(193,90) scale(1)" aria-label="Last Month Visitor Jan 20, 2013 82.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(236,83) scale(1)" aria-label="Last Month Visitor Jan 21, 2013 83.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(279,48) scale(1)" aria-label="Last Month Visitor Jan 22, 2013 88.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(322,69) scale(1)" aria-label="Last Month Visitor Jan 23, 2013 85.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(365,69) scale(1)" aria-label="Last Month Visitor Jan 24, 2013 85.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(408,104) scale(1)" aria-label="Last Month Visitor Jan 25, 2013 80.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(451,55) scale(1)" aria-label="Last Month Visitor Jan 26, 2013 87.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(494,76) scale(1)" aria-label="Last Month Visitor Jan 27, 2013 84.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(537,83) scale(1)" aria-label="Last Month Visitor Jan 28, 2013 83.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(580,76)" aria-label="Last Month Visitor Jan 29, 2013 84.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#0df3a3" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(623,97)" aria-label="Last Month Visitor Jan 30, 2013 81.00"></circle>
                              </g>
                              <g transform="translate(81,20)">
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(21,138)" aria-label="Average Visitor Jan 16, 2013 75.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(64,118) scale(1)" aria-label="Average Visitor Jan 17, 2013 78.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(107,48) scale(1)" aria-label="Average Visitor Jan 18, 2013 88.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(150,42) scale(1)" aria-label="Average Visitor Jan 19, 2013 89.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(193,42) scale(1)" aria-label="Average Visitor Jan 20, 2013 89.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(236,69) scale(1)" aria-label="Average Visitor Jan 21, 2013 85.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(279,21) scale(1)" aria-label="Average Visitor Jan 22, 2013 92.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(322,35) scale(1)" aria-label="Average Visitor Jan 23, 2013 90.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(365,28) scale(1)" aria-label="Average Visitor Jan 24, 2013 91.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(408,76) scale(1)" aria-label="Average Visitor Jan 25, 2013 84.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(451,21) scale(1)" aria-label="Average Visitor Jan 26, 2013 92.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(494,55) scale(1)" aria-label="Average Visitor Jan 27, 2013 87.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(537,48) scale(1)" aria-label="Average Visitor Jan 28, 2013 88.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(580,55)" aria-label="Average Visitor Jan 29, 2013 87.00"></circle>
                                 <circle r="2.5" cx="0" cy="0" fill="#FFFFFF" stroke="#fe5d70" fill-opacity="1" stroke-width="2" stroke-opacity="1" transform="translate(623,69)" aria-label="Average Visitor Jan 30, 2013 85.00"></circle>
                              </g>
                           </g>
                           <g>
                              <g></g>
                           </g>
                           <g>
                              <g transform="translate(81,20)" visibility="visible">
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(3,185.5)">
                                    <tspan y="6" x="0">Jan 16</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(89,185.5)">
                                    <tspan y="6" x="0">Jan 18</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(175,185.5)">
                                    <tspan y="6" x="0">Jan 20</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(261,185.5)">
                                    <tspan y="6" x="0">Jan 22</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(346,185.5)">
                                    <tspan y="6" x="0">Jan 24</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(432,185.5)">
                                    <tspan y="6" x="0">Jan 26</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(518,185.5)">
                                    <tspan y="6" x="0">Jan 28</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(604,185.5)">
                                    <tspan y="6" x="0">Jan 30</tspan>
                                 </text>
                              </g>
                              <g transform="translate(81,20)" visibility="visible">
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,172)">
                                    <tspan y="6" x="0">$0M</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,137)">
                                    <tspan y="6" x="0">$2M</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,103)">
                                    <tspan y="6" x="0">$4M</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,68)">
                                    <tspan y="6" x="0">$6M</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,34)">
                                    <tspan y="6" x="0">$8M</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,-1)">
                                    <tspan y="6" x="0">$10M</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="12px" opacity="1" font-weight="bold" text-anchor="middle" transform="translate(-60,87) rotate(-90)">
                                    <tspan y="6" x="0">Visitors</tspan>
                                 </text>
                              </g>
                              <g transform="translate(81,20)" visibility="visible">
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(654,172)">
                                    <tspan y="6" x="0">70</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(654,137)">
                                    <tspan y="6" x="0">75</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(654,103)">
                                    <tspan y="6" x="0">80</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(654,68)">
                                    <tspan y="6" x="0">85</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(654,34)">
                                    <tspan y="6" x="0">90</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="11px" opacity="1" text-anchor="start" transform="translate(654,-1)">
                                    <tspan y="6" x="0">95</tspan>
                                 </text>
                                 <text y="6" fill="#000000" font-family="Verdana" font-size="12px" opacity="1" font-weight="bold" text-anchor="middle" transform="translate(686,87) rotate(-90)">
                                    <tspan y="6" x="0">New Visitors</tspan>
                                 </text>
                              </g>
                           </g>
                           <g></g>
                           <g transform="translate(81,20)"></g>
                           <g></g>
                           <g></g>
                           <clipPath id="AmChartsEl-3">
                              <rect x="-1" y="-1" width="646" height="175" rx="0" ry="0" stroke-width="0"></rect>
                           </clipPath>
                        </svg>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-4 col-sm-12">
         <div class="card">
            <div class="card-block bg-c-green gr-chart bg-pd">
               <div id="proj-earning" style="height: 230px; overflow: hidden; text-align: left;">
                  <div class="amcharts-main-div" style="position: relative;">
                     <div class="amcharts-chart-div" style="overflow: hidden; position: relative; text-align: left; width: 362px; height: 230px; padding: 0px; touch-action: auto;">
                        <svg version="1.1" style="position: absolute; width: 362px; height: 230px; top: 0.390625px; left: 0.390625px;">
                           <desc>JavaScript chart by amCharts 3.21.5</desc>
                           <g>
                              <path cs="100,100" d="M0.5,0.5 L361.5,0.5 L361.5,229.5 L0.5,229.5 Z" fill="#FFFFFF" stroke="#000000" fill-opacity="0" stroke-width="1" stroke-opacity="0"></path>
                              <path cs="100,100" d="M0.5,0.5 L300.5,0.5 L300.5,178.5 L0.5,178.5 L0.5,0.5 Z" fill="#FFFFFF" stroke="#000000" fill-opacity="0" stroke-width="1" stroke-opacity="0" transform="translate(41,20)"></path>
                           </g>
                           <g transform="translate(41,20)" clip-path="url(#AmChartsEl-9)">
                              <g visibility="hidden"></g>
                           </g>
                           <g></g>
                           <g></g>
                           <g></g>
                           <g>
                              <g transform="translate(41,20)">
                                 <g>
                                    <g transform="translate(15,178)" visibility="visible" aria-label=" UI 10">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-58.5 L30.5,-58.5 L30.5,0.5 L0.5,0.5 Z" fill="#fff" stroke="#fff" fill-opacity="1" stroke-width="1" stroke-opacity="1"></path>
                                    </g>
                                    <g transform="translate(75,178)" visibility="visible" aria-label=" UX 15">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-132.5 L30.5,-132.5 L30.5,0.5 L0.5,0.5 Z" fill="#fff" stroke="#fff" fill-opacity="1" stroke-width="1" stroke-opacity="1"></path>
                                    </g>
                                    <g transform="translate(135,178)" visibility="visible" aria-label=" Web 12">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-88.5 L30.5,-88.5 L30.5,0.5 L0.5,0.5 Z" fill="#fff" stroke="#fff" fill-opacity="1" stroke-width="1" stroke-opacity="1"></path>
                                    </g>
                                    <g transform="translate(195,178)" visibility="visible" aria-label=" App 16">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-147.5 L30.5,-147.5 L30.5,0.5 L0.5,0.5 Z" fill="#fff" stroke="#fff" fill-opacity="1" stroke-width="1" stroke-opacity="1"></path>
                                    </g>
                                    <g transform="translate(255,178)" visibility="visible" aria-label=" SEO 8">
                                       <path cs="100,100" d="M0.5,0.5 L0.5,-29.5 L30.5,-29.5 L30.5,0.5 L0.5,0.5 Z" fill="#fff" stroke="#fff" fill-opacity="1" stroke-width="1" stroke-opacity="1"></path>
                                    </g>
                                 </g>
                              </g>
                           </g>
                           <g></g>
                           <g>
                              <g>
                                 <path cs="100,100" d="M0.5,0.5 L300.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#000000" transform="translate(41,198)"></path>
                              </g>
                              <g>
                                 <path cs="100,100" d="M0.5,0.5 L0.5,178.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(41,20)" visibility="visible"></path>
                              </g>
                           </g>
                           <g>
                              <g transform="translate(41,20)" clip-path="url(#AmChartsEl-10)" style="pointer-events: none;">
                                 <path cs="100,100" d="M0.5,0.5 L0.5,0.5 L0.5,178.5" fill="none" stroke-width="1" stroke-opacity="0" stroke="#000000" visibility="hidden"></path>
                                 <path cs="100,100" d="M0.5,0.5 L300.5,0.5 L300.5,0.5" fill="none" stroke-width="1" stroke="#000000" visibility="hidden"></path>
                              </g>
                              <clipPath id="AmChartsEl-10">
                                 <rect x="0" y="0" width="300" height="178" rx="0" ry="0" stroke-width="0"></rect>
                              </clipPath>
                           </g>
                           <g></g>
                           <g>
                              <g></g>
                           </g>
                           <g>
                              <g transform="translate(41,20)" visibility="visible">
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="12px" opacity="1" text-anchor="middle" transform="translate(30,191)">
                                    <tspan y="6" x="0">UI</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="12px" opacity="1" text-anchor="middle" transform="translate(90,191)">
                                    <tspan y="6" x="0">UX</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="12px" opacity="1" text-anchor="middle" transform="translate(150,191)">
                                    <tspan y="6" x="0">Web</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="12px" opacity="1" text-anchor="middle" transform="translate(210,191)">
                                    <tspan y="6" x="0">App</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="12px" opacity="1" text-anchor="middle" transform="translate(270,191)">
                                    <tspan y="6" x="0">SEO</tspan>
                                 </text>
                              </g>
                              <g transform="translate(41,20)" visibility="visible">
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,177)">
                                    <tspan y="6" x="0">6</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,147)">
                                    <tspan y="6" x="0">8</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,118)">
                                    <tspan y="6" x="0">10</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,88)">
                                    <tspan y="6" x="0">12</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,58)">
                                    <tspan y="6" x="0">14</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,29)">
                                    <tspan y="6" x="0">16</tspan>
                                 </text>
                                 <text y="6" fill="#fff" font-family="Verdana" font-size="11px" opacity="1" text-anchor="end" transform="translate(-12,-1)">
                                    <tspan y="6" x="0">18</tspan>
                                 </text>
                              </g>
                           </g>
                           <g transform="translate(41,20)"></g>
                           <g></g>
                           <g></g>
                           <g>
                              <g transform="translate(41,20)"></g>
                              <g transform="translate(41,20)" visibility="visible">
                                 <g>
                                    <path cs="100,100" d="M0.5,178.5 L6.5,178.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,178.5 L0.5,178.5 L300.5,178.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,148.5 L6.5,148.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,148.5 L0.5,148.5 L300.5,148.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,119.5 L6.5,119.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,119.5 L0.5,119.5 L300.5,119.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,89.5 L6.5,89.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,89.5 L0.5,89.5 L300.5,89.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,59.5 L6.5,59.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,59.5 L0.5,59.5 L300.5,59.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,30.5 L6.5,30.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,30.5 L0.5,30.5 L300.5,30.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                                 <g>
                                    <path cs="100,100" d="M0.5,0.5 L6.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="transparent" transform="translate(-6,0)"></path>
                                    <path cs="100,100" d="M0.5,0.5 L0.5,0.5 L300.5,0.5" fill="none" stroke-width="1" stroke-opacity="0.3" stroke="#fff"></path>
                                 </g>
                              </g>
                           </g>
                           <g>
                              <g transform="translate(41,20)"></g>
                           </g>
                           <g></g>
                           <clipPath id="AmChartsEl-9">
                              <rect x="-1" y="-1" width="302" height="180" rx="0" ry="0" stroke-width="0"></rect>
                           </clipPath>
                        </svg>
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-footer mn-ft-crd">
               <h6 class="text-muted m-b-30 m-t-15">Total completed project and
                  earning
               </h6>
               <div class="row text-center">
                  <div class="col-6 b-r-default">
                     <h6 class="text-muted m-b-10">Completed Projects</h6>
                     <h4 class="m-b-0 f-w-600 ">175</h4>
                  </div>
                  <div class="col-6">
                     <h6 class="text-muted m-b-10">Total Earnings</h6>
                     <h4 class="m-b-0 f-w-600 ">76.6M</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!--usama updated code start-->
   <div class="row">
   <div class="col-xl-5 col-md-12">
      <div class="card table-card">
         <div class="card-header">
            <h5>Global Sales by Top Locations</h5>
         </div>
         <div class="card-block">
            <div class="table-responsive">
               <table class="table table-hover table-borderless">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Country</th>
                        <th>Sales</th>
                        <th class="text-right">Average</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td><img src="../files/assets/images/widget/GERMANY.jpg" alt="" class="img-fluid img-30"></td>
                        <td>Germany</td>
                        <td>3,562</td>
                        <td class="text-right">56.23%</td>
                     </tr>
                     <tr>
                        <td><img src="../files/assets/images/widget/USA.jpg" alt="" class="img-fluid img-30"></td>
                        <td>USA</td>
                        <td>2,650</td>
                        <td class="text-right">25.23%</td>
                     </tr>
                     <tr>
                        <td><img src="../files/assets/images/widget/AUSTRALIA.jpg" alt="" class="img-fluid img-30"></td>
                        <td>Australia</td>
                        <td>956</td>
                        <td class="text-right">12.45%</td>
                     </tr>
                     <tr>
                        <td><img src="../files/assets/images/widget/UK.jpg" alt="" class="img-fluid img-30"></td>
                        <td>United Kingdom</td>
                        <td>689</td>
                        <td class="text-right">8.65%</td>
                     </tr>
                     <tr>
                        <td><img src="../files/assets/images/widget/BRAZIL.jpg" alt="" class="img-fluid img-30"></td>
                        <td>Brazil</td>
                        <td>560</td>
                        <td class="text-right">3.56%</td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="text-right  m-r-20">
               <a href="#!" class="b-b-primary text-primary">View all Sales
               Locations </a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-xl-4 col-md-6">
      <div class="card">
         <div class="card-header">
            <h5>New Users</h5>
         </div>
         <div>
            <canvas id="newuserchart" style="display: block;width: 422px;height: 196px;"></canvas>
         </div>
         <div class="card-footer mn-bx-cht">
            <div class="row text-center b-t-default">
               <div class="col-4 b-r-default m-t-15">
                  <h5>85%</h5>
                  <p class="text-muted m-b-0">Satisfied</p>
               </div>
               <div class="col-4 b-r-default m-t-15">
                  <h5>6%</h5>
                  <p class="text-muted m-b-0">Unsatisfied</p>
               </div>
               <div class="col-4 m-t-15">
                  <h5>9%</h5>
                  <p class="text-muted m-b-0">NA</p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-xl-3 col-md-6">
      <div class="card">
         <div class="card-block text-center">
            <i class="feather icon-mail text-c-lite-green d-block f-40"></i>
            <h4 class="m-t-15"><span class="text-c-lite-green">8.62k</span>
               Subscribers
            </h4>
            <p class="m-b-10">Your main list is growing</p>
            <button class="btn btn-primary btn-sm btn-round">Manage
            List</button>
         </div>
      </div>
      <div class="card">
         <div class="card-block text-center">
            <i class="feather icon-twitter text-c-green d-block f-40"></i>
            <h4 class="m-t-15"><span class="text-c-blgreenue">+40</span>
               Followers
            </h4>
            <p class="m-b-10">Your main list is growing</p>
            <button class="btn btn-success btn-sm btn-round">Check them
            out</button>
         </div>
      </div>
   </div>
   </div>
   
   <div class="row">
   <div class="col-xl-4 col-md-6">
      <div class="card o-hidden">
         <div class="card-block bg-c-pink text-white bg-pd">
            <div class="chartjs-size-monitor">
               <div class="chartjs-size-monitor-expand">
                  <div class=""></div>
               </div>
               <div class="chartjs-size-monitor-shrink">
                  <div class=""></div>
               </div>
            </div>
            <h6 class="text-white">Sales Per Day <span class="f-right"><i class="feather icon-activity m-r-15"></i>3%</span></h6>
            <canvas id="sale-chart1" height="181" style="display: block; width: 362px; height: 181px;" width="362" class="chartjs-render-monitor"></canvas>
         </div>
         <div class="card-footer text-center bd-red">
            <div class="row">
               <div class="col-6 b-r-default">
                  <h4>$4230</h4>
                  <p class="text-muted m-b-0">Total Revenue</p>
               </div>
               <div class="col-6">
                  <h4>321</h4>
                  <p class="text-muted m-b-0">Today Sales</p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-xl-4 col-md-6">
      <div class="card o-hidden">
         <div class="card-block bg-c-green text-white bg-pd">
            <div class="chartjs-size-monitor">
               <div class="chartjs-size-monitor-expand">
                  <div class=""></div>
               </div>
               <div class="chartjs-size-monitor-shrink">
                  <div class=""></div>
               </div>
            </div>
            <h6 class="text-white">Visits<span class="f-right"><i class="feather icon-activity m-r-15"></i>9%</span></h6>
            <canvas id="sale-chart2" height="181" style="display: block; width: 362px; height: 181px;" width="362" class="chartjs-render-monitor"></canvas>
         </div>
         <div class="card-footer text-center bd-red">
            <div class="row">
               <div class="col-6 b-r-default">
                  <h4>3562</h4>
                  <p class="text-muted m-b-0">Monthly Visits</p>
               </div>
               <div class="col-6">
                  <h4>96</h4>
                  <p class="text-muted m-b-0">Today Visits</p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-xl-4 col-md-6">
         <div class="card o-hidden">
            <div class="card-block bg-c-blue text-white bg-pd">
               <div class="chartjs-size-monitor">
                  <div class="chartjs-size-monitor-expand">
                     <div class=""></div>
                  </div>
                  <div class="chartjs-size-monitor-shrink">
                     <div class=""></div>
                  </div>
               </div>
               <h6 class="text-white">Orders<span class="f-right"><i class="feather icon-activity m-r-15"></i>12%</span></h6>
               <canvas id="sale-chart3" height="181" style="display: block; width: 362px; height: 181px;" width="362" class="chartjs-render-monitor"></canvas>
            </div>
            <div class="card-footer text-center bd-red">
               <div class="row">
                  <div class="col-6 b-r-default">
                     <h4>1695</h4>
                     <p class="text-muted m-b-0">Monthly Orders</p>
                  </div>
                  <div class="col-6">
                     <h4>52</h4>
                     <p class="text-muted m-b-0">Today Orders</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!--usama updated code end-->
   @endif
   @if(!empty($widgets['after_sales_last_30_days']))
   @foreach($widgets['after_sales_last_30_days'] as $widget)
   {!! $widget !!}
   @endforeach
   @endif
   @if(!empty($all_locations))
   <div class="row">
      <div class="col-sm-12">
      </div>
   </div>
   @endif
   @endif
   <!-- sales chart end -->
   @if(!empty($widgets['after_sales_current_fy']))
   @foreach($widgets['after_sales_current_fy'] as $widget)
   {!! $widget !!}
   @endforeach
   @endif
   @endif
   <!-- can('dashboard.data') end -->
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
   aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
   aria-labelledby="gridSystemModalLabel"></div>
@stop
@section('javascript')
<script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@includeIf('sales_order.common_js')
@includeIf('purchase_order.common_js')
@if(!empty($all_locations))
{!! $sells_chart_1->script() !!}
{!! $sells_chart_2->script() !!}
@endif
<script type="text/javascript">
   sales_order_table = $('#sales_order_table').DataTable({
     processing: true,
     serverSide: true,
     scrollY: "75vh",
     scrollX:        true,
     scrollCollapse: true,
     aaSorting: [[1, 'desc']],
     "ajax": {
         "url": '{{action("SellController@index")}}?sale_type=sales_order',
         "data": function ( d ) {
             d.for_dashboard_sales_order = true;
         }
     },
     columnDefs: [ {
         "targets": 7,
         "orderable": false,
         "searchable": false
     } ],
     columns: [
         { data: 'action', name: 'action'},
         { data: 'transaction_date', name: 'transaction_date'  },
         { data: 'invoice_no', name: 'invoice_no'},
         { data: 'conatct_name', name: 'conatct_name'},
         { data: 'mobile', name: 'contacts.mobile'},
         { data: 'business_location', name: 'bl.name'},
         { data: 'status', name: 'status'},
         { data: 'shipping_status', name: 'shipping_status'},
         { data: 'so_qty_remaining', name: 'so_qty_remaining', "searchable": false},
         { data: 'added_by', name: 'u.first_name'},
     ]
   });
   @if(!empty($common_settings['enable_purchase_order']))
   $(document).ready( function(){
     //Purchase table
     purchase_order_table = $('#purchase_order_table').DataTable({
         processing: true,
         serverSide: true,
         aaSorting: [[1, 'desc']],
         scrollY: "75vh",
         scrollX:        true,
         scrollCollapse: true,
         ajax: {
             url: '{{action("PurchaseOrderController@index")}}',
             data: function(d) {
                 d.from_dashboard = true;
             },
         },
         columns: [
             { data: 'action', name: 'action', orderable: false, searchable: false },
             { data: 'transaction_date', name: 'transaction_date' },
             { data: 'ref_no', name: 'ref_no' },
             { data: 'location_name', name: 'BS.name' },
             { data: 'name', name: 'contacts.name' },
             { data: 'status', name: 'transactions.status' },
             { data: 'po_qty_remaining', name: 'po_qty_remaining', "searchable": false},
             { data: 'added_by', name: 'u.first_name' }
         ]
     });
   })
   @endif
   
   sell_table = $('#shipments_table').DataTable({
       processing: true,
       serverSide: true,
       aaSorting: [[1, 'desc']],
       scrollY:        "75vh",
       scrollX:        true,
       scrollCollapse: true,
       "ajax": {
           "url": '{{action("SellController@index")}}',
           "data": function ( d ) {
               d.only_pending_shipments = true;
           }
       },
       columns: [
           { data: 'action', name: 'action', searchable: false, orderable: false},
           { data: 'transaction_date', name: 'transaction_date'  },
           { data: 'invoice_no', name: 'invoice_no'},
           { data: 'conatct_name', name: 'conatct_name'},
           { data: 'mobile', name: 'contacts.mobile'},
           { data: 'business_location', name: 'bl.name'},
           { data: 'shipping_status', name: 'shipping_status'},
           @if(!empty($custom_labels['shipping']['custom_field_1']))
               { data: 'shipping_custom_field_1', name: 'shipping_custom_field_1'},
           @endif
           @if(!empty($custom_labels['shipping']['custom_field_2']))
               { data: 'shipping_custom_field_2', name: 'shipping_custom_field_2'},
           @endif
           @if(!empty($custom_labels['shipping']['custom_field_3']))
               { data: 'shipping_custom_field_3', name: 'shipping_custom_field_3'},
           @endif
           @if(!empty($custom_labels['shipping']['custom_field_4']))
               { data: 'shipping_custom_field_4', name: 'shipping_custom_field_4'},
           @endif
           @if(!empty($custom_labels['shipping']['custom_field_5']))
               { data: 'shipping_custom_field_5', name: 'shipping_custom_field_5'},
           @endif
           { data: 'payment_status', name: 'payment_status'},
           { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif }
       ],
       "fnDrawCallback": function (oSettings) {
           __currency_convert_recursively($('#sell_table'));
       },
       createdRow: function( row, data, dataIndex ) {
           $( row ).find('td:eq(4)').attr('class', 'clickable_td');
       }
   });
   
   
   
   
   
   
</script>
<script>
   //         import * as am5 from "@amcharts/amcharts5";
   // import * as am5xy from "@amcharts/amcharts5/xy";
       
</script>
<script src="{{ asset('usama_js/analytic-dashboard.min.js') }}"></script>
<script src="{{ asset('usama_js/script.js') }}"></script>
<script src="{{ asset('usama_js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
@endsection