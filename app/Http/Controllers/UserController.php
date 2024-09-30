<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\TestMail;
use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use App\Notifications\RequestDeleteNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use PgSql\Lob;

use function Psy\debug;

class UserController extends Controller
{

    public function getUser()
    {
        $user = User::where('id', auth()->id())->with('role')->first();
        return response()->json($user);
    }
    public function getDepartUsers()
    {
        $users = User::where('department_id', auth()->user()->department_id)->where('id', '!=', auth()->id())
            ->paginate(5);
        return response()->json($users);
    }
    public function getAllUsers()
    {
        $user = User::where('role_id', '!=', 1)->with('role')->with('department')->paginate(7);
        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'string|required',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->save();

        return response()->json(['message' => 'Updated successfully']);
    }

    public function login(LoginRequest $request)
    { 
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();


            if (is_null($user->department_id)) {
                return response()->json([
                    'error' => 'Not verified by admin'
                ], 403);
            }

            $token = $user->createToken('authToken')->plainTextToken;


            return response()->json([
                'message' => 'Logged in',
                'user' => $user,
                'token' => $token
            ], 201);
        } else {
            return response()->json(['error' => 'Login failed'], 404);
        }
    }

    public function resetPassword(Request $request)
{

    $user = User::where('remember_token',$request->token)->first();

    $request->validate([
        'password' => 'required|confirmed|min:8',
    ]);

    if($user)
    {
        
$timeDiff = strtotime(Carbon::now()->timezone('Asia/Kathmandu')) - strtotime($user->updated_at->timezone('Asia/Kathmandu'));
if ($timeDiff > 5*60) {
    $user->update([
        'remember_token' =>null
    ]);
    $user->save();
    return response()->json(['message' => 'Token expired, Please send again.'], 422);
}
        $user->update([
            'password' =>Hash::make($request->password),
            'remember_token' =>null
        ]);
        $user->save();
        return response()->json(['message' => 'Password reset successfully.']);
    }
    else{
        return response()->json(['message' => 'Token expired, Please send again.'], 422);
    }
}

    public function createNewUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer',
            'department_id' => 'required|integer',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);
        User::create($validatedData);

        return response()->json(['message' => 'User created successfully']);
    }

    public function register(RegisterRequest $request)
    {
        try {
            $existingUser = User::where('email', $request->email)->first();

            if ($existingUser) {
                return response()->json(['message' => 'Email already exists'], 409);
            }else{   
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'department_id' => null,
                    'role_id' => 3,
                ]);
                return response()->json(['message' => 'Registration successful. Please wait for admin verification.'], 201);
            }
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json(['message' => 'Registration failed'], 500);
        }
    }



    public function updateRole(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->role_id = $request->role_id;
            $user->save();

            return response()->json(['message' => 'Role updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating role: ' . $e->getMessage()], 500);
        }
    }

    public function updateDepartment(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->department_id = $request->department_id;
            $user->save();

            return response()->json(['message' => 'Department updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating department: ' . $e->getMessage()], 500);
        }
    }


    public function fetchTotals()
    {
        $user = Auth::user();
        $totals = [];

        if ($user->role_id === 1) {
            $totals = [
                'totalPosts' => Post::count(),
                'totalPersonalPosts' => Post::where('user_id', $user->id)->count(),
                'totalUsers' => User::count(),
                'totalDepartments' => Department::count(),
            ];
        } elseif ($user->role_id === 2) {
            $department = $user->department;
            $totals = [ 
                'totalDepartmentPosts' => Post::whereHas('user', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })->count(),
                'totalPersonalPosts' => Post::where('user_id', $user->id)->count(),
                'totalDepartmentUsers' => User::where('department_id', $department->id)->count(),
                'departmentName' => $department->name,
            ];
        } else {
            $department = $user->department;
            $totals = [
                'totalPersonalPosts' => Post::where('user_id', $user->id)->count(),
                'totalValidatedPosts' => Post::where('user_id', $user->id)->whereIn('status', [2, 3])->count(),
                'totalDepartmentPosts' => Post::whereHas('user', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })->whereIn('status', [2, 3])->count(),
                'departmentName' => $department->name,
            ];
        }

        return response()->json($totals);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }



    public function requestSuperAdmin($userId)
    {
        $user = Auth::user();

        $userToDelete = User::findOrFail($userId);

        $superAdmins = User::where('role_id', 1)->get();

        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new RequestDeleteNotification($userToDelete, $user));
        }

        return response()->json(['message' => 'Super Admin notified successfully']);
    }
}
