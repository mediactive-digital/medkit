<?php

/**
 * Logout route
 */
Route::group([
    'middleware' => [
        'auth'
    ]
], function() {

    // Logout route
    Route::get('/logout', 'Back\Auth\LoginController@logout')->name('logout');

    Route::post('/gdpr/download', '\MediactiveDigital\MedKit\Http\Controllers\Back\GdprController@download')->name('back.gdpr_download');
});

/**
 * Back
 */
Route::group([
    'prefix' => 'gestion',
    'middleware' => 'guest'
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
        'auth',
        'admin',
        'menu:backoffice'
    ]
], function() {

    require_once('gestion.php');
});
