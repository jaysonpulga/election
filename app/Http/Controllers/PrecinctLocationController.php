<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\provinces;
use App\cities;
use App\barangays;
use App\precincts;




class PrecinctLocationController extends Controller
{
	
	
	public function getbarangaybelongtoCity(Request $request)
	{
		 //re-able ONLY_FULL_GROUP_BY
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		
		$select ="t1.id,t1.name";
		$getbarangay = \DB::table('barangays as t1')
		->select(\DB::raw($select))
		->where('t1.city_id', '=',$request->city)
		->groupBy('t1.id')
		->orderby('t1.name','asc')
		->get();
	
		$data = array("data" => $getbarangay);
		return response()->json($data);
		
		
		
	}
	
	
	public function precinct(Request $request){
		
		   $arraymenu = array('parent_menu'=>'masterData','child_menu'=>'LocationInfo');
		  return view('LocationInfo.precinct',array('menu'=>'precinct','cities'=>cities::All(),'precincts'=>precincts::All()), $arraymenu);		
	}
	
	
	public function saving_precinct_info(Request $request)
	{

		if($request->action == "create_new")
		{
			$provinces = new precincts();
			$provinces->name  	      =  $request->precinct;
			$provinces->cluster   	  =  $request->cluster;
			$provinces->barangay_id   =  $request->barangay;
			$provinces->save();
		}
		else if($request->action == "update")
		{
			
			\DB::table('precincts')
			  ->where('id', $request->id)
              ->update([
					'name' => $request->precinct,
					'cluster' => $request->cluster,
					'barangay_id' => $request->barangay,
			  ]);
		}
		
		
		echo "save";
	
	}
	
	
	public function get_precinct(Request $request)
	{

			
			
			$select ="t1.name,t1.id,t1.cluster,t1.barangay_id,t3.id as city_id";		
			$getallData = \DB::table('precincts as t1')
			->join('barangays as t2','t2.id','=','t1.barangay_id')
			->join('cities as t3','t3.id','=','t2.city_id')
			->select(\DB::raw($select))
			->where('t1.id',$request->id)
			->first();

				
			return response()->json(array('success' => true,'data'=>$getallData));
	}
	
	
	
	
	public function deleteprecinct(Request $request)
	{
		precincts::where('id',$request->id)->delete();
		echo "delete";
		
	}
	
	public function loadAllprecinct(Request $request)
	{
		
		
		//re-able ONLY_FULL_GROUP_BY
		\DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		
		$limit  	 = $request->input('length');
		$start   	 = $request->input('start');
		$dir 	     = $request->input('order.0.dir');
		$search	 	 = $request->input('search.value');
		$draw 		 = $request->input('draw');
		$select_precinct = $request->select_precinct;
		
        if($limit < 0){ $limit = ""; }
		
		
		$select ="t1.*";
		$totalPrecint = \DB::table('precincts')
		->select(\DB::raw($select))
		->count();
		
		
		if(empty($search))
		{            
		

				//$no_registered_voters = "(SELECT COUNT(id) from votersinfomations   where precint_number = t1.id ) as no_registered_voters";
        		//$select ="t1.name,t1.id,t1.cluster,t2.name as barangay,t3.name as city,t4.name as province,{$no_registered_voters}";
        		
        		$select ="t1.name,t1.id,t1.cluster,t2.name as barangay,t3.name as city,t4.name as province";
        		$getallData = \DB::table('precincts as t1')
        		->join('barangays as t2','t2.id','=','t1.barangay_id')
        		->join('cities as t3','t3.id','=','t2.city_id')
        		->join('provinces as t4','t4.id','=','t3.province_id')
        		->when(!empty($select_precinct), function ($q) use ($select_precinct) {
        			return $q->where('t1.id', $select_precinct );
        		})
        		->when(!empty($limit), function ($q) use ($limit,$start) {
    			    return $q->offset($start)->limit($limit);
        		})
                ->orderBy('t1.name', 'asc')
        		->select(\DB::raw($select))
        		->get();

				$totalFiltered = $totalPrecint;
				
		}else{
		    
		    
		    
		    	//$no_registered_voters = "(SELECT COUNT(id) from votersinfomations   where precint_number = t1.id ) as no_registered_voters";
        		//$select ="t1.name,t1.id,t1.cluster,t2.name as barangay,t3.name as city,t4.name as province,{$no_registered_voters}";
        		
        		$select ="t1.name,t1.id,t1.cluster,t2.name as barangay,t3.name as city,t4.name as province";
        		$getallData = \DB::table('precincts as t1')
        		->join('barangays as t2','t2.id','=','t1.barangay_id')
        		->join('cities as t3','t3.id','=','t2.city_id')
        		->join('provinces as t4','t4.id','=','t3.province_id')
        		->when(!empty($select_precinct), function ($q) use ($select_precinct) {
        			return $q->where('t1.id', $select_precinct );
        		})
    			
    			->when(!empty($limit), function ($q) use ($limit,$start) {
    			    return $q->offset($start)->limit($limit);
        		})
    			
    			
    			->where('t2.name','LIKE',"%{$search}%")
		    	->orWhere('t1.name', 'LIKE',"%{$search}%")
		    	->orWhere('t1.cluster', 'LIKE',"%{$search}%")
    			
        		->select(\DB::raw($select))
        		->orderBy('t1.name', 'asc')
        		->get();
        		

        		$select2 ="t1.name,t1.id,t1.cluster,t2.name as barangay,t3.name as city,t4.name as province";
        		$totalFiltered = \DB::table('precincts as t1')
        		->join('barangays as t2','t2.id','=','t1.barangay_id')
        		->join('cities as t3','t3.id','=','t2.city_id')
        		->join('provinces as t4','t4.id','=','t3.province_id')
        		
        		->when(!empty($select_precinct), function ($q) use ($select_precinct) {
        			return $q->where('t1.id', $select_precinct );
        		})
    			
        		->when(!empty($limit), function ($q) use ($limit,$start) {
        			    return $q->offset($start)->limit($limit);
            	})
            	
            	->where('t2.name','LIKE',"%{$search}%")
		    	->orWhere('t1.name', 'LIKE',"%{$search}%")
		    	
            		
        		->select(\DB::raw($select2))
        		->count();
		    
		

		    
		    
		}
		
		
		$data = array();
		
		if(!empty($getallData)){
		
			foreach ($getallData as $dd){
					
				$row = array();
				$row['id'] =  $dd->id;
				$row['precinct'] =  $dd->name;
				$row['barangay'] =  $dd->barangay;
				$row['city'] =  $dd->city;
				$row['province'] =  $dd->province;
				$row['cluster'] =  $dd->cluster;
				
				//$row['no_registered_voters'] = $dd->no_registered_voters;
				
				$row['action'] = "<a href='javascript:void(0)' class='btn btn-raised btn-primary btnEditPrecinct'  data-id=".$dd->id."><i class='fa fa-fw fa-pencil-square-o'></i> Edit</a>";
				
				
					$row['action'] .= " <a href='javascript:void(0)' class='btn btn-raised btn-danger btnDeletePrecinct'  data-id=".$dd->id."> Delete </a>";
				
				
				$data[] = $row;
			}
		}
		else{
			
			$data = [];
		}
		
		//$output = array("data" => $data);
		//return response()->json($output);
		
		$json_data = array(
						"draw"            => intval($draw),  
						"recordsTotal"    => intval($totalPrecint),  
						"recordsFiltered" => intval($totalFiltered), 
						"data"            => $data,
						);

		
		return response()->json($json_data);
		
		
		
	}

}