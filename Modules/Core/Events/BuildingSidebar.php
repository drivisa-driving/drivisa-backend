<?php

namespace Modules\Core\Events;

/**
 * Hook BuildingSidebar
 * Triggered when building the backend sidebar
 * Use this hook to add your sidebar items
 * @package Modules\Core\Events
 */
class BuildingSidebar
{
    /**
     * @var
     */
    private $menu;

    public function __construct($menu)
    {
        $this->menu = $menu;
    }

    /**
     * Add a menu group to the menu
     * @param $menu
     */
    public function add($menu)
    {
        $this->menu->add($menu);
    }

    /**
     * Get the current Laravel-Sidebar menu
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
