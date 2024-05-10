
@foreach($parent_data_recursive as $key => $data)
    <tr data-node-id="{{ $key }}" class="parent">
        <td>
            {{ $data['name'] }}
        </td>
        <td>
            {{ ($data['balance'] > 0) ? number_format($data['balance'],2) : number_format(0,2) }} 
        </td>
        <td>
            {{ ($data['balance'] < 0) ? number_format(abs($data['balance']),2) : number_format(0,2) }}
        </td>
    </tr>
    
    @if(isset($data['sub_account']) && count($data['sub_account']) > 0)
    
        @foreach($data['sub_account'] as $key2 => $items)
        
            <tr data-node-id="{{ $key.'.'.$key2 }}" data-node-pid="{{ $key }}" class="sub" >
                <td>
                    {{ $items['name'] }}
                </td>
                <td>
                    {{  ($items['balance'] > 0) ? number_format($items['balance'],2) : number_format(0,2)  }}
                </td>
                <td>
                    {{  ($items['balance'] < 0) ? number_format(abs($items['balance']),2) : number_format(0,2) }}
                </td>
            </tr>
            
            @if(isset($items['sub_account']) && count($items['sub_account']) > 0)
                @foreach($items['sub_account'] as $key3 => $items3)
                
                    <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3 }}" data-node-pid="{{ $key.'.'.$key2 }}" class="sub" >
                        <td>
                            {{ $items3['name'] }}
                        </td>
                        <td>
                            {{  ($items3['balance'] > 0) ? number_format($items3['balance'],2) : number_format(0,2) }}
                        </td>
                        <td>
                            {{  ($items3['balance'] < 0) ? number_format(abs($items3['balance']),2) : number_format(0,2) }}
                        </td>
                    </tr>
                    @if(isset($items3['sub_account']) && count($items3['sub_account']) > 0)
                        @foreach($items3['sub_account'] as $key4 => $items4)
                        
                            <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3 }}" class="sub" >
                                <td>
                                    {{ $items4['name'] }}
                                </td>
                                <td>
                                    {{  ($items4['balance'] > 0) ? number_format($items4['balance'],2) : number_format(0,2) }}
                                </td>
                                <td>
                                    {{  ($items4['balance'] < 0) ? number_format(abs($items4['balance']),2) : number_format(0,2) }}
                                </td>
                            </tr>
                            @if(isset($items4['sub_account']) && count($items4['sub_account']) > 0)
                                @foreach($items4['sub_account'] as $key5 => $items5)
                                
                                    <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4 }}" class="sub" >
                                        <td>
                                            {{ $items5['name'] }}
                                        </td>
                                        <td>
                                            {{  ($items5['balance'] > 0) ? number_format($items5['balance'],2) : number_format(0,2) }}
                                        </td>
                                        <td>
                                            {{  ($items5['balance'] < 0) ? number_format(abs($items5['balance']),2) : number_format(0,2) }}
                                        </td>
                                    </tr>
                                    
                                    @if(isset($items5['sub_account']) && count($items5['sub_account']) > 0)
                                        @foreach($items5['sub_account'] as $key6 => $items6)
                                        
                                            <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5 }}" class="sub" >
                                                <td>
                                                    {{ $items6['name'] }}
                                                </td>
                                                <td>
                                                    {{  ($items6['balance'] > 0) ? number_format($items6['balance'],2) : number_format(0,2) }}
                                                </td>
                                                <td>
                                                    {{  ($items6['balance'] < 0) ? number_format(abs($items6['balance']),2) : number_format(0,2) }}
                                                </td>
                                            </tr>
                                            @if(isset($items6['sub_account']) && count($items6['sub_account']) > 0)
                                                @foreach($items6['sub_account'] as $key7 => $items7)
                                                
                                                    <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6 }}" class="sub" >
                                                        <td>
                                                            {{ $items7['name'] }}
                                                        </td>
                                                        <td>
                                                            {{  ($items7['balance'] > 0) ? number_format($items7['balance'],2) : number_format(0,2) }}
                                                        </td>
                                                        <td>
                                                            {{  ($items7['balance'] < 0) ? number_format(abs($items7['balance']),2) : number_format(0,2) }}
                                                        </td>
                                                    </tr>
                                                    
                                                    
                                                   <!--transaction_account-->
                                                    @if(isset($items7['transaction_account']) && count($items7['transaction_account']) > 0)
                                                        @foreach($items7['transaction_account'] as $itemss6)
                                                            <tr data-node-id="" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7 }}" class="sub @if($itemss6['balance'] == 0) transaction_acc @endif" >
                                                                <td>
                                                                    <a href="{{ url('/account/account/'.$itemss6['id'] )}}" target="_blank">{{ $itemss6['name'] }}</a>
                                                                </td>
                                                                <td>
                                                                    {{  ($itemss6['balance'] > 0) ? number_format($itemss6['balance'],2) : number_format(0,2) }}
                                                                </td>
                                                                <td>
                                                                    {{  ($itemss6['balance'] < 0) ? number_format(abs($itemss6['balance']),2) : number_format(0,2) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach 
                                                    @endif
                                                @endforeach 
                                            @endif
                                            
                                           <!--transaction_account-->
                                            @if(isset($items6['transaction_account']) && count($items6['transaction_account']) > 0)
                                                @foreach($items6['transaction_account'] as $itemss5)
                                                    <tr data-node-id="" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6 }}" class="sub @if($itemss5['balance'] == 0) transaction_acc @endif" >
                                                        <td>
                                                            <a href="{{ url('/account/account/'.$itemss5['id'] )}}" target="_blank">{{ $itemss5['name'] }}</a>
                                                        </td>
                                                        <td>
                                                            {{  ($itemss5['balance'] > 0) ? number_format($itemss5['balance'],2) : number_format(0,2) }}
                                                        </td>
                                                        <td>
                                                            {{  ($itemss5['balance'] < 0) ? number_format(abs($itemss5['balance']),2) : number_format(0,2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach 
                                            @endif
                                           
                                        @endforeach 
                                    @endif
                                   
                                    <!--transaction_account-->
                                    @if(isset($items5['transaction_account']) && count($items5['transaction_account']) > 0)
                                        @foreach($items5['transaction_account'] as $itemss4)
                                            <tr data-node-id="" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5 }}" class="sub  @if($itemss4['balance'] == 0) transaction_acc @endif" >
                                                <td>
                                                    <a href="{{ url('/account/account/'.$itemss4['id'] )}}" target="_blank">{{ $itemss4['name'] }}</a>
                                                </td>
                                                <td>
                                                    {{  ($itemss4['balance'] > 0) ? number_format($itemss4['balance'],2) : number_format(0,2) }}
                                                </td>
                                                <td>
                                                    {{  ($itemss4['balance'] < 0) ? number_format(abs($itemss4['balance']),2) : number_format(0,2) }}
                                                </td>
                                            </tr>
                                        @endforeach 
                                    @endif
                                   
                                @endforeach 
                            @endif
                            
                            <!--transaction_account-->
                            @if(isset($items4['transaction_account']) && count($items4['transaction_account']) > 0)
                                @foreach($items4['transaction_account'] as $itemss3)
                                    <tr data-node-id="" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4 }}" class="sub  @if($itemss3['balance'] == 0) transaction_acc @endif" >
                                        <td>
                                            <a href="{{ url('/account/account/'.$itemss3['id'] )}}" target="_blank">{{ $itemss3['name'] }}</a>
                                        </td>
                                        <td>
                                            {{  ($itemss3['balance'] > 0) ? number_format($itemss3['balance'],2) : number_format(0,2) }}
                                        </td>
                                        <td>
                                            {{  ($itemss3['balance'] < 0) ? number_format(abs($itemss3['balance']),2) : number_format(0,2) }}
                                        </td>
                                    </tr>
                                @endforeach 
                            @endif
                           
                        @endforeach 
                    @endif
                    
                    <!--transaction_account-->
                    @if(isset($items3['transaction_account']) && count($items3['transaction_account']) > 0)
                        @foreach($items3['transaction_account'] as $itemss2)
                            <tr data-node-id="" data-node-pid="{{ $key.'.'.$key2.'.'.$key3 }}" class="sub @if($itemss2['balance'] == 0) transaction_acc @endif" >
                                <td>
                                    <a href="{{ url('/account/account/'.$itemss2['id'] )}}" target="_blank">{{ $itemss2['name'] }}</a>
                                </td>
                                <td>
                                    {{  ($itemss2['balance'] > 0) ? number_format($itemss2['balance'],2) : number_format(0,2) }}
                                </td>
                                <td>
                                    {{  ($itemss2['balance'] < 0) ? number_format(abs($itemss2['balance']),2) : number_format(0,2) }}
                                </td>
                            </tr>
                        @endforeach 
                    @endif

                @endforeach 
            @endif
            
            <!--transaction_account-->
            @if(isset($items['transaction_account']) && count($items['transaction_account']) > 0)
                @foreach($items['transaction_account'] as $itemss1)
                    <tr data-node-id="" data-node-pid="{{ $key.'.'.$key2 }}" class="sub @if($itemss1['balance'] == 0) transaction_acc @endif" >
                        <td>
                            <a href="{{ url('/account/account/'.$itemss1['id'] )}}" target="_blank">{{ $itemss1['name'] }}</a>
                        </td>
                        <td>
                            {{  ($itemss1['balance'] > 0) ? number_format($itemss1['balance'],2) : number_format(0,2) }}
                        </td>
                        <td>
                            {{  ($itemss1['balance'] < 0) ? number_format(abs($itemss1['balance']),2) : number_format(0,2) }}
                        </td>
                    </tr>
                @endforeach 
            @endif
            
    
        @endforeach 
    @endif
    
    <!--transaction_account-->
    @if(isset($data['transaction_account']) && count($data['transaction_account']) > 0)
        @foreach($data['transaction_account'] as $itemss)
            <tr data-node-id="" data-node-pid="{{ $key }}" class="sub @if($itemss['balance'] == 0) transaction_acc @endif" >
                <td>
                    <a href="{{ url('/account/account/'.$itemss['id'] )}}" target="_blank">{{ $itemss['name'] }}</a>
                </td>
                <td>
                    {{  ($itemss['balance'] > 0) ? number_format($itemss['balance'],2) : number_format(0,2) }}
                </td>
                <td>
                    {{  ($itemss['balance'] < 0) ? number_format(abs($itemss['balance']),2) : number_format(0,2) }}
                </td>
            </tr>
        @endforeach 
    @endif
    
@endforeach
