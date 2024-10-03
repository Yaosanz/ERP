<?php

use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-email', function () {
    $name = 'John Doe';
    Mail::to('mailtrapclub+test@gmail.com')->send(new MyTestEmail($name));
});
