<?php

use Illuminate\Support\Facades\Route;
use YourStoryz\StatamicYourstoryz\Http\Controllers\SettingsController;

Route::get('yourstoryz', [SettingsController::class, 'index']);
