<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    protected $fillable = [
        'title',
        'isForm',
        'isFile',
        'isEditable'
    ];
    /**
     * Get the user that created this document
     */
    public function user ()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get document workspace
     */
    public function workspace ()
    {
        return $this->belongsTo(WorkSpace::class);
    }

    
    /**
     * Get document meta
     */

     public function documentMeta ()
     {
        return $this->hasMany(DocumentMeta::class);
     }

     /**
      * Get document versions
      */
      public function documentVersions()
      {
        return $this->hasMany(DocumentVersion::class, "document_id");
      }

      /**
       * Get document tags
       */
      public function documentTags()
      {
        return $this->belongsToMany(Tag::class);
      }

       /**
       * Get document type
       */
      public function documentType()
      {
        return $this->belongsTo(DocumentType::class, 'document_type');
      }

}
