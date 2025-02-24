<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DocumentTypesController extends Controller
{
    /**
    * Create DocumentType
    * @param Request $request
    * @return response
    */
   public function store(Request $request)
   {
       try {
           $documentType = DocumentType::create([
               'name' => $request->name
           ]);

           return ResponseHelper::success(message: "documentType created", data: $documentType, statusCode:201);
       } catch (\Throwable $th) {
           Log::error("DocumentTypeController::store(): " . $th->getMessage());
           return ResponseHelper::error(message: "failed to create documentType", statusCode:500);
       }
       
   }

   /**
    * Get documentType
    * @param integer $document_type_id
    */
   public function show($document_type_id)
   {
       try
       {
           $documentType = DocumentType::find($document_type_id);
           if($documentType)
           {
               return ResponseHelper::success(message: 'documentType fetched successfully', data: $documentType, statusCode:200);
           }
           return ResponseHelper::error(message: 'documentType not found', statusCode:404);
       }
       catch (Throwable $th)
       {
           Log::error('DocumentTypeController::show(): ' . $th->getMessage());
       }
   }

   /**
    * Get all document types
    * 
    */
   public function index()
   {
       try {
           $documentTypes = DocumentType::all();
           return ResponseHelper::success(message:"documentTypes fetched successfully", data: $documentTypes);
       } catch (\Throwable $th) {
           return ResponseHelper::error(message:"failed to fetch documentTypes", statusCode:500);
       }
   }

   /**
    * Update DocumentType
    * @param integer $document_type_id
    * @param Request $request
    */
   public function update($document_type_id, Request $request)
   {
       try {
           $documentType = DocumentType::find($document_type_id);
           if(!$documentType)
           {
               return ResponseHelper::error(message: "documentType not found", statusCode:404);
           }
           $documentType->update([
               'name' => $request->name
           ]);
           return ResponseHelper::success(message: 'update successful', data: $documentType);
       } catch (\Throwable $th) {
           Log::error('DocumentTypeController::update(): ' . $th->getMessage() . " on line: ". $th->getLine());
           return ResponseHelper::error(message: "failed to update documentType", statusCode: 500);
       }
   }

   /**
    * Delete WorkSpace
    * @param integer $document_type_id
    */
   public function delete($document_type_id)
   {
       try {
           $documentType = DocumentType::find($document_type_id);
           if(!$documentType)
           {
               return ResponseHelper::error(message: "documentType not found", statusCode:404);
           }
           $documentType->delete();
           return ResponseHelper::success(message:'documentType deleted');
       } catch (\Throwable $th) {
           Log::error('DocumentTypeController::delete(): ' . $th->getMessage());
           return ResponseHelper::error(message: "failed to delete documentType", statusCode: 500);
       }
   }
}
