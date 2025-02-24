<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\Tag;
use App\Models\WorkSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Create Document
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $workSpace = WorkSpace::find($request->workspace_id);
            $documentType = DocumentType::find($request->document_type);

            $tagList = explode(',', $request->tags);
            $tags = Tag::whereIn('id', $tagList)->get();

            if(!$user)
            {
                return ResponseHelper::error(message: "user not found", statusCode: 400);
            }
            if(!$workSpace)
            {
                return ResponseHelper::error(message: "workspace not found", statusCode: 400);
            }
            if(!$documentType)
            {
                return ResponseHelper::error(message: "document type not found", statusCode: 400);
            }

            $filePath = null;
            if ($request->hasFile('file')) {
                $storedPath = $request->file('file')->store('documents', 'public');
                $filePath = asset('storage/' . $storedPath);
            }

            $document = new Document();

            if($request->filled('isForm'))
            {
                $document->isForm = true;
            }
            if($request->filled('isFile'))
            {
                $document->isFile = true;
            }
            if($request->filled('isEditable'))
            {
                $document->isEditable = true;
            }
            
            $document->title = $request->title;
            // $document->content = $request->content;
            // $document->path = $filePath;
            $document->created_by = $user->id;
            Log::info("Documentcontroller::store() user id:" . $user->id);
           
            $document->document_type = $documentType->id;
            $document->workspace_id = $workSpace->id;

            $document->save();
            // dd($document);
            $document->documentTags()->attach($tags);

            $version = new DocumentVersion();
            $version->version_number = 1;
            $version->file_path = $filePath;
            $version->content = $request->content;
            $version->document_id = $document->id;
            $version->created_by = $user->id;

            $version->save();
            DB::commit();

            return ResponseHelper::success(message: 'Document created successfully', data: new DocumentResource($document), statusCode: 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('DocumentController::store() ' . $th->getMessage() . " on line: " . $th->getLine());
            return ResponseHelper::error(message: 'failed to created document', statusCode:500);
        }
    }

    /**
     * Get documents
     */
     public function index()
     {
        try {
            $documents = Document::all();
            return ResponseHelper::success(message:"documents fetched successfully", data: DocumentResource::collection($documents));
        } catch (\Throwable $th) {
            Log::error('DocumentController::index() ' . $th->getMessage());
            return ResponseHelper::error(message:"failed to fetch documents", statusCode: 500);
        }
     }

     /**
      * Get Document
      */
      public function show($document_id)
      {
        try {
            $document = Document::find($document_id);
            if($document)
            {
                return ResponseHelper::success(message: "document fetched successfully" , data: new DocumentResource($document));
            }
            return ResponseHelper::error(message: 'document not found', statusCode:404);
        } catch (\Throwable $th) {
            Log::error('DocumentController::show() ' . $th->getMessage());
        }
      }

      /**
       * Update Document
       * @param integer $document_id
       * @param Request $request
       */
      public function update(Request $request, $document_id)
      {
        DB::beginTransaction();
        try {
            $document = Document::find($document_id);
           
           
            if(!$document)
            {
                return ResponseHelper::error(message: "document not found", statusCode:404);
            }

            $updateResponse = Gate::inspect('update', $document);
            if(!$updateResponse->allowed()){
                return ResponseHelper::error(message: $updateResponse->message(), statusCode:403);
            }

            if($request->filled('title'))
            {
                $document->title = $request->input('title');
            }

            if ($request->filled('tags')) {
                $tagList = explode(',', $request->input('tags'));
                $tags = Tag::whereIn('id', $tagList)->get();
                $document->tags()->sync($tags); // Sync tags for a many-to-many relationship
            }


            $document->save();

            $version = new DocumentVersion();
            
            if ($request->hasFile('file')) {
                $storedPath = $request->file('file')->store('documents', 'public');
                $filePath = asset('storage/' . $storedPath);
                $version->file_path = $filePath;
            }
            
            if($request->filled('content'))
            {
                $version->content = $request->input('content');
            }

            $lastVersion = DocumentVersion::where('document_id', $document->id)
            ->orderBy('version_number', 'desc')
            ->first();

            $newVersionNumber = $lastVersion ? $lastVersion->version + 1 : 1;
            $version->version_number = $newVersionNumber;

            $version->save();
            DB::commit();

            $data = [
                'document' => $document,
                'version' => $version
            ];
        
            return ResponseHelper::success(message: 'Document updated successfully', data: new DocumentResource($data));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('DocumentController::update() ' . $th->getMessage());
            return ResponseHelper::error(message: 'failed to update document', statusCode:500);
        }
      }

      /**
       * Delete Document
       */
      public function delete($document_id)
      {
        try {
            $document = Document::find($document_id);
            if(!$document)
            {
                return ResponseHelper::error(message: 'document not found', statusCode:404);
            }
            $document->delete();
            return ResponseHelper::success(message: "document deleted successfully");
        } catch (\Throwable $th) {
            Log::error('DocumentController::delete() ' . $th->getMessage());
            return ResponseHelper::error(message: "failed to delete document", statusCode:500);
        }
      }
}
