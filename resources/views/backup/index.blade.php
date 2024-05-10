@extends('layouts.app')

<style>
  .btn-edt {
      font-size: 14px !important;
      padding: 7px 8px 9px !important;
      border-radius: 50px !important;
  }
  
  .btn-vew {
      font-size: 14px !important;
      padding: 9px 8px 9px !important;
      border-radius: 50px !important;
  }
  
  .btn-dlt {
      font-size: 14px !important;
      padding: 7px 8px 9px !important;
      border-radius: 50px !important;
  }
      
  </style>
@section('title', __('lang_v1.backup'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.backup')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    
  @if (session('notification') || !empty($notification))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @if(!empty($notification['msg']))
                    {{$notification['msg']}}
                @elseif(session('notification.msg'))
                    {{ session('notification.msg') }}
                @endif
              </div>
          </div>  
      </div>     
  @endif

  <div class="row">
    <div class="col-sm-12">
      @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
          <div class="box-tools">
            <a id="create-new-backup-button" href="{{ url('backup/create') }}" class="btn btn-primary pull-right"
                     style="margin-bottom:2em;"><i
                          class="fa fa-plus"></i> @lang('lang_v1.create_new_backup')
            </a>
          </div>
        @endslot
        @if (count($backups))
                <table class="table table-striped table-bordered hide-footer dataTable table-styling table-hover table-primary">
                  <thead>
                  <tr>
                    <th  class="main-colum">@lang('messages.actions')</th>
                      <th>@lang('lang_v1.file')</th>
                      <th>@lang('lang_v1.size')</th>
                      <th>@lang('lang_v1.date')</th>
                      <th>@lang('lang_v1.age')</th>
                  </tr>
                  </thead>
                    <tbody>
                    @foreach($backups as $backup)
                        <tr>
                          <td>
                            <a class="btn btn-xs btn-success btn-vew"
                                 href="{{action('BackUpController@download', [$backup['file_name']])}}"><i class="glyphicon glyphicon-download-alt"></i></a>
                              <a class="btn btn-xs btn-danger link_confirmation btn-dlt" data-button-type="delete"
                                 href="{{action('BackUpController@delete', [$backup['file_name']])}}"><i class="glyphicon glyphicon-trash"></i></a>
                          </td>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ humanFilesize($backup['file_size']) }}</td>
                            <td>
                                {{ Carbon::createFromTimestamp($backup['last_modified'])->toDateTimeString() }}
                            </td>
                            <td>
                                {{ Carbon::createFromTimestamp($backup['last_modified'])->diffForHumans(Carbon::now()) }}
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
              </table>
            @else
                <div class="well">
                    <h4>There are no backups</h4>
                </div>
            @endif
            <br>
            <strong>@lang('lang_v1.auto_backup_instruction'):</strong><br>
            <code>{{$cron_job_command}}</code> <br>
            <strong>@lang('lang_v1.backup_clean_command_instruction'):</strong><br>
            <code>{{$backup_clean_cron_job_command}}</code>
      @endcomponent
    </div>
  </div>
</section>
@endsection