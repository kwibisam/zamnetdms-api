<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSpace extends Model
{
    protected $table = 'workspaces';

    //
    protected $fillable = [
        'name'
    ];

    /**
     * Get workspace users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get workspace documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
