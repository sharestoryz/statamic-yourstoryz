<?php

use Illuminate\Support\Facades\Route;
use YourStoryz\StatamicYourStoryz\Http\Controllers\ConfigController;

Route::singleton('yourstoryz', ConfigController::class)
    ->except('show');
