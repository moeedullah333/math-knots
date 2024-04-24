<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\listCar;
use App\Models\carImages;
use App\Models\carAds;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Validator;
use Image;
use Illuminate\Support\Facades\Auth;

class carListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $cars = listCar::all();
        
        return view('admin.cars.index',compact('cars'));
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
    public function show(listCar $listCar,$id)
    {
        //
       
        $carDetail = listCar::where('id',$id)->first();
        return view('admin.cars.detail',compact('carDetail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, listCar $listCar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(listCar $listCar)
    {
        //
    }
}
