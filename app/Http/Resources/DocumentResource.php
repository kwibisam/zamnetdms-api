<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isForm' => $this->isForm,
            'isFile' => $this->isFile,
            'isEditable' => $this->isEditable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tags' => $this->documentTags,
          

            'author' => $this->user->name, // Add author's name,
        

            'documentType' => $this->documentType,
       
            'workspace' => $this->workspace,
            'versions' => $this->documentVersions->map(function ($version) {
                return [
                    'version_number' => $version->version_number,
                    'file_path' => $version->file_path,
                    'content' => $version->content,
                ];
            }),
        ];
    }
}
