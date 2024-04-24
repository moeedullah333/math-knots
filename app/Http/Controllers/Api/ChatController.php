<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function get_group_id(Request $request){
        $friend_id = isset($request->friend_id) ? $request->friend_id : null;
        $user_id = Auth::user()->id;
        $req_users = $user_id . ',' . $friend_id;
        $all_user = explode(',' , $req_users);
        $get_group = ChatGroup::get();

        if (isset($get_group)) {
            foreach ($get_group as $key => $value) {
                // dd($value);
                $group_user = explode(',' , $value->users);
                $check_user = array_intersect($group_user , $all_user);
                if (count($check_user) == count($all_user)) {

                    return response()->json(['status' => true , 'group_id' => $value->id]);
                }
            }
        }

        $add_group = new ChatGroup();
        $add_group->users = $req_users;
        if ($add_group->save()) {
            return response()->json(['status' => true , 'group_id' => $add_group->id]);
        }
        else{
            return response()->json(['status' => false , 'message' => "Failed"]);
        }

    }
}
