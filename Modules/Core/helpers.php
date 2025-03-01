<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('on_route')) {
    function on_route($route)
    {
        return Route::current() ? Route::is($route) : false;
    }
}

if (!function_exists('locale')) {
    function locale($locale = null)
    {
        if (is_null($locale)) {
            return app()->getLocale();
        }

        app()->setLocale($locale);

        return app()->getLocale();
    }
}

if (!function_exists('is_module_enabled')) {
    function is_module_enabled($module)
    {
        return array_key_exists($module, app('modules')->allEnabled());
    }
}

if (!function_exists('is_core_module')) {
    function is_core_module($module)
    {
        return in_array(strtolower($module), app('ceo.ModulesList'));
    }
}