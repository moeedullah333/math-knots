<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForgetOtp;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    
     public function loginwithgoogle(Request $req)
    {
        
        $data = $req->all();
        
        
        $user = $data['user'];

        $userEmail = User::where('email', $user['email'])->where('user_role', 2)->first();


        if ($userEmail != '') {
            $success['token'] =  $userEmail->createToken('MyAuthApp')->plainTextToken;

            return Response(['status' => 'success', 'message' => 'User Login Successfully', 'data' => $success], 200);
        } else {
            $input['user_role'] = 2;
            $input['email'] = $user['email'];
            $input['first_name'] = $user['familyName'];
            $input['last_name'] = $user['givenName'];
            $input['image'] = 'uploads/user/dummy-user.png';
            $input['auth_id'] = $user['id'];
            $user = User::create($input);

            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            return Response(['status' => 'Success', 'message' => 'Account Create Successfully', 'data' => $success], 200);
        }
    }

    //
    //for registration user
    public function registerUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'errors' => $validator->errors()], 401);
        }
        $userEmail = User::where('email', $request->email)->where('user_role', 2)->first();

        if ($userEmail != '') {
            $success['email'] = 'Email Already Exist';
            return Response(['status' => false, 'errors' => $success], 401);
        } else {
            $input = $request->all();



            $input['password'] = bcrypt($input['password']);
            $input['user_role'] = 2;
            $input['image'] = 'uploads/user/dummy-user.png';
            $user = User::create($input);

            $success['token'] =  $user->createToken('MyApp')->plainTextToken;

            //For add user Role
            // Role_User::create([
            //     'role_id' => 2,
            //     'user_id' => $user->id,
            // ]);
            //For Upload Dummy Image
            // userDetail::create([
            //     'user_id' => $user->id,
            //     'image' => 'uploads/user/dummy-user.png',
            // ]);

            return Response(['status' => 'Success', 'message' => 'Account Create Successfully', 'data' => $success], 200);
        }
    }
    // public function registerMechanic(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required',
    //         'first_name' => 'required',
    //         'last_name' => 'required',
    //         'city' => 'required',
    //         'state' => 'required',
    //         'zip_code' => 'required',
    //         'ssn' => 'required',
    //         'years_of_exp' => 'required',
    //         'services' => 'required',
    //         'password' => 'required',
    //     ]);


    //     if ($validator->fails()) {
    //         return Response(['status' => false, 'errors' => $validator->errors()], 401);
    //     }
    //     // $userEmail = MechanicModel::where('email', $request->email)->first();
    //     $userEmail = User::where('email', $request->email)->where('user_role', 3)->first();

    //     if ($userEmail != '') {
    //         $success['email'] = 'Email Already Exist';
    //         return Response(['status' => false, 'errors' => $success], 401);
    //     } else {
                
    
    //         $input = $request->all();
    //         $input['password'] = bcrypt($input['password']);
    //         $input['user_role'] = 3;
    //         $input['services'] = json_encode($request->services);
    //         $input['image'] = 'uploads/user/dummy-user.png';
    //         $input['status'] = "pending";
    //         $mechanic = User::create($input);

    //         $success['token'] =  $mechanic->createToken('MyApp')->plainTextToken;

    //         //For add user Role
    //         // Role_User::create([
    //         //     'role_id' => 3,
    //         //     'user_id' => $mechanic->id,
    //         // ]);
    //         // //For Upload Dummy Image
    //         // MechanicDetailModel::create([
    //         //     'user_id' => $mechanic->id,
    //         //     'image' => null,
    //         // ]);

    //         return Response(['status' => 'Success', 'message' => 'Account Create Successfully', 'data' => $success], 200);
    //     }
    // }
    public function registerMechanic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'ssn' => 'required',
            'years_of_exp' => 'required',
            'services' => 'required|array', 
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response(['status' => false, 'errors' => $validator->errors()], 401);
        }
    
        $userEmail = User::where('email', $request->email)->where('user_role', 3)->first();
    
        if ($userEmail) {
            return response(['status' => false, 'errors' => ['email' => 'Email Already Exist']], 401);
        } else {
            
            $services = $request->services;
            asort($services);
            $services_array = json_encode(array_map('intval',array_values($services)));
            
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['user_role'] = 3;
            $input['services'] = $services_array; 
            $input['image'] = 'uploads/user/dummy-user.png';
            $input['status'] = "pending";
    
            
            $mechanic = User::create($input);
    
            $success['token'] =  $mechanic->createToken('MyApp')->plainTextToken;
    
            return response(['status' => 'Success', 'message' => 'Account Create Successfully', 'data' => $success], 200);
        }
    }

    public function loginMechanic(Request $request)
    {
        // $input = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        $email = $request->email;
        $pass = $request->password;
        $chk_email = User::where('email', $email)->where('user_role', 3)->first();

        //  if ($chk_email->status == "pending") {
        //     return Response(['status' => false, 'msg' => "The Approval is Pending ! Kindly Contact to your Admin"]);
        // }

        if ($chk_email != '') {
            //Check It is Email Or Username
            if (Hash::check($pass, $chk_email->password)) {
                // $authUser = Auth::user();
                $success['token'] =  $chk_email->createToken('MyAuthApp')->plainTextToken;

                return Response(['status' => true, 'message' => 'Mechanic Login Successfully', 'data' => $success], 200);
            } else {
                return Response(['status' => false, 'message' => 'Login credentials do not match', 'data' => null], 401);
            }
        } else {
            return Response(['status' => false, 'error' => 'Email or Username Not Exist'], 401);
        }
    }
    //for login user
    public function loginUser(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        $chk_email = User::where('email', $request->email)->where('user_role', 2)->orWhere('user_role',1)->first();
        if ($chk_email != '') {
            //Check It is Email Or Username

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $authUser = Auth::user();
                $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;

                return Response(['status' => true, 'message' => 'User Login Successfully', 'data' => $success], 200);
            } else {
                return Response(['status' => false, 'message' => 'Login credentials do not match', 'data' => null], 401);
            }
        } else {
            return Response(['status' => false, 'error' => 'Email or Username Not Exist'], 401);
        }
    }
    //for logout user
    public function userLogout()
    {

        if (auth('sanctum')->user()) {
            $user_id = auth('sanctum')->user()->id;
            DB::table('personal_access_tokens')->where('tokenable_id', $user_id)->delete();
            return Response(['status' => 'Success', 'message' => 'User Logout Successfully'], 200);
        } else {
            return Response(['status' => 'false', 'data' => 'Unathorized'], 401);
        }
    }

    //for get user Details
    public function userDetail()
    {
        $userDetail = User::where('id', auth()->user()->id)->where('user_role', 2)
            ->first(['id', 'first_name', 'last_name', 'phone', 'email', 'image', 'user_role', 'created_at', 'updated_at']);
        return Response(['status' => 'Success', 'message' => 'User Detials', 'data' => $userDetail], 200);
    }

    //for update user detail
    public function userUpdate(Request $request)
    {
        $userDetail = User::where('id', auth()->user()->id)->first();
        $userDetail->first_name = isset($request->first_name) ? $request->first_name : $userDetail->first_name;
        $userDetail->last_name = isset($request->last_name) ? $request->last_name : $userDetail->last_name;
        $userDetail->phone = isset($request->phone) ? $request->phone : $userDetail->phone;
        $userDetail->email = isset($request->email) ? $request->email : $userDetail->email;
        // $userDetail->save();

        $userDetail->save();
        return Response(['status' => 'Success', 'message' => 'Detials Update Successfully'], 200);
    }

    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        $opt = rand(10000, 99999);
        // dd($opt);
        // $currentDate = Carbon::now()->format('d-M-Y');

        $check_user = User::where('email', $request->email)->select('id', 'email')->first();
        // dd($check_user);
        if (!isset($check_user)) {
            return response()->json(['status' => false, 'message' => "User not found"]);
        }

        ForgetOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'email'     => $request->email,
                'otp'      => $opt
            ]
        );

        $data = [
            'email' => $request->email,
            'details' => [
                'heading' => 'Forget Password Opt',
                'content' => 'Your forget password otp : ' . $opt,
                'WebsiteName' => 'Certifier'
            ]

        ];
        // Mail::to($request->email)->send(new ForgotOtpMail($data));
        Mail::send('mail.sendopt', $data, function ($message) use ($data) {

            // $message->from('martingarix7878@gmail.com', "Math Knots Subscription");
            $message->to($data['email'])->subject($data['details']['heading']);
        });

        return response()->json(['status' => true, 'data' => $check_user, 'message' => "OTP send on your email address"]);
    }
    public function otp_verification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        $user = ForgetOtp::where(['email' => $request->email, 'otp' => $request->otp])->first();
        if (!isset($user)) {
            return response()->json(['status' => false, 'message' => "Otp is wrong"]);
        }
        $data['email'] = $user->email;
        $data['code'] = $user->otp;

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        // dd(uniqid());
        $get_otp = ForgetOtp::where(['email' => $request->email, 'otp' => $request->otp])->first();
        if (!isset($get_otp)) {
            return response()->json(['status' => false, 'message' => "Otp is wrong"]);
        } else {
            $get_otp->delete();
        }
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request['password']);

        if ($user->save()) {
            return response()->json(['status' => true, 'message' => "Password Reset"]);
        }
    }

    public function all_user()
    {
        $user = User::whereIn('user_role', '!=', [1, 3])->get();
        return Response(['status' => 'Success', 'data' => $user], 200);
    }
}
