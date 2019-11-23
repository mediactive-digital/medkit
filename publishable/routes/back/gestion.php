<?php

Route::get('/', function() {

    return view('medKitTheme::dashboard.back.index');
    
})->name('back.index');

// UI kit
Route::get('/ui-kit', '\MediactiveDigital\MedKit\Http\Controllers\Back\UiController@index')->name('back.ui_kit');

// BO Historique 
Route::get('/history', 'Back\HistoryController@index')->name('back.history.index');
Route::get('/history/list', 'Back\HistoryController@list')->name('back.history.list');