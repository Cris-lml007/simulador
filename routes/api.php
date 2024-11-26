<?php

use App\Models\Person;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/staff/{ci}',function($ci){
    $person = Person::where('ci',$ci)->first();
    return response()->json([
        'surname' => $person->surname,
        'name' => $person->name,
        'cellular' => $person->cellular,
        'range' => $person->range
    ]);
});

Route::get('/staff',function(Request $request){
    $persons = Person::all();
    $list = [];
    foreach($persons as $person){
        $list [] = [
            'ci' => $person->ci,
            'surname' => $person->surname,
            'name' => $person->name,
            'range' => $person->range,
            'cellular' => $person->cellular
        ];
    }
    return response()->json($list,200);
});

Route::get('/service/{service}',function(Service $service){
    return response()->json($service);
});

Route::post('/service',function(Request $request){
    $service = Service::create([
        'name' => $request->name,
        'formation' => $request->formation,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'date' => $request->date
    ]);

    if($service) return response()->json($service,200);
    else return response()->json([
        'message' => 'error to created services'
    ]);
});
