@extends('layouts.main')
@section('content')

<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

<style>
.btn {
    border-radius: 0px !important;
}

.invalid-feedback {
    width: 100%;
    margin-top: .25rem;
    font-size: 13px;
    color: #e3342f;
}
.highlight {
    border: 1px solid red;
}


table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
}

table th,  td {
  text-align: left;
  padding: 3px;

}


table th {
  font-size:12px;
}

table td {
  font-size:12px;
}

table#mainDatatables td {
  font-size:12px !important;
}

table.dataTable tbody tr.selected {
  background-color: #B0BED9 !important;
}


.dt-body-center
{
  text-align : right; 
}
</style>


<!-- Main content -->
<section class="content">
<!-- Main row -->
<div class="row">
	<div class="col-lg-12">

		<div class="box">

			
		   <div class="box-header with-border margin">
				<div class='row'>
					<div class="col-lg-8">
						<span class="pull-left">
						<table width="100%" style="width:100%" border="0" id='table' cellspacing="1" cellpadding="2">
						
								<tr >
									<td width="22%"><label for="report_type" class="control-label" >Type of Report</label></td>
									<td width="2%">:</td>
									<td colspan="2">
										<select class="form-control input-sm" id="report_type" name="report_type">
											 <option value="">--- Report  ---</option>
											 
											 <option value="projected_voters">Projected Votes</option>
											 <option value="voters_gender">Voter's Gender</option>
											 <option value="voters_age">Voter's Age</option>
											 <option value="voters_religion">Voter's Religion</option>
											 <option value="Barangay_Coordination_Report"> Barangay Coordination Report</option>
											
									    </select>
									</td>
									
						
									
								</tr>
								
								
								<tr >
									<td width="22%"><label for="report_level" class="control-label" id="reportname" >Report Level</label></td>
									<td width="2%">:</td>
									<td colspan="2">
									
										<span id="typeLevel">
											<select class="form-control input-sm" id="report_level" name="report_level" disabled="disabled" >
												 <option value="">--- Level  ---</option>
												 
												 <option value="Province">Province</option>
												 <option value="City">City</option>
												 <option value="Barangay">Barangay</option>
												 <option value="Precinct">Precinct</option>
											</select>
										</span>
										
										
										<span id="BarangayCoordination" style="display:none">
											<select class="form-control input-sm" id="barangayData" name="barangayData" >
												 <option value="">---All Barangay---</option>
												  @foreach($barangays as $barangay)
														<option value="{{$barangay->id}}"    >{{$barangay->name}}</option>
													@endforeach
												
											</select>
										</span>
										
										
									</td>
									
									<td> 
										<a href="javascript:void(0)" type="button" class="btn btn-success btn-sm  btn-flat submit">
											Submit
										</a>
										
										<a href="javascript:void(0)" type="button" class="btn btn-default btn-sm  btn-flat clear">
											Clear
										</a>
									</td>
									
								</tr>
								
								<script>
								
									$("#report_type").change(function () {
										
										var val = this.value;
										if(val == "Barangay_Coordination_Report" || val == "")
										{
											$("#report_level").val("");
											$("#report_level").attr("disabled","disabled");
											
											$("#typeLevel").css("display","none");
											$("#BarangayCoordination").css("display","block");
											$("#reportname").empty().html('Select Barangay');
											
											$("#printButton").empty().html('<button type="button" class="btn btn-danger btn-flat printDataJs">PRINT</button>');
											
											
											
												$("#ExcelButton").empty().html('<button  type="button" class="btn btn-warning btn-flat" onclick="ExportToExcel()">EXPORT</button>');
											
										
										}
										else
										{
											$("#report_level").removeAttr('disabled');
											$("#typeLevel").css("display","block");
											$("#BarangayCoordination").css("display","none");
											$("#reportname").empty().html('Report Level');
											$("#printButton").empty().html('<button type="button" class="btn btn-danger btn-flat printData">PRINT</button>');
											
											$("#ExcelButton").empty().html('<button type="button" class="btn btn-warning btn-flat exportData">EXPORT</button>');
											
										
										}
									
									});
																
								</script>
								<!--
								<tr>
									<td></td>
									<td width="5%" > 
										<label for="address1" class="control-label" style="margin-right:10px" >From</label>
									</td>
									<td> 
										<input type="text" class="form-control required_field input-sm" id="from_date" name="from_date">
									</td>
									<td width="5%"> 
										<label for="address1" class="control-label pull-right" style="margin-right:10px" >To</label>
									</td>
									<td> 
										<input type="text"   class="form-control required_field input-sm" id="to_date" name="to_date"  onkeyup="clear_onfield('to_date')" maxlength="5">
									</td>
									
									<td> 
										<a href="javascript:void(0)" type="button" class="btn btn-success btn-sm  btn-flat submit">
											Submit
										</a>
										
										<a href="javascript:void(0)" type="button" class="btn btn-default btn-sm  btn-flat clear">
											Clear
										</a>
									</td>
									<script>
										 $(function () {
										   var d = new Date();
										   var currMonth = d.getMonth();
										   var currYear = d.getFullYear();
										   var startDate = new Date(currYear, currMonth, 1);
									    
									        /*
										   $("#from_date").datepicker({ format: 'mm/dd/yyyy',autoClose: true, });
										   $("#from_date").datepicker("setDate", startDate);
										   
										   $('#to_date').datepicker({ format: 'mm/dd/yyyy',autoClose: true, });
										   $("#to_date").datepicker("setDate", d);
										   */
										   
										    //$("#from_date").datepicker("setDate", startDate);
										      //$("#to_date").datepicker("setDate", d);
										   
										   //Date picker
                                            $('#from_date').datepicker({
                                              autoclose: true,
                                              format: 'mm/dd/yyyy',
                                            });
                                            
                                            $("#from_date").datepicker("setDate", startDate);
                                            
                                             $('#to_date').datepicker({
                                              autoclose: true,
                                              format: 'mm/dd/yyyy'
                                            });
										   $("#to_date").datepicker("setDate", d);
										});
									</script>
									

								</tr>
								-->
								
								
						</table>
						</span>
					</div>
					
						
					<div class="col-lg-4">
						<span class="pull-right">
							<table  class="table  borderless"  width="100%" border="0" id="tablePrint" style="display:none" >
								<tr>
									<td>
									    
									    
									    
									   
									   
				                        
				                        
									   
									   <span id="ExcelButton"></span>

									   <span id="printButton"></span>
									   
									   
									</td>
								</tr>
							</table>	
						</span>
					</div>
					
					
					
				</div>
			</div>
			
			 <!-- /.box-header -->
            <div class="box-body margin">
				<table id="mainDatatables"  class="table  table-hover" cellspacing="0" width="100%" ></table>
            </div>
            <!-- /.box-body -->
		
		
		
		</div>	
	</div>	  
