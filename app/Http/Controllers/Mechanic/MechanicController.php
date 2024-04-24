<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\CarInspectionModel;
use App\Models\CarServicesModel;
use App\Models\ReportInspectionComment;
use App\Models\Categories;
use App\Models\InspectionReportQuestions;
use App\Models\ReportInspectionComplete;
use App\Models\SubCategories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MechanicController extends Controller
{
    public function mechanic_detail_add_update(Request $req)
    {
        $mechanic = User::where('id', auth()->user()->id)->first(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);

        $mechanic->email = isset($req->email) ? $req->email : $mechanic->email;
        $mechanic->first_name = isset($req->first_name) ? $req->first_name : $mechanic->first_name;
        $mechanic->last_name = isset($req->last_name) ? $req->last_name : $mechanic->last_name;
        $mechanic->city = isset($req->city) ? $req->city : $mechanic->city;
        $mechanic->state = isset($req->state) ? $req->state : $mechanic->state;
        $mechanic->zip_code = isset($req->zip_code) ? $req->zip_code : $mechanic->zip_code;
        $mechanic->ssn = isset($req->ssn) ? $req->ssn : $mechanic->ssn;
        $mechanic->years_of_exp = isset($req->years_of_exp) ? $req->years_of_exp : $mechanic->years_of_exp;
        $mechanic->services = isset($req->services) ? $req->services : $mechanic->services;
        if ($mechanic->save()) {
            return response()->json(['status' => true, 'message' => "Mechanic Detail Update Success", 'data' => $mechanic], 200);
        } else {
            return response()->json(['status' => false, 'message' => "Something Went Wrong!"], 200);
        }
    }

    public function view_profile()
    {
        $mechanic = User::where('id', auth()->user()->id)->first(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role','status', 'created_at', 'updated_at']);
        // $mechanic = MechanicModel::with('mechanic_image')->where('id', $role->user_id)->first();
        $data = CarInspectionModel::where(['status' => 'in progress', 'mechanic_id' => auth()->user()->id])->get();


        $mechanic->profile_status = isset($mechanic->status) ? $mechanic->status: "pending";  
        $new_jobs = [];
        $totalJobs = 0;
        
        
        
        $jobs = CarInspectionModel::where(['status' => 'completed', 'mechanic_id' => auth()->user()->id])->get()->toArray();
            
        if(!empty($jobs)){
            $totalJobs = count($jobs);
        }
        if(count($data) > 0){
           
            
            
            foreach ($data as $key => $item) {
                $services = [];
                $new_jobs[$key] = $item;
    
                foreach (json_decode($item->services) as $keyjob => $job) {
                    $services[$keyjob] = CarServicesModel::where('id', $job)->first(['id', 'name'])->toArray();
                }
                $new_jobs[$key]['services'] = $services;
            }
        }


        $earning = 0;
        if(!empty($new_jobs)){
            $earning = array_sum(array_column($new_jobs, 'amount'));
        }
        
        $mechanic->total_earnings = $earning;
        
        $mechanic->completed_jobs = $totalJobs;
        
        return response()->json(['status' => true, 'message' => "Mechanic Detail get Success", 'data' => $mechanic], 200);
    }

    public function mechanic_image_upload(Request $request)
    {
        $mechanic = User::where('id', auth()->user()->id)->first(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);

        if ($request->hasFile('image')) {

            // $mechaincDetail = MechanicDetailModel::where('user_id', $mechanic->id)->first();
            if ($mechanic->image != 'uploads/mechanic/dummy-user.png') {
                File::delete(public_path($mechanic->image));
            }

            $file = request()->file('image');
            $destination_path = 'uploads/mechanic/';
            $fileName = date("Ymdhis") . uniqid() . "." . $file->getClientOriginalExtension();
            //dd($photo,$filename);
            $file->move(public_path('uploads/mechanic/'), $fileName);
            $mechanic->image = $destination_path . $fileName;
            $mechanic->save();
        }

        return response()->json(['status' => true, 'message' => "Mechanic Detail get Success", 'data' => $mechanic], 200);
    }

    public function mechanic_listing()
    {
        $mechanic = User::where('user_role', 3)->get(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);

        return response()->json(['status' => true, 'message' => "Mechanic's get Success", 'data' => $mechanic], 200);
    }

    public function mechanic_search(Request $req)
    {

        $mechanic = User::where('user_role', 3)->first(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);

        if (isset($req->serch_value) && $req->search_value == "") {
            $search_value = $req->search_value;

            $mechanic
                ->orWhere('first_name', 'LIKE', "%{$search_value}%")
                ->orWhere('last_name', 'LIKE', "%{$search_value}%")
                ->orWhere('city', 'LIKE', "%{$search_value}%")
                ->orWhere('zip_code', 'LIKE', "%{$search_value}%")
                ->orWhere('state', 'LIKE', "%{$search_value}%")
                ->orWhere('ssn', 'LIKE', "%{$search_value}%")
                ->orWhere('years_of_exp', 'LIKE', "%{$search_value}%")
                ->get();

            return response()->json(['status' => true, 'message' => "Mechanic's get Success", 'data' => $mechanic], 200);
        } else {
            return response()->json(['status' => true, 'message' => "Mechanic's get Success", 'data' => $mechanic], 200);
        }
    }

    public function request_inspection_pick(CarInspectionModel $reqId)
    {
        $mechanic = User::where('id', auth()->user()->id)->first(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);

        $car_inspection = CarInspectionModel::where('mechanic_id', auth()->user()->id)->get();
        $count = 0;

        if ($car_inspection) {
            foreach ($car_inspection as $item) {
                if ($item->inspection_date == $reqId->inspection_date && $item->inspection_time == $reqId->inspection_time) {
                    $count++;
                }
            }
        }

        if ($count == 0) {
            $reqId->mechanic_id = $mechanic->id;
            $reqId->status = "in progress";
            $reqId->save();
        } else {
            return response()->json(['status' => false, 'msg' => "You Already Have Request Of Current Date and Time"]);
        }


        return response()->json(['status' => true, 'msg' => "Request Pick Success", 'data' => $reqId]);
    }

    public function mechanic_inspection_list()
    {
        $data = CarInspectionModel::with(['user_detail'])->where('status', 'pending')->get();
        
       
         $request_inspections = [];
        foreach ($data as $item) {
            if (isset($item->services)) {
               
                $check = array_intersect(json_decode($item->services), json_decode(auth()->user()->services));
                
                if ($check) {
                    $request_inspections[] = $item;
                }
                
            }
        }
               

        return response()->json(['status' => true, 'msg' => 'request inspection list get success', 'data' => $request_inspections]);
    }

    public function my_mechanic_inspection_list()
    {
        $data = CarInspectionModel::with(['user_detail'])->where(['status' => 'in progress', 'mechanic_id' => auth()->user()->id])->get();

        return response()->json(['status' => true, 'msg' => 'my request inspection list get success', 'data' => $data]);
    }
    
    public function my_completed_mechanic_inspection_list()
    {
        $data = CarInspectionModel::with(['user_detail'])->where(['status' => 'complete', 'mechanic_id' => auth()->user()->id])->get();

        
        return response()->json(['status' => true, 'msg' => 'my request inspection list get success', 'data' => $data]);
    }
    
    public function request_inspection_earning()
    {
        $data = CarInspectionModel::where(['status' => 'in progress', 'mechanic_id' => auth()->user()->id])->get();

        $new_jobs = [];
        foreach ($data as $key => $item) {
            $services = [];
            $new_jobs[$key] = $item;

            foreach (json_decode($item->services) as $keyjob => $job) {
                $services[$keyjob] = CarServicesModel::where('id', $job)->first(['id', 'name'])->toArray();
            }
            $new_jobs[$key]['services'] = $services;
        }

        $earning = array_sum(array_column($new_jobs, 'amount'));

        return response()->json(['status' => true, 'msg' => "Total Earning", 'data' => $earning]);
    }
    public function report_inspection()
	{

		$data = InspectionReportQuestions::with(
			'category_detail',
			'sub_category_detail'
		)->get()->toArray();

		$questions = [];

		foreach ($data as $key => $question) {
			$cat = Categories::where('id', $question['category_id'])->first()->toArray();

			if ($question['sub_category_id'] == null) {
				$questions[intval($cat['id'])]['id'] = $cat['id'];
				$questions[intval($cat['id'])]['text'] = $cat['name'];
					$options = json_decode($question['options']);
				$new_options = [];
				foreach($options as $opt )
				{
				    if($opt->text != null)
				    {
				        $new_options[] = $opt;
				    }
				}
				$questions[intval($cat['id'])]['questions'][$key] = ['id' => $question['id'], 'text' => $question['question'], 'options' => $new_options];
			}
			elseif ($question['sub_category_id'] !=  null) {
				$subcategory = SubCategories::where(['id' => $question['sub_category_id'], 'category_id' => $question['category_id']])->first()->toArray();
				$questions[intval($cat['id'])]['id'] = $cat['id'];
				$questions[intval($cat['id'])]['text'] = $cat['name'];
				$questions[intval($cat['id'])]['subcategories'][intval($subcategory['id'])]['id'] = $subcategory['id'];
				$questions[intval($cat['id'])]['subcategories'][intval($subcategory['id'])]['text'] = $subcategory['name'];
				$options = json_decode($question['options']);
				$new_options = [];
				foreach($options as $opt )
				{
				    if($opt->text != null)
				    {
				        $new_options[] = $opt;
				    }
				}
			
				$questions[intval($cat['id'])]['subcategories'][intval($subcategory['id'])]['questions'][] = ['id' => $question['id'], 'text' => $question['question'], 'options' => $new_options];
			}
			
		
		}
		
		$new_questions = [];
		
		foreach($questions as $key => $item)
		{
		    $new_questions[$key] = $item;
		   if(isset($new_questions[$key]['subcategories']))
		   {
		      $new_questions[$key]['subcategories'] = array_values($new_questions[$key]['subcategories']);
		   }
		}
		
	
	   
	    
		return response()->json(['status' => true, 'msg' => "inspection report questions get success", 'data' => array_values($new_questions)]);
	}
	
	 public function report_inpsection_submit(Request $req)
    {
        $user_id = auth()->user()->id;
        $car_id = $req->car_id;
        // return response()->json(['data'=>$req->report]);
        $report_inspection = $req->report;

      
        foreach ($report_inspection as $report) {

            $inspection_report = new ReportInspectionComplete();
            $inspection_report->car_id = $car_id;
            $inspection_report->mechanic_id = $user_id;
            $inspection_report->category_id = $report['category_id'];
            $inspection_report->sub_category_id = $report['subcategory_id'];
            $inspection_report->question_id = $report['question_id'];
            $inspection_report->option_id = $report['option_id'];
            $inspection_report->category_score = $report['category_score'];
            $inspection_report->save();
        }
        $check = ReportInspectionComment::where('car_id',$car_id)->first();
        if($check)
        {
            $check->comment = $req->comment;
            $check->save();
        }
        else
        {
            $comment = new ReportInspectionComment();
            $comment->car_id = $car_id;
            $comment->comment = $req->comment;
            $comment->save();
        }

        $car_inspection = CarInspectionModel::where(['car_id'=>$car_id,'mechanic_id'=>$user_id])->first();

        $car_inspection->status = "complete";
        $car_inspection->save();


        return response()->json(['status' => true, 'msg' => "Report Inspection Sumbit and Car Inspection Complete Success"]);
    }
// public function report_inpsection_submit(Request $req)
// {
//     try {
//         $user_id = auth()->user()->id;
//         $car_id = $req->car_id;
        
        
        
        
//         // Check if $req->report is a string before decoding
//         $report_inspection = is_string($req->report) ? json_decode($req->report) : [];

       
//         // Check if JSON decoding was successful
//         if (json_last_error() != JSON_ERROR_NONE) {
//             throw new \Exception('Error decoding JSON: ' . json_last_error_msg());
//         }

//         foreach ($report_inspection as $report) {
//             $inspection_report = new ReportInspectionComplete();
//             $inspection_report->car_id = $car_id;
//             $inspection_report->mechanic_id = $user_id;
//             $inspection_report->category_id = $report->category_id;
//             $inspection_report->sub_category_id = $report->subcategory_id;
//             $inspection_report->question_id = $report->question_id;
//             $inspection_report->option_id = $report->option_id;
//             $inspection_report->save();
//         }
        

//         $car_inspection = CarInspectionModel::where(['car_id' => $car_id, 'mechanic_id' => $user_id])->first();

//         $car_inspection->status = "complete";
//         $car_inspection->save();

//         return response()->json(['status' => true, 'msg' => "Report Inspection Submit and Car Inspection Complete Success"]);
//     } catch (\Exception $e) {
//         // Handle the exception, log it, or print an error message
//         return response()->json(['status' => false, 'msg' => $e->getMessage()]);
//     }
// }

}
