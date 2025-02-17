<?php

use FloatingPoint\Stylist\Theme\Theme;
use Modules\Core\Foundation\CodeEcho;
use Nwidart\Modules\Laravel\Module;

if (!function_exists('module_version')) {
    function module_version(Module $module)
    {
        if (is_core_module($module->getName()) === true) {
            return CodeEcho::VERSION;
        }

        return $module->json()->get('version');
    }
}

if (!function_exists('theme_version')) {
    function theme_version(Theme $theme)
    {
        if (is_core_theme($theme->getName()) === true) {
            return CodeEcho::VERSION;
        }

        return $theme->version;
    }
}
