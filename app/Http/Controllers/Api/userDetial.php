<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;

class userDetial extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    //for update user image
    public function update(Request $request, User $user)
    {
        $user_id = auth()->user()->id;

        $userImage = User::where('id', $user_id)->first(['id', 'first_name', 'last_name', 'phone', 'email', 'image', 'user_role', 'created_at', 'updated_at']);;

        if ($request->hasFile('profile')) {
            //for Delete old Image 
            if ($userImage->image != 'uploads/user/dummy-user.png') {
                $oldImage = public_path($userImage->image);
                File::delete($oldImage);
            }

            $file = request()->file('profile');
            $destination_path = 'uploads/user/';
            $fileName = date("Ymdhis") . uniqid() . "." . $file->getClientOriginalExtension();
            //dd($photo,$filename);
            $file->move(public_path('uploads/user/'), $fileName);
            $userImage->image = $destination_path . $fileName;
            $userImage->save();
        }
        return Response(['status' => 'Success', 'message' => 'Image Update Successfully', 'data' => $userImage], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
