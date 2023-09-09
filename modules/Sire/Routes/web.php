<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth'])->group(function () {
            Route::prefix('sire')->group(function() {
                Route::get('/configuration', 'SireController@getConfig')->name('tenant.sire.configuration');
                Route::post('/configuration/update', 'SireController@updateConfig');
                Route::get('/', 'SireController@index')->name('tenant.sire');
                Route::get('/tables', 'SireController@tables')->name('tenant.sire.tables');
                Route::get('/ticket/{period}', 'SireController@getTicket');
                Route::post('/query', 'SireController@queryTicket');
            });
        });
    });
};
