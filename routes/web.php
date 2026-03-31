<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
});

// Serve the Task Manager UI at the root path
Route::get('/', function () {
    return view('tasks');
});
