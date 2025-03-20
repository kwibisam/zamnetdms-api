<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        // return response()->json(['message' => UserResource::collection($users)]);
        return ResponseHelper::success(message:"users fetched successfully", data: UserResource::collection($users));
    }


      public function delete($user_id)
    {
        if (!Auth::check()) {
            return ResponseHelper::error(message: "Unauthorized", statusCode: 401);
        }

        $response = Gate::inspect('register', Auth::user());
        if ($response->denied()) {
            return ResponseHelper::error(message: $response->message(), statusCode: $response->code());
        }

        try {
            $user = User::find($user_id);
            if(!$user)
            {
                return ResponseHelper::error(message:"user not found", statusCode:404);
            }
            $user->delete();
            return ResponseHelper::success(message: "user deleted");
        } catch (\Throwable $th) {
            Log::error("AuthController::delete() " . $th->getMessage());
            return ResponseHelper::error(message:"user not found", statusCode:404);
        }
    }

    /**
     * Register new user.
     * @param
     */
    public function register(RegisterRequest $request)
{
    if (!Auth::check()) {
        return ResponseHelper::error(message: "Unauthorized", statusCode: 401);
    }

    $response = Gate::inspect('register', Auth::user());
    if ($response->denied()) {
        return ResponseHelper::error(message: $response->message(), statusCode: $response->code());
    }

    $department = Department::find($request->department_id);
    if (!$department) {
        return ResponseHelper::error(message: "department not found, try again", statusCode: 404);
    }

    DB::beginTransaction();
    try {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->department_id = $department->id;
        $user->save();
        // Assign role
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $user->roles()->attach($userRole->id);
        DB::commit();
         
        event(new Registered($user));

        return ResponseHelper::success(
            message: "User created successfully",
            data: new UserResource($user),
            statusCode: 201
        );
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error("Unable to create user: {$e->getMessage()} at line {$e->getLine()}");

        return ResponseHelper::error(message: "An unexpected error occurred. Please try again.", statusCode: 500);
    }
}


    /**
     * 
     * authenticates the user
     * @param LoginRequest $request
     */
    public function login (LoginRequest $request)
    {
        Log::info("Login function called: ");
        try {

            // dd(Auth::attempt(['email' => $request->email, 'password' => $request->password]));

            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return ResponseHelper::error(message: 'username or password incorrect', statusCode: 401);
            }

            $user = Auth::user();
            // dd($user);
            $token = $user->createToken($user->name)->plainTextToken;
            $authUser = [
                'user' => $user,
                'token' => $token
            ];
            return ResponseHelper::success(message: "login successful", data: $authUser);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Display authenticated user
     * @return JsonResponse
     */
    public function showProfile() 
    {
        try {
            // dd(Auth::user());
            $user = Auth::user();
            if(!$user) {
                return ResponseHelper::error(message: 'unable to fetch user profile', statusCode: 401);
            }
            return ResponseHelper::success(message: "user profile fetched successfully", data: new UserResource($user)); 
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('error getting auth user '. $th->getMessage());
            return ResponseHelper::error(message: 'failed to get auth user', statusCode: 401);
        }
    }



    /**
     * get specified user
     * @return JsonResponse
     */
    public function show(Request $request) 
    {
        try {
            // dd(Auth::user());
            $user = User::find($request->id);
            if(!$user) {
                return ResponseHelper::error(message: 'user not found', statusCode: 404);
            }
            
            return ResponseHelper::success(message: "user fetched successfully", data: new UserResource($user)); 
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('error getting auth user '. $th->getMessage());
            return ResponseHelper::error(message: 'failed to get user', statusCode: 401);
        }
    }

      /**
     * Deletes auth user token
     * @return JsonResponse
     */
    public function logout() 
    {
        try {
            // dd(Auth::user());
            $user = Auth::user();
            if($user) {
                $user->currentAccessToken()->delete();
                return ResponseHelper::success(message: 'user loggout out', statusCode: 200);
            }
            return ResponseHelper::error(message: "user loggout failed", statusCode:401); 
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('error logging user out '. $th->getMessage());
            return ResponseHelper::error(message: 'failed to logout user', statusCode: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function addUserToWorkSpace($user_id, $workspace_id)
    {
        $response = Gate::inspect('updateUserWorkSpace',Auth::user());
        if($response->denied()){
            return ResponseHelper::error(message: $response->message(), statusCode: $response->code());
        }
        try {
            $user = User::find($user_id);
            $workspace = WorkSpace::find($workspace_id);
            if(!$user || !$workspace) {
                return ResponseHelper::error(message:"user or worksapce not found", statusCode: 404);
            }

            if($user->workspaces()->where('name', $workspace->name)->exists()){
                return ResponseHelper::error(message: "user already belongs to workspace", statusCode:400);
            }
            $user->workspaces()->attach($workspace->id);
            return ResponseHelper::success(message: "user workspace added", data: new UserResource($user));
        } catch (\Throwable $th) {
            Log::error('AuthController::addUserToWorkSpace() ' . $th->getMessage());
        }
    }


    public function removeUserFromWorkSpace($user_id, $workspace_id)
    {
        $response = Gate::inspect('updateUserWorkSpace',Auth::user());
        if($response->denied()){
            return ResponseHelper::error(message: $response->message(), statusCode: $response->code());
        }
        try {
            $user = User::find($user_id);
            $workspace = WorkSpace::find($workspace_id);
            if(!$user || !$workspace) {
                return ResponseHelper::error(message:"user or worksapce not found", statusCode: 404);
            }

            if(!$user->workspaces()->where('name', $workspace->name)->exists()){
                return ResponseHelper::error(message: "user is not in this workspace", statusCode:400);
            }
            $user->workspaces()->detach($workspace_id);
            return ResponseHelper::success(message: "user removed from workspace", data: new UserResource($user));
        } catch (\Throwable $th) {
            Log::error('AuthController::removeUserFromWorkSpace() ' . $th->getMessage());
        }
    }

    public function addRoleToUser($user_id, $role_id)
    {
        $response = Gate::inspect('updateUserRole', Auth::user());
        if($response->denied()) {
            return ResponseHelper::error(message: $response->message(), statusCode: $response->code());
        }
        try {
            $user = User::find($user_id);
            $role = Role::find($role_id);
            if(!$user || !$role) {
                return ResponseHelper::error(message:"user or worksapce not found", statusCode: 404);
            }

            //is role already assigned
            if($user->roles()->where('name', $role->name)->exists())
            {
                return ResponseHelper::error(message: "role already added", statusCode: 400);
            }
            $user->roles()->attach($role->id);
            return ResponseHelper::success(message: "user role added", data: new UserResource($user));
        } catch (\Throwable $th) {
            Log::error('AuthController::addUserToRole() ' . $th->getMessage());
        }
    }

    public function removeUserRole($user_id, $role_id)
    {
        $response = Gate::inspect('updateUserRole',Auth::user());
        if($response->denied()){
            return ResponseHelper::error(message: $response->message(), statusCode: $response->code());
        }
        try {
            $user = User::find($user_id);
            $role = Role::find($role_id);
            if(!$user || !$role) {
                return ResponseHelper::error(message:"user or role not found", statusCode: 404);
            }

            if(!$user->roles()->where('name', $role->name)->exists()){
                return ResponseHelper::error(message: "user does not have this role", statusCode:400);
            }
            $user->roles()->detach($role_id);
            return ResponseHelper::success(message: "user role removed", data: new UserResource($user));
        } catch (\Throwable $th) {
            Log::error('AuthController::removeUserRole() ' . $th->getMessage());
        }
    }

    public function setDefaultWorkspace($user_id, $workspace_id)
    {
    try {
        // Find the user
        $user = User::find($user_id);
        if (!$user) {
            return ResponseHelper::error(message: "User not found", statusCode: 404);
        }

        // Ensure the workspace belongs to the user
        $user_workspace = DB::table('user_workspace')
            ->where('user_id', $user_id)
            ->where('workspace_id', $workspace_id)
            ->first();

        if (!$user_workspace) {
            return ResponseHelper::error(message: "Workspace not found for this user", statusCode: 404);
        }

        // Begin transaction
        DB::transaction(function () use ($user_id, $workspace_id) {
            // Step 1: Set all workspaces to non-default for this user where is_default = true
            DB::table('user_workspace')
                ->where('user_id', $user_id)
                ->where('is_default', 1)
                ->update(['is_default' => false]);

            // Step 2: Set the selected workspace as default
            DB::table('user_workspace')
                ->where('user_id', $user_id)
                ->where('workspace_id', $workspace_id)
                ->update(['is_default' => true]);
        });

        // Return success response
        return ResponseHelper::success(message: "Default workspace updated successfully", data: new UserResource($user));
    } catch (\Throwable $th) {
        Log::error($th->getMessage());
        return ResponseHelper::error(message: "An error occurred", statusCode: 500);
    }
    }


    public function updateUserDepartment(Request $request)
    {
    try {
        $user = User::find($request->user_id);
        $department = Department::find($request->department_id);

        if(!$user || !$department) {
            return ResponseHelper::error(message:"user or department not found", statusCode:404);
        }
        $user->department_id = $department->id;
        $user->save();
        return ResponseHelper::success(message:"user department changed successfully");
    } catch (\Throwable $th) {
        Log::error("AuthController::updateDepartment() " . $th->getMessage());
    }
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        try {

            $request->fulfill();
            return ResponseHelper::success(message: "email verified");
        } catch (\Throwable $th) {
            Log::error("AuthController::verifyEmail() " . $th->getMessage());
            return ResponseHelper::error(message: "email verification failed", statusCode: 400);
        }
    }

}
