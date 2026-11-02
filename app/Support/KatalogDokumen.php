<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Daftar dokumen formal yang bisa dibuat dari ruang kerja pasien.
 * 'form' menentukan isian yang dipakai: sakit, sehat, rujukan, persetujuan, atau umum.
 * Catatan untuk IT RSCC: dokumen baru cukup ditambahkan ke daftar di bawah.
 */
class KatalogDokumen
{
    private const SURAT = [
        'Surat Sehat', 'Surat Sakit', 'Surat Bebas Buta Warna', 'Surat Bebas Narkoba',
        'Surat Keterangan Diagnosa', 'Surat Rujuk Balik', 'Surat Perlu Perawatan di RS',
        'Surat Rujukan Keluar', 'Surat Rujukan / Konsul Intern', 'Surat Keterangan Sehat Rohani',
        'Surat Rujukan Psikolog', 'SKDP Jantung', 'SKDP Stroke', 'SKDP Hipertensi', 'SKDP Epilepsi',
        'SKDP Schizophrenia', 'SKDP Diabetes Melitus', 'Surat Pengantar Rawat Inap',
        'Surat Pelimpahan Wewenang', 'Surat Pernyataan Pulang APS',
        'Surat Izin Keluar Rawat Inap Sementara', 'Surat Keterangan Kematian',
    ];

    private const PERSETUJUAN = [
        'General Consent', 'Persetujuan Tindakan Kedokteran', 'Penolakan Tindakan Kedokteran',
        'Penunjukkan DPJP', 'Penerimaan Orientasi Pasien Baru', 'Persetujuan General Anestesi',
        'Persetujuan Regional Anestesi', 'Pemberian Informasi dan Edukasi',
        'Persetujuan Tindakan Hemodialisa', 'Persetujuan Tindakan Hemodialisa Transfusi',
        'Rekam Medis Hemodialisa', 'Monitoring Pasien di Ambulance',
        'Lembar Monitoring Early Warning (Dewasa)', 'Lembar Monitoring Early Warning (Obstetrik)',
        'Lembar Monitoring Early Warning (Anak)', 'Lembar Hak dan Kewajiban Pasien',
        'Pemberian Edukasi Pasien Terintegrasi', 'Berita Acara Serah Terima Jenazah (Perawat/Bidan)',
        'Berita Acara Serah Terima Jenazah (Petugas Security)', 'Pelayanan Ambulance Pasien BPJS',
        'Persetujuan Perawatan ICU', 'Pemberian Informasi dan Persetujuan Perawatan ICU',
        'Permintaan Pelayanan Kerohanian', 'Persetujuan Tindakan Pembiusan',
        'Persetujuan Tindakan Restrain / Seklusi', 'Do Not Resuscitate (DNR)', 'Penolakan Resusitasi (DNR)',
    ];

    private const POPULER = [
        'Surat Sakit', 'Surat Sehat', 'Surat Rujukan Keluar', 'Surat Rujuk Balik',
        'General Consent', 'Persetujuan Tindakan Kedokteran', 'Pemberian Informasi dan Edukasi',
    ];

    public static function semua(string $jenis): array
    {
        $daftar = $jenis === 'surat' ? self::SURAT : self::PERSETUJUAN;

        return array_map(fn ($nama) => [
            'nama'    => $nama,
            'slug'    => Str::slug($nama),
            'form'    => self::jenisForm($jenis, $nama),
            'populer' => in_array($nama, self::POPULER, true),
        ], $daftar);
    }

    public static function jumlah(): array
    {
        return ['surat' => count(self::SURAT), 'persetujuan' => count(self::PERSETUJUAN)];
    }

    private static function jenisForm(string $jenis, string $nama): string
    {
        if ($jenis === 'persetujuan') {
            return Str::contains($nama, ['Monitoring', 'Rekam Medis', 'Berita Acara']) ? 'umum' : 'persetujuan';
        }

        return match (true) {
            Str::contains($nama, 'Sakit')                                  => 'sakit',
            Str::contains($nama, ['Sehat', 'Buta Warna', 'Narkoba'])       => 'sehat',
            Str::contains($nama, ['Rujuk', 'Rujukan'])                     => 'rujukan',
            default                                                        => 'umum',
        };
    }
}