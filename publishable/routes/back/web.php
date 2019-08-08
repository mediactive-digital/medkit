<?php

/**
 * Logout route
 */
Route::group([
    'middleware' => [
        'auth:admin'
    ]
], function() {

    // Logout route
    Route::get('/logout', 'Back\Auth\LoginController@logout')->name('logout');

});


/**
 * Back
 */
Route::group([
    'prefix' => 'gestion',
    'middleware' => 'guest:admin'
], function() {

    // Login routes
    Route::get('/login', 'Back\Auth\LoginController@showLoginForm')->name('back.login');
    Route::post('/login', 'Back\Auth\LoginController@login');

    // Password reset routes
    Route::get('/password/reset', 'Back\Auth\ForgotPasswordController@showLinkRequestForm' )->name('back.password.request');
    Route::post('/password/email', 'Back\Auth\ForgotPasswordController@sendResetLinkEmail' )->name('back.password.email');
    Route::get('/password/reset/{token}', 'Back\Auth\ResetPasswordController@showResetForm' )->name('back.password.reset');
    Route::post('/password/reset', 'Back\Auth\ResetPasswordController@reset');

});

Route::group([
    'prefix' => 'gestion',
    'middleware' => [
        'auth:admin',
        'menu:backoffice'
    ]
], function() {

    Route::get('/', function() {
        return view('medKitTheme::dashboard.back.index');
    })->name('back.index');

    // UI kit
    Route::get('/ui-kit', 'Back\UiController@index')->name('back.ui_kit');

});
