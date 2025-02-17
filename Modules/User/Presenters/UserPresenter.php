<?php

namespace Modules\User\Presenters;

use Illuminate\Support\Facades\App;
use Laracasts\Presenter\Presenter;
use Modules\User\Entities\Sentinel\User;
use Nwidart\Modules\Facades\Module;

class UserPresenter extends Presenter
{
    /**
     * Return the gravatar link for the user
     * @return string
     */
    public function gravatar()
    {
        if ($file = $this->entity->filesByZone('profile_picture')->first()) {
            return "{$file->path}";
        }
        if ($this->entity->photo_url) {
            return "{$this->entity->photo_url}";
        }
        $url = Module::asset('user:images/default-profile-photo.png');
        return strpos($url, '/', 0) === 0 ? (App::isLocal() ? "http:{$url}" : "http:{$url}") : $url;
    }

    public function cover()
    {
        if ($file = $this->entity->filesByZone('cover_picture')->first()) {
            return "{$file->path}";
        }
        if ($this->entity->photo_url) {
            return "{$this->entity->photo_url}";
        }
        $url = Module::asset('user:images/default-profile-photo.png');
        return strpos($url, '/', 0) === 0 ? (App::isLocal() ? "http:{$url}" : "http:{$url}") : $url;
    }

    public function carCover()
    {
        if ($this->entity instanceof User) {
            if ($file = $this->entity->instructor->filesByZone('front_picture_of_car_showing_the_plate')->first()) {
                return "{$file->path}";
            }
        }
        if ($this->entity->photo_url) {
            return "{$this->entity->photo_url}";
        }
        $url = Module::asset('user:images/default-profile-photo.png');
        return strpos($url, '/', 0) === 0 ? (App::isLocal() ? "http:{$url}" : "http:{$url}") : $url;
    }

    /**
     * @return string
     */
    public function fullname()
    {
        return $this->name ?: $this->first_name . ' ' . $this->last_name;
    }
}
