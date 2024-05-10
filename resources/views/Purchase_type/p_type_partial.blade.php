<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title" id="exampleModalLabel">Add Purchase Type</h5>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('PurchaseOrderController@purchase_store_partial'), 'method' => 'post', 'id'
            => 'add_purchase_form', 'files' => true ]) !!}
            <div class="form-group">
                {!! Form::label('Prefix') !!}
                {!! Form::text('prefix', null, ['class' => 'form-control']); !!}
            </div>
            <div class="form-group">
                {!! Form::label('Remarks',__('Type')) !!}
                {!! Form::text('type', null, ['class' => 'form-control', 'rows' => 2]); !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            {!! Form::close() !!}
            </form>
        </div>
        
    </div>
</div>