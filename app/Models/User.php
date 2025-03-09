<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get documents made by this user
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get user department
     */
     public function department()
     {
        return $this->belongsTo(Department::class);
     }

    /**
     * Get user WorkSpaces
     */

     public function workspaces () 
     {
        return $this->belongsToMany(WorkSpace::class, 'user_workspace', 'user_id', 'workspace_id');
     }

    //  public function defaultWorkspace()
    //  {
    //     return $this->belongsToMany(WorkSpace::class, 'user_workspace', 'user_id', 'workspace_id')
    //     ->wherePivot('is_default', true)
    //     ->withPivot('is_default');
    //  }


    /**
     * Get user Roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a given role.
     *
     * @param  string  $roleName
     * @return bool
    */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}
