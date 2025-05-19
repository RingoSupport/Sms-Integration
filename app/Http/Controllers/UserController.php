<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

  
    //
    public function store (Request $request)
    {

           $user = auth()->user();

        if (!$user || ($user->role->access_type !== 'admin' && $user->role->access_type !== 'superadmin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // Validate the request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'company_id' => 'required|exists:companies,id',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'required|string',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => $request->company_id,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
        ]);

        return response()->json($user, 201);
    }

    public function getAllUsers()
    {
         $user = auth()->user();

        if (!$user || $user->role->access_type !== 'admin' || $user->role->access_type !== 'superadmin')  
        {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $users = User::all();
        return response()->json($users, 200);
    }

    public function search(Request $request)
    {
        $query = User::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found'], 404);
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
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|string|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'company_id' => 'sometimes|required|exists:companies,id',
            'role_id' => 'sometimes|required|exists:roles,id',
            'phone' => 'sometimes|required|string',
        ]);

        // Find the user
        $user = User::findOrFail($id);

        // Update the user
        $user->update($request->all());

        return response()->json($user, 200);
    }
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->role->access_type !== 'admin' || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // Find the user
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
