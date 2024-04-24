@extends('layouts.app')
@push('before-css')
<style>

    .galImg{
        border: 1px solid black;
        /* object-fit: cover; */
        height: 100px;
        width:120px;
    }
    .mainHead{
        text-align: center;
        font-size: 20px;
        font-weight: 700;
    }
</style>
@endpush
@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Car Details #{{ $carDetail->id }}</h3>
        <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Home</li>
                    <li class="breadcrumb-item active">Car</li>
                    <li class="breadcrumb-item active">Car Detail #{{ $carDetail->id }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-4 col-12">
        <div class="btn-group float-md-right">
            <a class="btn btn-info mb-1" href="{{ url('/admin/contact/inquiries') }}">Back</a>
        </div>
    </div>
</div>
<div class="content-body">
    <section id="basic-form-layouts">
        <div class="row match-height">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="basic-layout-form">Car Detail View Page #{{ $carDetail->id }}</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr><th>ID</th><td>{{ $carDetail->id }}</td></tr>

                                    <tr><th>Car Name</th><td>{{ $carDetail->name }}</td></tr>

                                    <tr><th>Car Image</th><td><img src="{{asset($carDetail->image)}}" alt="" title="" width="150"></td></tr>

                                    <tr><th>Car Model </th><td> {{ $carDetail->model }} </td></tr>
                                    
                                    <tr><th>Car Make </th><td> {{ $carDetail->make }} </td></tr>
                                    
                                    <tr><th> Car Year  </th><td> {{ $carDetail->year }} </td></tr>
                                    
                                    <tr><th> Car Mileage </th><td> {{ $carDetail->mileage }} </td></tr>
                                   
                                    <tr><th> Car Fuel  </th><td> {{ $carDetail->fuel }} </td></tr>
                                    
                                   
                                    <tr><th> Car Documents </th><td> {{ $carDetail->documents }} </td></tr>
                                   
                                   
                                    <tr><th> Transmission  </th><td> {{ $carDetail->trnasmission }} </td></tr>
                                    
                                    
                                    <tr><th> Condition </th><td> {{ $carDetail->condition }} </td></tr>
                                    
                                    
                                    <tr><th> Car Color </th><td> {{ $carDetail->color }} </td></tr>
                                    
                                    <tr><th> Car Price </th><td> ${{ $carDetail->price }} </td></tr>

                                    <tr><th> Car Description </th><td> {{$carDetail->description }} </td></tr>

                                    </hr>

                                    <tr>
                                        <td colspan="2" class="mainHead">Add Details</td>
                                    </tr>
                                     
                                    <tr><th> Add Title </th><td> {{$carDetail->carAdd->title }} </td></tr>

                                    <tr><th> Seller Name </th><td> {{$carDetail->userDetail->first_name . ' '.  $carDetail->userDetail->last_name }} </td></tr>

                                    <tr><th> Seller Email </th><td> {{$carDetail->userDetail->email }} </td></tr>

                                    <tr><th> Contact No </th><td> {{$carDetail->carAdd->contact_no }} </td></tr>
                                   
                                    <tr>
                                        <td colspan="2" class="mainHead">Gallery Images</td>
                                    </tr>
                                    <tr>
                                        <th> Gallery Images </th>
                                        <td>
                                        @foreach($carDetail->carImages as $item)
                                        <span>

                                            <img src="{{asset($item->image)}}" class="galImg" alt="" title=""> 
                                        </span>
                                        @endforeach
                                        </td>
                                    </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

