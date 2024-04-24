<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\listCar;
use App\Models\carImages;
use App\Models\carAds;
use App\Models\CarAdSafeUnSafe;
use App\Models\ReportInspectionComment;
use App\Models\CarInspectionModel;
use App\Models\CarServicesModel;
use App\Models\Categories;
use App\Models\InspectionReportQuestions;
use App\Models\ReportInspectionComplete;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ListCarController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //list All Cars
    public function index(listCar $listCar)
    {
        if (isset($listCar->id)) {
            $listCar = listCar::with(['carAdd', 'userDetail', 'carImages', 'car_inspection_detail'])->find($listCar->id);
            
             $report_inspection = ReportInspectionComplete::where('car_id',$listCar->id)->get();
             
             
                $check = CarInspectionModel::where(['car_id' => $listCar->id])->first();
                
                if ($check) {
                    if ($check->status == "in progress") {
                        $listCar->inspection_status = "in progress";
                        $listCar->inspection = true;
                    } else {
                        $listCar->inspection_status = "pending";
                        $listCar->inspection = true;
                    }
                } else {
                    $listCar->inspection_status = "not avaliable";
                    $listCar->inspection = false;
                }
            
        
            
            // $listCar->carAdd();
            return Response(['status' => 'Success', 'message' => 'Cars Detail', 'data' => $listCar], 200);
        }

        $search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : '';


        if ($search !== '') {
            $data = listCar::where('name', 'like', '%' . $search . '%')
                ->orWhere('make', 'like', '%' . $search . '%')
                ->orWhere('model', 'like', '%' . $search . '%')
                ->orWhere('year', 'like', '%' . $search . '%')
                ->get();


            foreach ($data as $car) {
                $check = CarInspectionModel::where(['car_id' => $car->id])->first();
                if ($check) {
                    if ($check->status == "in progress") {
                        $car->inspection_status = "in progress";
                        $car->inspection = true;
                    } else {
                        $car->inspection_status = "pending";
                        $car->inspection = true;
                    }
                } else {
                    $car->inspection_status = "not avaliable";
                    $car->inspection = false;
                }
            }

            return Response(['status' => 'Success', 'message' => 'All Cars', 'data' => $data], 200);
        } else {
            $cars = listCar::all();

            foreach ($cars as $car) {
                $check = CarInspectionModel::where(['car_id' => $car->id, 'user_id' => auth()->user()->id])->first();
                if ($check) {
                    if ($check->status == "in progress") {
                        $car->inspection_status = "in progress";
                        $car->inspection = true;
                    } else {
                        $car->inspection_status = "pending";
                        $car->inspection = true;
                    }
                } else {
                    $car->inspection_status = "not avaliable";
                    $car->inspection = false;
                }
            }

            return Response(['status' => 'Success', 'message' => 'All Cars', 'data' => $cars], 200);
        }
    }
    
    public function report_inspection(listCar $listCar)
    {
         $report_inspection = ReportInspectionComplete::where('car_id',$listCar->id)->get();
             
             $count = ReportInspectionComplete::where('car_id',$listCar->id)->first();
         
        
           
            if(isset($count))
            {

                $questions = [];
                $rating = 0;
                foreach($report_inspection as $report)
                {
                   
                    $data = InspectionReportQuestions::with(
                        'category_detail',
                        'sub_category_detail'
                    )->where(['id'=>$report->question_id,'category_id'=>$report->category_id,'sub_category_id'=>$report->sub_category_id])->first()->toArray();
            
                 
            
                    // foreach ($data as $key => $question) {
                        $cat = Categories::where('id', $data['category_id'])->first()->toArray();
            
                        if ($data['sub_category_id'] == null) {
                            $rating =$report->category_score;
                            $questions[intval($cat['id'])]['id'] = $cat['id'];
                            $questions[intval($cat['id'])]['text'] = $cat['name'];
                            $questions[intval($cat['id'])]['category_score'] = $rating;
                            $options = json_decode($data['options']);
                            $new_options = "";
                            foreach ($options as $key => $opt) {
                                if ($opt->text != null && $opt->id == $report->option_id) {
                                  
                                    $new_options = $opt;
                                   
                                }
                                // else if($opt->text != null && $opt->id != $report->option_id)
                                // {
                                //     $opt->select = false;
                                //     $new_options[$key] = $opt;
                                // }
                            }
                           
                            $questions[intval($cat['id'])]['questions'][] = ['id' => $data['id'], 'text' => $data['question'], 'options' => $new_options];
                        } elseif ($data['sub_category_id'] !=  null) {
                            $rating = $report->category_score;
                            $subcategory = SubCategories::where(['id' => $data['sub_category_id'], 'category_id' => $data['category_id']])->first()->toArray();
                            $questions[intval($cat['id'])]['id'] = $cat['id'];
                            $questions[intval($cat['id'])]['text'] = $cat['name'];
                            $questions[intval($cat['id'])]['category_score'] = $rating;
                            $questions[intval($cat['id'])]['subcategories'][intval($subcategory['id'])]['id'] = $subcategory['id'];
                            $questions[intval($cat['id'])]['subcategories'][intval($subcategory['id'])]['text'] = $subcategory['name'];
                            $options = json_decode($data['options']);
                            $new_options = "";
                            foreach ($options as $key => $opt) {
                                if ($opt->text != null && $opt->id == $report->option_id) {
                                    
                                    $new_options = $opt;
                                }
                                // else if($opt->text != null && $opt->id != $report->option_id)
                                // {
                                //     $opt->select = false;
                                //     $new_options[$key] = $opt;
                                // }
                            }
            
                            $questions[intval($cat['id'])]['subcategories'][intval($subcategory['id'])]['questions'][] = ['id' => $data['id'], 'text' => $data['question'], 'options' => $new_options];
                        }
                    // }
            
                }
                
                
                
                $new_questions = [];
        
                foreach ($questions as $key => $item) {
                    $new_questions[$key] = $item;
                    if (isset($new_questions[$key]['subcategories'])) {
                        $new_questions[$key]['subcategories'] = array_values($new_questions[$key]['subcategories']);
                    }
                }
                
                
                    
                
    
                $new_questions = array_values($new_questions);
    
            }
            else{
                return response()->json(['status'=>true,'msg'=>"Report Inspection Didn't Exist!"]);
            }
           
            $comment_req = ReportInspectionComment::where('car_id',$listCar->id)->first(['comment','created_at']);
            $comment = "";
            $date = "";
            if($comment_req)
            {
                $comment = $comment_req->comment;
               
                $date = $comment_req->created_at;
            }
            else
            {
                $comment_req = "";
                $date = "";
            }
            
            
            return response()->json(['status'=>true,'msg'=>"report get success",'comment'=>$comment,'date'=>$date,'report'=>$new_questions]);
            
    }

    public function users_car(listCar $listCar)
    {
        $cars = listCar::where('user_id', Auth::user()->id)->get();
        return Response(['status' => 'Success', 'message' => 'All Cars', 'data' => $cars], 200);
    }

    // public function specific_car(listCar $listCar)
    // {

    //         return Response(['status'=>'Success','message'=>'All Cars','data'=>$listCar],200);
    // }
    /**
     * Store a newly created resource in storage.
     */

    // add cars with post add and multiple images

    public function store(Request $request)
    {

        //   dd($request->image, request()->file('images'), request()->file('image'));

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'image'=>'required',
        //     'make'=>'required',
        //     'model' => 'required',
        //     'year' => 'required',
        //     'address'=>'required',
        //     'mileage'=>'required',
        //     'fuel' => 'required',
        //     'documents'=>'required',
        //     'trnasmission' => 'required',
        //     'condition' => 'required',
        //     'color'=>'required',
        //     'price' => 'required',
        //     'description'=>'required',
        //     'title'=>'required',
        //     'contact'=>'required',
        //     'images'=>'required',
        // ]);
        // if($validator->fails()){
        //     return Response(['status'=>false,'errors'=>$validator->errors()],401);
        // }

        $listCar = new listCar;
        $listCar->user_id = Auth::user()->id;
        $listCar->name = $request->name;
        $listCar->make = $request->make;
        $listCar->model = $request->model;
        $listCar->address = $request->address;
        $listCar->year = $request->year;
        $listCar->mileage = $request->mileage;
        $listCar->fuel = $request->fuel;
        $listCar->documents = $request->documents;
        $listCar->trnasmission = $request->trnasmission;
        $listCar->condition = $request->condition;
        $listCar->color = $request->color;
        $listCar->price = $request->price;
        $listCar->description = $request->description;
        $listCar->city = $request->city;
        $listCar->state = $request->state;
        $listCar->zip_code = $request->zip_code;

        if ($request->hasFile('image')) {
            $file = request()->file('image');
            $destination_path = 'uploads/car/';
            $fileName = date("Ymdhis") . uniqid() . "." . $file->getClientOriginalExtension();
            //dd($photo,$filename);
            $file->move(public_path('uploads/car/'), $fileName);
            $listCar->image = $destination_path . $fileName;
        }

        $listCar->save();

        //For Save Multiple Images

        // dd($_FILES);
        if (isset($_FILES['images'])) {
            $photos = request()->file('images');
            if (!empty($photos)) {
                foreach ($photos as $photo) {
                    $destinationPath = 'uploads/car/';

                    $filename = date("Ymdhis") . uniqid() . "." . $photo->getClientOriginalExtension();
                    //dd($photo,$filename);
                    $photo->move(public_path('uploads/car/'), $filename);
                    // Image::make($photo)->save(public_path($destinationPath) . DIRECTORY_SEPARATOR. $filename);
                    $carImage  = new carImages;
                    $carImage->car_id = $listCar->id;
                    $carImage->image = $destinationPath . $filename;
                    $carImage->save();
                }
            }
        }
        //For Save Post Add
        // $carAds = new carAds;
        // $carAds->car_id =  $listCar->id;
        // $carAds->title =  $request->title;
        // $carAds->contact_no =  $request->contact;
        // $carAds->save();
        return Response(['status' => 'Success', 'message' => 'Post Add Successfully', 'data' => $listCar], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(listCar $listCar)
    {


        dd($listCar);

        return Response(['status' => 'Success', 'message' => 'All Cars', 'data' => $listCar], 200);
    }

    public function car_images_delete($car_id, $id)
    {
        $files = carImages::where('car_id', $car_id)->where('id', $id)->first();

        if ($files) {

            if (File::exists(public_path($files->image))) {
                File::delete(public_path($files->image));
            }

            $files->delete();

            return response()->json(['status' => true, 'msg' => "Car Image Delete Success", 'data' => $files]);
        } else {
            return response()->json(['status' => false, 'msg' => "Car Image Not Found"]);
        }
    }

    public function cars_ad_add_update(Request $request, $carAd = null)
    {

        $msg = isset($carAd) ? "Update Success" : "Add Success";

        if (isset($carAd)) {

            // $carAds = carAds::where('car_id', $request->car_id)->where('id', $carAd)->first();
            $carAds = carAds::where('car_id', $carAd)->orWhere('id', $carAd)->first();
            // dd($carAds);
            // // $carAds->car_id =  $listCar->id;
            // dd($request->title,$carAds);
            $carAds->title = isset($request->title) ? $request->title : $carAds->title;
            $carAds->contact_no = isset($request->contact_no) ? $request->contact_no : $carAds->contact_no;
            $carAds->location = isset($request->location) ? $request->location : $carAds->location;
            $carAds->save();
        } else {
            $carAds = new carAds;
            $carAds->car_id =  $request->car_id;
            $carAds->title =  $request->title;
            $carAds->contact_no =  $request->contact;
            $carAds->location = $request->location;
            $carAds->save();
        }

        return Response(['status' => 'Success', 'message' => 'Car Ad ' . $msg, 'data' => $carAds], 200);
    }

    public function car_ad_listing()
    {
        $data = carAds::with('car_detail')->get();

        foreach ($data as $carAd) {
            $userfavouriteCarAd = CarAdSafeUnSafe::where(['user_id' => auth()->user()->id, 'car_ad_id' => $carAd->id])->first();

            if ($userfavouriteCarAd) {
                $carAd['wish'] = true;
            } else {
                $carAd['wish'] = false;
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Car Ads Listing', 'data' => $data]);
    }

    public function my_car_ad_listing()
    {
        $data = carAds::with('car_detail')->get();

        $user_ad = [];
        foreach ($data as $carAd) {
            if (isset($carAd->car_detail)) {

                if (isset($carAd->car_detail->userDetail) && $carAd->car_detail->userDetail->id == auth()->user()->id) {
                    $user_ad[] = $carAd;
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'my Car Ads Listing', 'data' => $user_ad]);
    }

    public function car_ad_delete($car_ad_id)
    {
        $data = carAds::with('car_detail')->get();

        foreach ($data as $carAd) {
            if (isset($carAd->car_detail)) {
                if ($carAd->car_detail->userDetail->id == auth()->user()->id) {


                    if ($carAd->id == $car_ad_id || $carAd->car_id == $car_ad_id) {
                        $carAd->delete();
                        return response()->json(['status' => true, 'message' => 'Car Ad Delete Success'], 200);
                    } else {
                        return response()->json(['status' => false, 'message' => 'Car Ad Not Found'], 200);
                    }
                }
            }
        }
    }

    public function car_ad_save_unsafe($carAd)
    {
        $carAd = carAds::where('car_id', $carAd)->orWhere('id', $carAd)->first();

        if (!$carAd) {
            return response()->json(['status' => false, 'msg' => 'car ad did not exists!']);
        }

        $userfavouriteCarAd = CarAdSafeUnSafe::where(['user_id' => auth()->user()->id, 'car_ad_id' => $carAd->id])->first();


        $msg = "";
        if ($userfavouriteCarAd) {
            $userfavouriteCarAd->delete();
            $msg = "UnSafe";
        } else {
            $userfavouritenewCarAd = new CarAdSafeUnSafe();
            $userfavouritenewCarAd->user_id = auth()->user()->id;
            $userfavouritenewCarAd->car_ad_id = $carAd->id;
            $userfavouritenewCarAd->save();
            $msg = "Safe";
        }

        return response()->json(['status' => true, 'message' => 'Car Ad ' . $msg . ' Success'], 200);
    }

    public function view_car_ad($car_ad_id)
    {
        $data = carAds::with('car_detail')->where('car_id', $car_ad_id)->orWhere('id', $car_ad_id)->first();

        return response()->json(['status' => true, 'message' => 'car Ad specific get success', 'data' => $data], 200);
    }

    public function car_ad_save_listing()
    {

        $data = carAds::get();
        $user_favourite_car_ad = [];
        foreach ($data as $carAd) {
            $userfavouriteCarAd = CarAdSafeUnSafe::with('user_detail', 'car_ad_detail.car_detail.carImages')->where(['user_id' => auth()->user()->id, 'car_ad_id' => $carAd->id])->first();

            if ($userfavouriteCarAd) {
                $userfavouriteCarAd->wish = true;
                $user_favourite_car_ad[] = $userfavouriteCarAd;
            }
        }

        return response()->json(['status' => true, 'message' => "save car Ad's get success", 'data' => $user_favourite_car_ad], 200);
    }

    public function myCarsList()
    {
        $cars = listCar::with(['carAdd', 'userDetail', 'carImages', 'car_inspection_detail'])->where('user_id', auth()->user()->id)->get();


        $cars_data = [];
        foreach ($cars->toArray() as $key => $car) {
            $cars_data[$key] = $car;
            $check = CarInspectionModel::where(['car_id' => $car['id'], 'user_id' => auth()->user()->id])->first();
            if ($check) {
                if ($check->status == "in progress") {
                    $cars_data[$key]['inspection_status'] = "in progress";
                    $cars_data[$key]['inspection'] = true;
                } else {
                    $cars_data[$key]['inspection_status'] = "pending";
                    $cars_data[$key]['inspection'] = true;
                }
            } else {
                $cars_data[$key]['inspection_status'] = "not avaliable";
                $cars_data[$key]['inspection'] = false;
                // $car->inspection = false;
            }
        }


        return Response(['status' => 'Success', 'message' => 'my Cars List', 'data' => $cars_data], 200);
    }

    public function request_inspection(Request $req)
    {
        $user_id = auth()->user()->id;

        $request_inspection = new CarInspectionModel();

         

        $services = $req->services;
        asort($services);
        $services_array = json_encode(array_map('intval',array_values($services)));
        
        $request_inspection->user_id = $user_id;
        $request_inspection->mechanic_id = null;
        $request_inspection->car_id = $req->car_id;
        $request_inspection->location = $req->location;
        $request_inspection->address = $req->address;
        $request_inspection->inspection_date = $req->inspection_date;
        $request_inspection->inspection_time = $req->inspection_time;
        $request_inspection->services = $services_array;
        $request_inspection->amount = $req->amount;
        $request_inspection->status = "pending";


       
        // $request_inspection->save();

        if ($request_inspection->save()) {
            return response()->json(['status' => true, 'msg' => "Request Inspection Added Success", 'data' => $request_inspection]);
        } else {
            return response()->json(['status' => false, 'msg' => "Something Went Wrong!"]);
        }
    }

    public function request_inspection_view(CarInspectionModel $reqId)
    {
        $role = User::where('id', auth()->user()->id)->where('user_role', 3)->first();
        if ($role) {
            $data = CarInspectionModel::with(['car_detail', 'user_detail', 'mechanic_detail'])->where('id', $reqId->id)->first();

            if ($data->status == "in progress" && $data->mechanic_id == auth()->user()->id) {
                $data->inspection = true;
            } else {
                $data->inspection = false;
            }
            
            if($data->services && $data->services !== "" && $data->services !== '[]'){
                $services = json_decode($data->services, true);
                
                $getServices = CarServicesModel::whereIn('id', $services)->latest()->get();
                
                $data->services = $getServices;
            }
            
            return response()->json(['status' => true, 'msg' => 'request inspection list get success', 'data' => $data]);
        } else {
            $data = CarInspectionModel::with(['car_detail', 'user_detail', 'mechanic_detail'])->where('id', $reqId->id)->first();
          if($data->services && $data->services !== "" && $data->services !== '[]'){
                $services = json_decode($data->services, true);
                
                $getServices = CarServicesModel::whereIn('id', $services)->latest()->get();
                
                $data->services = $getServices;
            }
            return response()->json(['status' => true, 'msg' => 'request inspection list get success', 'data' => $data]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $listCar)
    {
        // dd($listCar);
        $listCar = listCar::find($listCar);
        if (isset($listCar)) {

            $listCar->user_id = Auth::user()->id;
            $listCar->name = isset($request->name) ? $request->name : $listCar->name;
            $listCar->make = isset($request->make) ? $request->make : $listCar->make;
            $listCar->model = isset($request->model) ? $request->model : $listCar->model;
            $listCar->address = isset($request->address) ? $request->address : $listCar->address;
            $listCar->year = isset($request->year) ? $request->year : $listCar->year;
            $listCar->mileage = isset($request->mileage) ? $request->mileage : $listCar->mileage;
            $listCar->fuel = isset($request->fuel) ? $request->fuel : $listCar->fuel;
            $listCar->documents = isset($request->documents) ? $request->documents : $listCar->documents;
            $listCar->trnasmission = isset($request->trnasmission) ? $request->trnasmission : $listCar->trnasmission;
            $listCar->condition = isset($request->condition) ? $request->condition : $listCar->condition;
            $listCar->color = isset($request->color) ? $request->color : $listCar->color;
            $listCar->price = isset($request->price) ?  $request->price : $listCar->price;
            $listCar->description = isset($request->description) ? $request->description : $listCar->description;
            $listCar->city = isset($request->city) ? $request->city : $listCar->city;
            $listCar->state = isset($request->state) ? $request->state : $listCar->state;
            $listCar->zip_code = isset($request->zip_code) ? $request->zip_code : $listCar->zip_code;
            if ($request->hasFile('image')) {
                $file = request()->file('image');
                $destination_path = 'uploads/car/';
                $fileName = date("Ymdhis") . uniqid() . "." . $file->getClientOriginalExtension();
                //dd($photo,$filename);
                $file->move(public_path('uploads/car/'), $fileName);
                $listCar->image = $destination_path . $fileName;
            }

            $listCar->save();

            //For Save Multiple Images
            $photos = request()->file('images');
            if ($photos) {

                foreach ($photos as $photo) {
                    $destinationPath = 'uploads/car/';

                    $filename = date("Ymdhis") . uniqid() . "." . $photo->getClientOriginalExtension();
                    //dd($photo,$filename);
                    $photo->move(public_path('uploads/car/'), $filename);
                    // Image::make($photo)->save(public_path($destinationPath) . DIRECTORY_SEPARATOR. $filename);
                    $carImage  = new carImages;
                    $carImage->car_id = $listCar->id;
                    $carImage->image = $destinationPath . $filename;
                    $carImage->save();
                }
            }

            //For Save Post Add
            // $carAds = carAds::where('car_id' , $listCar->id)->first();
            // // $carAds->car_id =  $listCar->id;
            // $carAds->title =  $request->title;
            // $carAds->contact_no =  $request->contact;
            // $carAds->save();
            return Response(['status' => 'Success', 'message' => 'Post Update Successfully', 'data' => $listCar], 200);
        } else {
            return Response(['status' => false, 'message' => 'Data not found']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(listCar $listCar)
    {
        //
    }
}
