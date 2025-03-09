<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $fillable = [
        'name'
    ];

    /**
     * Get department users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
