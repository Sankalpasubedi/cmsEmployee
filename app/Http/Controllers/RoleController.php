<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getAllRoles()
    {
        $role = Role::where('id','!=',1)->get();
        return response()->json($role);
    }
    
}