</div>
<!-- /.row (main row) -->
</section>
<!-- /.content -->

<!--- ////   PRINT FUNCTION   /////////// -->
 <script type="text/javascript">
$(document).on('click', '.clear', function(e){
e.preventDefault();

location.reload();
});
</script>

 <script type="text/javascript">
$(document).on('click', '.submit', function(e){
e.preventDefault();

var report_type = $("#report_type option:selected" ).val();
var report_level = $("#report_level option:selected" ).val();


if(report_type == "Barangay_Coordination_Report"){

loadData(report_type);
return false;	
}


if(report_type == "" ||  report_level ==""){
	
	 swal({
			type:'warning',
			title:"",
			html:"Please Select <br> <u>Type of Report </u>  And  <u>Report Level</u>"
		})
	
	return false;
}


loadData(report_type);

});
</script>

<script type="text/javascript">
function loadData(report_type)
{
    
    if(report_type == "voters_gender")
    {
         var url = "{{ asset('voters_gender')}}";
    }
    else if(report_type == "voters_age")
    {
         var url = "{{ asset('voters_age')}}";
    }
	else if(report_type == "voters_religion")
    {
        var url = "{{ asset('voters_religion')}}";
    }
	else if(report_type == "projected_voters")
    {
        var url = "{{ asset('projected_voters')}}";
    }
	else if(report_type == "Barangay_Coordination_Report")
    {
        var url = "{{ asset('Barangay_Coordination_Report')}}";
    }
	
	
	
    
    $.ajax({
				url     : url,
                type    : "POST",
                headers :{"X-CSRF-TOKEN":$('meta[name="_token"]').attr('content')},
                data	: {report_level:$("#report_level option:selected" ).val(),barangayData:$("#barangayData option:selected" ).val()},
        		beforeSend:function(){
                     $("body").waitMe({
                        effect: 'timer',
                        text: 'Please wait ........ ',
                        bg: 'rgba(255,255,255,0.90)',
                        color: '#555'
                    }); 
                },
				success: function(json){
					
					$('body').waitMe('hide');
                    $("#mainDatatables").empty().html(json.table);
					
					
                    $("#data_form").val(JSON.stringify(json.table));
					$("#data_form_excel").val(JSON.stringify(json.excel));
					$("#data_report_level").val(json.report_level);
				
					$("#tablePrint").css('display','inline');
					
				
				},
				error:function(xhr, status, error){
					
					
                    $('body').waitMe('hide');
                    swal({
                        type:'error',
                        title:"Oops..",
                        text:""
                    })
                }
				
		});

    
    
}
</script>


<!--- ////   PRINT FUNCTION   /////////// -->
 <script type="text/javascript">
