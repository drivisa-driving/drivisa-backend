<?php

namespace Modules\Core\Events\Handlers;

class RegisterCoreSidebar
{
    /**
     * Method used to define your sidebar menu groups and items
     * @param $menu
     * @return mixed
     */
    public function extendWith($menu)
    {
        $menu->group(trans('core::sidebar.content'), function ($group) {
            $group->weight(50);
        });

        return $menu;
    }
}
