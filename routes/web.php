<?php

use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/{any}', function () {
    return view('app');
})->where('any','.*');


