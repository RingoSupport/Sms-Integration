<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    //
    
    public function store(Request $request)
    {

         $user = auth()->user();

        if (!$user || $user->role->access_type !== 'admin' || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        // Validate the request
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'access_type' => 'required|string|in:admin,superadmin',
        ]);

        // Create the role
        $role = Role::create([
            'name' => $request->name,
            'access_type' => $request->access_type,
        ]);

        return response()->json($role, 201);
    }

    public function getAllRoles()
    {
         $user = auth()->user();

        if (!$user || $user->role->access_type !== 'admin' || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $roles = Role::all();
        return response()->json($roles, 200);
    }
    public function search(Request $request)
    {
        $query = Role::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('access_type')) {
            $query->where('access_type', 'like', '%' . $request->access_type . '%');
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No roles found'], 404);
        }

        return response()->json($results, 200);
    }  
    
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || $user->role->access_type !== 'admin' || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        // Validate the request
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'access_type' => 'required|string|in:admin,superadmin',
        ]);

        // Find the role
        $role = Role::findOrFail($id);

        // Update the role
        $role->update([
            'name' => $request->name,
            'access_type' => $request->access_type,
        ]);

        return response()->json($role, 200);
    }
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->role->access_type !== 'admin' || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        // Find the role
        $role = Role::findOrFail($id);

        // Delete the role
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    } 
    
    
}
