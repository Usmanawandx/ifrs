<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
      @if($purchase->type == 'purchase_order' || $purchase->type == 'purchase_return' )
    @include('purchase.partials.show_details')
    @else
     @include('purchase.partials.show_details1')
     @endif
    <div class="modal-footer" style="text-align:center !important; ">
      <button type="button" class="btn btn-primary no-print print_btn" aria-label="Print" 
      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )
      </button>
      @if($purchase->type == 'Purchase Requisition')
    <button style="color: white"  class="btn btn-primary no-print btn-flat d_button" >Download JPEG</button>
    <button type="button" style="color: white"  class="btn btn-primary no-print btn-flat export_button">Export Excel</button>

    <a style="color: white"  class="btn btn-primary no-print btn-flat" id="edit_btn" href="{{action('PurchaseOrderController@edit_req', [$purchase->id])}}">Edit</a>
    @if($purchase->purchase_order_ids == 0)
    
    <a class="btn btn-primary " href="/purchase-order/create?convert_id={{$purchase->id}}">Convert To Purchase Order</a>
    @endif
    @elseif($purchase->type == 'purchase_order')
    <button style="color: white"  class="btn btn-primary no-print btn-flat d_button" >Download JPEG</button>
    <button type="button" style="color: white"  class="btn btn-primary no-print btn-flat export_button">Export Excel</button>
    <a style="color: white"  class="btn btn-primary no-print btn-flat" id="edit_btn" href="{{action('PurchaseOrderController@edit', [$purchase->id])}}">Edit</a>

    @if($purchase->po_id == null || $purchase->po_id == 0)
      <a class="btn btn-primary " href="/purchases/create?convert_id={{$purchase->id}}">Convert To Grn</a>
    @endif
    @elseif($purchase->type == 'Purchase_invoice')
    <button style="color: white"  class="btn btn-primary no-print btn-flat d_button" >Download JPEG</button>
    <button type="button" style="color: white"  class="btn btn-primary no-print btn-flat export_button">Export Excel</button>

    <a style="color: white"  class="btn btn-primary no-print btn-flat" id="edit_btn" href="{{action('PurchaseOrderController@edit_invoice', [$purchase->id])}}">Edit</a>
    @elseif($purchase->type == 'purchase_return')
    <button style="color: white"  class="btn btn-primary no-print btn-flat d_button" >Download JPEG</button>
    <button type="button" style="color: white"  class="btn btn-primary no-print btn-flat export_button">Export Excel</button>
    <a style="color: white"  class="btn btn-primary no-print btn-flat" id="edit_btn" href="{{action('CombinedPurchaseReturnController@edit', $purchase->id)}}" >Edit</a>
    @endif
    
      <button type="button" class="btn btn-danger no-print close_btn" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div>
</div>
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script src="{{ asset('js/html2canvas.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
	$(document).ready(function(){
		var element = $('div.modal-xl');
		__currency_convert_recursively(element);
		
		if({{ request()->isprint ?? 0 }} == true){
		    $(document).find('.modal-dialog').addClass('hide');
		    setTimeout(function(){
		        $(".print_btn").click();
		    },1000)
		    $(".close_btn").click();
		  //  $(document).find('.modal-dialog').removeClass('hide');
		}
		
		
	});
	$("body").on("keydown", function(e){
  if(e.altKey && e.which == 69) {
    var href = $("#edit_btn").attr("href");
    window.location.href = href;
  }
});
var invoice = document.getElementsByClassName("photo")[0];
var d_btn = document.getElementsByClassName("d_button")[0];
d_btn.addEventListener("click",()=>{
domtoimage.toJpeg(invoice).then((data)=>{
var link  = document.createElement("a");
var name = '<?php echo $purchase->ref_no ?>';

link.download = name+".jpeg";
link.href = data;
link.click();
});
});
function html_table_to_excel(type)
    {
        var data = document.getElementById('employee_data');

        var file = XLSX.utils.table_to_book(data, {sheet: "sheet1"});

        var namefor = '<?php echo $purchase->ref_no ?>';

        XLSX.write(file, { bookType: type, bookSST: true, type: 'base64' });

        XLSX.writeFile(file, namefor+"." + type);
    }

    var export_button = document.getElementsByClassName('export_button')[0];

    export_button.addEventListener('click', () =>  {
        html_table_to_excel('xlsx');
    });
</script>