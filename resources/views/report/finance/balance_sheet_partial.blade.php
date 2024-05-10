<style>
     @media print {
        @page {
            margin: 15mm !important;
        }
        body{
            font-family: sans-serif !important;
            font-size: 8px !important;
        }
        .border {
            border: 1px solid #bfbfbf !important;
        }
        .bg-color {
            background-color: #eaeef3 !important;
        }
        .financial-period {
            font-size: 10px !important;
            padding: 0px !important;
            text-align: center !important;
        }
        #asOndate{
            padding-left: 20px !important;
            text-decoration: underline !important;
        }
        table th,
        table td {
            padding: 2px !important;
        }
        .font-16{
            font-size: 10px !important;
        }
        h4{
            font-size: 12px;
        }
    }
    .font-16{
        font-size: 16px;
    }
</style>


@php
    $total_finish = 0;
    $total_current = 0;
    $total_current_liability = 0;
    $total_long_term = 0;
    $total_equity = 0;
@endphp
<div class="row">
    {{-- Company Name --}}
    <div class="col-md-6 col-xs-6">
        <h6>COMPANY NAME</h6>
        <div class="col-md-12 col-xs-12 border bg-color">
            <h4><b>{{ Session::get('business.name') }}</b></h4>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th colspan="5" class="text-center"><h5>Assets</h5></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="5"><b>Fixed Assets</b></th>
                </tr>
                @foreach ($fixed_assets as $key => $val)
                    <tr>
                        <td colspan="3"><b>{{ str_repeat('&nbsp;', 5) }}{{ $val->name }}</b></td>
                        <td colspan="2" class="text-right"><b>{{ number_format(($val->balance), 2) }}</b></td>
                    </tr>
                    @php $total_finish += ($val->balance); @endphp
                    @if ($showAccounts)
                        @foreach ($val->accounts as $val2)
                            @if ($val2->balance != 0 || $showzero)
                                <tr>
                                    <td colspan="3">{{ str_repeat('&nbsp;', 10) }}{{ $val2->name }}</td>
                                    <td colspan="2" class="text-right">{{ number_format(($val2->balance), 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <tr class="bg-color">
                    <td colspan="3" class="text-right"><b>Total Fixed Assets</b></td>
                    <td class="text-right"><b>{{ number_format($total_finish, 2) }}</b></td>
                </tr>
                <br>
                <tr>
                    <td colspan="5"></td>
                </tr>
                {{-- Current Assets --}}
                <tr class="bg-color">
                    <th colspan="5"><b>Current Assets</b></th>
                </tr>
                @foreach ($current_assets as $key => $val)
                    <tr>
                        <td colspan="3"><b>{{ str_repeat('&nbsp;', 5) }}{{ $val->name }}</b></td>
                        <td colspan="2" class="text-right"><b>{{ number_format(($val->balance), 2) }}</b></td>
                    </tr>
                    @php $total_current += (($val->balance)); @endphp
                    @if ($showAccounts)
                        @foreach ($val->accounts as $val2)
                            @if ($val2->balance != 0)
                                <tr>
                                    <td colspan="3">{{ str_repeat('&nbsp;', 10) }}{{ $val2->name }}</td>
                                    <td colspan="2" class="text-right">{{ number_format(($val2->balance), 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                <tr>
                    <td colspan="3"><b>{{ str_repeat('&nbsp;', 5) }} Inventory</b></td>
                    <td colspan="2" class="text-right"><b>{{ number_format(($opening_stock), 2) }}</b></td>
                </tr>

                <tr class="bg-color">
                    <td colspan="3" class="text-right"><b>Total Current Assets</b></td>
                    <td class="text-right"><b>{{ number_format(($total_current + $opening_stock), 2) }}</b></td>
                </tr>


            </tbody>
        </table>

    </div>
    {{-- Date As On --}}
    <div class="col-md-6 col-xs-6">
        <h6>`</h6>
        <div class="col-md-12 col-xs-12 border bg-color">
            <h4>Date As On:<span id="asOndate"></span></h4>
        </div>
        <table class="table table-hover">
            <tbody>
                <tr>
                    <th colspan="5" class="text-center"><h5>LIABILITIES AND OWNER'S EQUITY</h5></th>
                </tr>
                <tr>
                    <th colspan="5"><b>Current Liabilty</b></th>
                </tr>
                @foreach ($current_liability as $key => $val)
                    <tr>
                        <td colspan="3"><b>{{ str_repeat('&nbsp;', 5) }}{{ $val->name }}</b></td>
                        <!--<td colspan="2" class="text-right"><b>{{ number_format(abs($val->balance), 2) }}</b></td>-->
                        <td colspan="2" class="text-right"><b>{{ number_format(($val->balance >= 0) ? -$val->balance : abs($val->balance), 2) }}</b></td>
                    </tr>
                    @php $total_current_liability += ($val->balance >= 0) ? -$val->balance : abs($val->balance); @endphp
                    @if ($showAccounts)
                        @foreach ($val->accounts as $val2)
                            @if ($val2->balance != 0)
                                <tr>
                                    <td colspan="3">{{ str_repeat('&nbsp;', 10) }}{{ $val2->name }}</td>
                                    <!--<td colspan="2" class="text-right">{{ number_format(abs($val2->balance), 2) }}</td>--> 
                                    <td colspan="2" class="text-right">{{ number_format(($val2->balance >= 0) ? -$val2->balance : abs($val2->balance), 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <tr class="bg-color">
                    <td colspan="3" class="text-right"><b>Total Current Liability</b></td>
                    <td class="text-right"><b>{{ number_format(($total_current_liability), 2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                </tr>
                {{-- Current Assets --}}
                <tr class="bg-color">
                    <th colspan="5"><b>Long Term Liability</b></th>
                </tr>
                @foreach ($long_term_liability as $key => $val)
                    <tr>
                        <td colspan="3"><b>{{ str_repeat('&nbsp;', 5) }}{{ $val->name }}</b></td>
                        <!--<td colspan="2" class="text-right"><b>{{ number_format(abs($val->balance), 2) }}</b></td>-->
                        <td colspan="2" class="text-right"><b>{{ number_format(($val->balance >= 0) ? -$val->balance : abs($val->balance), 2) }}</b></td>
                    </tr>
                    @php $total_long_term += ($val->balance >= 0) ? -$val->balance : abs($val->balance); @endphp
                    @if ($showAccounts)
                        @foreach ($val->accounts as $val2)
                            @if ($val2->balance != 0)
                                <tr>
                                    <td colspan="3">{{ str_repeat('&nbsp;', 10) }}{{ $val2->name }}</td>
                                    <!--<td colspan="2" class="text-right">{{ number_format(abs($val2->balance), 2) }}</td>-->
                                    <td colspan="2" class="text-right">{{ number_format(($val2->balance >= 0) ? -$val2->balance : abs($val2->balance), 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <tr class="bg-color">
                    <td colspan="3" class="text-right"><b>Total Long Term Liability </b></td>
                    <td class="text-right"><b>{{ number_format($total_long_term, 2) }}</b></td>
                </tr>
                {{-- Owners Equity --}}
                <tr>
                    <td colspan="5"></td>
                </tr>
                <tr class="bg-color">
                    <th colspan="5"><b>Owners Equity</b></th>
                </tr>
                @foreach ($owners_equity as $key => $val)
                    <tr>
                        <td colspan="3"><b>{{ str_repeat('&nbsp;', 5) }}{{ $val->name }}</b></td>
                        <!--<td colspan="2" class="text-right"><b>{{ number_format(abs($val->balance), 2) }}</b></td>-->
                        <td colspan="2" class="text-right"><b>{{ number_format(($val->balance >= 0) ? -$val->balance : abs($val->balance), 2) }}</b></td>
                    </tr>
                    @php $total_equity += ($val->balance >= 0) ? -$val->balance : abs($val->balance); @endphp
                    @if ($showAccounts)
                        @foreach ($val->accounts as $val2)
                            @if ($val2->balance != 0)
                                <tr>
                                    <td colspan="3">{{ str_repeat('&nbsp;', 10) }}{{ $val2->name }}</td>
                                    <!--<td colspan="2" class="text-right">{{ number_format(abs($val2->balance), 2) }}</td>-->
                                    <td colspan="2" class="text-right">{{ number_format(($val2->balance >= 0) ? -$val2->balance : abs($val2->balance), 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                
                <tr class="bg-color">
                    <td colspan="3" class="text-right"><b>Total Owners Equity </b></td>
                    <td class="text-right"><b>{{ number_format($total_equity, 2) }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@php
    $net_asset = ($total_finish + $total_current + $opening_stock);
    $net_liabilities = ($total_current_liability + $total_long_term + $total_equity);
    $diff = ($net_asset - abs($net_liabilities));
@endphp
<div class="row">
    <div class="col-md-12 col-xs-12 text-right">
        <div style="border-top: 1px solid black;font-size: 16px;">
             PROFIT & LOSS BEFORE TAXATION : &nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($diff, 2) }}
        </div>
    </div>
    <div class="col-md-6 col-xs-6 text-right">
        <div style="border-top: 1px solid black;border-bottom: 1px solid black;font-size: 16px;">
            NET Assets : &nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($net_asset, 2) }}
        </div>
    </div>
    <div class="col-md-6 col-xs-6 text-right">
        <div style="border-top: 1px solid black;border-bottom: 1px solid black;font-size: 16px;">
            NEt LIABILITIES & Equity  :&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format(($net_liabilities), 2) }}
        </div>
    </div>
</div>