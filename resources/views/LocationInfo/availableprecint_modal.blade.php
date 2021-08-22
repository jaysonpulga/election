<div id="AllPrecint_modal" tabindex="-1"  class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h5>Search Precinct</h5>
		  </div>
		  <div class="modal-body">
			<div class="box-body">
				<div class="row">
				
						<table id="precintTables"  class="table table-bordered table-striped" >
							<thead>
								<tr>
								 <th></th>
								 <th>precinct</th>
								 </tr>
							</thead>
							
							<tbody></tbody>
						</table>

				</div>	
            </div>
		  </div>
		  <div class="modal-footer">
			  <div class='row'>
				  <div class="col-sm-12">
					  <table width="100%" style="width:100%" border="0" id='table'>
						  <tr>
								<td>	
									<span class="pull-left">
										<button type="button"   type="button"    class="btn btn-default  btn-flat select_precinct" disabled>
											 Select precinct
										</button>
									</span>
								</td>
								<td>
								<span class="pull-right">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</span>	
								<td>
								
						  </tr>
					  </table>
				 </div> 
			 </div>
		 </div>
		</div>
	  </div>
 </div> 
 
  <script>
$('#btnSearchPrecint').click(function() {
	

	var city_val = $("#city option:selected" ).val();
	var barangay_val = $("#barangay2 option:selected" ).val();
	var precinctVal = $('#precinctVal').val();
	$(".select_precinct").attr("disabled", true);

	if(city_val == "" || barangay_val == "")
	{
		
		swal({
				type:'warning',
				title:"",
				text:"Please Select City and  Barangay"
			})
		
		return false;
	}
	else{
		

			SearchPrecint(city_val,barangay_val,precinctVal);
			
			var options = { backdrop : 'static'}
			$('#AllPrecint_modal').modal(options); 
			
			
		
	}

});
</script>

<script>
var tablemember;

function SearchPrecint(city_val,barangay_val,precinctVal)
{
	
	
	
	
		
	tablemember =	 $('#precintTables').DataTable({ 
				"order":[],
				"processing": true, //Feature control the processing indicator.
				"ajax": {
					"url": "{{asset('searchAvailablePrecint')}}",
					"data" : {_token: "{{csrf_token()}}",city:city_val,barangay:barangay_val,search:precinctVal},
					"type": "post",
				},
					"bFilter": true,
				  "bDestroy": true,
				  "aLengthMenu": [[5,7,10, 15, 25, 50, 75, -1], [5,7,10, 15, 25, 50, 75, "All"]],
				  "iDisplayLength": 10,
				  
				  "columns"    : [
					
					
					
					{'data': 'checkbox'},
					{'data': 'precinct'}

					
					
				],
				"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                	
					// Find index of specifi object using findIndex method.
					var objIndex = temporary_array.findIndex(obj => obj.precinct  === aData["precinct"] );
				
					 if (objIndex < 0 ) {
						
					}
					else{
						
						$(nRow).remove();
						$(nRow).hide();
					}
				
				
				}	
				

			});
			
    


}
</script>

<script type="text/javascript">
$('#precintTables').on('change', 'input[name="selected_checkbox"]', function() {
		
	
	
	if ($(this).is(":checked")) 
	{
	  $(this).closest("tr").addClass('addcolorThis'); 
    }
	else
	{
		
		$(this).closest("tr").removeClass('addcolorThis');		
	}
	
	
	var checked = $('#precintTables').find('input[name=selected_checkbox]:checked').length;
	
	

	if(checked > 0)
	{
		$(".select_precinct").attr("disabled", false);
	}
	else{
		$(".select_precinct").attr("disabled", true);
	}
	
	
});  
</script>


<script type="text/javascript">
$(document).on('click', '.select_precinct', function(e){
e.preventDefault(); 
    

	 
     $.each($("input[name='selected_checkbox']:checked"), function(){
           precint_id =  $(this).attr('data-precint_id');
		   name =  $(this).attr('data-name');
		   
		   
		   	var value =  {
				  "precint_id" : precint_id,
				  "name"	   : name,
				  "action": '<a href="javascript:void(0)" class="delete" data-precint_id="'+precint_id+'"  title="remove Precinct" ><i class="fa fa-fw fa-ellipsis-v"></i></a>',
				  "status": 'AddNew',
				  
				};
	
			if(temporary_array.contains(value)) 
			{ } 
			else
			{ 
				temporary_array.push(value);
			}
				   
		   
     });
	 
		
	
	precinttableLists();
	
	$('#preview').css('display','block');
	$('#AllPrecint_modal').modal('hide'); 

});
</script>




<style>
.dataTables_filter input {
    width: 300px !important;
}
.addcolorThis {
    background-color: #f8fb4699 !important;
}
</style>