<?php

namespace Modules\User\Http\Controllers\Api;

use Cartalyst\Sentinel\Roles\EloquentRole;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\User\Http\Requests\CreateRoleRequest;
use Modules\User\Http\Requests\UpdateRoleRequest;
use Modules\User\Permissions\PermissionManager;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Transformers\FullRoleTransformer;
use Modules\User\Transformers\RoleTransformer;

class RoleController extends ApiBaseController
{
    /**
     * @var RoleRepository
     */
    private $role;
    /**
     * @var PermissionManager
     */
    private $permissions;

    public function __construct(RoleRepository $role, PermissionManager $permissions)
    {
        $this->role = $role;
        $this->permissions = $permissions;
    }

    public function index(Request $request)
    {
        $request['company_id'] = $this->getUserFromRequest($request)->company_id;
        return RoleTransformer::collection($this->role->serverPaginationFilteringFor($request));
    }

    public function find(EloquentRole $role)
    {
        return new FullRoleTransformer($role->load('users'));
    }

    public function store(CreateRoleRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);
        $data['company_id'] = $this->getUserFromRequest($request)->company_id;

        $role = $this->role->create($data);

        return response()->json([
            'role' => new FullRoleTransformer($role),
            'message' => trans('user::roles.messages.role_created', [], $request->get('locale', locale())),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function mergeRequestWithPermissions(Request $request)
    {
        $permissions = $this->permissions->clean($request->get('permissions'));

        return array_merge($request->all(), ['permissions' => $permissions]);
    }

    public function update(EloquentRole $role, UpdateRoleRequest $request)
    {
        $data = $this->mergeRequestWithPermissions($request);

        $role = $this->role->update($role->id, $data);

        return response()->json([
            'role' => new FullRoleTransformer($role),
            'message' => trans('user::roles.messages.role_updated', [], $request->get('locale', locale())),
        ], Response::HTTP_OK);
    }

    public function destroy(EloquentRole $role, Request $request)
    {
        $this->role->delete($role->id);

        return response()->json([
            'message' => trans('user::messages.role deleted', [], $request->get('locale', locale())),
        ], Response::HTTP_OK);
    }
}
