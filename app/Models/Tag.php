<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * tag documents
     */
    public function tagDocuments ()
    {
        return $this->belongsToMany(Document::class);
    }
}
