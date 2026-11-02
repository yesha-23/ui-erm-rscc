<?php

namespace App\Support;

/**
 * Data contoh untuk tampilan (BUKAN data pasien asli).
 * Catatan untuk IT RSCC: ganti isi tiap fungsi dengan query database
 * dengan bentuk array yang sama, tampilan tidak perlu diubah.
 */
class DataContoh
{
    public static function ringkasan(): array
    {
        return [
            'kunjungan'   => 284,
            'rawat_jalan' => 283,
            'rawat_inap'  => 1,
        ];
    }

    public static function kunjunganPerUnit(): array
    {
        return [
            ['unit' => 'Klinik Penyakit Dalam', 'jumlah' => 44],
            ['unit' => 'Klinik Syaraf',         'jumlah' => 29],
            ['unit' => 'Klinik Gigi dan Mulut', 'jumlah' => 29],
            ['unit' => 'Fisioterapi',           'jumlah' => 25],
            ['unit' => 'Laboratorium',          'jumlah' => 24],
            ['unit' => 'IGD',                   'jumlah' => 23],
        ];
    }

        /**
     * Data kunjungan per unit (data contoh, 12 baris).
     * Catatan untuk IT: ganti dengan query kunjungan per unit, dengan nama kunci yang sama.
     */
    public static function kunjungan(string $unit): array
    {
        $pasien = [
            ['Andi Pratama', '000123'], ['Siti Rahmawati', '000124'], ['Budi Santoso', '000131'],
            ['Dewi Lestari', '000142'], ['Hendra Wijaya', '000157'], ['Putri Maharani', '000163'],
            ['Rudi Hartono', '000170'], ['Nur Aisyah', '000181'], ['Agus Salim', '000195'],
            ['Rina Kartika', '000203'], ['Eko Prasetyo', '000214'], ['Lina Marlina', '000226'],
        ];
        $dokter  = ['dr. Arif Nugroho', 'dr. Laras Wulandari', 'dr. Rizky Ananda'];
        $perawat = ['Bagus Setiawan, A.Md.Kep', 'Fitri Handayani, A.Md.Kep', 'Maya Lestari, AMK'];
        $terapis = ['Citra Ayu, S.Tr.Kes', 'Rina Marlina, A.Md.TW', 'Sari Utami, A.Md.OT'];
        $tujuan  = [
            'igd'   => ['IGD', 'IGD, Laboratorium', 'IGD, Radiologi'],
            'hd'    => ['Hemodialisa', 'Laboratorium, Hemodialisa'],
            'rm'    => ['Okupasi Terapi', 'Terapi Wicara', 'Klinik Rehabilitasi Medik'],
            'rajal' => ['Klinik Penyakit Dalam', 'Klinik Syaraf', 'Klinik Gigi dan Mulut', 'Klinik Mata'],
        ];
        $ruang    = [
            ['Kenanga I', 'Kenanga I-C', 'Kelas III'], ['Lily II (Isolasi)', 'Lily II-A', 'Kelas I'],
            ['Mawar', 'Mawar-D', 'Kelas II'], ['Anggrek IV', 'Anggrek IV', 'VIP'],
        ];
        $diagnosa = ['Demam tifoid', 'Pneumonia', 'Selulitis pedis', 'Dispepsia', ''];
        $daftarTujuan = $tujuan[$unit] ?? ['—'];

        $hasil = [];
        foreach ($pasien as $i => [$nama, $rm]) {
            $hasil[] = [
                'no_rm'        => $rm,
                'nama'         => $nama,
                'no_reg'       => '26110200' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'no_sep'       => $i % 4 === 2 ? '' : '0179R01211260' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'tgl'          => '02 Nov 2026',
                'jam'          => sprintf('%02d:%02d', 7 + $i, ($i * 13) % 60),
                'penjamin'     => $i % 5 === 3 ? 'NON JKN' : 'JKN',
                'sudah_datang' => $i % 6 !== 4,
                'terbayar'     => $i % 3 === 0,
                'unit_tujuan'  => $daftarTujuan[$i % count($daftarTujuan)],
                'brm'          => $i % 4 !== 3,
                'close'        => $i % 3 === 0,
                'rujukan'      => $i % 5 === 0,
                'kontrol'      => $i % 4 === 1,
                'dokter_isi'   => $dokter[$i % 3],
                'dokter_pulang'=> $i % 3 === 0 ? $dokter[$i % 3] : '',
                'perawat'      => $perawat[$i % 3],
                'ppa1'         => in_array($unit, ['rm', 'rajal']) ? $terapis[$i % 3] : $dokter[$i % 3],
                'ppa2'         => $perawat[($i + 1) % 3],
                'ppa_farmasi'  => '',
                'kelas'        => $ruang[$i % 4][2],
                'ruang'        => $ruang[$i % 4][0],
                'bed'          => $ruang[$i % 4][1],
                'dpjp'         => $dokter[$i % 3] . ', Sp.PD',
                'diagnosa'     => $diagnosa[$i % 5],
                'unit_asal'    => $i % 3 ? 'UGD' : 'Poliklinik',
            ];
        }

        return $hasil;
    }
}