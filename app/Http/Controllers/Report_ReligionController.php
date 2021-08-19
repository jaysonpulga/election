<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\cities;

use App\campaign_groups;
use App\campaign_group_members;


use Auth;
use Hash;
use PDF;
use App\Exports\Excel_voters_religion;
use Maatwebsite\Excel\Facades\Excel;


use App\Exports\Excel_voters_projected_province;
use App\Exports\Excel_voters_projected_city;
use App\Exports\Excel_voters_projected_barangay;
use App\Exports\Excel_voters_projected_precint;

class Report_ReligionController extends Controller
{

	public function voters_religion2(Request $request)
	{
		
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		
		 $from_date = Carbon::parse(@$request->from_date)->format('Y-m-d');
	     $to_date =  Carbon::parse(@$request->to_date)->format('Y-m-d');
		
		
		
		
		$select ="t1.*,t2.name as religion";
		$getallReligions = \DB::table('votersinfomations as t1')
		->select(\DB::raw($select))
		->leftJoin('religions AS t2','t2.id','=','t1.religion')
		->where('t1.created_at','>=',$from_date)
		->where('t1.created_at','<=',$to_date)
		->get();
		
		
		$ReligionArray= array();
		foreach($getallReligions  as $data)
		{
			if($data->religion == "")
			{
				$ReligionArray['No Religion'][] = $data;
			}
			else{
				$ReligionArray[$data->religion][] = $data;
			}

			
		}
		
		
		
		$div = "<thead>
					<tr>
					  <th></th>
					  <th>Vin Number</th>
					  <th>Name</th>
					  <th>Religion</th>
					</tr>
				</thead>";
		
		
		
		$data = array();
		if(!empty($ReligionArray))
		{
	
			$div .= "<tbody>";
			foreach ($ReligionArray as $gdata => $data) 
			{
					$div .= "<tr class='border_top'>";
					$div .= "<td colspan='10' style='background-color:#c5bfbf;font-size:15px' ><b>".$gdata."</b></td>";
					$div .=	"</tr>";
					$count = 1;
					foreach ($data as  $value) 
					{
						
							$age = \Carbon\Carbon::parse($value->dob)->age;
							
							$div .="<tr>";
								$div .="<td>".$count."</td>";
								$div .="<td>".$value->vin_number."</td>";
								$div .="<td>".$value->name."</td>";
								$div .="<td>".$value->religion."</td>";
							$div .="</tr>";
							$count++;
					}
					
					
			}
			$div .= "</tbody>";
		}
		else
		{
			$div .= "<tbody>";
			$div .="<tr>";
			$div .="<td colspan='10'><center>No data available in table</center></td>";
			$div .="</tr>";
			$div .= "</tbody>";
		}
		
		$output = array("table" => $div, 'excel' => $ReligionArray);
		return response()->json($output);
	}
	
	
	
		
	public function voters_religion(Request $request)
	{
		
		   
		$report_level = $request->report_level;
		
		if($report_level == "Province")
		{
			
			return $this->Province();
		}
		else if($report_level == "City")
		{
			
			return $this->City();
		}
		else if($report_level == "Barangay")
		{
			
			return $this->Barangay();
		}
		else if($report_level == "Precinct")
		{
			
			return $this->Precinct();
		}
		
			
	}
	
	
	##################################### PROVINCE ###################################################
	function Province()
	{
		
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
				

			
			
			$div = "<thead>
						<tr>
						  <th>Province</th>
						  <th>City/Municipality</th>
						  
						  <th><center>Registered Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Projected Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Undecided Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Deceased</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Abroad</center></th>
						  <th><center>%</center></th>
						</tr>
					</thead>";	
		
		$excel =  array();
		
		
		$select ="t1.id";
		$allvoters = \DB::table('votersinfomations as t1')		
		->select(\DB::raw($select))
		->get();
		
		
		$select ="t1.name,t1.id";
		$getallprovince = \DB::table('provinces as t1')		
		->select(\DB::raw($select))
		->get();
		

		$select ="t1.*,t1.name";
		$getallReligions = \DB::table('religions as t1')
		->select(\DB::raw($select))
		->get();

			
		$religions = array();
		foreach($getallReligions  as $data)
		{	
		
			$religions[] = (object) array(
							'id' => $data->id,
							'name' => $data->name,
						 );

		}
		

		$religions[] = (object) ['id' =>0,'name' => 'Undetermined'];
		
		$allreligions = (object)$religions;
		

		
		if($getallprovince && count($getallprovince) > 0)
		{
			
			$div .= "<tbody>";
				foreach($getallprovince as $province)
				{
					
					$select ="t1.name,t1.id";
					$getallcities = \DB::table('cities as t1')		
					->select(\DB::raw($select))
					->where('t1.province_id', '=',$province->id)
					->get();
					
					
					
					foreach($getallcities as $city)
					{
						

						
								 $div .="<tr style='background-color:#dddcdc;'  >";
									$div .="<td colspan='1'>".$province->name."</td>";
									$div .="<td colspan='12'>".$city->name."</td>";	
								$div .="</tr>";	
								
								
									$sub_countRegisterdVoters = 0;
									$sub_countRegisterdVoters_percent = 0;
									
									$sub_projected_voters = 0;
									$sub_projected_voters_percent = 0;
									
									$sub_undecided = 0;
									$sub_undecided_percent = 0;
									
									$sub_countDeceased = 0;
									$sub_countDeceased_percent = 0;
									
									$sub_countAbroad = 0;
									$sub_countAbroad_percent = 0;
								
								
								
								foreach($allreligions as $val)
								{	
										
										
										
										$coordinators = "( SELECT COUNT(a.coordinator_id) from coordinators as a 
															   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
															   where (b.religion = t1.religion) AND b.city_municipality = a.city
															 ) as coordinators ";
															 
											$leaders = "( SELECT COUNT(a.leader_id) from leaders as a 
															   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
															   where (b.religion = t1.religion) AND b.city_municipality = a.city
															 ) as leaders ";				 
											
											$members      = "(SELECT COUNT(b.id) from campaign_groups as a 
															   LEFT JOIN  campaign_group_members as b ON a.group_id = b.group_id
															   LEFT JOIN  votersinfomations as c ON c.id = b.user_id
															   where (c.religion = t1.religion) AND c.city_municipality = a.city
															 ) as members ";
											

											$select ="t1.name,t1.id,t1.status,{$coordinators},{$leaders},{$members}";
											$registerVoters = \DB::table('votersinfomations as t1')		
											->select(\DB::raw($select))
											->where('t1.religion', '=',$val->id)
											->where('t1.city_municipality', '=',$city->id)
											->get();
											
											$countRegisterdVoters = array();
											$countAbroad = array();
											$countDeceased = array();
											
											$Countcoordinators  = 0;
											$Countleaders = 0;
											$Countmembers = 0;
											
											foreach($registerVoters as $voters)
											{
												$countRegisterdVoters[] = $voters;
												
												if($voters->status == 1)
												{
													$countDeceased[] = $voters;
												}
												else if($voters->status == 2)
												{
													$countAbroad[] = $voters;
												}
												
												$Countcoordinators = $voters->coordinators;
												$Countleaders	   = $voters->leaders;
												$Countmembers	   = $voters->members;
												
											
											}
											
												$projected_voters = $Countcoordinators + $Countleaders + $Countmembers;
												$undecided = count($countRegisterdVoters) - $projected_voters;
											
												$sub_countRegisterdVoters = $sub_countRegisterdVoters + count($countRegisterdVoters);					
												$percent = (count($countRegisterdVoters) == 0 ) ? 0 : ((count($countRegisterdVoters) * 100) / count($allvoters));
												$sub_countRegisterdVoters_percent = $sub_countRegisterdVoters_percent + $percent;
											
												$sub_projected_voters = $sub_projected_voters +  $projected_voters;
												$projected_voters_percent  = ( count($countRegisterdVoters)  == 0 )  ? 0  : (($projected_voters * 100) / count($allvoters));
												$sub_projected_voters_percent = $sub_projected_voters_percent + $projected_voters_percent;
												
												$sub_undecided = $sub_undecided + $undecided;
												$undecided_percent  = ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( ($undecided * 100) / count($allvoters) );
												$sub_undecided_percent = $sub_undecided_percent + $undecided_percent;
												
												$sub_countDeceased = $sub_countDeceased + count($countDeceased);
												$countDeceased_percent  =  ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countDeceased) * 100) / count($allvoters) );
												$sub_countDeceased_percent = 	$sub_countDeceased_percent + $countDeceased_percent;	
												
												$sub_countAbroad = $sub_countAbroad + count($countAbroad);
												$countAbroad_percent = (count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countAbroad) * 100) / count($allvoters) );
												$sub_countAbroad_percent = $sub_countAbroad_percent + $countAbroad_percent;
									
									
											 $div .="<tr>";
											 
													$div .="<td></td>";
													$div .="<td>".$val->name."</td>";
													
													
													$div .="<td><center>".count($countRegisterdVoters)."</center></td>";
													$div .="<td><center>".number_format($percent,2)."</center></td>";
													
													$div .="<td><center>".$projected_voters."</center></td>";
													$div .="<td><center>".number_format($projected_voters_percent,2)."</center></td>";

													$div .="<td><center>".abs($undecided)."</center></td>";
													$div .="<td><center>".number_format(abs($undecided_percent),2)."</center></td>";
													
													$div .="<td><center>".count($countDeceased)."</center></td>";
													$div .="<td><center>".number_format($countDeceased_percent,2)."</center></td>";
													
													$div .="<td><center>".count($countAbroad)."</center></td>";
													$div .="<td><center>".number_format($countAbroad_percent,2)."</center></td>";
													
													
											$div .="</tr>";
											
												//EXCEL
											$excel[$province->name][$city->name][$val->name] = array(
											
												"province"                 => $province->name,
												"city"                     => $city->name,
												"registered_voters"        => count($countRegisterdVoters),
												"percent"                  => $percent,
												"projected_voters" 		   => $projected_voters,
												"projected_voters_percent" => $projected_voters_percent,  
												"undecided" 			   => $undecided,
												"undecided_percent" 	   => $undecided_percent,
												"countDeceased"			   => count($countDeceased),
												"countDeceased_percent"    => $countDeceased_percent,
												"countAbroad" 			   => count($countAbroad),
												"countAbroad_percent"      => $countAbroad_percent,
											);
											
											
									
								}
								
									// Sub footer
									$div .="<tr style='border-top:2px solid #b6aeae;'> ";
									
										$div .="<td><b>Sub Total</b></td>";
										$div .="<td></td>";
										
										$div .="<td><center><b>".$sub_countRegisterdVoters."</b></center></td>";
										$div .="<td><center><b>".number_format( $sub_countRegisterdVoters_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_projected_voters."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_projected_voters_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_undecided."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_undecided_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_countDeceased."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_countDeceased_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_countAbroad."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_countAbroad_percent, 2)."</b></center></td>";
									
									$div .="</tr>";
								
								
								
							
						
			
					}
					
					
					
				}
			$div .= "</tbody>";	

		}
		else
		{
			$div .= "<tbody>";
			$div .="<tr>";
			$div .="<td colspan='10'><center>No data available in table</center></td>";
			$div .="</tr>";
			$div .= "</tbody>";
		}
		
		$output = array("table" => $div, 'excel' => $excel , 'report_level' => 'Province');
		return response()->json($output);

			
	}
	
	
	
	########################################## CITY ###########################################
	
	function City()
	{
		
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
				

			
			
			$div = "<thead>
						<tr>
						  <th>City/Municipality</th>
						  
						  <th><center>Registered Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Projected Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Undecided Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Deceased</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Abroad</center></th>
						  <th><center>%</center></th>
						</tr>
					</thead>";	
		
		$excel =  array();
		
		
		$select ="t1.id";
		$allvoters = \DB::table('votersinfomations as t1')		
		->select(\DB::raw($select))
		->get();
		

		$select ="t1.*,t1.name";
		$getallReligions = \DB::table('religions as t1')
		->select(\DB::raw($select))
		->get();

			
		$religions = array();
		foreach($getallReligions  as $data)
		{	
		
			$religions[] = (object) array(
							'id' => $data->id,
							'name' => $data->name,
						 );

		}
	
		$religions[] = (object) ['id' =>0,'name' => 'Undetermined'];
		$allreligions = (object)$religions;
		

		$select ="t1.name,t1.id";
		$getallcities = \DB::table('cities as t1')		
		->select(\DB::raw($select))
		->get();

		
		if($getallcities && count($getallcities) > 0)
		{
			
			
			$div .= "<tbody>";
					
					foreach($getallcities as $city)
					{
						

						
								 $div .="<tr style='background-color:#dddcdc;'  >";
									$div .="<td colspan='12'>".$city->name."</td>";	
								$div .="</tr>";	
								
								
									$sub_countRegisterdVoters = 0;
									$sub_countRegisterdVoters_percent = 0;
									
									$sub_projected_voters = 0;
									$sub_projected_voters_percent = 0;
									
									$sub_undecided = 0;
									$sub_undecided_percent = 0;
									
									$sub_countDeceased = 0;
									$sub_countDeceased_percent = 0;
									
									$sub_countAbroad = 0;
									$sub_countAbroad_percent = 0;
								
								
								
								foreach($allreligions as $val)
								{	
										
										
										
										$coordinators = "( SELECT COUNT(a.coordinator_id) from coordinators as a 
															   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
															   where (b.religion = t1.religion) AND b.city_municipality = a.city
															 ) as coordinators ";
															 
											$leaders = "( SELECT COUNT(a.leader_id) from leaders as a 
															   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
															   where (b.religion = t1.religion) AND b.city_municipality = a.city
															 ) as leaders ";				 
											
											$members      = "(SELECT COUNT(b.id) from campaign_groups as a 
															   LEFT JOIN  campaign_group_members as b ON a.group_id = b.group_id
															   LEFT JOIN  votersinfomations as c ON c.id = b.user_id
															   where (c.religion = t1.religion) AND c.city_municipality = a.city
															 ) as members ";
											

											$select ="t1.name,t1.id,t1.status,{$coordinators},{$leaders},{$members}";
											$registerVoters = \DB::table('votersinfomations as t1')		
											->select(\DB::raw($select))
											->where('t1.religion', '=',$val->id)
											->where('t1.city_municipality', '=',$city->id)
											->get();
											
											$countRegisterdVoters = array();
											$countAbroad = array();
											$countDeceased = array();
											
											$Countcoordinators  = 0;
											$Countleaders = 0;
											$Countmembers = 0;
											
											foreach($registerVoters as $voters)
											{
												$countRegisterdVoters[] = $voters;
												
												if($voters->status == 1)
												{
													$countDeceased[] = $voters;
												}
												else if($voters->status == 2)
												{
													$countAbroad[] = $voters;
												}
												
												$Countcoordinators = $voters->coordinators;
												$Countleaders	   = $voters->leaders;
												$Countmembers	   = $voters->members;
												
											
											}
											
												$projected_voters = $Countcoordinators + $Countleaders + $Countmembers;
												$undecided = count($countRegisterdVoters) - $projected_voters;
											
												$sub_countRegisterdVoters = $sub_countRegisterdVoters + count($countRegisterdVoters);					
												$percent = (count($countRegisterdVoters) == 0 ) ? 0 : ((count($countRegisterdVoters) * 100) / count($allvoters));
												$sub_countRegisterdVoters_percent = $sub_countRegisterdVoters_percent + $percent;
											
												$sub_projected_voters = $sub_projected_voters +  $projected_voters;
												$projected_voters_percent  = ( count($countRegisterdVoters)  == 0 )  ? 0  : (($projected_voters * 100) / count($allvoters));
												$sub_projected_voters_percent = $sub_projected_voters_percent + $projected_voters_percent;
												
												$sub_undecided = $sub_undecided + $undecided;
												$undecided_percent  = ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( ($undecided * 100) / count($allvoters) );
												$sub_undecided_percent = $sub_undecided_percent + $undecided_percent;
												
												$sub_countDeceased = $sub_countDeceased + count($countDeceased);
												$countDeceased_percent  =  ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countDeceased) * 100) / count($allvoters) );
												$sub_countDeceased_percent = 	$sub_countDeceased_percent + $countDeceased_percent;	
												
												$sub_countAbroad = $sub_countAbroad + count($countAbroad);
												$countAbroad_percent = (count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countAbroad) * 100) / count($allvoters) );
												$sub_countAbroad_percent = $sub_countAbroad_percent + $countAbroad_percent;
									
									
											 $div .="<tr>";
											 
													$div .="<td>".$val->name."</td>";
													
													$div .="<td><center>".count($countRegisterdVoters)."</center></td>";
													$div .="<td><center>".number_format($percent,2)."</center></td>";
													
													$div .="<td><center>".$projected_voters."</center></td>";
													$div .="<td><center>".number_format($projected_voters_percent,2)."</center></td>";

													$div .="<td><center>".abs($undecided)."</center></td>";
													$div .="<td><center>".number_format(abs($undecided_percent),2)."</center></td>";
													
													$div .="<td><center>".count($countDeceased)."</center></td>";
													$div .="<td><center>".number_format($countDeceased_percent,2)."</center></td>";
													
													$div .="<td><center>".count($countAbroad)."</center></td>";
													$div .="<td><center>".number_format($countAbroad_percent,2)."</center></td>";
													
													
											$div .="</tr>";
											
												//EXCEL
											$excel[$city->name][$val->name] = array(
										
												"city"                     => $city->name,
												"registered_voters"        => count($countRegisterdVoters),
												"percent"                  => $percent,
												"projected_voters" 		   => $projected_voters,
												"projected_voters_percent" => $projected_voters_percent,  
												"undecided" 			   => $undecided,
												"undecided_percent" 	   => $undecided_percent,
												"countDeceased"			   => count($countDeceased),
												"countDeceased_percent"    => $countDeceased_percent,
												"countAbroad" 			   => count($countAbroad),
												"countAbroad_percent"      => $countAbroad_percent,
											);
											
											
									
								}
								
									// Sub footer
									$div .="<tr style='border-top:2px solid #b6aeae;'> ";
									
										$div .="<td><b>Sub Total</b></td>";										
										$div .="<td><center><b>".$sub_countRegisterdVoters."</b></center></td>";
										$div .="<td><center><b>".number_format( $sub_countRegisterdVoters_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_projected_voters."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_projected_voters_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_undecided."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_undecided_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_countDeceased."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_countDeceased_percent, 2)."</b></center></td>";
										
										$div .="<td><center><b>".$sub_countAbroad."</b></center></td>";
										$div .="<td><center><b>".number_format($sub_countAbroad_percent, 2)."</b></center></td>";
									
									$div .="</tr>";
								
								
								
							
						
			
					}
					
					
					
			
			$div .= "</tbody>";	

		}
		else
		{
			$div .= "<tbody>";
			$div .="<tr>";
			$div .="<td colspan='10'><center>No data available in table</center></td>";
			$div .="</tr>";
			$div .= "</tbody>";
		}
		
		$output = array("table" => $div, 'excel' => $excel , 'report_level' => 'City');
		return response()->json($output);

			
	}
	
	
	
		
	################################################ Barangay #######################################################
	
	
	function Barangay()
	{
		
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		

			
			$div = "<thead>
						<tr>
						  <th>Barangay</th>
						  
						  <th><center>Registered Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Projected Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Undecided Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Deceased</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Abroad</center></th>
						  <th><center>%</center></th>
						</tr>
					</thead>";	
		
		$excel =  array();
		
		$select ="t1.id";
		$allvoters = \DB::table('votersinfomations as t1')		
		->select(\DB::raw($select))
		->get();
	
		$select ="t1.name,t1.id";
		$getallbarangays = \DB::table('barangays as t1')		
		->select(\DB::raw($select))
		->get();
		
		
		$select ="t1.*,t1.name";
		$getallReligions = \DB::table('religions as t1')
		->select(\DB::raw($select))
		->get();

			
		$religions = array();
		foreach($getallReligions  as $data)
		{	
		
			$religions[] = (object) array(
							'id' => $data->id,
							'name' => $data->name,
						 );

		}
	
		$religions[] = (object) ['id' =>0,'name' => 'Undetermined'];
		$allreligions = (object)$religions;
		
		
		$grand_countRegisterdVoters = 0;
		$grand_countRegisterdVoters_percent = 0;
		
		$grand_projected_voters = 0;
		$grand_projected_voters_percent = 0;
		
		$grand_undecided = 0;
		$grand_undecided_percent = 0;
		
		$grand_countDeceased = 0;
		$grand_countDeceased_percent = 0;
		
		$grand_countAbroad = 0;
		$grand_countAbroad_percent = 0;
		
		if($getallbarangays && count($getallbarangays) > 0)
		{
			
					$div .= "<tbody>";
					foreach($getallbarangays as $barangay)
					{
					
							 $div .="<tr style='background-color:#dddcdc;font-size:15px'  >";
								$div .="<td colspan='12'>".$barangay->name."</td>";
							$div .="</tr>";
			
							
							$sub_countRegisterdVoters = 0;
							$sub_countRegisterdVoters_percent = 0;
							
							$sub_projected_voters = 0;
							$sub_projected_voters_percent = 0;
							
							$sub_undecided = 0;
							$sub_undecided_percent = 0;
							
							$sub_countDeceased = 0;
							$sub_countDeceased_percent = 0;
							
							$sub_countAbroad = 0;
							$sub_countAbroad_percent = 0;
							
						
							foreach($allreligions as $val){	
									
									
									
										$coordinators = "( SELECT COUNT(a.coordinator_id) from coordinators as a 
															   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
															   where b.religion = t1.religion AND a.barangay = t1.barangay
															 ) as coordinators ";
															 
											$leaders = "( SELECT COUNT(a.leader_id) from leaders as a 
															   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
															   where b.religion = t1.religion AND a.barangay = t1.barangay
															 ) as leaders ";				 
											
											$members      = "(SELECT COUNT(b.id) from campaign_groups as a 
															   LEFT JOIN  campaign_group_members as b ON a.group_id = b.group_id
															   LEFT JOIN  votersinfomations as c ON c.id = b.user_id
															   where c.religion = t1.religion AND a.barangay = t1.gender
															 ) as members ";
											

											$select ="t1.name,t1.id,t1.status,{$coordinators},{$leaders},{$members}";
											$registerVoters = \DB::table('votersinfomations as t1')		
											->select(\DB::raw($select))
											->where('t1.religion', '=',$val->id)
											->where('t1.barangay', '=',$barangay->id)
											->get();
											
											$countRegisterdVoters = array();
											$countAbroad = array();
											$countDeceased = array();
											
											$Countcoordinators  = 0;
											$Countleaders = 0;
											$Countmembers = 0;
											
											foreach($registerVoters as $voters)
											{
												$countRegisterdVoters[] = $voters;
												
												if($voters->status == 1)
												{
													$countDeceased[] = $voters;
												}
												else if($voters->status == 2)
												{
													$countAbroad[] = $voters;
												}
												
												$Countcoordinators = $voters->coordinators;
												$Countleaders	   = $voters->leaders;
												$Countmembers	   = $voters->members;
												
											
											}
											
												$projected_voters = $Countcoordinators + $Countleaders + $Countmembers;
												$undecided = count($countRegisterdVoters) - $projected_voters;
											
												$sub_countRegisterdVoters = $sub_countRegisterdVoters + count($countRegisterdVoters);					
												$percent = (count($countRegisterdVoters) == 0 ) ? 0 : ((count($countRegisterdVoters) * 100) / count($countRegisterdVoters));
												//$sub_countRegisterdVoters_percent = $sub_countRegisterdVoters_percent + $percent;
											
												$sub_projected_voters = $sub_projected_voters +  $projected_voters;
												$projected_voters_percent  = ( count($countRegisterdVoters)  == 0 )  ? 0  : (($projected_voters * 100) / count($countRegisterdVoters));
												//$sub_projected_voters_percent = $sub_projected_voters_percent + $projected_voters_percent;
												
												$sub_undecided = $sub_undecided + $undecided;
												$undecided_percent  = ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( ($undecided * 100) / count($countRegisterdVoters) );
												//$sub_undecided_percent = $sub_undecided_percent + $undecided_percent;
												
												$sub_countDeceased = $sub_countDeceased + count($countDeceased);
												$countDeceased_percent  =  ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countDeceased) * 100) / count($countRegisterdVoters) );
												//$sub_countDeceased_percent = 	$sub_countDeceased_percent + $countDeceased_percent;	
												
												$sub_countAbroad = $sub_countAbroad + count($countAbroad);
												$countAbroad_percent = (count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countAbroad) * 100) / count($countRegisterdVoters) );
												//$sub_countAbroad_percent = $sub_countAbroad_percent + $countAbroad_percent;
									
								
									 $div .="<tr>";
											
											
													$div .="<td>".$val->name."</td>";
													
													$div .="<td><center>".count($countRegisterdVoters)."</center></td>";
													$div .="<td><center>".number_format($percent,2)."</center></td>";
													
													$div .="<td><center>".$projected_voters."</center></td>";
													$div .="<td><center>".number_format($projected_voters_percent,2)."</center></td>";

													$div .="<td><center>".abs($undecided)."</center></td>";
													$div .="<td><center>".number_format(abs($undecided_percent),2)."</center></td>";
													
													$div .="<td><center>".count($countDeceased)."</center></td>";
													$div .="<td><center>".number_format($countDeceased_percent,2)."</center></td>";
													
													$div .="<td><center>".count($countAbroad)."</center></td>";
													$div .="<td><center>".number_format($countAbroad_percent,2)."</center></td>";
											
									$div .="</tr>";
									
									
										
										//EXCEL
										$excel[$barangay->name][$val->name] = array(
										
											"barangay"                 => $barangay->name,
											"registered_voters"        => count($countRegisterdVoters),
											"percent"                  => $percent,
											"projected_voters" 		   => $projected_voters,
											"projected_voters_percent" => $projected_voters_percent,  
											"undecided" 			   => $undecided,
											"undecided_percent" 	   => $undecided_percent,
											"countDeceased"			   => count($countDeceased),
											"countDeceased_percent"    => $countDeceased_percent,
											"countAbroad" 			   => count($countAbroad),
											"countAbroad_percent"      => $countAbroad_percent,
										);
										
							
							}
							
							
							$sub_countRegisterdVoters_percent = ($sub_countRegisterdVoters == 0 ) ? 0 : ( ($sub_countRegisterdVoters * 100) / ($sub_countRegisterdVoters));
							$sub_projected_voters_percent 	  = ($sub_projected_voters == 0 ) ? 0 : ( ($sub_projected_voters * 100) / ($sub_countRegisterdVoters));	
							$sub_undecided_percent 			  = ($sub_undecided == 0 ) ? 0 : ( ($sub_undecided * 100) / ($sub_countRegisterdVoters));	
							$sub_countDeceased_percent	 	  = ($sub_countDeceased == 0 ) ? 0 : ( ($sub_countDeceased * 100) / ($sub_countRegisterdVoters));	
							$sub_countAbroad_percent 		  = ($sub_countAbroad == 0 ) ? 0 : ( ($sub_countAbroad * 100) / ($sub_countRegisterdVoters));
							
							
							// Sub footer
							$div .="<tr style='border-top:2px solid #b6aeae;'> ";
							
								$div .="<td><b>Sub Total</b></td>";
								
								$div .="<td><center><b>".$sub_countRegisterdVoters."</b></center></td>";
								$div .="<td><center><b>".number_format( $sub_countRegisterdVoters_percent, 2)."</b></center></td>";
								
								$div .="<td><center><b>".$sub_projected_voters."</b></center></td>";
								$div .="<td><center><b>".number_format($sub_projected_voters_percent, 2)."</b></center></td>";
								
								$div .="<td><center><b>".$sub_undecided."</b></center></td>";
								$div .="<td><center><b>".number_format($sub_undecided_percent, 2)."</b></center></td>";
								
								$div .="<td><center><b>".$sub_countDeceased."</b></center></td>";
								$div .="<td><center><b>".number_format($sub_countDeceased_percent, 2)."</b></center></td>";
								
								$div .="<td><center><b>".$sub_countAbroad."</b></center></td>";
								$div .="<td><center><b>".number_format($sub_countAbroad_percent, 2)."</b></center></td>";
							
							$div .="</tr>";
							
								$grand_countRegisterdVoters = $grand_countRegisterdVoters + $sub_countRegisterdVoters;
								//$grand_countRegisterdVoters_percent = $grand_countRegisterdVoters_percent + $sub_countRegisterdVoters_percent;
								
								$grand_projected_voters = $grand_projected_voters + $sub_projected_voters;
								//$grand_projected_voters_percent = $grand_projected_voters_percent  + $sub_projected_voters_percent ;
								
								$grand_undecided = $grand_undecided + $sub_undecided;
								//$grand_undecided_percent = $grand_undecided_percent + $sub_undecided_percent;
								
								$grand_countDeceased = $grand_countDeceased + $sub_countDeceased;
								//$grand_countDeceased_percent = $grand_countDeceased_percent + $sub_countDeceased_percent;
								
								$grand_countAbroad = $grand_countAbroad  + $sub_countAbroad ;
								//$grand_countAbroad_percent = $grand_countAbroad_percent  + $sub_countAbroad_percent ;
							
							
							
							
						
					}
					$div .= "</tbody>";	
					
					
				$grand_countRegisterdVoters_percent	  = ($grand_countRegisterdVoters == 0 ) ? 0 : ( ($grand_countRegisterdVoters * 100) / ($grand_countRegisterdVoters));
				$grand_projected_voters_percent 	  = ($grand_projected_voters == 0 ) ? 0 : ( ($grand_projected_voters * 100) / ($grand_countRegisterdVoters));	
				$grand_undecided_percent 			  = ($grand_undecided == 0 ) ? 0 : ( ($grand_undecided * 100) / ($grand_countRegisterdVoters));	
				$grand_countDeceased_percent	 	  = ($grand_countDeceased == 0 ) ? 0 : ( ($grand_countDeceased * 100) / ($grand_countRegisterdVoters));	
				$grand_countAbroad_percent 		  	  = ($grand_countAbroad == 0 ) ? 0 : ( ($grand_countAbroad * 100) / ($grand_countRegisterdVoters));
					
					
					// Grand footer
						$div .="<tfoot style='border-top:3px solid #000;'>";
							$div .="<tr>";
							
							$div .="<td><b>Grand Total</b></td>";
							
							$div .="<td><center><b>".$grand_countRegisterdVoters."</b></center></td>";
							$div .="<td><center><b>".number_format($grand_countRegisterdVoters_percent,2)."</b></center></td>";
							
							$div .="<td><center><b>".$grand_projected_voters."</b></center></td>";
							$div .="<td><center><b>".number_format($grand_projected_voters_percent, 2)."</b></center></td>";
							
							$div .="<td><center><b>".$grand_undecided."</b></center></td>";
							$div .="<td><center><b>".number_format($grand_undecided_percent, 2)."</b></center></td>";
							
							$div .="<td><center><b>".$grand_countDeceased."</b></center></td>";
							$div .="<td><center><b>".number_format($grand_countDeceased_percent, 2)."</b></center></td>";
							
							$div .="<td><center><b>".$grand_countAbroad."</b></center></td>";
							$div .="<td><center><b>".number_format($grand_countAbroad_percent, 2)."</b></center></td>";

							
							$div .="</tr>";
						$div .="</tfoot>";
			
					
		
			

		}
		else
		{
			$div .= "<tbody>";
			$div .="<tr>";
			$div .="<td colspan='10'><center>No data available in table</center></td>";
			$div .="</tr>";
			$div .= "</tbody>";
		}
		
		$output = array("table" => $div, 'excel' => $excel , 'report_level' => 'Barangay');
		return response()->json($output);

			
	}
	
	
	################################################ PRECINT #######################################################		
	function Precinct(){
		
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		
			
			$div = "<thead>
						<tr>
						  <th>Precint Number</th>
						  <th><center>Registered Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Projected Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Undecided Voters</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Deceased</center></th>
						  <th><center>%</center></th>
						  
						  <th><center>Abroad</center></th>
						  <th><center>%</center></th>
						</tr>
					</thead>";	
	
		
		$select ="t1.id";
		$allvoters = \DB::table('votersinfomations as t1')		
		->select(\DB::raw($select))
		->get();
	
		$select ="t1.name,t1.id";
		$getallbarangays = \DB::table('barangays as t1')		
		->select(\DB::raw($select))
		->get();
		
		
		$select ="t1.*,t1.name";
		$getallReligions = \DB::table('religions as t1')
		->select(\DB::raw($select))
		->get();

			
		$religions = array();
		foreach($getallReligions  as $data)
		{	
		
			$religions[] = (object) array(
							'id' => $data->id,
							'name' => $data->name,
						 );

		}
	
		$religions[] = (object) ['id' =>0,'name' => 'Undetermined'];
		$allreligions = (object)$religions;
		
		
		$grand_countRegisterdVoters = 0;
		$grand_countRegisterdVoters_percent = 0;
		
		$grand_projected_voters = 0;
		$grand_projected_voters_percent = 0;
		
		$grand_undecided = 0;
		$grand_undecided_percent = 0;
		
		$grand_countDeceased = 0;
		$grand_countDeceased_percent = 0;
		
		$grand_countAbroad = 0;
		$grand_countAbroad_percent = 0;
		
		
		if($getallbarangays && count($getallbarangays) > 0)
		{
			
			
				$div .= "<tbody>";
					foreach($getallbarangays as $barangay)
					{
					
							 $div .="<tr style='background-color:#9dbbc9;'  >";
								$div .="<td colspan='12'>".$barangay->name."</td>";
							$div .="</tr>";
							
							
							$select ="t1.name,t1.id ";
							$getallprecints = \DB::table('precincts as t1')		
							->select(\DB::raw($select))
							->where('t1.barangay_id', $barangay->id)
							->get();
							
							if($getallprecints && count($getallprecints) > 0)
							{
									
									$sub_countRegisterdVoters = 0;
									$sub_countRegisterdVoters_percent = 0;
									
									$sub_projected_voters = 0;
									$sub_projected_voters_percent = 0;
									
									$sub_undecided = 0;
									$sub_undecided_percent = 0;
									
									$sub_countDeceased = 0;
									$sub_countDeceased_percent = 0;
									
									$sub_countAbroad = 0;
									$sub_countAbroad_percent = 0;
									
									
								
									foreach($getallprecints as $precint)
									{
										$div .="<tr>";
											$div .="<td colspan='12'>".$precint->name."</td>";
										$div .="</tr>";
		
												
								
											
											foreach($allreligions as $val)
											{
												
												
												$coordinators = "( SELECT COUNT(a.coordinator_id) from coordinators as a 
																	   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
																	   where (b.religion = t1.religion) AND b.precint_number = t1.precint_number
																	 ) as coordinators ";
																	 
													$leaders = "( SELECT COUNT(a.leader_id) from leaders as a 
																	   LEFT JOIN  votersinfomations as b ON b.id = a.user_id
																	   where (b.religion = t1.religion) AND b.precint_number = t1.precint_number
																	 ) as leaders ";				 
													
													$members      = "(SELECT COUNT(b.id) from campaign_groups as a 
																	   LEFT JOIN  campaign_group_members as b ON a.group_id = b.group_id
																	   LEFT JOIN  votersinfomations as c ON c.id = b.user_id
																	   where (c.religion = t1.religion) AND c.precint_number = t1.precint_number
																	 ) as members ";
													

											$select ="t1.name,t1.id,t1.status,{$coordinators},{$leaders},{$members}";
											$registerVoters = \DB::table('votersinfomations as t1')		
											->select(\DB::raw($select))
											->where('t1.religion', '=',$val->id)
											->where('t1.precint_number', '=',$precint->id)
											->get();
											
											$countRegisterdVoters = array();
											$countAbroad = array();
											$countDeceased = array();
											
											$Countcoordinators  = 0;
											$Countleaders = 0;
											$Countmembers = 0;
											
											foreach($registerVoters as $voters)
											{
												$countRegisterdVoters[] = $voters;
												
												if($voters->status == 1)
												{
													$countDeceased[] = $voters;
												}
												else if($voters->status == 2)
												{
													$countAbroad[] = $voters;
												}
												
												$Countcoordinators = $voters->coordinators;
												$Countleaders	   = $voters->leaders;
												$Countmembers	   = $voters->members;
												
											
											}
											
												$projected_voters = $Countcoordinators + $Countleaders + $Countmembers;
												$undecided = count($countRegisterdVoters) - $projected_voters;
											
												$sub_countRegisterdVoters = $sub_countRegisterdVoters + count($countRegisterdVoters);					
												$percent = (count($countRegisterdVoters) == 0 ) ? 0 : ((count($countRegisterdVoters) * 100) / count($countRegisterdVoters));
												//$sub_countRegisterdVoters_percent = $sub_countRegisterdVoters_percent + $percent;
											
												$sub_projected_voters = $sub_projected_voters +  $projected_voters;
												$projected_voters_percent  = ( count($countRegisterdVoters)  == 0 )  ? 0  : (($projected_voters * 100) / count($countRegisterdVoters));
												//$sub_projected_voters_percent = $sub_projected_voters_percent + $projected_voters_percent;
												
												$sub_undecided = $sub_undecided + $undecided;
												$undecided_percent  = ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( ($undecided * 100) / count($countRegisterdVoters) );
												//$sub_undecided_percent = $sub_undecided_percent + $undecided_percent;
												
												$sub_countDeceased = $sub_countDeceased + count($countDeceased);
												$countDeceased_percent  =  ( count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countDeceased) * 100) / count($countRegisterdVoters) );
												//$sub_countDeceased_percent = 	$sub_countDeceased_percent + $countDeceased_percent;	
												
												$sub_countAbroad = $sub_countAbroad + count($countAbroad);
												$countAbroad_percent = (count($countRegisterdVoters)  == 0 ) ? 0 :  ( (count($countAbroad) * 100) / count($countRegisterdVoters) );
												//$sub_countAbroad_percent = $sub_countAbroad_percent + $countAbroad_percent;
									
								
									 $div .="<tr>";
											
											
													$div .="<td style='padding-left:80px'>".$val->name."</td>";
													
													$div .="<td><center>".count($countRegisterdVoters)."</center></td>";
													$div .="<td><center>".number_format($percent,2)."</center></td>";
													
													$div .="<td><center>".$projected_voters."</center></td>";
													$div .="<td><center>".number_format($projected_voters_percent,2)."</center></td>";

													$div .="<td><center>".abs($undecided)."</center></td>";
													$div .="<td><center>".number_format(abs($undecided_percent),2)."</center></td>";
													
													$div .="<td><center>".count($countDeceased)."</center></td>";
													$div .="<td><center>".number_format($countDeceased_percent,2)."</center></td>";
													
													$div .="<td><center>".count($countAbroad)."</center></td>";
													$div .="<td><center>".number_format($countAbroad_percent,2)."</center></td>";
											
									$div .="</tr>";
									
									
										
										//EXCEL
										$excel[$barangay->name][$precint->name][$val->name] = array(
										
											"Precint"                 => $barangay->name,
											"registered_voters"        => count($countRegisterdVoters),
											"percent"                  => $percent,
											"projected_voters" 		   => $projected_voters,
											"projected_voters_percent" => $projected_voters_percent,  
											"undecided" 			   => $undecided,
											"undecided_percent" 	   => $undecided_percent,
											"countDeceased"			   => count($countDeceased),
											"countDeceased_percent"    => $countDeceased_percent,
											"countAbroad" 			   => count($countAbroad),
											"countAbroad_percent"      => $countAbroad_percent,
										);
										
												
												
												
											
											} // all religions
											
											
									} // get all precint
									
									
								$sub_countRegisterdVoters_percent = ($sub_countRegisterdVoters == 0 ) ? 0 : ( ($sub_countRegisterdVoters * 100) / ($sub_countRegisterdVoters));
								$sub_projected_voters_percent 	  = ($sub_projected_voters == 0 ) ? 0 : ( ($sub_projected_voters * 100) / ($sub_countRegisterdVoters));	
								$sub_undecided_percent 			  = ($sub_undecided == 0 ) ? 0 : ( ($sub_undecided * 100) / ($sub_countRegisterdVoters));	
								$sub_countDeceased_percent	 	  = ($sub_countDeceased == 0 ) ? 0 : ( ($sub_countDeceased * 100) / ($sub_countRegisterdVoters));	
								$sub_countAbroad_percent 		  = ($sub_countAbroad == 0 ) ? 0 : ( ($sub_countAbroad * 100) / ($sub_countRegisterdVoters));
										
									
										// Sub footer
										$div .="<tr style='border-top:2px solid #b6aeae;background-color:#f2efef;' > ";
										
											$div .="<td><b>Sub Total</b></td>";
											
											$div .="<td><center><b>".$sub_countRegisterdVoters."</b></center></td>";
											$div .="<td><center><b>".number_format( $sub_countRegisterdVoters_percent, 2)."</b></center></td>";
											
											$div .="<td><center><b>".$sub_projected_voters."</b></center></td>";
											$div .="<td><center><b>".number_format($sub_projected_voters_percent, 2)."</b></center></td>";
											
											$div .="<td><center><b>".$sub_undecided."</b></center></td>";
											$div .="<td><center><b>".number_format($sub_undecided_percent, 2)."</b></center></td>";
											
											$div .="<td><center><b>".$sub_countDeceased."</b></center></td>";
											$div .="<td><center><b>".number_format($sub_countDeceased_percent, 2)."</b></center></td>";
											
											$div .="<td><center><b>".$sub_countAbroad."</b></center></td>";
											$div .="<td><center><b>".number_format($sub_countAbroad_percent, 2)."</b></center></td>";
										
										$div .="</tr>";
										
										
										$grand_countRegisterdVoters = $grand_countRegisterdVoters + $sub_countRegisterdVoters;
										//$grand_countRegisterdVoters_percent = $grand_countRegisterdVoters_percent + $sub_countRegisterdVoters_percent;
										
										$grand_projected_voters = $grand_projected_voters + $sub_projected_voters;
										//$grand_projected_voters_percent = $grand_projected_voters_percent  + $sub_projected_voters_percent ;
										
										$grand_undecided = $grand_undecided + $sub_undecided;
										//$grand_undecided_percent = $grand_undecided_percent + $sub_undecided_percent;
										
										$grand_countDeceased = $grand_countDeceased + $sub_countDeceased;
										//$grand_countDeceased_percent = $grand_countDeceased_percent + $sub_countDeceased_percent;
										
										$grand_countAbroad = $grand_countAbroad  + $sub_countAbroad ;
										//$grand_countAbroad_percent = $grand_countAbroad_percent  + $sub_countAbroad_percent ;
									
									
									

							}
							
							
					} // all barangay
				$div .= "</tbody>";
				
				
				$grand_countRegisterdVoters_percent	  = ($grand_countRegisterdVoters == 0 ) ? 0 : ( ($grand_countRegisterdVoters * 100) / ($grand_countRegisterdVoters));
				$grand_projected_voters_percent 	  = ($grand_projected_voters == 0 ) ? 0 : ( ($grand_projected_voters * 100) / ($grand_countRegisterdVoters));	
				$grand_undecided_percent 			  = ($grand_undecided == 0 ) ? 0 : ( ($grand_undecided * 100) / ($grand_countRegisterdVoters));	
				$grand_countDeceased_percent	 	  = ($grand_countDeceased == 0 ) ? 0 : ( ($grand_countDeceased * 100) / ($grand_countRegisterdVoters));	
				$grand_countAbroad_percent 		  	  = ($grand_countAbroad == 0 ) ? 0 : ( ($grand_countAbroad * 100) / ($grand_countRegisterdVoters));
				
					// Grand footer
				$div .="<tfoot style='border-top:3px solid #000;'>";
					$div .="<tr>";
					
					$div .="<td><b>Grand Total</b></td>";
					
					$div .="<td><center><b>".$grand_countRegisterdVoters."</b></center></td>";
					$div .="<td><center><b>".number_format($grand_countRegisterdVoters_percent,2)."</b></center></td>";
					
					$div .="<td><center><b>".$grand_projected_voters."</b></center></td>";
					$div .="<td><center><b>".number_format($grand_projected_voters_percent, 2)."</b></center></td>";
					
					$div .="<td><center><b>".$grand_undecided."</b></center></td>";
					$div .="<td><center><b>".number_format($grand_undecided_percent, 2)."</b></center></td>";
					
					$div .="<td><center><b>".$grand_countDeceased."</b></center></td>";
					$div .="<td><center><b>".number_format($grand_countDeceased_percent, 2)."</b></center></td>";
					
					$div .="<td><center><b>".$grand_countAbroad."</b></center></td>";
					$div .="<td><center><b>".number_format($grand_countAbroad_percent, 2)."</b></center></td>";

					
					$div .="</tr>";
				$div .="</tfoot>";
			
		}
		else
		{
			$div .= "<tbody>";
			$div .="<tr>";
			$div .="<td colspan='10'><center>No data available in table</center></td>";
			$div .="</tr>";
			$div .= "</tbody>";
		}
		
		
		
		$output = array("table" => $div, 'excel' => @$excel , 'report_level' => 'Precinct');
		return response()->json($output);
		
	}
	
	
	
	
	
	
	public function print_voters_religion(Request $requests)
    {
        $array = $requests->data_form;
		 $body_data = json_decode($array);
		/*
		$header_data = array(
						   'city'      =>  $requests->print_city,
						   'barangay'  =>  $requests->print_barangay,
						);
		*/				
       //$header = (object) $header_data;
	   	
       // This  $data array will be passed to our PDF blade
       $datax = [
			  'user_id' 		  =>  Auth::user()->name,
			  'date_genarated'    =>  Carbon::now(),
			  'title' 			  => 'List of Voters Category by Religion',
			  //'header_data'       => $header,
			  'table_data'        => $body_data,
          ];
     		

	    $pdf = PDF::loadView('pdf_reports.print_report', $datax);  
        $filename = trans('Voters Religion Report');
		return $pdf->download($filename.'.pdf');
		
    }
	
	/*
	public function excel_voters_religion(Request $requests)
    {
        $array = $requests->data_form_excel;
        $body_data = json_decode($array);
		$excel_data = array();
			
		
		foreach ($body_data as $gdata => $data) 
		{
				
				 $excel_data[] = array(
					   'Vin Number' => $gdata,
					   'Name' => '',
					   'Religion' => '',
				);
				
				foreach ($data as  $value) 
				{
					$age = \Carbon\Carbon::parse($value->dob)->age;
					
						 $excel_data[] = array(
							'Vin Number' => $value->vin_number,
							'Name' => $value->name,
							'Religion' => $value->religion,
	
						);
				}
				
				
		}
		
		$filename = trans('Voters Religion Report');
		return Excel::download(new Excel_voters_religion($excel_data), $filename.'.xlsx');
		
    }
	*/
	
	public function excel_voters_religion(Request $requests)
    {
		
        $array = $requests->data_form_excel;
        $body_data = json_decode($array);
		$data_report_level = $requests->data_report_level;
	

		if($data_report_level == "Province")
		{
			$excel_data = $this->ExcelProvince($body_data);
			$filename = trans('Religions Voters Report By Province');
			return Excel::download(new Excel_voters_projected_province($excel_data), $filename.'.xlsx');
					
		}
		else if($data_report_level == "City")
		{
		
			$excel_data = $this->ExcelCity($body_data);	
			$filename = trans('Religions Voters Report By City');
			return Excel::download(new Excel_voters_projected_city($excel_data), $filename.'.xlsx');
			 
		}
		else if($data_report_level == "Barangay")
		{
			$excel_data = $this->ExcelBarangay($body_data);
			$filename = trans('Religions Voters Report By Barangay');
			return Excel::download(new Excel_voters_projected_barangay($excel_data), $filename.'.xlsx');
			
		}
		else if($data_report_level == "Precinct")
		{
			 $excel_data = $this->ExcelPrecint($body_data);
			$filename = trans('Religions Voters Repor By Precint');
			return Excel::download(new Excel_voters_projected_precint($excel_data), $filename.'.xlsx');
		}
		

    }
	
	
	function ExcelProvince($body_data){
		
		
				$excel_data = array();
			
				foreach ($body_data as $province => $cities) 
				{	
				
						foreach( $cities as $city => $age)
						{
							
							 $excel_data[] = array(
							 
									"Province" => $province,
									"City/Municipality" => $city,
									"Registered Voters" => '',
									"Registered %" => '',  
									"Projected Voters" => '',
									"Projected %" => '',
									"Undecided Voters" => '',
									"Undecided %" => '',
									"Deceased" => '',
									"Deceased %" => '',
									"Abroad" => '',
									"Abroad %" => '',
								);
								
								
								foreach($age as $keyname => $value)
								{
									
									 $excel_data[] = array(
											"Province" => $keyname,
											"City/Municipality" => '',
											"Registered Voters" => $value->registered_voters,
											"Registered %" => number_format($value->percent,2),  
											"Projected Voters" => $value->projected_voters,
											"Projected %" => number_format($value->projected_voters_percent,2),
											"Undecided Voters" => $value->undecided,
											"Undecided %" => number_format($value->undecided_percent,2),
											"Deceased" => $value->countDeceased,
											"Deceased %" => number_format($value->countDeceased_percent,2),
											"Abroad" => $value->countAbroad,
											"Abroad %" => number_format($value->countAbroad_percent,2),
										);
									
								}
							
						}
						
				}
				
				return $excel_data;
			
	}
	
	
		function ExcelCity($body_data){
		
		
				$excel_data = array();
			
			
				foreach( $body_data as $city => $gender)
				{
					
					 $excel_data[] = array(	
					 
							"City/Municipality" => $city,
							"Registered Voters" => '',
							"Registered %" => '',  
							"Projected Voters" => '',
							"Projected %" => '',
							"Undecided Voters" => '',
							"Undecided %" => '',
							"Deceased" => '',
							"Deceased %" => '',
							"Abroad" => '',
							"Abroad %" => '',
						);
						
						
						foreach($gender as $keyname => $value)
						{
							
							 $excel_data[] = array(
									"City/Municipality" => $keyname,
									"Registered Voters" => $value->registered_voters,
									"Registered %" => number_format($value->percent,2),  
									"Projected Voters" => $value->projected_voters,
									"Projected %" => number_format($value->projected_voters_percent,2),
									"Undecided Voters" => $value->undecided,
									"Undecided %" => number_format($value->undecided_percent,2),
									"Deceased" => $value->countDeceased,
									"Deceased %" => number_format($value->countDeceased_percent,2),
									"Abroad" => $value->countAbroad,
									"Abroad %" => number_format($value->countAbroad_percent,2),
								);
							
						}
					
				}
				
				
				return $excel_data;
			
	}
	
	function ExcelBarangay($body_data){
		
		
				$excel_data = array();
			
			
				foreach( $body_data as $barangay => $gender)
				{
					
					 $excel_data[] = array(	
					 
							"Barangay" => $barangay,
							"Registered Voters" => '',
							"Registered %" => '',  
							"Projected Voters" => '',
							"Projected %" => '',
							"Undecided Voters" => '',
							"Undecided %" => '',
							"Deceased" => '',
							"Deceased %" => '',
							"Abroad" => '',
							"Abroad %" => '',
						);
						
						
						foreach($gender as $keyname => $value)
						{
							
							 $excel_data[] = array(
									"Barangay" => $keyname,
									"Registered Voters" => $value->registered_voters,
									"Registered %" => number_format($value->percent,2),  
									"Projected Voters" => $value->projected_voters,
									"Projected %" => number_format($value->projected_voters_percent,2),
									"Undecided Voters" => $value->undecided,
									"Undecided %" => number_format($value->undecided_percent,2),
									"Deceased" => $value->countDeceased,
									"Deceased %" => number_format($value->countDeceased_percent,2),
									"Abroad" => $value->countAbroad,
									"Abroad %" => number_format($value->countAbroad_percent,2),
								);
							
						}
					
				}
				
				
				return $excel_data;
			
	}
	
	
	
	function ExcelPrecint($body_data){
		
		
				$excel_data = array();
			
				foreach ($body_data as $barangay => $precints) 
				{	
					
						 $excel_data[] = array(
							 
									"Precint Number" => $barangay,
									"Registered Voters" => '',
									"Registered %" => '',  
									"Projected Voters" => '',
									"Projected %" => '',
									"Undecided Voters" => '',
									"Undecided %" => '',
									"Deceased" => '',
									"Deceased %" => '',
									"Abroad" => '',
									"Abroad %" => '',
								);
					
					
						foreach( $precints as $precint => $gender)
						{
							
							 $excel_data[] = array(
							 
									"Precint Number" => $precint,
									"Registered Voters" => '',
									"Registered %" => '',  
									"Projected Voters" => '',
									"Projected %" => '',
									"Undecided Voters" => '',
									"Undecided %" => '',
									"Deceased" => '',
									"Deceased %" => '',
									"Abroad" => '',
									"Abroad %" => '',
								);
								
								
								foreach($gender as $keyname => $value)
								{
									
									 $excel_data[] = array(
											"Precint Number" => $keyname,
											"Registered Voters" => $value->registered_voters,
											"Registered %" => number_format($value->percent,2),  
											"Projected Voters" => $value->projected_voters,
											"Projected %" => number_format($value->projected_voters_percent,2),
											"Undecided Voters" => $value->undecided,
											"Undecided %" => number_format($value->undecided_percent,2),
											"Deceased" => $value->countDeceased,
											"Deceased %" => number_format($value->countDeceased_percent,2),
											"Abroad" => $value->countAbroad,
											"Abroad %" => number_format($value->countAbroad_percent,2),
										);
									
								}
							
						}
						
				}
				
				return $excel_data;
			
	}
	
	
	
}