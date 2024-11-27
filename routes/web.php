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

Route::post('/service/send', function (Request $request) {
    $client = new Client();
    $response = $client->post("http://$request->ip/api/service", ['json' => json_decode($request->json, true)]);
    // Convertir el JSON a array en lugar de stdClass
    $r = json_decode($request->json, true);
    // Añadir la respuesta del servidor
    $r[] = ['respuesta servidor' => (string)$response->getBody()];
    return response()->json($r);
})->name('service');

Route::get('/service',function(){
    $persons = Person::all();
    return view('service',compact(['persons']));
});

