<?php

namespace App\Support;

/**
 * Isi menu rekam medis — dipakai components/menu-rm dan cari menu (Ctrl+K).
 * Menu menyesuaikan unit kunjungan (lihat KonfigurasiRekamMedis).
 * 'href' => '#' berarti halaman belum dibuat → tampil dengan label "Segera".
 */
class MenuRekamMedis
{
    public static function grup(string $noReg, ?string $unit = null): array
    {
        $unit ??= KonfigurasiRekamMedis::unitAktif($noReg)['unit'];
        $r   = fn (string $nama, array $param = []) => route("rekam-medis.$nama", [$noReg, ...$param]);
        $dok = KatalogDokumen::jumlah();
        $cek = KatalogChecklist::jumlah();

        return [
            ['judul' => 'Asesmen & pemeriksaan', 'ikon' => 'bi-clipboard2-pulse', 'items' => [
                ['key' => 'ringkasan', 'label' => 'Ringkasan pasien', 'href' => $r('show')],
                ...KonfigurasiRekamMedis::formUtama($unit, $r),
                ['key' => 'ttv', 'label' => 'Tanda vital', 'href' => $r('ttv')],
            ]],
            ...KonfigurasiRekamMedis::grupKhusus($unit),
            ['judul' => 'Catatan terintegrasi', 'ikon' => 'bi-journal-text', 'items' => [
                ['key' => 'cppt', 'label' => 'CPPT / SBAR', 'href' => $r('cppt')],
            ]],
            ['judul' => 'Penunjang', 'ikon' => 'bi-eyedropper', 'items' => [
                ['key' => 'permintaan', 'label' => 'Permintaan lab / radiologi', 'href' => $r('permintaan')],
                ['key' => 'berkas',     'label' => 'Berkas digital',             'href' => $r('berkas')],
            ]],
            ['judul' => 'Farmasi', 'ikon' => 'bi-capsule', 'items' => [
                ['key' => 'resep', 'label' => 'E-Resep', 'href' => $r('resep')],
            ]],
            ['judul' => 'Riwayat', 'ikon' => 'bi-clock-history', 'items' => [
                ['key' => 'riwayat', 'label' => 'Riwayat pemeriksaan', 'href' => $r('riwayat')],
            ]],
            ['judul' => 'Dokumen', 'ikon' => 'bi-folder2-open', 'items' => [
                ['key' => 'surat',       'label' => 'Pembuatan surat', 'jumlah' => $dok['surat'],       'href' => $r('dokumen', ['surat'])],
                ['key' => 'persetujuan', 'label' => 'Persetujuan',     'jumlah' => $dok['persetujuan'], 'href' => $r('dokumen', ['persetujuan'])],
                ['key' => 'cetak',       'label' => 'Cetak',           'href' => $r('cetak')],
            ]],
            ['judul' => 'Mutu & lainnya', 'ikon' => 'bi-shield-check', 'items' => [
                ['key' => 'ppi',       'label' => 'PPI',               'jumlah' => $cek['ppi'],       'href' => $r('checklist', ['ppi'])],
                ['key' => 'cp',        'label' => 'Clinical pathways', 'jumlah' => $cek['cp'],        'href' => $r('checklist', ['cp'])],
                ['key' => 'psikologi', 'label' => 'Psikologi',         'jumlah' => $cek['psikologi'], 'href' => $r('checklist', ['psikologi'])],
            ]],
        ];
    }
}