$(document).on('click', '.printData', function(e){
e.preventDefault();


var report_type = $("#report_type option:selected" ).val();
if(report_type == "voters_gender")
{
	 var url = "{{ asset('print_voters_gender')}}";
}
else if(report_type == "voters_age")
{
	 var url = "{{ asset('print_voters_age')}}";
}
else if(report_type == "voters_religion")
{
	var url = "{{ asset('print_voters_religion')}}";
} 
else if(report_type == "projected_voters")
{
	var url = "{{ asset('print_projected_voters')}}";
}
else if(report_type == "Barangay_Coordination_Report")
{
	var url = "{{ asset('print_Barangay_Coordination_Report')}}";
}

$("#print_Datax").attr('action',url);
$("#print_Datax" ).submit();

});
</script>

<!--- ////   PRINT FUNCTION   /////////// -->
 <script type="text/javascript">
$(document).on('click', '.exportData', function(e){
e.preventDefault();

var report_type = $("#report_type option:selected" ).val();
if(report_type == "voters_gender")
{
	 var url = "{{ asset('excel_voters_gender')}}";
}
else if(report_type == "voters_age")
{
	 var url = "{{ asset('excel_voters_age')}}";
}
else if(report_type == "voters_religion")
{
	var url = "{{ asset('excel_voters_religion')}}";
} 
else if(report_type == "projected_voters")
{
	var url = "{{ asset('excel_projected_voters')}}";
}
else if(report_type == "Barangay_Coordination_Report")
{
	var url = "{{ asset('excel_Barangay_Coordination_Report')}}";
}


$("#print_Datax").attr('action',url);
$("#print_Datax" ).submit();

});
</script>




<!--- ////   PRINT FUNCTION   /////////// -->
 <script type="text/javascript">
$(document).on('click', '.printDataJs', function(e){
e.preventDefault();


	var barangayData = $("#barangayData option:selected" ).text();

	console.log('Start downloading');
	
	  $("body").waitMe({
			effect: 'timer',
			text: 'Start Downloading Please Wait ........ ',
			bg: 'rgba(255,255,255,0.90)',
			color: '#555'
		}); 

	setTimeout(()=>{
		StartDownLoad(barangayData);
	},200)

     



});


function StartDownLoad(barangayData)
{
	
	  var doc = new jsPDF()
	
        // Simple data example
		/*
        var head = [['ID', 'Country', 'Rank', 'Capital']]
        var body = [
          [1, 'Denmark', 7.526, 'Copenhagen'],
          [2, 'Switzerland', 7.509, 'Bern'],
          [3, 'Iceland', 7.501, 'Reykjavík'],
        ]
		*/
        //doc.autoTable({ head: head, body: body })

        // Simple html example
		if(barangayData == "---All Barangay---")
		{
			barangayData ="ALL BARANGAY"
		}

		 doc.setFontSize(13)
		  doc.text("Barangay Coordination Report: "+barangayData+" ", 14, 15)
		  doc.setFontSize(11)
		  doc.setTextColor(100)

		var totalPagesExp = '{total_pages_count_string}'
		doc.setFontSize(5);
	
		doc.autoTable({
		columnStyles: {
      id: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
    },	
		html: '#mainDatatables',
		showHead: 'firstPage',
		 startY: 22,
		  didDrawPage: function (data) {
			  // Header
			  doc.setFontSize(5)
			
			 doc.setFontSize(5)

			  // Footer
			  var str = 'Page ' + doc.internal.getNumberOfPages()
			  // Total page number plugin only available in jspdf v1.0+
			  if (typeof doc.putTotalPages === 'function') {
				//str = str + ' of ' + doc.putTotalPages()
				str = str;
			  }
			  
			  
		
			  //if (typeof doc.putTotalPages === 'function') { doc.putTotalPages(totalPagesExp); }
			  
			  
			  doc.setFontSize(10)
			  // jsPDF 1.4+ uses getWidth, <1.4 uses .width
			  var pageSize = doc.internal.pageSize
			  var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight()
			  doc.text(str, data.settings.margin.left, pageHeight - 10)
			},

	  })
	  

 

        //doc.save('table.pdf')
		
		doc.save("Barangay_Coordination_Report.pdf", {returnPromise:true}).then(DoneDownload());
}

function DoneDownload()
{
	
	$('body').waitMe('hide') ;
	
		 swal({
			type:'success',
			title:"",
			html:"Done File Downloaded"
		})
		
		
		console.log('Done Download');
	
}
</script>


<form id="print_Datax" target="_blank" action="" method="POST">
 @csrf
<input type="hidden"  id="data_form" name="data_form" />
<input type="hidden"  id="data_form_excel" name="data_form_excel" />
<input type="hidden"  id="data_report_level" name="data_report_level" />
</form>


<script>

function ExportToExcel(type="xlsx", fn, dl) {
       var elt = document.getElementById('mainDatatables');
       var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
       return dl ?
         XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
         XLSX.writeFile(wb, fn || ('Barangay_Coordination_Report.' + (type || 'xlsx')));
    }  
</script>

@endsection
