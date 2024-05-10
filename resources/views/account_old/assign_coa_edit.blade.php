@extends('layouts.app')
@section('title', 'Assign Chart Of Account')

@section('content')
<section class="content-header">
    <h1>Assign Chart Of Account
        <small>@lang('account.manage_your_account')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    	{!! Form::open(['url' => action('AccountController@assign_coa_update'), 'method' => 'post', 'id' => 'assign_coa_store' ]) !!}
    	    @component('components.widget', ['class' => 'box-primary'])
    		    <div class="row">
    		        <div class="col-md-12">
    		            <input type="hidden" name="role_id" value="{{ $assign_coa[0]->role_id }}" />
    		            <div class="col-md-6">
        		            <label for="acc_type">Control Account</label>
            				<div class="form-group">
        					<select class="form-control select2" multiple name="acc_type[]" id="acc_type" required>
                                @foreach($acc_types as $acc_type)
                                    @php
                                        $selected = '';
                                        foreach($assign_coa as $a_coa) {
                                            if($acc_type['id'] == $a_coa->control_account) {
                                                $selected = 'selected';
                                                break;
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $acc_type['id'] }}" {{ $selected }}>{{ $acc_type['name'] }}</option>
                                @endforeach
                            </select>

            				</div>
            			</div>
    		            
    		            <div class="col-md-6">
        		            <label for="transaction_acc">Transaction Account</label>
            				<div class="form-group">
        						<select class="form-control select2" multiple name="transaction_acc[]" id="transaction_acc" required>
        						    
        						</select>
            				</div>
            			</div>
            			
            			<div class="clearfix"></div>
            			
            			<div class="col-md-6">
        		            <label for="role">Role</label>
            				<div class="form-group">
        						<select class="form-control select2" name="role" id="role" required>
        						    <option value="">Select Please</option>
        						    @foreach($roles as $role)
        						        <option value="{{ $role['id'] }}" {{ ($role['id'] == $assign_coa[0]->role_id) ? 'selected' : ''  }}>{{ strstr($role['name'], '#', true) }}</option>
        						    @endforeach
        						</select>
            				</div>
            			</div>
            			
            			<div class="col-md-12">
            			    <input type="submit" class="btn btn-primary" value="Submit"/>
            			</div>
            			
    		        </div>
    		    </div>
        	@endcomponent
        {!! Form::close() !!}
    
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        $('#acc_type').trigger('change');
    });
    $(document).on('change','#acc_type',function(){
        var acc_type = $('#acc_type').val();
        
        $.ajax({
            url: '/account/get_transaction_acc',  
            method: 'POST',
            data: {
                acc_type: acc_type,
            },
            success: function(response) {
                console.log(response);
                // var select = $('#transaction_acc');
                // select.html('');
                // $.each(response, function(index, value) {
                //     var option = $(`<option value="${value.id}">${value.name}</option>`);
                //     select.append(option);
                // });
                
                var select = $('#transaction_acc');
                select.html('');
                
                $.each(response, function(index, value) {
                    var selected = '';
                    var foundMatch = false;
                
                    @foreach($assign_coa as $a_coa)
                        if (value.id == {{ $a_coa->transaction_acc_id }}) {
                            selected = 'selected';
                            foundMatch = true;
                        }
                    @endforeach
                
                    if (foundMatch) {
                        var option = $(`<option value="${value.id}" selected>${value.name}</option>`);
                    } else {
                        var option = $(`<option value="${value.id}">${value.name}</option>`);
                    }
                    
                    select.append(option);
                });
            }
        });

    })
</script>
@endsection





