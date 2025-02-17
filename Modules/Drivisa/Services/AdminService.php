<?php

namespace Modules\Drivisa\Services;

use Cartalyst\Sentinel\Activations\EloquentActivation;
use Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Entities\Sentinel\User;

class AdminService
{
    private $admin;
    private $fileService;
    private $imagy;
    private $file;

    public function __construct(AdminRepository $admin,
                                Imagy           $imagy,
                                FileRepository  $file,
                                FileService     $fileService)
    {
        $this->admin = $admin;
        $this->fileService = $fileService;
        $this->imagy = $imagy;
        $this->file = $file;
    }


    public function getProfileInfo($user)
    {
        return $user;
    }

    public function createAdmin($user, $data)
    {
        $admin = new User();

        $admin->first_name = $data['first_name'];
        $admin->last_name = $data['last_name'];
        $admin->email = $data['email'];
        $admin->password = bcrypt($data['password']);
        $admin->created_by = $user->id;
        $admin->user_type = 0;
        $admin->save();

        $this->activateAdmin($admin->id);
    }

    private function activateAdmin($admin_id)
    {
        $activation = new EloquentActivation();
        $activation->user_id = $admin_id;
        $activation->code = rand(111111, 999999);
        $activation->completed_at = now();
    }

    public function updateAdmin($user, $data)
    {
        $admin = User::findOrFail($data['id']);

        $admin->first_name = $data['first_name'];
        $admin->last_name = $data['last_name'];
        $admin->email = $data['email'];
        if (!empty($admin->password)) {
            $admin->password = bcrypt($data['password']);
        }
        $admin->save();
    }

    public function delete($user, $id)
    {
        User::destroy($id);
    }
}