<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = Kategori::orderBy('id')->get();
        if ($kategoriList->isEmpty()) {
            return;
        }

        $k1 = $kategoriList[0]->id;
        $k2 = $kategoriList[1]->id ?? $k1;
        $k3 = $kategoriList[2]->id ?? $k1;
        $k4 = $kategoriList[3]->id ?? $k1;
        $k5 = $kategoriList[4]->id ?? $k1;

        $alat = [
            [
                'kategori_id' => $k1,
                'nama_alat' => 'Router Mikrotik RB941-2nD',
                'stok' => 15,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Router nirkabel rumahan yang cocock untuk praktik jaringan dasar',
                'gambar' => 'mikrotik_rb941.jpg',
            ],
            [
                'kategori_id' => $k2,
                'nama_alat' => 'Kamera DSLR Canon EOS 3000D',
                'stok' => 5,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Kamera pemula untuk kebutuhan dokumentasi dan pembuatan aset media',
                'gambar' => 'Canon_3000D.jpg',
            ],
            [
                'kategori_id' => $k3,
                'nama_alat' => 'Mini PC Intel NUC 11',
                'stok' => 8,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Perangkat komputtasi ringkas untuk server lokal skala kecil.',
                'gambar' => 'intel_nuc.jpg',
            ],
            [
                'kategori_id' => $k4,
                'nama_alat' => 'Tang Crimping RJ45/RJ11',
                'stok' => 20,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'alat potong dan pasang konektor kabel UTP',
                'gambar' => 'crimping_proskit.jpg',
            ],
            [
                'kategori_id' => $k5,
                'nama_alat' => 'Adapter HDMI to VGA dengan Audio',
                'stok' => 25,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Konverter display untuk menyambuangkan perangkat modern ke proyektor lama',
                'gambar' => 'hdmi_vga.jpg',
            ],
        ];

        foreach ($alat as $item) {
            Alat::firstOrCreate(['nama_alat' => $item['nama_alat']], $item);
        }
    }
}
