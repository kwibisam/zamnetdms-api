<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given document can be updated by the user.
     */
    public function update(User $user, Document $document): Response
    {
        // Allow update if the user is an admin.
        if ($user->hasRole('admin')) {
            return Response::allow();
        }
        
        return $user->id === $document->created_by ?
        Response::allow() : Response::deny('you do not own this document');
    }

    
    /**
     * Determine if user can register another user.
     */
    public function register(User $user): Response
    {
        if($user->hasRole('admin')){
            return Response::allow();
        }
        return Response::deny(message: "User not authorized to perform this action", code:403);
        
    }

    
    /**
     * Determine if user can update user roles
     */
    public function updateUserRole(User $user): Response
    {
        if($user->hasRole('admin')){
            return Response::allow();
        }
        return Response::deny(message: "User not authorized to perform this action", code:403);
        
    }

    
    /**
     * Determine if user can update user workspaces
     */
    public function updateUserWorkSpace(User $user): Response
    {
        if($user->hasRole('admin')){
            return Response::allow();
        }
        return Response::deny(message: "User not authorized to perform this action", code:403);
        
    }
}
