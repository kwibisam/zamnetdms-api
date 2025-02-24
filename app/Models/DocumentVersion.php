<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    //
    protected $fillable = [
        'version_number',
        'file_path',
        'content'
    ];

    /**
     * Get version document
     */
    public function document()
    {
        return $this->belongsTo(Document::class, "document_id");
    }
}
