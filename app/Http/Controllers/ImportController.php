<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Carbon\Carbon;

use App\votersinfomations;

set_time_limit(50000); // 


class ImportController extends Controller
{
  
	
	public function importdata(){
		
		 return view('importdata');
	}
	
	
	public function importExcel(Request $request)
	{
		
		
		
			$arr_file = explode('.', $_FILES['file']['name']);
			
			$extension = end($arr_file);
	
			if('csv' == $extension){
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			} else {
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			}
	
			$spreadsheet = $reader->load($_FILES['file']['tmp_name']);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			
			/*
			echo "<pre>";
			print_r($sheetData);
			echo "</pre>";
			exit;
			*/
			
			
			$highestRow = $spreadsheet->getActiveSheet()->getHighestRow();
			$highestColumn = $spreadsheet->getActiveSheet()->getHighestColumn();
			
			$i = 1;
			
			foreach($sheetData as $data)
			{
					if($data[1] == "vin_number")
					{
						
						
					}
					else
					{
						
							$id = $data[0];
							$vin_number = $data[1];
							$name = $data[2];
							$religion = $data[3];
							$address = $data[4];
							$barangay = $data[5];
							$city = $data[6];
							$gender = $data[7];
							$dob = Carbon::parse($data[8])->format('Y-m-d');
							$age = str_replace(' ', '',$data[9]);
							$IS_SC_TAG = $data[10];
							$IS_PWD_TAG = $data[11];
							$IS_IL_TAG = $data[12];
							$mobile_number = $data[13];
							$precint_number = $data[14];
							$cluster = $data[15];
							
							
							$select ="id";
							$precincts = \DB::table('precincts')
							->select(\DB::raw($select))
							->where('name',$precint_number)
							->first();
							
							
							$votersinfomations = new votersinfomations(); 
							$votersinfomations->id        	 		=  $id;
							$votersinfomations->vin_number   		=  @$vin_number;
							$votersinfomations->religion   			=  0;
							$votersinfomations->name         		=  @$name;
							$votersinfomations->address      		=  @$address;
							$votersinfomations->barangay       		=  @$barangay;
							$votersinfomations->city_municipality   =  @$city;
							$votersinfomations->gender      		=  @$gender;
							$votersinfomations->dob       			=  Carbon::parse(@$dob)->format('Y-m-d');
							$votersinfomations->age       			=  0;
							$votersinfomations->IS_SC_TAG       	=  @$IS_SC_TAG;
							$votersinfomations->IS_PWD_TAG       	=  @$IS_PWD_TAG;
							$votersinfomations->IS_IL_TAG       	=  @$IS_IL_TAG;
							$votersinfomations->mobile_number       =  @$mobile_number;
							$votersinfomations->precint_number      =  $precincts->id;							
							$votersinfomations->cluster_delete      =  '';
							$votersinfomations->status      		=  0;
							$votersinfomations->remarks      		=  "";
							$votersinfomations->save();
							
							
						
					
						$i++;
					}
					
				
			}
			
			
			echo  "save recrod = " .$i;
	
		
			
		 
		
		
	}
	
	
}
