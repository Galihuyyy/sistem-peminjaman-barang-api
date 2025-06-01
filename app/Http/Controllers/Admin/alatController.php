<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\alat;
use App\Models\foto_alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class alatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alat = Alat::with('foto_alat')->get();

        $alat->map(function ($item) {
            $item->foto_alat->map(function ($foto) {
                $foto->foto = asset('storage/' . $foto->foto);
            });
            return $item;
        });

        if ($alat) {
            return response()->json([
                'message' => 'berhasil mendapatkan data alat!',
                'data' => [
                    "alat_tersedia" => $alat->where('stok', '>', 0)->values(),
                    "alat_tidak_tersedia" => $alat->where('stok', '=', 0)->values()
                ]
            ], 200);
        }

        return response()->json([
            'message' => 'gagal mendapatkan alat'
        ], 500);
}

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'stok' => ['required', 'integer', 'min:1'],
            'keterangan' => ['required', 'string'],
            'foto_produk.*' => ['file', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);
    
        // Simpan data alat
        $alat = Alat::create([
            'name' => $request->name,
            'deskripsi' => $request->deskripsi,
            'stok' => $request->stok,
            'keterangan' => $request->keterangan,
        ]);
    
        $fotoUrls = [];

        if ( $request->hasFile('foto_produk') ) {
            foreach ($request->file('foto_produk') as $foto) {

                $path = $foto->store('foto_alat', 'public');
                
                $foto_alat = foto_alat::create([
                    'alat_id' => $alat->id,
                    'foto' => $path
                ]);

                $fotoUrls[] = asset('storage/'. $foto_alat->foto);
                
            }
        }

        return response()->json([
            'message' => 'Data alat berhasil disimpan',
            'data' => [
                'alat' => $alat,
                'foto_produk' => $fotoUrls,
            ],
        ], 201);



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $alat = Alat::with([
            'foto_alat',
            'peminjaman' => function ($query) {
                $query->whereHas('transaksi', function ($q) {
                    $q->where('status', 'dikembalikan');
                });
            },
            'peminjaman.ulasan',
            'peminjaman.peminjam.profile',
            'peminjaman.transaksi',
        ])->find($id);

        $peminjamanDenganUlasan = $alat->peminjaman->filter(function ($p) {
            return $p->ulasan->isNotEmpty();
        });

        $ratings = $peminjamanDenganUlasan->flatMap(function ($p) {
            return $p->ulasan->pluck('rating');
        });

        $totalRating = $ratings->sum();
        $jumlahUlasan = $ratings->count();

        $rataRata = $jumlahUlasan > 0 ? $totalRating / $jumlahUlasan : 0;

        
        if (!$alat) {
            return response()->json([
                'message' => 'ID alat tidak ditemukan'
            ], 404);
        }

        // Generate full URL untuk setiap foto
        $fotoUrls = $alat->foto_alat->map(function ($foto) {
            return asset('storage/' . $foto->foto);
        });

        return response()->json([
            'message' => 'Data ditemukan',
            'rating' => round($rataRata,2),
            'data' => [
                'alat' => [
                    'id' => $alat->id,
                    'name' => $alat->name,
                    'deskripsi' => $alat->deskripsi,
                    'stok' => $alat->stok,
                    'keterangan' => $alat->keterangan,
                    'foto_alat' => $fotoUrls,
                    'created_at' => $alat->created_at,
                    'updated_at' => $alat->updated_at,
                ],
                'peminjaman' => $alat->peminjaman
            ]
        ], 200);
    }


    public function update(Request $request, string $id)
    {
        $alat = Alat::find($id);

        if (!$alat) {
            return response()->json([
                'message' => 'ID alat tidak ditemukan'
            ], 404);
        }

        $val = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'deskripsi' => ['sometimes', 'string'],
            'stok' => ['sometimes', 'integer', 'min:1'],
            'keterangan' => ['sometimes', 'string'],
            'foto_produk.*' => ['mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $alat->update($request->only(['name', 'deskripsi', 'stok', 'keterangan']));

        if ($request->hasFile('foto_produk')) {
            foreach ($request->file('foto_produk') as $foto) {
                $path = $foto->store('foto_alat', 'public');

                $foto_alat = foto_alat::create([
                    'alat_id' => $alat->id,
                    'foto' => $path
                ]);

                $fotoUrls[] = asset('storage/' . $path);
            }
            return response()->json([
                'message' => 'Data berhasil diperbarui',
                'data' => [
                    'alat' => $alat,
                    'foto_produk' => $fotoUrls
                ]
            ], 200);
        }
        
        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'data' => [
                'alat' => $alat,
            ]
        ], 200);

    }


    public function destroy(string $id)
    {
        $alat = Alat::with('foto_alat')->find($id);
    
        if (!$alat) {
            return response()->json([
                'message' => 'ID alat tidak ditemukan'
            ], 404);
        }
    
        // Hapus foto dari storage
        foreach ($alat->foto_alat as $foto) {
            if (Storage::disk('public')->exists($foto->foto)) {
                Storage::disk('public')->delete($foto->foto);
            }
            $foto->delete();
        }
    
        $alat->delete();
    
        return response()->json([
            'message' => 'Data alat dan fotonya berhasil dihapus'
        ], 200);
    }
    
}
