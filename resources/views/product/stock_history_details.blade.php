<div class="row">
	@php
	    $in                = 0;
	    $out               = 0;
	    $opening_balance   = $opening_balance->opening_balance ?? 0;
		$opening_weight	   = (($opening_balance ?? 0) * (!empty($opening_stock->net_weight) ? $opening_stock->net_weight : 0));
	@endphp

	<div class="col-md-12">
	    <div class="table-responsive">
		    <table class="table table-bordered table-striped table-hover" id="stock_history__table">
    			<thead>
    			<tr>
    				<th rowspan="2">Location</th>
    				<th rowspan="2">Date</th>
    				<th>Voucher</th>
    				<th >Voucher</th>
    				<th rowspan="2" >Voucher Name</th>
    				<th rowspan="2">Description</th>
    				<th rowspan="2">Purchase Rate</th>
    				{{-- <th rowspan="2">Avg Rate</th> --}}
    				<th colspan="2">Received</th>
    				<th colspan="2">Issued</th>
    				<th colspan="2">Balance</th>
    			</tr>
    			<tr>
    				<th>Type</th>
    				<th >No</th>
    				<th>Qty</th>
    				<th>Weight</th>
    				<th>Qty</th>
    				<th>Weight</th>
    				<th>Qty</th>
    				<th>Weight</th>
    			</tr>
    			</thead>
    			<tbody>
    				@if($opening_stock != null && !empty($opening_stock))
        			    <tr>
        					<td>{{$stock_history[0]->location??''}}</td>
        					<td>{{ @format_date($opening_stock->transaction_date) }}</td>
        					<td> OP </td>
        					<td> </td>
        					<td></td>
        					<td>opening</td>
        					<td>{{ ($opening_stock->purchase_rate) ? number_format($opening_stock->purchase_rate, 2) : '' }}</td>
        					{{-- <td>
        					    {{ (!empty($opening_stock->avg_rate_without_tax)) ? number_format($opening_stock->avg_rate_without_tax, 2) : 0 }}
        					</td> --}}
        					<td id="opening_recived">
								@if($opening_balance > 0)
									{{ number_format($opening_balance,2) }}
								@endif
							</td>
        					<td id="opening_recived_weight">
								@if($opening_balance > 0)
									{{ $opening_weight }}
								@endif
							</td>
        					<td id="opening_issued"> 
								@if($opening_balance < 0)
									{{ number_format($opening_balance,2) }}
								@endif
							</td>
        					<td id="opening_issued_weight">
								@if($opening_balance < 0)
									{{ $opening_weight }}
								@endif
							</td>
        					<td>{{number_format($opening_balance,2)}}</td>
        					<td>{{ $opening_weight }}</td>
        				</tr>
    				@endif
    
    			@forelse($stock_history as $history)
    				<tr>
    					<td>{{$history->location ??''}}</td>
    					<td>{{ @format_date($history->date) }}</td>
    					<td >{{ explode('-', $history->ref_no)[0] ?? $history->ref_no }}</td>
    					
    					<td class="stock_ref">
							
    					    @php
                                $codeToDescription = [
                                    "DI" => "Delivery Note",
                                    "GRN" => "Purchase",
                                    "DN" => "Purchase Return",
                                    "PR" => "Purchase Requisition",
                                    "PI" => "Purchase Invoice",
                                    "PO" => "Purchase Order",
                                    "ST" => "Stock Transfer",
                                    "SA" => "Stock Adjustment",
                                    "CN" => "Sell Return",
                                    "EXP" => "Expense",
                                    "CO" => "Contacts",
                                    "PP" => "Purchase Payment",
                                    "SP" => "Sell Payment",
                                    "EP" => "Expense Payment",
                                    "BL" => "Business Location",
                                    "SO" => "Sales Order",
                                    "ML" => "Milling",
                                    "DI" => "Delivery Note",
                                    "SI" => "Sales Invoice",
                                    "SR" => "Sale Return Invoice",
                                    "PRD" => "Production",
                                    "MPRD" => "Multi Production",
                                ];
                                
                                $code = explode('-', $history->ref_no)[0];
                                $description = $codeToDescription[$code] ?? $code; 


							$voucherNo = "";
							if (explode('-', $history->ref_no)[0] == 'MPRD' || explode('-', $history->ref_no)[0] == 'SA'){
                                $voucherNo = isset(explode('-', $history->ref_no)[1]) && isset(explode('-', $history->ref_no)[2]) ? explode('-', $history->ref_no)[1] . '-' . explode('-', $history->ref_no)[2] : $history->ref_no;
							}elseif (explode('-', $history->ref_no)[0] == 'PRD'){
                                $voucherNo = isset(explode('-', $history->ref_no)[1]) ? explode('-', $history->ref_no)[1] : $history->ref_no;
							}else{
                                $voucherNo = isset(explode('-', $history->ref_no)[2]) ? explode('-', $history->ref_no)[2] : $history->ref_no;
                            }
							
							
							$details = '';
							if (!empty($description)) {
								if ($description == 'Delivery Note') {
									$details = '<a href="#" data-href="' . action("SellController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
								}elseif ($description == 'Purchase Invoice') {
									$details = '<a href="#" data-href="' . action("PurchaseOrderController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
								}elseif ($description == 'Sale Return Invoice') {
									$details = '<a href="#" data-href="' . action("SellController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
								}elseif ($description == 'Purchase Return') {
									$details = '<a href="#" data-href="' . action("PurchaseReturnController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
								}elseif ($description == 'Purchase') {
									$details = '<a href="#" data-href="' . action("PurchaseController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
								}else{
									$details = $voucherNo;
								}
							}else{
								$details = $voucherNo;
							}

							echo $details;
                            @endphp
    					</td>
    					
    					<td>
    					    {{ $description }}
    					</td> 
    					<td>
    					    
    					    @if(explode('-', $history->ref_no)[0] == 'MPRD' || explode('-', $history->ref_no)[0] == 'PRD' || explode('-', $history->ref_no)[0] == 'SA')
    					        {{ $history->pro_name ?? '' }}
    					    @else
    					        {{ $history->contact_name ?? '' }}
    					    @endif
    					</td>
    					<td>
    					    @if($history->type == "delivery_note" || $history->type == "sale_return_invoice")
    					        {{ number_format($history->unit_price_before_discount, 2) }}
    					    @else
    					        {{ number_format($history->pp_without_discount, 2) }}
    					    @endif
							{{-- {{ ($history->purchase_rate) ? number_format($history->purchase_rate, 2) : '' }} --}}
    					</td>
        				{{-- <td>{{ !empty($history->avg_rate_without_tax) ? number_format($history->avg_rate_without_tax, 2) : '' }}</td> --}}
    					<td>
    					    @php
                        	    $in              +=  $history->stock_in;
                        	    $out             +=  $history->stock_out;
    							$history->net_weight = !empty($history->net_weight) ? $history->net_weight : 0;
                        	@endphp
    					    {{ $history->stock_in }}
    					</td>
    					<td>{{ (($history->stock_in * $history->net_weight) != 0) ? ($history->stock_in * $history->net_weight) : '' }}</td>
    					<td>{{ $history->stock_out }}</td>
    					<td>{{ (($history->stock_out * $history->net_weight) != 0) ? ($history->stock_out * $history->net_weight) : '' }}</td>
    					<td>{{ ($opening_balance + $in - $out) }}</td>
    					<td>{{ (($opening_balance + $in - $out) * ($history->net_weight ?? 0)) }}</td>
    				</tr>
    			@empty
    				<tr>
    					<td colspan="13" class="text-center">
    						@lang('lang_v1.no_stock_history_found')
    					</td>
    				</tr>
    			@endforelse
    			</tbody>
    			<tfoot>
            	    <tr>
            	        <td colspan="7"><b>Total:</b></td>
            	        <td></td>
            	        <td></td>
            	        <td></td>
            	        <td></td>
            	        <td></td>
            	        <td><b></b><span id=""></td>
            	    </tr>
            	    <tr>
            	        <td colspan="7"><b>Current Session Total:</b></td>
            	        <td id="total_received_qty"></td>
            	        <td id="total_received_weight"></td>
            	        <td id="total_issue_qty"></td>
            	        <td id="total_issue_weight"></td>
            	        <td></td>
            	        <td></td>
            	    </tr>
            	</tfoot>
    		</table>
		</div>
	</div>
</div>