<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends  \Illuminate\Routing\Controller
{
    // Méthode pour créer un rôle
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        return response()->json($role, 201);
    }

    // Méthode pour obtenir un rôle par son nom
    public function getRoleByName($name)
    {
        $role = Role::where('name', $name)->first();

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        return response()->json($role);
    }
}
