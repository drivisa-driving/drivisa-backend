<?php

/*
|--------------------------------------------------------------------------
| Language Settings
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\App;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

$lang = null;

if (App::environment() == 'testing') {
    $lang = 'fr';
}

LaravelLocalization::setLocale($lang);
