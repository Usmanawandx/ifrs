<div class="row m-5">
    <div class="col-md-12">
        <div class="col-sm-6">
            <h5>
                <b>Date: </b>
                {{ $vehicle_rates['date'] }}
            </h5>
        </div>
        <div class="col-sm-6">
            <h5>
                <b>Transporter Name: </b>
                @foreach($contact as $c)
                  {{ ($c->id == $vehicle_rates['vehicle_id']) ? $c->supplier_business_name : '' }}
                @endforeach
            </h5>
        </div>
    </div>
    <br><br><br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
            <table class="table table-bordered table-striped contractor_rates bg-gray">
                 <thead>
                    <tr>
                       <th>Sub Category Name</th>
                       <th>Transporter Rate</th>
                    </tr>
                 </thead>
                 <tbody>
                     @foreach($vehicle_child as $child)
                     <tr>
                         <td>
                            @foreach($sub as $s)
                              {{ ($s->id == $child->child_id) ? $s->name : '' }}
                            @endforeach
                         </td>
                         <td>
                            {{ $child->rate }}
                         </td>
                     </tr>
                    @endforeach
                 </tbody>
            </table>
        </div>
        </div>
    </div>
    
</div>
            