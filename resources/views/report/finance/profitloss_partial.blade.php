<style>
    @media print {
       @page {
           margin: 15mm !important;
       }
       .border{
            border: 1px solid #bfbfbf !important;
        }
        .bg-color{
            background-color: #eaeef3 !important;
        }
        .financial-period{
            font-size: 14px !important;
            padding: 0px !important;
            text-align: center !important;
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
</style>

@php
    $net_sales = 0;
    $net_sales_return = 0;
    $net_others_revenue = 0;
    $total_manufacturing_expenses = 0;
    $administrator_expenses = 0;
    $financial_expenses = 0;
    $total_purchase = 0;
    $total_purchase_return = 0;
    $net_cogs = 0;
    $net_profit = 0;
@endphp
<div class="row">
    {{-- Revenue --}}
    <div class="col-md-6 col-xs-6">
        <h6>COMPANY NAME</h6>
        <div class="col-md-12 col-xs-12 border bg-color">
            <h4><b>{{ Session::get('business.name') }}</b></h4>
        </div>
        <table class="table table-hover">
            <tbody>
                {{-- Sales --}}
                <tr>
                    <th colspan="3" class="text-center" style="font-size: 16px;"><b>Revenue</b></th>
                </tr>
                <tr>
                    <th colspan="3"><b>Sales</b></th>
                </tr>
                @foreach ($sales as $sales_val)
                    <tr>
                        <td colspan="2">{{ $sales_val->name }}</td>
                        <td class="text-right">{{ number_format(str_replace('-', '', $sales_val->balance)) }}</td>
                        @php
                            $net_sales += str_replace('-', '', $sales_val->balance);
                        @endphp
                    </tr>
                @endforeach


                <tr class="bg-color">
                    <td colspan="2" class="text-right"><b>Net Sale</b></td>
                    <td class="text-right">{{ number_format($net_sales, 2) }}</td>
                </tr>
                {{-- Sales return And Discount --}}
                <tr>
                    <th colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="3"><b>LESS SALE RETURNS & DISCOUNTS</b></th>
                </tr>


                @foreach ($sales_return_discount as $return_val)
                    <tr>
                        <td colspan="2">{{ $return_val->name }}</td>
                        <td class="text-right">{{ number_format(str_replace('-', '', $return_val->balance)) }}</td>
                        @php
                            $net_sales_return += str_replace('-', '', $return_val->balance);
                        @endphp
                    </tr>
                @endforeach
                <tr class="bg-color">
                    <td colspan="2" class="text-right"><b>NET SALE RETURNS & DISCOUNTS</b></td>
                    <td class="text-right">{{ number_format($net_sales_return, 2) }}</td>
                </tr>

                {{-- Other Revenue --}}
                <tr>
                    <th colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="3"><b>Others Revenue</b></th>
                </tr>
                @foreach ($other_revenue as $revenue_val)
                    <tr>
                        <td colspan="2">{{ $revenue_val->name }}</td>
                        <td class="text-right">{{ number_format(str_replace('-', '', $revenue_val->balance)) }}</td>
                        @php
                            $net_others_revenue += str_replace('-', '', $revenue_val->balance);
                        @endphp
                    </tr>
                @endforeach
                <tr class="bg-color">
                    <td colspan="2" class="text-right"><b>NET OTHERS REVENUE</b></td>
                    <td class="text-right">{{ number_format($net_others_revenue, 2) }}</td>
                </tr>
                <tr>
                    <th colspan="3">&nbsp;</th>
                </tr>
                <tr class="bg-color">
                    <td colspan="2" class="text-right"><b>NET REVENUE</b></td>
                    <td class="text-right">{{ number_format($net_revenue = $net_sales - $net_sales_return + $net_others_revenue, 2) }}
                    </td>
                </tr>
                {{-- Cost Of Good Sold --}}
                <tr>
                    <th colspan="3">&nbsp;</th>
                </tr>
                <tr class="bg-color">
                    <th colspan="3" class="text-center"><b>Cost Of Good Sold</b></th>
                </tr>
                <tr>
                    <td colspan="2">Opening Invetory</td>
                    <td>{{ number_format($opening_stock, 2) }}</td>
                </tr>
                @foreach ($purchase as $purchases)
                    <tr>
                        <td colspan="2">{{ $purchases->name }}</td>
                        <td class="text-right" style="padding-right: 70px">
                            {{ number_format(str_replace('-', '', $purchases->balance)) }} </td>
                        @php
                            $total_purchase += str_replace('-', '', $purchases->balance);
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3"><b>Less Purchase Return $ discount</b></th>
                </tr>
                @foreach ($purchase_retun as $return)
                    <tr>
                        <td colspan="2">{{ $return->name }}</td>
                        <td class="text-right" style="padding-right: 70px">
                            {{ number_format(str_replace('-', '', $return->balance)) }} </td>
                        @php
                            $total_purchase_return += str_replace('-', '', $return->balance);
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2">Closing Inventory</td>
                    <td>{{ number_format($closing, 2) }}</td>
                </tr>
                <tr class="bg-color">
                    <td colspan="2" class="text-right"><b>Total Cost of Good Solds</b></td>
                    <td class="text-right">{{ number_format($net_cogs= $opening_stock + ($total_purchase - $total_purchase_return) - $closing , 2) }}</td>
                </tr>

            </tbody>
        </table>
    </div>
    {{-- Expense --}}
    <div class="col-md-6 col-xs-6">
        <h6>STATEMENT REPORTING PERIOD</h6>
        <div class="col-md-12 col-xs-12 border financial-period">
            <table class="col-md-12 col-xs-12">
                <tr class="bg-color" style="border-bottom: 1px solid #bfbfbf;">
                    <td><span>Starting Date</span></td>
                    <td></td>
                    <td><span>Ending Date</span></td>

                </tr>
                <tr>
                    <td><span id="starting_date">{{ Session::get('financial_year.start') }}</span></td>
                    <td><span>to</span></td>
                    <td><span id="ending_date">{{ Session::get('financial_year.end')  }}span></td>
                </tr>

            </table>


        </div>
    </div>
    <div class="col-md-6 col-xs-6">
        <table class="table table-hover">
            <tr>
                <th colspan="3" class="text-center" style="font-size: 16px;"><b>Expenses</b></th>
            </tr>
            {{-- Manufacturing Expenese --}}
            <tr>
                <th colspan="3"><b>Manufacturing Expenese</b></th>
            </tr>
            @foreach ($manufacturing_expense as $manufacturing)
                <tr>
                    <td colspan="2">{{ $manufacturing->name }}</td>
                    <td class="text-right">{{ number_format(str_replace('-', '', $manufacturing->balance)) }}</td>
                    @php
                        $total_manufacturing_expenses += str_replace('-', '', $manufacturing->balance);
                    @endphp
                </tr>
            @endforeach
            <tr class="bg-color">
                <td colspan="2" class="text-right"><b>Total manufacturing Expenses</b></td>
                <td class="text-right">{{ number_format($total_manufacturing_expenses, 2) }}</td>
            </tr>
            <tr>
                <th colspan="3">&nbsp;</th>
            </tr>
            {{-- Administrator & Selling Expenses  --}}
            <tr>
                <th colspan="3"><b>Administrator & Selling Expenses</b></th>
            </tr>
            @foreach ($Administrator_expense as $admin_expense)
                <tr>
                    <td colspan="2">{{ $admin_expense->name }}</td>
                    <td class="text-right">{{ number_format(str_replace('-', '', $admin_expense->balance)) }}</td>
                    @php
                        $administrator_expenses += str_replace('-', '', $admin_expense->balance);
                    @endphp
                </tr>
            @endforeach
            <tr class="bg-color">
                <td colspan="2" class="text-right"><b>Total Administrator & Selling Expeneses</b></td>
                <td class="text-right">{{ number_format($administrator_expenses, 2) }}</td>
            </tr>
            <tr>
                <th colspan="3">&nbsp;</th>
            </tr>
            {{-- Financial Expenses --}}
            <tr>
                <th colspan="3"><b>Financial Expenses</b></th>
            </tr>
            @foreach ($financial_expense as $finance_expense)
                <tr>
                    <td colspan="2">{{ $finance_expense->name }}</td>
                    <td class="text-right">{{ number_format(str_replace('-', '', $finance_expense->balance)) }}</td>
                    @php
                        $financial_expenses += str_replace('-', '', $finance_expense->balance);
                    @endphp
                </tr>
            @endforeach
            <tr class="bg-color">
                <td colspan="2" class="text-right"><b>Total financial Expenses</b></td>
                <td class="text-right">{{ number_format($financial_expenses, 2) }}</td>
            </tr>
            <tr class="bg-color">
                <td colspan="2" class="text-right"><b>Net Expenses</b></td>
                <td class="text-right">{{ number_format($total_expense = $total_manufacturing_expenses + $administrator_expenses + $financial_expenses, 2) }}
                </td>
            </tr>
        </table>
    </div>

</div>
<div class="row">
    <div class="col-md-6 col-xs-6 text-right">
        <div style="border-top: 3px solid black;border-bottom: 3px solid black;font-size: 16px;">Gross Revenue : &nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($gross_profit = $net_revenue - $net_cogs,2) }}</div>
    </div>
    <div class="col-md-6 col-xs-6 text-right">
        <div style="border-top: 3px solid black;border-bottom: 3px solid black;font-size: 16px;">Net Revenue :&nbsp;&nbsp;&nbsp;&nbsp;{{number_format( $net_profit =$gross_profit - $total_expense,2) }}</div>
    </div>
</div>
