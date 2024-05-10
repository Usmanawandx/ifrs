@php
    $debit_amount  = 0;
    $credit_amount = 0;
    $bal           = 0;
@endphp
{{-- table table-bordered table-hover table-striped hide-footer dataTable   --}}
<table class="table table-bordered table-striped table-primary table-styling table-hover table-sm" id="account_book">
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
	    <tr class="main-row">
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
                <button type="button" class="btn btn-success btn-xs account_book_show" data-id="{{ $val->id }}" data-refno="{{ $val->ref_no }}"> <i class="fa fa-eye"></i></button>
            </td>
	    </tr>

			@if (isset($val->transaction) && !empty($val->contact_id))
				{{-- <tr class="small-row t-head">
					<td>S#</td>
					<td>Product Name</td>
					<td></td>
					<td class="text-right">Qty</td>
					<td class="text-right">Weight</td>
					<td class="text-right">Price</td>
					<td class="text-right">Sale Tax</td>
					<td class="text-right">Further Tax</td>
					<td class="text-right">Amount</td>
				</tr> --}}
				@php
					$totalQty = 0;
					$totalRate = 0;
					$totalWeight = 0;
					$totalSaleTax = 0;
					$totalFurtherTax = 0;
					$totalAmount = 0;
				@endphp
				@foreach ($val->transaction->purchase_lines as $purchase_line)	
					@php
						if($val->transaction->type == "purchase_return"){
							$purchase_quantity = $purchase_line->quantity_returned;
						}else{
							$purchase_quantity = $purchase_line->quantity;
						}
						$amount = $purchase_quantity * $purchase_line->pp_without_discount;
						$saletaxRate = $purchase_line->line_tax->amount ?? 0;
						$salestax = $saletaxRate > 0 ? ($amount * $saletaxRate) / 100 : 0;
						$furthertaxRate = $purchase_line->further_taxs->amount ?? 0;
						$furtherTax = $furthertaxRate > 0 ? ($amount * $furthertaxRate) / 100 : 0;

						$totalQty += $purchase_quantity;
						$totalWeight += $purchase_line->product->product_custom_field1 * $purchase_quantity;
						$totalRate += $purchase_line->pp_without_discount;
						$totalSaleTax += $salestax;
						$totalFurtherTax += $furtherTax;
						$totalAmount += ($amount + $salestax + $furtherTax);
					@endphp
					<tr class="small-row purchase">
						<td></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $purchase_line->product->name }}</td>
						<td class="text-right">{{ number_format($purchase_quantity) }} </td>
						<td class="text-right">@ {{ number_format($purchase_line->pp_without_discount, 2) }} 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							= {{ number_format(($amount), 2) }}
						</td>
						<td></td><td></td><td></td><td></td><td></td>
						{{-- <td class="text-right">{{ (($purchase_line->product->product_custom_field1 ?? 0) * $purchase_quantity) }}</td>
						<td class="text-right">{{ number_format($salestax) }}</td>
						<td class="text-right">{{ number_format($furtherTax) }}</td> --}}
					</tr>
				@endforeach
				@foreach ($val->transaction->sell_lines as $sell_line)	
					@php
						$amount = $sell_line->quantity * $sell_line->unit_price;
						$saletaxRate = $sell_line->line_tax->amount ?? 0;
						$salestax = $saletaxRate > 0 ? ($amount * $saletaxRate) / 100 : 0;
						$furthertaxRate = $sell_line->further_taxs->amount ?? 0;
						$furtherTax = $furthertaxRate > 0 ? ($amount * $furthertaxRate) / 100 : 0;

						$totalQty += $sell_line->quantity;
						$totalWeight += $sell_line->product->product_custom_field1 * $sell_line->quantity;
						$totalRate += $sell_line->unit_price;
						$totalSaleTax += $salestax;
						$totalFurtherTax += $furtherTax;
						$totalAmount += ($amount + $salestax + $furtherTax);
					@endphp
					<tr class="small-row sale">
						<td></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $sell_line->product->name }}</td>
						<td class="text-right">{{ number_format($sell_line->quantity) }}</td>
						<td class="text-right">@ {{ number_format($sell_line->unit_price, 2) }}
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							= {{ number_format(($amount), 2) }}
						</td>
						<td></td><td></td><td></td><td></td><td></td>

						{{-- <td></td>
						<td class="text-right">{{ (($sell_line->product->product_custom_field1 ?? 0) * $sell_line->quantity) }}</td>
						<td class="text-right">{{ number_format($salestax) }}</td>
						<td class="text-right">{{ number_format($furtherTax) }}</td>
						<td class="text-right">{{ number_format(($amount + $salestax + $furtherTax), 2) }}</td> --}}
					</tr>
				@endforeach
				@if($totalSaleTax > 0)
					<tr class="small-row">
						<td></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sale Tax</td>
						<td></td><td class="text-right">{{ number_format($totalSaleTax, 2) }}</td><td></td><td></td><td></td><td></td><td></td>
					</tr>
				@endif
				@if($totalFurtherTax > 0)
					<tr class="small-row">
						<td></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Further Tax</td>
						<td></td><td class="text-right">{{ number_format($totalFurtherTax, 2) }}</td><td></td><td></td><td></td><td></td><td></td>
					</tr>
				@endif
				@if($val->transaction->add_charges || $val->transaction->less_charges)
					<tr class="small-row">
						<td></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add Or Less Charges</td>
						<td></td><td class="text-right">{{ number_format($val->transaction->add_charges, 2) }}</td><td></td><td></td><td></td><td></td><td></td>
					</tr>
				@endif
				{{-- <tr class="small-row total-row">
					<td>Total :</td>
					<td></td>
					<td></td>
					<td class="text-right">{{ number_format($totalQty, 2) }}</td>
					<td class="text-right">{{ number_format($totalWeight, 2) }}</td>
					<td class="text-right">{{ number_format($totalRate, 2) }}</td>
					<td class="text-right">{{ number_format($totalSaleTax, 2) }}</td>
					<td class="text-right">{{ number_format($totalFurtherTax, 2) }}</td>
					<td class="text-right">{{ number_format($totalAmount, 2) }}</td>
				</tr> --}}
			@endif
			
			{{-- <tr style="background-color: rgb(0 0 0 / 8%) !important;">
				<td>&nbsp;</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr> --}}

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