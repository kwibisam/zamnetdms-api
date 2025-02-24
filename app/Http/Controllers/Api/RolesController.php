<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{

    /**
     * Get all roles
     * @return response
     */
    public function index() 
    {
        
        try {
            $roles = Role::all();
            return ResponseHelper::success(message: 'roles fetched successfully',data: $roles);
        } catch (\Throwable $th) {
            Log::error('RolesController: index(): error: '. $th->getMessage());
            return ResponseHelper::error(message:"something went wrong", statusCode:500);
        }
    }

    /**
     * Create Role
     * @param RoleRequest $request
     * @return response
     */
    public function store(RoleRequest $request)
    {
        try {
            Role::create(
                [
                    'name' => $request->name
                ]
            );
            return ResponseHelper::success(message:"role created", statusCode:201);
        } catch (\Throwable $th) {
            Log::error('RolesController::store() error: ' . $th->getMessage());
        }
    }

    /**
     * Get Role
     * @param integer $role_id
     * @return response
     */
    public function show($role_id)
    {
        try {
            $role = Role::find($role_id);
            if($role){
                return ResponseHelper::success(message:"role fetched", data: $role,statusCode:200); 
            }
            return ResponseHelper::error(message:"role not found",statusCode:404);
        } catch (\Throwable $th) {
            Log::error('RolesController::store() error: ' . $th->getMessage());
        }
    }

    /**
     * Update role
     * @param interger $role_id
     * @param Request $request
     * @return response
     */
    public function update(Request $request, $role_id)
    {
        try {
            $role = Role::find($role_id);
            if(!$role)
            {
                return ResponseHelper::error(message:"role not found",statusCode:404);
            }

            $role->update(
                ['name' => $request->name]
            );
            return ResponseHelper::success(message:"role updated", statusCode:200);
        } catch (\Throwable $th) {
            Log::error('RolesController::update() '. $th->getMessage());
            return ResponseHelper::error(message:"role update failed", statusCode:500);
        }
       
    }

    /**
     * Delete role
     * @return response
     */
    public function delete($role_id)
    {
        try {
            //find user_role records of this role and delete them.
            $role = Role::find($role_id);
            if(!$role)
            {
                return ResponseHelper::error(message:"role not found",statusCode:404);
            }
            $role->delete();
            return ResponseHelper::success(message:"role deleted", statusCode:200);
        } catch (\Throwable $th) {
            Log::error('RolesController::delete() '. $th->getMessage());
            return ResponseHelper::error(message:"role delete failed", statusCode:500);
        }
    }


    
}
