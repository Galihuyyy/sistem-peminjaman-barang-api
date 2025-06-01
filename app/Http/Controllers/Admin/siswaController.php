<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class siswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa = User::where('role', 'siswa')->with('profile.kelas', 'profile.jurusan')->get();

        $res_data = $siswa->map(function ($siswa) {
            return [
                "id" => $siswa->id,
                "username" => $siswa->username,
                "email" => $siswa->email,
                "created_at" => $siswa->created_at,
                "updated_at" => $siswa->updated_at,
                "profile" => [
                    "name" => $siswa->profile->name,
                    "kelas" => $siswa->profile->kelas->name,
                    "jurusan" => $siswa->profile->jurusan->name,
                    "gender" => $siswa->profile->gender,
                    "no_telp" => $siswa->profile->no_telp,
                    ]
                ];
        });
        
        return response()->json([
            'message' => 'berhasil mendapatkan data siswa',
            'data' => $res_data
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
            'kelas_id' => ['required' ,'exists:kelas,id'],
            'jurusan_id' => ['required' ,'exists:jurusan,id'],
            'gender' => ['required', 'in:pria,wanita'],
            'no_telp' => ['required', 'min:10' ,'max:15', 'unique:profile,no_telp'],
        ]);

        $data_user = [
            "username" => $request->input('username'),
            "email" => $request->input('email'),
            'password' => Hash::make($request->input('password'))
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
            'kelas_id' => $request->input('kelas_id'),
            'jurusan_id' => $request->input('jurusan_id'),
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
            'message' => 'tambah siswa success'
        ], 200);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siswa = User::where('role', 'siswa')->where('id', $id)->with(['profile.kelas', 'profile.jurusan'])->first();

        $res_data = [
            "id" => $siswa->id,
            "username" => $siswa->username,
            "email" => $siswa->email,
            "created_at" => $siswa->created_at,
            "updated_at" => $siswa->updated_at,
            "profile" => [
                "name" => $siswa->profile->name,
                "kelas" => $siswa->profile->kelas->name,
                "jurusan" => $siswa->profile->jurusan->name,
                "gender" => $siswa->profile->gender,
                "no_telp" => $siswa->profile->no_telp
                ]
            ];
        
        if (!$siswa) {
            return response()->json([
                'message' => 'ID tidak ditemukan'
            ], 400);
        }

        return response()->json($res_data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        if (User::find($id)->role !== "siswa") {
            return response()->json([
                'message' => 'User bukan siswa'
            ], 400);
        }
        
        $val = $request->validate([
            'username' => ['required', 'min:4', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
            'name' => ['required'],
            'kelas_id' => ['required' ,'exists:kelas,id'],
            'jurusan_id' => ['required' ,'exists:jurusan,id'],
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
            'kelas_id' => $request->input('kelas_id'),
            'jurusan_id' => $request->input('jurusan_id'),
            'gender' => $request->input('gender'),
            'no_telp' => $request->input('no_telp')
        ]);
    
        return response()->json([
            'message' => 'Update siswa success'
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
