<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')
    <style>
    .row.eq-height-row{
        background-color: #243949;
        height: 100% !important;
        min-height: 100vh !important;
    }
    .eq-height-col{
        justify-content: center;
    }
    .center-login{
        /*margin-top: 120px;*/
        background-color: #fff;
        border-radius: 20px;
        color: #000 !important;
        height: 400px;
        width: 550px;
    }
    .right-col label{
        color: #000;
    }
    .right-col a{
        color: #337ab7;
    }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif
    <div class="container-fluid">
        <div class="row eq-height-row">
            <?php $d= DB::table('business')->first(); ?>
            <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                <img src="<?php echo url('/');?>/uploads/business_logos/<?php echo $d->logo ?>" height="120px" width="300px" />
            </div>
            
            <div class="col-md-5 col-sm-5 hidden-xs left-col eq-height-col" style="display: none;">
                <div class="left-col-content login-header"> 
                    <div style="margin-top: 50%;">
                    <a href="/">
                        <?php
                        // $query=app/Business::All();
                      $d= DB::table('business')->first();
                     
                        ?>
                    @if(file_exists(public_path('uploads/logo.png')))
                        <img src="/uploads/logo.png" class="img-rounded" alt="Logo" width="150">
                    @else
                      <h2 style="color:white;"><?php echo $d->name ?></h2>
                    @endif 
                    
                    </a>
                    <br/>
                    @if(!empty(config('constants.app_title')))
                        <small>{{config('constants.app_title')}}</small>
                    @endif
                    </div>
                </div>
            </div>
            
            
            <div class="col-md-12 col-sm-12 col-xs-12 right-col eq-height-col">
                <div class="row center-login">
                    <div class="col-md-3 col-xs-4" style="text-align: left; display: none;">
                        <select class="form-control input-sm" id="change_lang" style="margin: 10px;">
                        @foreach(config('constants.langs') as $key => $val)
                            <option value="{{$key}}" 
                                @if( (empty(request()->lang) && config('app.locale') == $key) 
                                || request()->lang == $key) 
                                    selected 
                                @endif
                            >
                                {{$val['full_name']}}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    
                    
                    
                    <div class="col-md-11 col-xs-8" style="text-align: right;padding-top: 10px; display: none;">
                        @if($request->segment(1) != 'login')
                            &nbsp; &nbsp;<span >{{ __('business.already_registered')}} </span><a href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
                        @endif
                    </div>
                    
                    @yield('content')
                </div>
                
                
                
                
                
            </div>
            
            
            <div class="col-md-12" style="text-align: end;">
                <h4 style="color: #fff;"><?php echo $d->name ?></h4>
            </div>
            
            
        </div>
    </div>

    
    @include('layouts.partials.javascripts')
    
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

</html>