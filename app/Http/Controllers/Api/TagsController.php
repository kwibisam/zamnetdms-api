<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TagsController extends Controller
{
     /**
     * Create Tag
     * @param Request $request
     * @return response
     */
    public function store(Request $request)
    {
        try {
            $tag = Tag::create([
                'name' => $request->name
            ]);

            return ResponseHelper::success(message: "tag created", data: $tag, statusCode:201);
        } catch (\Throwable $th) {
            Log::error("TagController::store(): " . $th->getMessage());
            return ResponseHelper::error(message: "failed to create tag", statusCode:500);
        }
        
    }

    /**
     * Get tag
     * @param integer $tag_id
     */
    public function show($tag_id)
    {
        try
        {
            $tag = Tag::find($tag_id);
            if($tag)
            {
                return ResponseHelper::success(message: 'tag fetched successfully', data: $tag, statusCode:200);
            }
            return ResponseHelper::error(message: 'tag not found', statusCode:404);
        }
        catch (Throwable $th)
        {
            Log::error('TagController::show(): ' . $th->getMessage());
        }
    }

    /**
     * Get all tags
     * 
     */
    public function index()
    {
        try {
            $tags = Tag::all();
            return ResponseHelper::success(message:"tags fetched successfully", data: $tags);
        } catch (\Throwable $th) {
            return ResponseHelper::error(message:"failed to fetch tags", statusCode:500);
        }
    }

    /**
     * Update Tag
     * @param integer $tag_id
     * @param Request $request
     */
    public function update($tag_id, Request $request)
    {
        try {
            $tag = Tag::find($tag_id);
            if(!$tag)
            {
                return ResponseHelper::error(message: "tag not found", statusCode:404);
            }
            $tag->update([
                'name' => $request->name
            ]);
            return ResponseHelper::success(message: 'update successful', data: $tag);
        } catch (\Throwable $th) {
            Log::error('TagController::update(): ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to update tag", statusCode: 500);
        }
    }

    /**
     * Delete WorkSpace
     * @param integer $tag_id
     */
    public function delete($tag_id)
    {
        try {
            $tag = Tag::find($tag_id);
            if(!$tag)
            {
                return ResponseHelper::error(message: "tag not found", statusCode:404);
            }
            $tag->delete();
            return ResponseHelper::success(message:'tag deleted');
        } catch (\Throwable $th) {
            Log::error('TagController::delete(): ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to delete tag", statusCode: 500);
        }
    }
}
