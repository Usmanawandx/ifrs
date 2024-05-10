@inject('request', 'Illuminate\Http\Request')

@if($request->segment(1) == 'pos' && ($request->segment(2) == 'create' || $request->segment(3) == 'edit'))
    @php
        $pos_layout = true;
    @endphp
@else
    @php
        $pos_layout = false;
    @endphp
@endif

@php
    $whitelist = ['127.0.0.1', '::1'];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ Session::get('business.name') }}</title>

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/bootstrap/css/bootstrap.min.css') }}"> --}}
        <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/feather/css/feather.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/jquery.mCustomScrollbar.css') }}">
        
        @include('layouts.partials.css')

        @yield('css')
        <style>
            /*.btn{*/
            /*    border-radius: 50px!important;*/
            /*}*/
            /*.btn-flat.btn-modal,.btn-file{*/
            /*    border-radius: 0px!important;*/
            /*}*/
            .col-sm-12:has(.submit_product_form){
                position: fixed;
                bottom: 30px;
                left: 50%;
                transform: translateX(-42%);
            }
            /* .col-sm-12:has(#submit_purchase_form_req, #submit_purchase_form, #submit-sell){
                text-align: right !important;
            } */
            .col-md-12:has(#submit_purchase_return_form){
                text-align: right !important;
            }
            .btn-group.pull-right:has(.toggle-font-size){
                display: none;
            }
            
        </style>
    </head>

    <body class="@if($pos_layout) hold-transition lockscreen @else hold-transition skin-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'blue-light'}}@endif sidebar-mini @endif">
        
        {{-- Popup --}}

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    
                    <div class="modal-body">
                        <h5 class="modal-title" id="myModalLabel">Can set trusted PC?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" value="Yes" onclick="enableBackground('yes')">Yes</button>
                        <button type="button" class="btn btn-secondary" value="No" onclick="enableBackground('no')">No</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- popup end --}}
     
        <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
        
        <div class="wrapper thetop">
            <script type="text/javascript">
                if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
            </script>
            @if(!$pos_layout)
                @include('layouts.partials.header')
                @include('layouts.partials.sidebar')
            @else
                @include('layouts.partials.header-pos')
            @endif

            @if(in_array($_SERVER['REMOTE_ADDR'], $whitelist))
                <input type="hidden" id="__is_localhost" value="true">
            @endif

            <!-- Content Wrapper. Contains page content -->
            <div class="@if(!$pos_layout) content-wrapper @endif">
                <!-- empty div for vuejs -->
                <div id="app">
                    @yield('vue')
                </div>
                <!-- Add currency related field-->
                <input type="hidden" id="__code" value="{{session('currency')['code']}}">
                <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
                <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
                <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
                <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
                <input type="hidden" id="__precision" value="{{config('constants.currency_precision', 2)}}">
                <input type="hidden" id="__quantity_precision" value="{{config('constants.quantity_precision', 2)}}">
                <!-- End of currency related field-->

                @if (session('status'))
                    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
                @endif
                @yield('content')

                <div class='scrolltop no-print'>
                    <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
                </div>

                @if(config('constants.iraqi_selling_price_adjustment'))
                    <input type="hidden" id="iraqi_selling_price_adjustment">
                @endif

                <!-- This will be printed -->
                <section class="invoice print_section" id="receipt_section">
                </section>
                
            </div>
            @include('home.todays_profit_modal')
            <!-- /.content-wrapper -->

            @if(!$pos_layout)
                @include('layouts.partials.footer')
            @else
                @include('layouts.partials.footer_pos')
            @endif

            <audio id="success-audio">
              <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
            <audio id="error-audio">
              <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
            <audio id="warning-audio">
              <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
        </div>

        @if(!empty($__additional_html))
            {!! $__additional_html !!}
        @endif

        @include('layouts.partials.javascripts')

        <div class="modal fade view_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

        @if(!empty($__additional_views) && is_array($__additional_views))
            @foreach($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif
        <script>
        $(document).ready(function(){
            $('.theme-loader').hide();
        })
        
          function enableBackground(response) {
      
            // Enable the background screen
            $("body").css("overflow", "auto");

            // Close the modal
            $('#myModal').modal('hide');

            
            if (response === 'yes') {
                
            if (!getLoginCookie()) {
                // Create a login cookie with a 1-year validity
                createLoginCookie();

            }
            } else {

                window.location.href = '{{ url("/logout") }}'; 
            }

        }
        
        function getLoginCookie() {
            // Function to check if the login cookie exists
            return document.cookie.includes("login_cookie=");
        }
        function createLoginCookie() {
            // Function to create a login cookie with a 1-year validity
            const expirationDate = new Date();
            expirationDate.setFullYear(expirationDate.getFullYear() + 1);

            document.cookie = "login_cookie=your_value; expires=" + expirationDate.toUTCString() + "; path=/";
        }
        </script>
        @if(auth()->user()->roles[0]->can_set_trusted == 1)
        <script>
            $(document).ready(function () {
                // Disable the background screen
                $("body").css("overflow", "hidden");
                if (!getLoginCookie()) {
                // Show the modal
                $('#myModal').modal('show');
            }
                // Enable the background screen when modal is closed
                $('#myModal').on('hidden.bs.modal', function () {
                    $("body").css("overflow", "auto");
                });
            });
        </script>
        @elseif(auth()->user()->roles[0]->can_set_trusted == 0)
        <script>
            if (!getLoginCookie()) {
            window.location.href = '{{ url("/logout") }}';
            }
        </script>
        @endif
    </body>

</html>