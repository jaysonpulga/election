<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\view_voters_by_age;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     
     
     public function index()
     {
         //re-able ONLY_FULL_GROUP_BY
        \DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$arraymenu = array('parent_menu'=>'dashboard');	
        
        
         //Total Voters and Projected Voters
    	   $member  = \DB::table('campaign_group_members')->count();
    	   $coordinators  = \DB::table('coordinators')->count();
    	   $leaders  = \DB::table('leaders')->count();
    	   $TotalprojectedVoters = $coordinators + $leaders + $member ;
    	   $TotalVoters  = \DB::table('votersinfomations')->count(); 
            
           $Young           = "COUNT(CASE WHEN  year(curdate())- year(t1.dob) >= 18 AND year(curdate())- year(t1.dob) <= 30  THEN 'YOUNG' END) as Young";
    	   $Middle          = "COUNT(CASE WHEN  year(curdate())- year(t1.dob) >= 31 AND year(curdate())- year(t1.dob) <= 45  THEN 'Middle' END) as Middle";
    	   $OLD             = "COUNT(CASE WHEN  year(curdate())- year(t1.dob) >= 46 AND year(curdate())- year(t1.dob) <= 59  THEN 'OLD' END) as OLD";
    	   $SeniorCitizens   = "COUNT(CASE WHEN  year(curdate())- year(t1.dob) >= 60 AND t1.dob != '0000-00-00'  THEN 'SENIOR' END) as SeniorCitizens";
    	   //member by age
    		$select2 ="{$Young},{$Middle},{$OLD},{$SeniorCitizens}";
    		$ageData = \DB::table('votersinfomations as t1')
    		->select(\DB::raw($select2))
    		->first();
    		

  
            //get all voters by gender
    		$select ="gender, COUNT(*) AS total_number";
    		$allgender = \DB::table('votersinfomations')
    		->select(\DB::raw($select))
    		->groupBy('gender')
    		->get();
    		
    	    $male = 0;
    	    $female = 0;
    	    $unknown = 0;
    	    
        	foreach($allgender  as $data)
    		{
    		    
    		    if($data->gender == 'M')
    		    {
    		        $male = $data->total_number; 
    		    }
    		    else if($data->gender == 'F')
    		    {
    		        $female = $data->total_number; 
    		    }
    		    else{
    		        $unknown = $unknown + $data->total_number;
    		    }
    			
    		}
        	
        		
        
    	
    		
    		
    		
    		//get member voters by gender
    		$select2 ="t1.gender,COUNT(t1.id) AS total_number";
    		$allmembergender = \DB::table('votersinfomations as t1')
    		->select(\DB::raw($select2))
    		->join('campaign_group_members AS t2','t2.user_id','=','t1.id')
    		->groupBy('t1.gender')
    		->get();
    		
    	   
    	   	$voterMemberMale = 0;
    	    $voterMemberFemale = 0;
    	    $voterMemberUnknown = 0;
    	    
        	foreach($allmembergender  as $data)
    		{
    		    
    		    if($data->gender == 'M')
    		    {
    		        $voterMemberMale = $data->total_number; 
    		    }
    		    else if($data->gender == 'F')
    		    {
    		        $voterMemberFemale = $data->total_number; 
    		    }
    		    else{
    		        $voterMemberUnknown = $voterMemberUnknown + $data->total_number;
    		    }
    			
    		}
    	   
    	   
    		
    		$gender = array(
						'Male' => $male, 
						'Female' => $female,
						'unknow_gender' => $unknown,
						'voterMemberMale' => @$voterMemberMale,
						'voterMemberFemale' => @$femaleVoters,
						'voterMemberUnknown' => @$voterMemberUnknown,
						);
    		$genderData = (object)$gender;
    
			 
			$total_number = "(SELECT count(id) FROM votersinfomations where religion = t1.id) as total_number";
			$select2 ="t1.id,t1.name AS religion_name,{$total_number}";
    		$allmemberReligion = \DB::table('religions as t1')
    		->select(\DB::raw($select2))
    		->groupBy('t1.id','t1.name')
    		->get();
    		$background_colors = array('#4698d4', '#81ae0a', '#e82b96', '#01a096', '#FF3838');
    		$i = 0;
    		$religions = array();
    		foreach($allmemberReligion  as $data)
    		{	
    			
    			
    				$religions[$data->religion_name] = array(
    					
    														'count' => $data->total_number,
    														'backgroundColor' => $background_colors[$i],
    														'percentage' => number_format (($data->total_number * 100) / $TotalVoters ,2),
    												);
    			
    			$i++;
    		}
    		
    		
    		
    		return view( 'dashboard', 
							$arraymenu,
							array( 
								'TotalVoters' => $TotalVoters, 
								'TotalprojectedVoters' => $TotalprojectedVoters,
								'ChartAge' => $ageData,
								'ChartSex'=> $genderData,
								'dataReligion' => $religions,
							)
				);
    		
    		
    		
    		
    		
    
          
     }
     
     
     
	 
	 public function index3(){
		 
		 //re-able ONLY_FULL_GROUP_BY
       
       \DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$arraymenu = array('parent_menu'=>'dashboard');	

	/*
	 //member by barangay
		$select2 ="t1.*";
		$barangays = \DB::table('view_barangay as t1')
		->select(\DB::raw($select2))
		->offset(0)->limit(30)->get();
	 */
	
		
	/*
	 $coordinators = "(SELECT COUNT(coordinator_id) from coordinators  where barangay = t1.id) as coordinators";
	   $leaders      = "(SELECT COUNT(leader_id) from leaders  where barangay = t1.id) as leaders";
	   $members      = "(SELECT COUNT(b.id) from campaign_groups as a LEFT JOIN campaign_group_members as b ON a.group_id = b.group_id  where a.barangay = t1.id ) as members";
	   $totalvoters  = "(SELECT COUNT(id) from votersinfomations  where barangay = t1.id) as total_voters";
	
	   //member by barangay
		$select2 ="t1.id,t1.name , {$coordinators},{$leaders},{$members},{$totalvoters} ";
		$barangays = \DB::table('barangays as t1')
		->select(\DB::raw($select2))
		->get();
	   
	   	
		$databarangay = array();
		foreach($barangays as  $barangay)
		{
			
			$databarangay[] = array(
			
								'id' => $barangay->id,
								//'name'=> $barangay->name .":".$barangay->total_voters,
								'name'=> $barangay->name,
								'coordinators' => $barangay->coordinators,
								'leaders' => $barangay->leaders,
								'members' => $barangay->members,
								'total_voters' => $barangay->total_voters,
								
						);
		}
		
		
		echo "<pre>";
		print_r($databarangay);
		echo "</pre>";
		exit;
		*/


		  //Total Voters and Projected Voters
		   $member  = \DB::table('campaign_group_members')->count();
		   $coordinators  = \DB::table('coordinators')->count();
		   $leaders  = \DB::table('leaders')->count();
		   $TotalprojectedVoters = $coordinators + $leaders + $member ;
		   $TotalVoters  = \DB::table('votersinfomations')->count();
		   
		   

		//member by age
		$select ="*";
		$ageData = \DB::table('view_voters_by_age')
		->select(\DB::raw($select))
		->get();
        
		
		//get all voters by gender
		$select ="t1.*";
		$allgender = \DB::table('voter_gender as t1')
		->select(\DB::raw($select))
		->get();

		//get member voters by gender
		$select ="t1.*";
		$allmembergender = \DB::table('voter_member_gender as t1')
		->select(\DB::raw($select))
		->get();
      
		$gender = array(
						'Male' => @$allgender[2]->total_number, 
						'Female' => @$allgender[1]->total_number,
						'voterMemberMale' => @$allmembergender[2]->total_number,
						'voterMemberFemale' => @$allmembergender[1]->total_number,
						);


		//get member voters by gender
		
		$select ="t1.*";
		$allmemberReligion = \DB::table('view_voters_by_religion as t1')
		->select(\DB::raw($select))
		->get();


		$background_colors = array('#4698d4', '#81ae0a', '#e82b96', '#01a096', '#FF3838');
		$i = 0;
		$religions = array();
		foreach($allmemberReligion  as $data)
		{	
			
			
				$religions[$data->religion_name] = array(
					
														'count' => $data->total_number,
														'backgroundColor' => $background_colors[$i],
														'percentage' => number_format (($data->total_number * 100) / $TotalVoters ,2),
												);
			
			$i++;
		}

	


        return view( 'dashboard', 
							$arraymenu,
							array( 
								'TotalVoters' => $TotalVoters, 
								'TotalprojectedVoters' => $TotalprojectedVoters,
								'ChartAge' => $ageData,
								'ChartSex'=> (object)$gender,
								'dataReligion' => $religions,
							)
				);
		 
		 
		 
	 }
	 
	 
	 
	 public function BarangayData(Request $request)
	 {
		 
		// member by barangay
		$select2 ="t1.*";
		$barangays = \DB::table('view_barangay as t1')
		->select(\DB::raw($select2))
		->offset(0)->limit(30)->get();
		
			return response()->json($barangays);
		
	 }
	 
    public function index2()
    {
		
		//re-able ONLY_FULL_GROUP_BY
       \DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
	   
	   
	   
	   
	     // GET FEMALE AND MALE
	    $voterMale  = \DB::table('votersinfomations')->where('gender', ['M'])->count();	
	    $voterFemale  = \DB::table('votersinfomations')->where('gender', ['F'])->count();	
		
		
		
		  //Voters Gender
		$select2 ="t1.*";
		$getGenderVoters = \DB::table('votersinfomations as t1')
		->select(\DB::raw($select2))
		->Join('campaign_group_members AS t2','t2.user_id','=','t1.id')
		->get();
	
		$genderVoters = array();
		foreach($getGenderVoters  as $data)
		{	
			$genderVoters[$data->gender][] = $data;
			
		}
		
		if(@$genderVoters['M'] && count(@$genderVoters['M']) > 0)
		{
			$voterMemberMale  = count(@$genderVoters['M']);
		}
		else{
			$voterMemberMale  = 0;
		}
	
		
		if(@$genderVoters['F'] && count(@$genderVoters['F']) > 0)
		{
			$voterMemberFemale  = count(@$genderVoters['F']);
		}
		else{
			$voterMemberFemale  = 0;
		}
		

		
		$gender = array('Male' => $voterMale , 'Female' => $voterFemale, 'voterMemberMale' => $voterMemberMale, 'voterMemberFemale' => $voterMemberFemale );
		
		
	
	   
	   //Total Voters and Projected Voters
	   $TotalVoters  = \DB::table('votersinfomations')->count();
	   $TotalprojectedVoters  = \DB::table('campaign_group_members')->count();
	   
	   
	   $getMothchart  = \DB::table('campaign_group_members')
								->selectRaw('year(created_at) year, monthname(created_at) month, count(*) data')
								->where(\DB::raw('YEAR(created_at)'), '=', '2021' )
								 ->groupBy('year', 'month')
								 ->orderBy('year', 'desc')
								 ->get();
	   
	   $months = array(
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December',
		);
		
		
		
	 
	   $getMonth = array();
	   foreach( $months as $data)
	   {	
			$ss = (object) array(
					 'year' => "", 
					 'month' => "",
					 'data' => 0
					
			);
			foreach($getMothchart as $dd){
				
				if($data  == $dd->month)
				{
					$ss = $dd;
				}
				
			}

			$getMonth[$data] = $ss;
		   
	   }
	   
	   
	   $coordinators = "(SELECT COUNT(coordinator_id) from coordinators  where barangay = t1.id) as coordinators";
	   $leaders      = "(SELECT COUNT(leader_id) from leaders  where barangay = t1.id) as leaders";
	   $members      = "(SELECT COUNT(b.id) from campaign_groups as a LEFT JOIN campaign_group_members as b ON a.group_id = b.group_id  where a.barangay = t1.id ) as members";
	   $totalvoters  = "(SELECT COUNT(id) from votersinfomations  where barangay = t1.id) as total_voters";
	
	   //member by barangay
		$select2 ="t1.id,t1.name , {$coordinators},{$leaders},{$members},{$totalvoters} ";
		$barangays = \DB::table('barangays as t1')
		->select(\DB::raw($select2))
		->get();
	   
	   	
		$databarangay = array();
		foreach($barangays as  $barangay)
		{
			
			$databarangay[] = array(
			
								'id' => $barangay->id,
								//'name'=> $barangay->name .":".$barangay->total_voters,
								'name'=> $barangay->name,
								'coordinators' => $barangay->coordinators,
								'leaders' => $barangay->leaders,
								'members' => $barangay->members,
								'total_voters' => $barangay->total_voters,
								
						);
		}
		
		/*
		echo "<pre>";
		print_r($databarangay);
		echo "</pre>";
		exit;
		*/
		
	   
	   
	   
	   //Religions
		$select2 ="t1.name,t2.name as religion";
		$getallReligions = \DB::table('votersinfomations as t1')
		->select(\DB::raw($select2))
		->leftJoin('religions AS t2','t2.id','=','t1.religion')
		->get();	
		
		$religions = array();
		foreach($getallReligions  as $data)
		{	
			if($data->religion == "")
			{
				
				$religions['Undetermined'][] = $data;
			}
			else
			{
				$religions[$data->religion][] = $data;
			}
			
		}
		
		$background_colors = array('#4698d4', '#81ae0a', '#e82b96', '#01a096', '#FF3838');
		$i = 0;
		$dataReligion = array();
		foreach($religions as $key => $religion){
			
			$count = 1;
			foreach($religion as $datax)
			{
				$count++;
			}

			$arr = array(
						'count' => $count,
						'backgroundColor' => $background_colors[$i],
						'percentage' => number_format (($count * 100) / $TotalVoters ,2),
			);
			
			$dataReligion[$key] = $arr;
			
			$i++;
		}
		
	
		
		$age  = \DB::table('votersinfomations')->select('*')->get();		

		if(!empty($age)){
		
			$age18to30  = 0;
			$age31to45  = 0;
			$age46to59  = 0;
			$age60plus  = 0;


		
		
			foreach($age  as $value)
			{
				
					$years = \Carbon\Carbon::parse($value->dob)->diff(\Carbon\Carbon::now())->format('%y');
				
					if($years >= 18 && $years <= 30)
					{
						$age18to30 = $age18to30 + 1;
					}
					else if($years >= 31 && $years <= 45)
					{
						$age31to45 = $age31to45 + 1;
					}
					else if($years >= 46 && $years <= 59)
					{
						$age46to59 = $age46to59 + 1;
					}
					else if($years >= 60)
					{
						$age60plus = $age60plus + 1;
					}
			}
			
			
					
			
		}
		
		$arraymenu = array('parent_menu'=>'dashboard');	
		
		$ageData = array( 
						'age18to30'  => $age18to30, 
						'age31to45'  => $age31to45,
						'age46to59'  => $age46to59,						
						'age60plus'  => $age60plus, 
					);

		
        return view( 'dashboard', 
							array( 
								'ChartSex'=> (object)$gender , 
								'ChartAge' => (object)$ageData ,
								'TotalVoters' => $TotalVoters, 
								'TotalprojectedVoters' => $TotalprojectedVoters, 
								'religions' => $religions, 
								'dataReligion' => ($dataReligion), 
								'MonthChart' => $getMonth,
								'barangays' => $databarangay,
							),
					$arraymenu
				);
    }
	
	
}
