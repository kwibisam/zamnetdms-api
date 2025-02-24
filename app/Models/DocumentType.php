<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name'
    ];
    /**
     * Get Type Documents
     */
    public function typeDocuments()
    {
        return $this->hasMany(Document::class);
    }
}
