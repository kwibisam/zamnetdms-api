<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DepartmentsController extends Controller
{
    /**
     * Create Department
     * @param Request $request
     * @return response
     */
    public function store(Request $request)
    {
        try {
            $department = Department::create([
                'name' => $request->name
            ]);

            return ResponseHelper::success(message: "department created", data: $department, statusCode:201);
        } catch (\Throwable $th) {
            Log::error("DepartmentsController::store(): " . $th->getMessage());
            return ResponseHelper::error(message: "failed to create department", statusCode:500);
        }
        
    }

    /**
     * Get department
     * @param integer $department_id
     */
    public function show($department_id)
    {
        try
        {
            $department = Department::find($department_id);
            if($department)
            {
                return ResponseHelper::success(message: 'department fetched successfully', data: $department, statusCode:200);
            }
            return ResponseHelper::error(message: 'department not found', statusCode:404);
        }
        catch (Throwable $th)
        {
            Log::error('DepartmentsController::show(): ' . $th->getMessage());
        }
    }

    /**
     * Get all departments
     * 
     */
    public function index()
    {
        try {
            $departments = Department::all();
            return ResponseHelper::success(message:"departments fetched successfully", data: $departments);
        } catch (\Throwable $th) {
            Log::error("DepartmentsContrller:index() ". $th->getMessage());
            return ResponseHelper::error(message:"failed to fetch departments", statusCode:500);
        }
    }

    /**
     * Update Department
     * @param integer $department_id
     * @param Request $request
     */
    public function update($department_id, Request $request)
    {
        try {
            $department = Department::find($department_id);
            if(!$department)
            {
                return ResponseHelper::error(message: "department not found", statusCode:404);
            }
            $department->update([
                'name' => $request->name
            ]);
            return ResponseHelper::success(message: 'update successful', data: $department);
        } catch (\Throwable $th) {
            Log::error('DepartmentsController::update(): ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to update department", statusCode: 500);
        }
    }

    /**
     * Delete Department
     * @param integer $department_id
     */
    public function delete($department_id)
    {
        try {
            $department = Department::find($department_id);
            if(!$department)
            {
                return ResponseHelper::error(message: "department not found", statusCode:404);
            }
            $department->delete();
            return ResponseHelper::success(message:'department deleted');
        } catch (\Throwable $th) {
            Log::error('DepartmentsController::update(): ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to delete department", statusCode: 500);
        }
    }
}
