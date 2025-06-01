<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function PHPSTORM_META\map;

class adminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->whereNot('id', '=', auth()->user()->id)->with('profile')->get();

        return response()->json([
            'message' => 'berhasil mendapatkan data admin',
            'data' => $admins
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'username' => ['required', 'min:4', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
            'name' => ['required'],
            'gender' => ['required', 'in:pria,wanita'],
            'no_telp' => ['required', 'min:10' ,'max:15', 'unique:profile,no_telp'],
        ]);

        $data_user = [
            "username" => $request->input('username'),
            "email" => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'admin'
        ];

        $new_user = User::create($data_user);

        if (!$new_user){
            return response()->json([
                'message' => 'ada masalah dengan server!'
            ], 500);
        }

        $data_profile = [
            'user_id' => $new_user->id,
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'no_telp' => $request->input('no_telp')
        ];

        $new_profile = profile::create($data_profile);

        if (!$new_profile) {
            return response()->json([
                'message' => 'ada masalah saat menambahkan profile!'
            ], 400);
        }

        return response()->json([
            'message' => 'register success'
        ], 200);
            
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (User::find($id)->role != "admin") {
            return response()->json([
                'message' => 'User ini bukan admin'
            ], 400);
        }
        $admin = User::where('role', 'admin')->where('id', $id)->with('profile')->first();

        if (!$admin) {
            return response()->json([
                'message' => 'ID tidak ditemukan'
            ], 400);
        }

        return response()->json($admin, 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $val = $request->validate([
            'username' => ['required', 'min:4', 'max:50'],
            'email' => ['required', 'email'],
            'name' => ['required'],
            'gender' => ['required', 'in:pria,wanita'],
            'no_telp' => ['required', 'min:10', 'max:15'],
        ]);
    
        // cari user
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan!'
            ], 404);
        }
    
        // update user
        $user->update([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'admin'
        ]);
    
        // cari profile
        $profile = Profile::where('user_id', $id)->first();
        if (!$profile) {
            return response()->json([
                'message' => 'Profile tidak ditemukan!'
            ], 404);
        }

        // update profile
        $profile->update([
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'no_telp' => $request->input('no_telp')
        ]);
    
        return response()->json([
            'message' => 'Update success'
        ], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user->delete()) {
            return response()->json([
                'message' => 'berhasil hapus data',
                'data_dihapus' => $user
            ], 200);
        }

        return response()->json([
            'message' => 'gagal menghapus data'
        ], 500);
    }

 
}
