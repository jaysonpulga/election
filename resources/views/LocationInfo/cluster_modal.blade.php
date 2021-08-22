<div id="cluster_modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Cluster</h4>
		  </div>
		  <div class="modal-body">
		  <form id="ClusterForm">
			@csrf
					
			<div class="box-body">
			
              
					<div class="form-group">
					  <label for="account_type" class="col-sm-3 control-label">City</label>
					  <div class="col-sm-9">
						  <select class="form-control" id="city" name="city" required>
							<option value="">Select City</option>
							@foreach($cities as $city)
								<option value="{{$city->id}}">{{$city->name}}</option>
							@endforeach
						  </select>
					  </div>
					</div>
					
					<div style="display:block;margin-bottom:12px;clear:both"></div>
					
					<div class="form-group">
					  <label for="account_type" class="col-sm-3 control-label">Barangay</label>
					  <div class="col-sm-9">
						  <select class="form-control" id="barangay2" name="barangay2" required disabled>
						  </select>
					  </div>
					</div>
					
					<div style="display:block;margin-bottom:12px;clear:both"></div>
					
					<div class="form-group">
					  <label for="account_type" class="col-sm-3 control-label">Enter Cluster</label>
					  <div class="col-sm-9">
						<input type="text"   class="form-control" id="clusterVal" name="clusterVal"  maxlength="5" required>
					  </div>
					</div>
					
					<div style="display:block;margin-bottom:12px;clear:both"></div>
					
					<div class="form-group">
					  <label for="account_type" class="col-sm-3 control-label">Enter Precinct</label>
					  <div class="col-sm-7">
						<input type="text"   class="form-control" id="precinctVal" name="precinctVal"  maxlength="80">
					  </div>
					  <div class="col-sm-2">
						<button type="button"   class="btn btn-success btn-primary  btn-flat" id="btnSearchPrecint" >SEARCH</button>
					  </div>
					</div>

					<div style="display:block;margin-bottom:25px;clear:both"></div>
					
					
					<div id="preview">
						<table  class="table precinttableLists"  width="100%" border="0" width="100%" >
							<thead>
								<tr>
									  <th>Precint</th>
									  <th></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					
				
					
				
            </div>
		  
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" form="ClusterForm" class="btn btn-success btn-primary  btn-flat submitCluster">Save</button>
		  </div>
		 </form>
		</div>
	  </div>
 </div>
<!-- /.content -->


<script type="text/javascript">
$(document).on('click', '.btnAddCluster', function(e){
	e.preventDefault(); 
	$(".submitCluster").attr("disabled", true);
	 temporary_array = [];
	 $('#preview').css('display','none');
	
	

	var count =  "<?php echo (count($cities));  ?>";
	
	if(count == 1){
		$('select[id="city"]').val("<?php echo $cities[0]->id; ?>");
		$('select[id="city"]').trigger("change");
	}
	else{
		$('#city').val("");
		$('select[id="barangay"]').attr('disabled', 'disabled');
	}

	$('#barangay').val("");
	$('#precinct').val("");
	$('#action').val("create_new");
	var options = { backdrop : 'static'}
	$('#cluster_modal').modal(options); 

});
</script>

<!--- DELETE  -->
<script>
$(document).on('click','.delete', function(e){
e.preventDefault();
	
	
	
	
	var precint_id = $(this).attr('data-precint_id');
	
	// Find index of specifi object using findIndex method.
	var objIndex = temporary_array.findIndex(obj => obj.precint_id  === precint_id );
	


	// get the  status value if  is equal to current
	var status = temporary_array[objIndex].status;
	var name = temporary_array[objIndex].name;
	var id = temporary_array[objIndex].id;
	
	

	swal({
        type:'question',
        title:'',
        text:'Do you really wish to remove this Precinct',
        showCancelButton: true,
        confirmButtonText: 'Yes',
		confirmButtonColor: '#d33',
        cancelButtonText: 'No'
    }).then(function(isConfirm){

		// remove on array
		temporary_array.splice(objIndex,1);
		
		// reload table
		precinttableLists();
	
	
	});
	
	
});
</script>

<script>
$('#ClusterForm').on('submit', function(event){
event.preventDefault();
	
	

	

	var barangay_val = $("#barangay2 option:selected" ).val();
	var clusterVal = $("#clusterVal").val();
	
	
	var GroupId = $("#GroupId").val();
	
		
	if( temporary_array.length == 0){
		
		swal({
			type:'warning',
			text:"Please select data"
		})
		return false;
		
	}
	
	
	$.ajax({
		url: "{{ asset('AddClusterinPrecints') }}",
		data : {cluster:clusterVal,barangay:barangay_val,array_precinct:temporary_array},
		headers:{'X-CSRF-TOKEN':$('meta[name="_token"]').attr('content')},
		type : 'POST',
		beforeSend:function(){
			
			 $("body").waitMe({
				effect: 'timer',
				text: 'Saving  Details ........ ',
				bg: 'rgba(255,255,255,0.90)',
				color: '#555'
			}); 
		},
		success:function(data){
			$('body').waitMe('hide');
			
			if(data =="save")
			{
				
				swal({
					type:'success',
					title:"Data Saved!",
					text:""
				}).then(function(){
					
						temporary_array = [];
						delete_item_array =[];
						
						reload_table();
						$('#cluster_modal').modal('hide'); 
						
				});
				
			}
		
		},
		error:function(){
			$('body').waitMe('hide');
			swal({
				type:'error',
				title:"Oops..",
				text:"Internal error"
			})
		}
				
	});


});
</script>