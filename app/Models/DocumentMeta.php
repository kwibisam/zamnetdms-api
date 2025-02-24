<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentMeta extends Model
{
    //
    protected $fillable = [
        'key',
        'value'
    ];


    /**
     * Get document of this meta
     */
    public function document ()
    {
        return $this->belongsTo(Document::class);
    }
}
