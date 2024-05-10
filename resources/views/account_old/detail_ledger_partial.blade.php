@php
    $debit_amount  = 0;
    $credit_amount = 0;
    $bal           = 0;
@endphp
<table class="table table-bordered table-striped" id="account_book">
	<thead>
		<tr>
            <th>@lang( 'messages.date' )</th>
            <th>Account</th>
            <th>@lang( 'lang_v1.description' )</th>
            <th>@lang( 'brand.note' )</th>
            <th>Ref No</th>
            <th>Document #</th>
            <th>@lang('account.debit')</th>
            <th>@lang('account.credit')</th>
			<th>@lang( 'lang_v1.balance' )</th> 
		</tr>
	</thead>
	<tbody>
	    @foreach($opening as $key => $val)
	        <tr>
	            <td>{{ @format_date($val->operation_date) }}</td>
	            <td></td>
	            <td>{{ $val->description }}</td>
	            <td>Opening Balance</td>
	            <td>{{ $val->ref_no }}</td>
	            <td>{{ $val->document_no }}</td>
	            <td id="opening_debit">{{ ($opening_balance->opening_balance > 0) ? @number_format(abs($opening_balance->opening_balance),2) : '' }}</td>
	            <td id="opening_credit">{{ ($opening_balance->opening_balance < 0) ? @number_format(abs($opening_balance->opening_balance),2) : '' }}</td>
	            <td>
	                {{--
	                @php
	                    $bal = $val->opening_balance;
	                @endphp
	                {{ @number_format($val->opening_balance,2) }}
	                --}}
	                @php
	                    $bal = $opening_balance->opening_balance;
	                @endphp
	                {{ @number_format($opening_balance->opening_balance,2) }}
	            </td>
	        </tr>
	        @php
	            break;
	        @endphp
	    @endforeach
	    @foreach($accounts as $key => $val)
	        <?php
        	    if($val->sub_type == "opening_balance"){
        	       continue;
                }
            ?>
	    <tr>
	        <td>{{ @format_date($val->operation_date) }}</td>
	        <td>
				<?php
	            	$acc_name='';
                    if($val->sub_type != 'opening_balance'){
                //      if(!empty($val->transaction_id)){
				// 			$contact_id = DB::table('transactions')->where('id',$val->transaction_id)->first()->contact_id ?? 0;
				// 			if(!empty($contact_id)){
				// 				$supp_name = DB::table('contacts')->where('id',$contact_id)->first()->supplier_business_name ?? '';
				// 			}
				// 			echo $supp_name ?? '';
				// 		}else{
							if(str_contains($val->ref_no, 'ab-') || str_contains($val->ref_no, 'Ab-') || str_contains($val->ref_no, 'AB-')){
								$acct_id = DB::table('account_transactions')->where('id',$val->id+1)->first();
							}else{
								$acct_id = DB::table('account_transactions')->where('id',$val->id-1)->first();
								
							}
							if($acct_id){ 
								$acc_name = DB::table('accounts')->where('id',$acct_id->account_id)->first();
							}
							if($acc_name){ 
								echo $acc_name->name??'';
							}
				// 		}
                    }
                ?>
	        </td>
	        <td>{{ $val->description }}</td>
	        <td>
	            <?php
	            $note = app('App\Http\Controllers\AccountController')->__getPaymentDetails($val); 
                    if($note == ''){
                     if (str_contains($val->ref_no, 'd-') || str_contains($val->ref_no, 'D-')) {
                         $note = 'Payment voucher';
                     }else if(str_contains($val->ref_no, 'r-') || str_contains($val->ref_no, 'R-')){
                         $note = 'Recipt voucher';
                     }else if(str_contains($val->ref_no, 'ab-') || str_contains($val->ref_no, 'Ab-') || str_contains($val->ref_no, 'AB-')){
                         $note = 'Bank Book';
                     }else if(str_contains($val->ref_no, 'j-') || str_contains($val->ref_no, 'J-')){
                         $note = 'Journal Voucher';
                     }else if(str_contains($val->ref_no, 'cb-') || str_contains($val->ref_no, 'CB-')){
                         $note = 'Cash Book';
                     }else if(str_contains($val->ref_no, 'crv-') || str_contains($val->ref_no, 'CRV-')){
                         $note = 'Cash Recipt Voucher';
                     }else if(str_contains($val->ref_no, 'cpv-') || str_contains($val->ref_no, 'CPV-')){
                         $note = 'Cash Payment Voucher';
                     }else if($val->sub_type == 'opening_balance'){
                         $note = 'Opening Balance';
                     }
                    }
                    echo $note;
                ?>
	        </td>
	        <td>{{ $val->ref_no }}</td>
	        <td>{{ $val->document_no }}</td>
	        <td>
                {{ ($val->type == "debit") ? @number_format($val->amount,2) : '' }}
	        </td>
	        <td>
                {{ ($val->type == "credit") ? @number_format($val->amount,2) : '' }}
	        </td>
	        <td>
	            <?php 
	                if($val->sub_type != "opening_balance"){
                        if($val->type == "debit"){
                            $debit_amount += $val->amount;
                        }
                        if($val->type == "credit"){
                            $credit_amount += $val->amount;
                        }
                    }
                    echo @number_format(($bal + $debit_amount - $credit_amount),2);
	            ?>
	        </td>
	    </tr>
	    @endforeach
	</tbody>
    <tfoot>
	    <tr>
	        <td><b>Total:</b></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td></td>
	        <td><b>Balance:</b><span id="account_balance"></td>
	    </tr>
	    <tr>
	        <td colspan="6"><b>Current Session Total</b></td>
	        <td id="curr_debit_total"></td>
	        <td id="curr_credit_total"></td>
	        <td></td>
	    </tr>
	</tfoot>
</table>