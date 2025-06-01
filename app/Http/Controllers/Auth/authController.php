<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\jurusan;
use App\Models\kelas;
use App\Models\profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    public function login(Request $request) {
        $val = $request->validate([
            'credentials' => ['required'],
            'password' => ['required']
        ]);

        $credentials = $request->input('credentials');
        $password = $request->input('password');

        $user = User::where('username', $credentials)->with(['profile.kelas', 'profile.jurusan'])->first();
        
        // cari username / email
        if (!$user) {
            $user = User::where('email', $credentials)->with(['profile.kelas', 'profile.jurusan'])->first();
            if (!$user){
                return response()->json([
                    'message' => 'username atau email salah!'
                ], 400);
            }
        }

        // samakan pass
        $cek_pass = Hash::check($password, $user->password);

        if (!$cek_pass) {
            return response()->json([
                'message' => 'password salah!',
            ], 400);
        }

        Auth::login($user);

        $res_data = [
            "id" => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "created_at" => $user->created_at,
            "updated_at" => $user->updated_at,
            "profile" => [
                "name" => $user->profile->name,
                "kelas" => $user->profile->kelas?->name,
                "jurusan" => $user->profile->jurusan?->name,
                "gender" => $user->profile->gender,
                "no_telp" => $user->profile->no_telp,
                ]
            ];
            
        if ($user->role == "admin") {
            $res_data["profile"] = [
                "name" => $user->profile->name,
                "gender" => $user->profile->gender,
                "no_telp" => $user->profile->no_telp,
            ];
        }

        $token = $user->createToken("auth")->plainTextToken;

        return response()->json([
            'message' => 'login berhasil!',
            'token' => $token,
            'role' => $user->role,
            "data" => $res_data
        ], 200);

    }

    public function register(Request $request) {
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
            'message' => 'register success'
        ], 200);
        
    }

    public function logout(Request $request) {
        $logout = auth()->user()->currentAccessToken()->delete();

        if ($logout) {
            return response()->json([
                'message' => 'logout success'
            ], 200);
        }

        return response()->json([
            'message' => 'logout gagal!'
        ], 500);
    }

    public function getKelas() {
        $kelas = kelas::all();

        return response()->json($kelas->map(function ($kelas) {
            return [
                'id' => $kelas->id,
                'name' => $kelas->name
            ];
        }), 200);
    }
    
    public function getJurusan() {
        $jurusan = jurusan::all();
    
        return response()->json($jurusan->map(function ($jurusan) {
            return [
                'id' => $jurusan->id,
                'name' => $jurusan->name
            ];
        }), 200);
        
    }
}
