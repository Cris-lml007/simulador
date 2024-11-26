<?php

use App\Models\Person;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/service/send',function(Request $request){
    return response()->json(json_decode($request));
    $client = new Client();
    $response = $client->post('http://localhost:8000/api/service',['json' => json_decode($request->json,true)]);
    return $response->getBody();
    // return response()->json(json_decode($request->json));
})->name('service');

Route::get('/service',function(){
    $persons = Person::all();
    return view('service',compact(['persons']));
});

