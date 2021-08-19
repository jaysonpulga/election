<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ $title }}</title>
<style>
body
{
   font-family: Arial, Helvetica, sans-serif;
   font-size: 13px;
}
 
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
}

thead
{
	border: 1px solid #ddd;
}
table th, #table td {
  text-align: left;
  padding: 8px;
}

table tr:nth-child(even) {
  background-color: #f2f2f2;
}

#company_name
{
	font-size : 14px;
	font-weight : bold;
}

#company_address
{
	font-size : 14px;
	font-weight : normal;
}

.heading
{
	font-size : 14px;
	font-weight : bold;
}
</style>

</head>
<body>

<div  style='clear:both;width:100%'> 
	<div><center><span id='company_address'><b>{{$title}}</b></span></center></div>
</div>  

<div style='clear:both;width:100%'> 
	<div style='float:left;'>City : {{$header_data->city}}</span></div>
	<div style='float:right;'><span >User ID: {{$user_id}} </span></div>
</div>  

<div  style='clear:both;width:100%'> 
	<div style='float:left;'>Barangay : {{$header_data->barangay}}</span></div>
	<div style='float:right;'><span>{{$date_genarated}} </span></div>
</div>  

<br>
<br>


<table width="100%" style="width:100%" border="0" id='table_data'>
	<thead>
	<tr>
	  	  <th>Leader</th>
		  <th>Coordinator</th>
		  <th>Province</th>
		  <th>Cities/Municipalities</th>
		  <th>Barangay</th>
	</tr>
	</thead>
	
	@if(!empty($table_data))
			 <tbody>
			    
                      
					@foreach ($table_data as $datas)
					
				

		 
						<tr>
							<td> {{$datas->leader }} </td>
							<td> {{$datas->coordinator }} </td>
							<td> {{$datas->province }} </td>
							<td> {{$datas->city }} </td>
							<td> {{$datas->barangay }} </td>

						</tr>
					  
					 @endforeach
			 </tbody>
			       
			 
			 
		@else	 
			 
			<tbody><tr><td colspan="7"><center>No data available in table</center></td></tr></tbody>
		
		@endif
	
	
	
	
	
</table>
 

</body>
</html>