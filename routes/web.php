<?php


Route::get('/', function () {
    return view('welcome');
});


Route::resource('multidelete','MultiDeleteController');
Route::post('multidelete/update', 'MultiDeleteController@update')->name('multidelete.update');
Route::get('multidelete/destroy/{id}', 'MultiDeleteController@destroy');
Route::get('multideletemass', 'MultiDeleteController@mass')->name('multidelete.mass');
