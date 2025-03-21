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
            $documentType = DocumentType::find($request->type_id);

            $tagList = explode(',', $request->tags);
            $tags = Tag::whereIn('id', $tagList)->get();

            if(!$user)
            {
                Log::error("DocumentController::store user not found");
                return ResponseHelper::error(message: "user not found", statusCode: 400);
            }
            if(!$workSpace)
            {
                 Log::error("DocumentController::store workspace not found");
                return ResponseHelper::error(message: "workspace not found", statusCode: 400);
            }
            if(!$documentType)
            {
                Log::error("DocumentController::store documentType not found");
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
            $user = Auth::user();
              if(!$user)
            {
                Log::error("DocumentController::store user not found");
                return ResponseHelper::error(message: "user not found", statusCode: 400);
            }
            $document = Document::find($document_id);
            if(!$document)
            {
                return ResponseHelper::error(message: "document not found", statusCode:404);
            }

            // $updateResponse = Gate::inspect('update', $document);
            // if(!$updateResponse->allowed()){
            //     return ResponseHelper::error(message: $updateResponse->message(), statusCode:403);
            // }
            if($request->filled('title'))
            {
                $document->title = $request->input('title');
                // $document->save();
            }

            if ($request->filled('tags')) {
                $tagList = explode(',', $request->input('tags'));
                $tags = Tag::whereIn('id', $tagList)->get();
                $document->tags()->sync($tags); // Sync tags for a many-to-many relationship
                // $document->save();
            }

            $document->save();

            if($request->filled('content')) {
                $version = new DocumentVersion();
                $lastVersion = DocumentVersion::where('document_id', $document->id)
                ->orderBy('version_number', 'desc')
                ->first();
                $newVersionNumber = $lastVersion ? $lastVersion->version_number + 1 : 1;    
                $version->version_number = $newVersionNumber;
                $version->content = $request->content;
                $version->document_id = $document->id;
                $version->created_by = $user->id;

                $version->save();
            }

            Log::info("DocumentController: checking if the document has file ". $request->file);
          


            if ($request->hasFile('file')) {
                Log::info("DocumentController: the document has file");
                $version = new DocumentVersion();
                $lastVersion = DocumentVersion::where('document_id', $document->id)
                ->orderBy('version_number', 'desc')
                ->first();
                $newVersionNumber = $lastVersion ? $lastVersion->version_number + 1 : 1;    
                $version->version_number = $newVersionNumber;

                $storedPath = $request->file('file')->store('documents', 'public');
                $filePath = asset('storage/' . $storedPath);
                $version->file_path = $filePath;
                $version->document_id = $document->id;
                $version->created_by = $user->id;

                $version->save();
            }    

            DB::commit();
            return ResponseHelper::success(message: 'Document updated successfully', data: new DocumentResource($document));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('DocumentController::update() ' . "line: ". $th->getLine() . " "  . $th->getMessage());
            return ResponseHelper::error(message: 'failed to update document', statusCode:500);
        }
      }


      public function testUpdate(Request $request) {
        if($request->hasFile('file')) {
            return response()->json(["message" => "file is present in the request"]);
        }else{
            return response()->json(["message" => "theres no file"], 400);
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
            Log::error('DocumentController::delete() ' . "line: ". $th->getLine() . " " . $th->getMessage());
            return ResponseHelper::error(message: "failed to delete document", statusCode:500);
        }
      }

    public function recentDocuments(Request $request)
    {
    try {
        $limit = $request->query('limit', 5); // Default to 10 recent documents
        $documents = Document::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return ResponseHelper::success(
            message: "Recent documents fetched successfully",
            data: DocumentResource::collection($documents)
        );
    } catch (\Throwable $th) {
        Log::error('DocumentController::recentDocuments() ' . $th->getMessage());
        return ResponseHelper::error(message: "Failed to fetch recent documents", statusCode: 500);
    }
}
}
