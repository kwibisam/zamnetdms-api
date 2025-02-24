<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\WorkSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WorkSpaceController extends Controller
{
    /**
     * Create WorkSpace
     * @param Request $request
     * @return response
     */
    public function store(Request $request)
    {
        try {
            $workSpace = WorkSpace::create([
                'name' => $request->name
            ]);

            return ResponseHelper::success(message: "workspace created", data: $workSpace, statusCode:201);
        } catch (\Throwable $th) {
            Log::error("WorkSpaceController::store(): " . $th->getMessage());
            return ResponseHelper::error(message: "failed to create workspace", statusCode:500);
        }
        
    }

    /**
     * Get workspace
     * @param integer $workspace_id
     */
    public function show($workspace_id)
    {
        try
        {
            $workSpace = WorkSpace::find($workspace_id);
            if($workSpace)
            {
                return ResponseHelper::success(message: 'workspace fetched successfully', data: $workSpace, statusCode:200);
            }
            return ResponseHelper::error(message: 'workspace not found', statusCode:404);
        }
        catch (Throwable $th)
        {
            Log::error('WorkSpaceController::show(): ' . $th->getMessage());
        }
    }

    /**
     * Get all workspaces
     * 
     */
    public function index()
    {
        try {
            $workSpaces = WorkSpace::all();
            return ResponseHelper::success(message:"workspaces fetched successfully", data: $workSpaces);
        } catch (\Throwable $th) {
            return ResponseHelper::error(message:"failed to fetch workspaces", statusCode:500);
        }
    }

    /**
     * Update WorkSpace
     * @param integer $workspace_id
     * @param Request $request
     */
    public function update($workspace_id, Request $request)
    {
        try {
            $workSpace = WorkSpace::find($workspace_id);
            if(!$workSpace)
            {
                return ResponseHelper::error(message: "workspace not found", statusCode:404);
            }
            $workSpace->update([
                'name' => $request->name
            ]);
            return ResponseHelper::success(message: 'update successful', data: $workSpace);
        } catch (\Throwable $th) {
            Log::error('WorkSpaceController::update(): ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to update workspace", statusCode: 500);
        }
    }

    /**
     * Delete WorkSpace
     * @param integer $workspace_id
     */
    public function delete($workspace_id)
    {
        try {
            $workSpace = WorkSpace::find($workspace_id);
            if(!$workSpace)
            {
                return ResponseHelper::error(message: "workspace not found", statusCode:404);
            }
            $workSpace->delete();
            return ResponseHelper::success(message:'workspace deleted');
        } catch (\Throwable $th) {
            Log::error('WorkSpaceController::update(): ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to delete workspace", statusCode: 500);
        }
    }
}
