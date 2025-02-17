<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Permissions\PermissionManager;

class FullUserTransformer extends JsonResource
{
    public function toArray($request)
    {
        $permissionsManager = app(PermissionManager::class);
        $permissions = $this->buildPermissionList($permissionsManager->all());

        $data = [
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'username' => $this->resource->username,
            'userType' => $this->resource->user_type,
            'email' => $this->resource->email,
            'activated' => $this->resource->isActivated(),
            'last_login' => $this->resource->last_login,
            'created_at' => $this->resource->created_at,
            'permissions' => $permissions,
            'roles' => $this->resource->roles->pluck('id'),
            'urls' => [
                'delete_url' => route('api.user.user.destroy', $this->resource->id),
            ],
        ];

        return $data;
    }

    private function buildPermissionList(array $permissionsConfig): array
    {
        $list = [];

        if ($permissionsConfig === null) {
            return $list;
        }

        foreach ($permissionsConfig as $mainKey => $subPermissions) {
            foreach ($subPermissions as $key => $permissionGroup) {
                foreach ($permissionGroup as $lastKey => $description) {
                    $list[strtolower($key) . '.' . $lastKey] = current_permission_value($this, $key, $lastKey);
                }
            }
        }

        return $list;
    }
}
