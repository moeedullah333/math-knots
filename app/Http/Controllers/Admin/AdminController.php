<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\imagetable;
use Illuminate\Support\Facades\Auth;
use App\inquiry;
use App\Models\CarServicesModel;
use App\Models\carAds;
use App\Models\CarInspectionModel;
use App\Models\Categories;
use App\Models\InquiriesModel;
use App\Models\InspectionReportQuestions;
use App\Models\listCar;
use App\Models\MechanicModel;
use App\Models\SubCategories;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return void
	 */

	public function __construct()
	{
		//$this->middleware('auth');


	}

	public function index()
	{
		return view('auth.login')->with('title', 'Josue Francois');;
	}

	public function dashboard()
	{
		return view('admin.dashboard.index');
	}


	public function configSettingUpdate()
	{

		if (isset($_POST)) {

			foreach ($_POST as $key => $value) {
				if ($key == '_token') {
					continue;
				}

				DB::UPDATE("UPDATE m_flag set flag_value = '" . $value . "',flag_additionalText = '" . $value . "' where flag_type = '" . $key . "'");
			}
		}
		session()->flash('message', 'Successfully Updated');
		return redirect('admin/config/setting');
	}

	public function faviconEdit()
	{

		$user = Auth::user();
		$favicon = DB::table('imagetable')->where('table_name', 'favicon')->first();

		return view('admin.dashboard.index-favicon')->with(compact('favicon'))->with('title', $user->name . ' Edit Favicon');
	}

	public function faviconUpload(Request $request)
	{

		$validArr = array();
		if ($request->file('image')) {
			$validArr['image'] = 'required|mimes:jpeg,jpg,png,gif|required|max:10000';
		}

		$this->validate($request, $validArr);

		$requestData = $request->all();
		$imagetable = imagetable::where('table_name', 'favicon')->first();

		if (count($imagetable) == 0) {

			$file = $request->file('image');

			$destination_path = public_path('uploads/imagetable/');
			$profileImage = date("Ymd") . "." . $file->getClientOriginalExtension();

			Image::make($file)->resize(16, 16)->save($destination_path . DIRECTORY_SEPARATOR . $profileImage);

			$image = new imagetable;
			$image->img_path = 'uploads/imagetable/' . $profileImage;
			$image->table_name = 'favicon';
			$image->save();
		} else {

			if ($request->hasFile('image')) {
				$image_path = public_path($imagetable->img_path);

				if (File::exists($image_path)) {
					File::delete($image_path);
				}

				$file = $request->file('image');
				$fileNameExt = $request->file('image')->getClientOriginalName();
				$fileNameForm = str_replace(' ', '_', $fileNameExt);
				$fileName = pathinfo($fileNameForm, PATHINFO_FILENAME);
				$fileExt = $request->file('image')->getClientOriginalExtension();
				$fileNameToStore = $fileName . '_' . time() . '.' . $fileExt;


				$pathToStore = public_path('uploads/imagetable/');
				Image::make($file)->resize(16, 16)->save($pathToStore . DIRECTORY_SEPARATOR . $fileNameToStore);


				imagetable::where('table_name', 'favicon')
					->update(['img_path' => 'uploads/imagetable/' . $fileNameToStore]);
			}
		}

		session()->flash('message', 'Successfully updated the favicon');
		return redirect('admin/favicon/edit');
	}


	public function logoEdit()
	{

		$user = Auth::user();

		return view('admin.dashboard.index-logo')->with('title', $user->name . '  Edit Logo');
	}

	public function logoUpload(Request $request)
	{

		$validArr = array();
		if ($request->file('image')) {
			$validArr['image'] = 'required|mimes:jpeg,jpg,png,gif|required|max:10000';
		}

		$this->validate($request, $validArr);

		$requestData = $request->all();
		$imagetable = imagetable::where('table_name', 'logo')->first();

		if (count($imagetable) == 0) {

			$file = $request->file('image');

			$destination_path = public_path('uploads/imagetable/');
			$profileImage = date("Ymd") . "." . $file->getClientOriginalExtension();

			Image::make($file)->save($destination_path . DIRECTORY_SEPARATOR . $profileImage);

			$image = new imagetable;
			$image->img_path = 'uploads/imagetable/' . $profileImage;
			$image->table_name = 'logo';
			$image->save();
		} else {

			if ($request->hasFile('image')) {

				$image_path = public_path($imagetable->img_path);

				if (File::exists($image_path)) {
					File::delete($image_path);
				}

				$file = $request->file('image');
				$fileNameExt = $request->file('image')->getClientOriginalName();
				$fileNameForm = str_replace(' ', '_', $fileNameExt);
				$fileName = pathinfo($fileNameForm, PATHINFO_FILENAME);
				$fileExt = $request->file('image')->getClientOriginalExtension();
				$fileNameToStore = $fileName . '_' . time() . '.' . $fileExt;


				$pathToStore = public_path('uploads/imagetable/');
				Image::make($file)->save($pathToStore . DIRECTORY_SEPARATOR . $fileNameToStore);


				imagetable::where('table_name', 'logo')
					->update(['img_path' => 'uploads/imagetable/' . $fileNameToStore]);
			}
		}

		session()->flash('message', 'Successfully updated the logo');
		return redirect('admin/logo/edit');
	}


	public function contactSubmissions()
	{
		$contact_inquiries = DB::table('inquiry')->get();

		return view('admin.inquires.contact_inquiries', compact('contact_inquiries'));
	}

	public function contactSubmissionsDelete($id)
	{

		$del = DB::table('inquiry')->where('id', $id)->delete();

		if ($del) {
			return redirect('admin/contact/inquiries')->with('flash_message', 'Contact deleted!');
		}
	}

	public function inquiryshow($id)
	{
		$inquiry = inquiry::findOrFail($id);
		return view('admin.inquires.inquirydetail', compact('inquiry'));
	}

	public function newsletterInquiries()
	{

		$newsletter_inquiries = DB::table('newsletter')->get();

		return view('admin.inquires.newsletter_inquiries', compact('newsletter_inquiries'));
	}

	public function newsletterInquiriesDelete($id)
	{
		$del = DB::table('newsletter')->where('id', $id)->delete();

		if ($del) {
			return redirect('admin/newsletter/inquiries')->with('flash_message', 'Contact deleted!');
		}
	}

	public function configSetting()
	{
		return view('admin.dashboard.index-config');
	}

	public function user_listing()
	{
		$users = User::with('user_role')->where('user_role',2)->get(['id', 'first_name', 'last_name', 'phone', 'email', 'image', 'user_role', 'created_at', 'updated_at']);
		return response()->json(['status' => true, 'msg' => "User Get Success", 'data' => $users]);
	}
	
	public function view_user($id)
    {
        $userDetail = User::with('user_role')->where('id', $id)->where('user_role', 2)
            ->first(['id', 'first_name', 'last_name', 'phone', 'email', 'image', 'user_role', 'created_at', 'updated_at']);
        return Response(['status' => 'Success', 'message' => 'User Details', 'data' => $userDetail], 200);
    }
    
    public function view_mechanic($id)
    {
        $userDetail = User::with('user_role')->where('id', $id)->where('user_role', 3)
            ->first(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);
            
        if($userDetail) 
        {
		    $userDetail->status = ($userDetail->status == "pending") ? 0: 1;
		
		    $car_services = [];
		    if(isset($userDetail->services))
		    {
		        
    		    foreach(json_decode($userDetail->services) as $data)
    		    {
    		        $car_services[] = CarServicesModel::where('id',$data)->first(['id','name']);
    		    }
		    }
		    
		    $userDetail->car_services = $car_services;
		   
            return Response(['status' => 'Success', 'message' => 'Mechanic Details', 'data' => $userDetail], 200);
        }
        else
        {
            return Response(['status' => 'Fail', 'message' => 'Not Found'], 200);
        }
      
    }

	public function mechanic_listing()
	{
		$mechanics = User::with('user_role')->where('user_role', 3)->get(['id', 'first_name', 'last_name', 'email', 'city', 'zip_code', 'state', 'ssn', 'years_of_exp', 'services', 'image', 'user_role', 'created_at', 'updated_at']);
		
		foreach($mechanics as $item)
		{
		    $item->status = ($item->status == "pending") ? 0: 1;
		
		    $car_services = [];
		    if(isset($item->services))
		    {
		        
    		    foreach(json_decode($item->services) as $data)
    		    {
    		        $car_services[] = CarServicesModel::where('id',$data)->first(['id','name']);
    		    }
		    }
		    
		    $item->car_services = $car_services;
		    
		}
		
		
		
		

		return response()->json(['status' => true, 'msg' => "Mechanics Get Success", 'data' => $mechanics]);
	}

	public function mechanic_status_change($user)
	{
		$user = User::where('id', $user)->first();

        
		if ($user) {
		    $user->status = ($user->status == "pending") ? "approved" : "pending";
			$user->save();

			return response()->json(['status' => true, 'msg' => "Mechanics Status Change Success", 'data' => $user]);
		} else {
			return response()->json(['status' => true, 'msg' => "Mechanics Status Didn't Exists Or Can't Be Change! "]);
		}
	}

	public function cars_listing()
	{
		$cars = listCar::with(['carAdd', 'userDetail', 'carImages', 'car_inspection_detail'])->get();

		return response()->json(['status' => true, 'msg' => "Mechanics Get Success", 'data' => $cars]);
	}

	public function ads_listing()
	{
		$data = carAds::with('car_detail')->get();

		return response()->json(['status' => true, 'msg' => "Car Ad's Get Success", 'data' => $data]);
	}

	public function car_services_listing()
	{
		$data = CarServicesModel::get(['id', 'name']);

		return response()->json(['status' => true, 'msg' => "Car Services Get Success", 'data' => $data]);
	}

	public function car_service_add_update(Request $req, CarServicesModel $car)
	{
		$msg = isset($car->id) ? "Update Success" : "Add Success";

		$car->name = isset($req->name) ? $req->name : $car->name;

		$car->save();

		return response()->json(['status' => true, 'msg' => "Car Service" . $msg, 'data' => $car]);
	}

	public function car_service_delete(CarServicesModel $car)
	{
		$car->delete();

		return response()->json(['status' => true, 'msg' => "Car Service Delete Success", 'data' => $car]);
	}

	public function view_car_service(CarServicesModel $car)
	{

		return response()->json(['status' => true, 'msg' => "Car Service Get Success", 'data' => $car]);

	}
	
	public function request_inspection_new_listing()
	{
		$data = CarInspectionModel::where('status', 'pending')->latest()->get();

		$new_jobs = [];
		foreach ($data as $key => $item) {
			$services = [];
			$new_jobs[$key] = $item;

            
			foreach (json_decode($item->services) as $keyjob => $job) {
				$services[$keyjob] = CarServicesModel::where('id', $job)->first(['id', 'name'])->toArray();
			}
			$new_jobs[$key]['services'] = $services;
		}
		// dd($new_jobs);

		return response()->json(['status' => true, 'msg' => "Car Request Inspections New Get Success", 'data' => $new_jobs]);
	}

	public function request_inspection_inprogress_listing()
	{
		$data = CarInspectionModel::where('status', 'in progress')->get();

		$new_jobs = [];
		foreach ($data as $key => $item) {
			$services = [];
			$new_jobs[$key] = $item;

			foreach (json_decode($item->services) as $keyjob => $job) {
				$services[$keyjob] = CarServicesModel::where('id', $job)->first(['id', 'name'])->toArray();
			}
			$new_jobs[$key]['services'] = $services;
		}

		return response()->json(['status' => true, 'msg' => "Car Request Inspections In progress Get Success", 'data' => $new_jobs]);
	}
	
	public function request_inspection_completed_listing()
	{
		$data = CarInspectionModel::where('status', 'complete')->get();

		$new_jobs = [];
		foreach ($data as $key => $item) {
			$services = [];
			$new_jobs[$key] = $item;

			foreach (json_decode($item->services) as $keyjob => $job) {
				$services[$keyjob] = CarServicesModel::where('id', $job)->first(['id', 'name'])->toArray();
			}
			$new_jobs[$key]['services'] = $services;
		}

		return response()->json(['status' => true, 'msg' => "Car Request Inspections Complete Get Success", 'data' => $new_jobs]);
	}

	public function inquiry_add(Request $req)
	{
		$inquiry = new InquiriesModel();
		$inquiry->name = $req->name;
		$inquiry->email = $req->email;
		$inquiry->message = $req->message;

		$inquiry->save();

		return response()->json(['status' => true, 'msg' => 'Inquiry Send Success']);
	}

	public function inquiry_listing()
	{
		$data = InquiriesModel::get();

		return response()->json(['status' => true, 'msg' => "Inquiries Get Success", 'data' => $data]);
	}

	public function request_inspection_earning()
	{
		$data = CarInspectionModel::where('status', 'in progress')->get();

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
	
	public function category_add_update(Request $req, Categories $category)
	{
		$msg = isset($category->id) ? "Update Success" : "Add Success";

		$category->name = isset($req->name) ? $req->name : $category->name;

		if ($category->save()) {
			return response()->json(['status' => true, 'msg' => 'Category ' . $msg, 'data' => $category]);
		} else {
			return response()->json(['status' => false, 'msg' => 'Something Went Wrong!']);
		}
	}

	public function sub_category_add_update(Request $req, SubCategories $subcategory)
	{
		$msg = isset($subcategory->id) ? "Update Success" : "Add Success";

		if (!isset($subcategory->id)) {
			$validator =
				[
					'name' => 'required',
					'category_id' => 'required'
				];
			$validate = Validator::make($req->all(), $validator);


			if ($validate->fails()) {
				$response =
					[
						'success' => false,
						'message' => $validate->errors()
					];

				return response()->json(["error" => $response, 'msg' => 'validator Error'], 400);
			}
		}

		$subcategory->name = isset($req->name) ? $req->name : $subcategory->name;
		$subcategory->category_id = isset($req->category_id) ? $req->category_id : $subcategory->category_id;

		if ($subcategory->save()) {
			return response()->json(['status' => true, 'msg' => 'Category ' . $msg, 'data' => $subcategory]);
		} else {
			return response()->json(['status' => false, 'msg' => 'Something Went Wrong!']);
		}
	}

	public function category_listing()
	{
		$data = Categories::get();
		
		$category_data = [];
		foreach ($data as $key => $item) {
			$category_data[$key] = $item;
			$question = InspectionReportQuestions::where('category_id', $item->id)->count();
			$category_data[$key]['total_question'] = $question;
		}


		return response()->json(['status' => true, 'msg' => "Categories get Success", 'data' => $category_data]);

// 		return response()->json(['status' => true, 'msg' => "Categories get Success", 'data' => $data]);
	}

	public function sub_category_listing($category_id)
	{
		$data = SubCategories::where('category_id', $category_id)->get();
		
		$category_data = [];
		foreach ($data as $key => $item) {
			$category_data[$key] = $item;
			$question = InspectionReportQuestions::where(['sub_category_id'=> $item->id,'category_id'=>$category_id])->count();
			$category_data[$key]['total_question'] = $question;
		}

       
		if (count($category_data) > 0) {
			return response()->json(['status' => true, 'msg' => "SubCategories get Success", 'data' => $category_data]);
		} else {
			return response()->json(['status' => false, 'msg' => 'No SubCategory Found!',"data"=>[]]);
		}
	}

	public function catgeory_delete($category_id)
	{
		$category = Categories::where('id', $category_id)->first();

		if ($category) {

			$category->delete();

			return response()->json(['status' => true, 'msg' => "Category Delete Success", 'data' => $category]);
		} else {
			return response()->json(['status' => false, 'msg' => 'No Category Found!']);
		}
	}

	public function sub_catgeory_delete($category_id, $subcategory_id)
	{
		$subcategory = SubCategories::where(['id' => $subcategory_id, 'category_id' => $category_id])->first();

		if ($subcategory) {
			$subcategory->delete();
			return response()->json(['status' => true, 'msg' => "Sub Category Delete Success", 'data' => $subcategory]);
		} else {
			return response()->json(['status' => false, 'msg' => 'No Sub Category Found!']);
		}
	}

	public function add_update_inspection_report_question(Request $req, InspectionReportQuestions $question_id)
	{
	    
	   
		$msg = isset($question_id->id) ?  "Update Success" : "Add Success";

		if (!isset($question_id->id)) {
			$validator =
				[
					'category_id' => 'required',
					'question' => 'required'
				];
			$validate = Validator::make($req->all(), $validator);


			if ($validate->fails()) {
				$response =
					[
						'success' => false,
						'message' => $validate->errors()
					];

				return response()->json(["error" => $response, 'msg' => 'validator Error'], 400);
			}
		}
		
		
		$options = [];
		if(isset($req->options))
		{
    		foreach($req->options as $key=> $option)
    		{
    			$options[] = [
    				"id"=>$key+1,
    				"text"=>$option
    			];
    		}
		}
		
		$question_id->category_id = isset($req->category_id) ? $req->category_id : $question_id->category_id;
		$question_id->sub_category_id = isset($req->sub_category_id) ? $req->sub_category_id : $question_id->sub_category_id;
    	$question_id->question = isset($req->question) ? $req->question : $question_id->question;
		$question_id->options = isset($req->options) ? json_encode($options) : $question_id->options;
    
		if ($question_id->save()) {
			return response()->json(['status' => true, 'msg' => "Question " . $msg, 'data' => $question_id]);
		} else {
			return response()->json(['status' => false, 'msg' => "Something Went Wrong!"]);
		}
	}

	public function question_listing()
	{
		$data = InspectionReportQuestions::with(
			'category_detail',
			'sub_category_detail'
		)->get();

		$questions = [];
		foreach ($data as $key => $question) {
			$questions[$key] = $question;
			$questions[$key]['options'] = json_decode($question->options);
		}

		return response()->json(['status' => true, 'msg' => "inspection report questions get success",'data'=>$questions]);
	}
	
	
	public function report_inspection()
	{

		// if ($question['sub_category_id'] != null) {
		// 	$subcategory = SubCategories::where(['id' => $question['sub_category_id'], 'category_id' => $question['category_id']])->first()->toArray();
		// 	$questions[$cat['id']][$subcategory['name']] = ["question" => $question['question'], 'options' => json_decode($question['options'])];
		// } else {
		// 	$questions[$cat['id']][$question['question']] = ["question" => $question['question'], 'options' => json_decode($question['options'])];
		// }

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

	public function question_view($question)
	{
		$data = InspectionReportQuestions::with(
			'category_detail',
			'sub_category_detail'
		)
		->where('id', $question)
		->first();

		if($data)
		{
			$data->options = json_decode($data->options);
			
			return response()->json(['status' => true, 'msg' => "inspection report questions get success", 'data' => $data]);
		}
		else
		{
			return response()->json(['status' => false, 'msg' => "not found"]);

		}
	}

	public function question_search($category_id, $subcategory_id = null)
	{
		$data = "";
		if (isset($category_id) && isset($subcategory_id)) {
			$data = InspectionReportQuestions::with(
				'category_detail',
				'sub_category_detail'
			)
				->where('category_id', $category_id)
				->where('sub_category_id', $subcategory_id)
				->get();
				
			foreach($data as $item)
			{
			   	$item->options = json_decode($item->options); 
			}
		} else if (isset($category_id) && !isset($subcategory_id)) {
			$data = InspectionReportQuestions::with(
				'category_detail',
				'sub_category_detail'
			)
				->where('category_id', $category_id)
				->get();

			foreach($data as $item)
			{
			   	$item->options = json_decode($item->options); 
			}	
		}

		if(isset($data))
		{
			return response()->json(['status' => true, 'msg' => "inspection report questions get success",'data'=>$data]);
		}
		else
		{
			return response()->json(['status' => true, 'msg' => "not found"]);

		}

	}

	public function question_delete(InspectionReportQuestions $question)
	{
		$question->delete();

		return response()->json(['status' => true, 'msg' => "inspection report questions delete success", 'data' => $question]);
	}
}
