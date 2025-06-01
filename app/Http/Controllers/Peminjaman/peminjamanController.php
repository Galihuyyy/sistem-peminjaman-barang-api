<?php

namespace App\Http\Controllers\Peminjaman;

use App\Http\Controllers\Controller;
use App\Models\alat;
use App\Models\Peminjaman;
use App\Models\Transaksi;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class peminjamanController extends Controller
{

    
    // 1. Tambah ke cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'alat_id' => 'required|exists:alat,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $profile_id = Auth::user()->profile->id;

        $peminjaman = Peminjaman::create([
            'alat_id' => $request->alat_id,
            'jumlah' => $request->jumlah,
            'transaksi_id' => null,
            'peminjam_id' => $profile_id,
        ]);

        return response()->json([
            'message' => 'Alat berhasil ditambahkan ke keranjang',
            'data' => $peminjaman
        ], 201);
    }

    // 2. Lihat cart
    public function viewCart()
    {
        $profile_id = Auth::user()->id;
        $items = Peminjaman::with('alat')
            ->where('peminjam_id', $profile_id)
            ->whereNull('transaksi_id')
            ->get();

        return response()->json([
            'message' => 'Cart berhasil diambil',
            'data' => $items
        ]);
    }

    // 3. Checkout
    public function checkout(Request $request)
    {
        $user_id = auth()->user()->id;
        $peminjaman_id = $request->input('peminjaman_id');

        $transaksi = Transaksi::create([
            'peminjam_id' => $user_id,
            "tanggal_pinjam" => now(),
            "tanggal_kembali" => now()->addDays(3),
            "status" => "pending"
        ]);

        foreach ($peminjaman_id as $id) {
            Peminjaman::where('id', $id)
                ->where('peminjam_id', $user_id)
                ->update(['transaksi_id' => $transaksi->id]);
        }
        
        if (!$transaksi) {
            return response()->json([
                "message" => "gagal melakukan checkout"
            ], 500);
        }

        return response()->json([
            "message" => "berhasil checkout",
            "transaksi_id" => $transaksi->id
        ], 200);
        
    }

    // 4. Konfirmasi oleh admin
    public function confirmTransaksi($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'dipinjam';
        $transaksi->save();

        return response()->json([
            'message' => 'Transaksi berhasil dikonfirmasi',
            'data' => $transaksi
        ]);
    }

    // 5. Pengembalian alat
    public function kembalikan($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'dikembalikan';
        $transaksi->tanggal_kembali = now();
        $transaksi->save();

        return response()->json([
            'message' => 'Alat berhasil dikembalikan',
            'data' => $transaksi
        ]);
    }

    // 6. Tambah ulasan setelah pengembalian
    public function tambahUlasan(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string'
        ]);

        $peminjaman = Peminjaman::find($request->peminjaman_id);

        if ($peminjaman->transaksi->status !== 'dikembalikan') {
            return response()->json(['message' => 'Belum bisa ulasan sebelum pengembalian'], 403);
        }

        $ulasan = Ulasan::create([
            'peminjaman_id' => $request->peminjaman_id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'message' => 'Ulasan berhasil ditambahkan',
            'data' => $ulasan
        ], 201);
    }

    public function transaksiPending(string $id)
    {
        $transaksi = Transaksi::find( $id );

        $transaksi->delete();

        return response()->json([
            'message' => 'Transaksi berhasil didelete',
        ]);
    }

    public function riwayatTransaksi()
    {
        $profile_id = Auth::user()->profile->id;
    
        $transaksi = Transaksi::with(['peminjaman.alat', 'peminjaman.peminjam.profile'])
            ->where('peminjam_id', $profile_id)
            ->whereIn('status', ['dipinjam', 'dikembalikan'])
            ->get();
    
        return response()->json([
            'message' => 'Riwayat transaksi berhasil diambil',
            'data' => $transaksi
        ]);
    }

    public function getTransaksi()
    {
    
        $transaksi = Transaksi::with(['peminjaman.alat', 'peminjaman.peminjam.profile'])
            ->get();
    
        return response()->json([
            'message' => 'Riwayat transaksi berhasil diambil',
            'data' => $transaksi
        ]);
    }
    
    public function pinjamLangsung(Request $request)
    {

        $request->validate([
            'alat_id' => 'required|exists:alat,id',
            'jumlah' => 'required|integer|min:1'
        ]);
    
        $profile_id = Auth::user()->id;
    
        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'peminjam_id' => $profile_id,
                'tanggal_pinjam' => now(),
                'status' => 'pending'
            ]);
            
            $peminjaman = Peminjaman::create([
                'alat_id' => $request->alat_id,
                'peminjam_id' => $profile_id,
                'jumlah' => $request->jumlah,
                'transaksi_id' => $transaksi->id,
            ]);


            $alat = alat::find($request->alat_id);
            
            $alat->stok -= $request->jumlah;

            $alat->save();
    
            DB::commit();
            return response()->json([
                'message' => 'Alat berhasil langsung dipinjam',
                'data' => [
                    'transaksi' => $transaksi,
                    'peminjaman' => $peminjaman
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Gagal pinjam langsung', 'error' => $e->getMessage()], 500);
        }
    }
    

}
