<?php

use Illuminate\Support\Facades\Route;
use YourStoryz\StatamicYourStoryz\Http\Controllers\SettingsController;

Route::prefix('yourstoryz')->name('yourstoryz.')->group(function () {
    Route::redirect('', '/cp/yourstoryz/settings/edit')->name('index');
    Route::singleton('settings', SettingsController::class)
        ->except('show');
});
