<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="exampleModalLabel">Add Sale Type</h4>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('SalesOrderController@sale_store_partial'), 'method' => 'post', 'id' =>
            'add_purchase_form', 'files' => true ]) !!}

            <div class="form-group">
                {!! Form::label('prefix','Prefix') !!}
                {!! Form::text('prefix', null, ['class' => 'form-control','required']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('name',__('Sales Type')) !!}
                {!! Form::text('name', null, ['class' => 'form-control','required']); !!}
            </div>
            <div class="form-group">
                {!! Form::label('purchase_type',__('Purchase Type')) !!}
                <select name="purchase_type" class="form-control" required>
                    @foreach ($purchase_type as $p)
                        <option value="{{$p->name}}">{{$p->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
            </form>
        </div>
        
    </div>
</div>