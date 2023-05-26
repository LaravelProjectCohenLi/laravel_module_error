<?php 
 use Illuminate\Support\Facades\Route;

 Route::group(['namespace' => 'Modules\Teacher\src\Http\Controllers', 'middleware' => 'web'], function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('teacher')->name('teacher.')->group(function () {
            Route::get('/', 'TeacherController@index')->name('index');

            
        });
    });
});