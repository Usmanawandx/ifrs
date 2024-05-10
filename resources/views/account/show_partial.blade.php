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
            <th>Ref No</th>
            {{-- <th>Ref No</th> --}}
            <th>Document #</th>
            <th>@lang('account.debit')</th>
            <th>@lang('account.credit')</th>
			<th>@lang( 'lang_v1.balance' )</th> 
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	    @if(!empty($opening) && count($opening) > 0)
    	    @foreach($opening as $key => $val)
    	        <tr>
    	            <td>{{ @format_date($val->operation_date) }}</td>
    	            <td></td>
    	            <td>{{ $val->description }}</td>
    	            <td>Opening Balance</td>
    	            {{-- <td>{{ $val->ref_no }}</td> --}}
    	            <td>{{ $val->document_no }}</td>
    	            <td id="opening_debit" class="text-right">{{ ($opening_balance->opening_balance > 0) ? @number_format(abs($opening_balance->opening_balance),2) : '' }}</td>
    	            <td id="opening_credit" class="text-right">{{ ($opening_balance->opening_balance < 0) ? @number_format(abs($opening_balance->opening_balance),2) : '' }}</td>
    	            <td class="text-right">
    	                @php
    	                    $bal = $opening_balance->opening_balance;
    	                @endphp
    	                {{ @number_format($opening_balance->opening_balance,2) }}
    	            </td>
    	            <td></td>
    	        </tr>
    	        @php
    	            break;
    	        @endphp
    	    @endforeach
	    @else
	        <tr>
    	            <td>{{ @format_date(Session::get("financial_year.start")) }}</td>
    	            <td></td>
    	            <td></td>
    	            <td>Opening Balance</td>
    	            <td></td>
    	            <td id="opening_debit" class="text-right">0.00</td>
    	            <td id="opening_credit" class="text-right">0.00</td>
    	            <td class="text-right">0.00</td>
    	            <td></td>
    	        </tr>
	    @endif
	    
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
				$acc_name = '';
	
				// if (!empty($val->transaction_id-1)) {
				
				// 	$contact_id = DB::table('transactions')->where('id', $val->transaction_id)->first()->contact_id ?? 0;
				// 	if (!empty($contact_id)) {
				// 		$supp_name = DB::table('contacts')->where('id', $contact_id)->first()->supplier_business_name ?? '';
				// 	}
				// 	echo $supp_name ?? '';
				// }
				if ($val->sub_type != 'opening_balance') {
					// Check if the reference number contains 'ab-', 'Ab-', or 'AB-'
					if (str_contains($val->ref_no, 'ab-') || str_contains($val->ref_no, 'Ab-') || str_contains($val->ref_no, 'AB-')) {
						$opposite_transaction_id = $val->type == "debit" ? $val->id + 1 : $val->id - 1;
					} elseif (str_contains($val->ref_no, 'CRV-') || str_contains($val->ref_no, 'crv-')) {
						// If the reference number does not contain the specified patterns, use the current transaction ID
						$opposite_transaction_id = $val->type == "debit" ? $val->id - 1 : $val->id + 1;
					}elseif (str_contains($val->ref_no, 'sell-') || str_contains($val->ref_no, 'purchase-') ) {
						$opposite_account_id = $val->against_id;

					} else {
						$opposite_transaction_id = $val->type == "debit" ? $val->id + 1 : $val->id - 1;
					}
					
			        if(str_contains($val->ref_no, 'sell-') || str_contains($val->ref_no, 'purchase-')){
						$opposite_acct_id = $val->against_id;
					}else{
    					$opposite_acct_id = DB::table('account_transactions')->where('id', $opposite_transaction_id)->value('account_id');
					}
					
					// Get the account name based on the retrieved opposite account ID
					$acc_name = $opposite_acct_id ? DB::table('accounts')->where('id', $opposite_acct_id)->value('name') : '';
				}
			
				echo $acc_name;

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
                    $ref__no = '';
                    if(empty($val->transaction_id)){ 
                        $ref__no = ' - <a href="#" class="account_book_show" data-href="' . action("AccountController@print_pr", [$val->ref_no]) . '" data-id="'. $val->id .'" data-refno="'. $val->ref_no .'">' . $val->ref_no . '</a>';
                    }
					// $ref__no = '<a href="'.action("AccountController@print_pr", [$val->ref_no]).'" target="_blank">' . $val->ref_no . '</a>';
                    echo $note . $ref__no;
				?>
	        </td>
	        {{-- <td>{{ $val->ref_no }}</td> --}}
	        <td>{{ $val->document_no }}</td>
	        <td class="text-right">
                {{ ($val->type == "debit") ? @number_format($val->amount,2) : '' }}
	        </td>
	        <td class="text-right">
                {{ ($val->type == "credit") ? @number_format($val->amount,2) : '' }}
	        </td>
	        <td class="text-right">
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
	        <td>
                <button type="button" class="btn btn-success btn-xs account_book_show" data-id="{{ $val->id }}" data-refno="{{ $val->ref_no }}">  Show </button>
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
	        {{-- <td></td> --}}
	        <td><b>Balance:</b><span id="account_balance"></td>
	        <td></td>
	    </tr>
	    <tr>
	        <td colspan="5"><b>Current Session Total</b></td>
	        <td id="curr_debit_total"></td>
	        <td id="curr_credit_total"></td>
	        <td></td>
	        <td></td>
	    </tr>
	</tfoot>
</table>