<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Daftar form checklist PPI, Clinical Pathways, dan Psikologi (CONTOH).
 * Satu template halaman untuk 17 form. IT RSCC / Komite: sesuaikan isi dengan SPO RS.
 */
class KatalogChecklist
{
    public const KATEGORI = [
        'ppi' => [
            'judul'   => 'PPI',
            'panjang' => 'Pencegahan dan Pengendalian Infeksi',
            'ikon'    => 'bi-shield-check',
            'opsi'    => ['ya' => 'Ya', 'tidak' => 'Tidak', 'na' => 'N/A'],
            'skor'    => 'Kepatuhan bundle',
        ],
        'cp' => [
            'judul'   => 'Clinical pathways',
            'panjang' => 'Clinical Pathways',
            'ikon'    => 'bi-signpost-split',
            'opsi'    => ['ya' => 'Ya', 'tidak' => 'Tidak', 'na' => 'N/A'],
            'skor'    => 'Kepatuhan clinical pathway',
        ],
        'psikologi' => [
            'judul'   => 'Psikologi',
            'panjang' => 'Pemeriksaan Psikologi',
            'ikon'    => 'bi-person-lines-fill',
            'opsi'    => ['baik' => 'Baik', 'cukup' => 'Cukup', 'kurang' => 'Kurang'],
            'skor'    => 'Aspek dengan nilai baik',
        ],
    ];

    /** Isi CP yang belum dirinci: pakai bagian umum ini. */
    private const CP_UMUM = [
        'Asesmen'               => ['Asesmen awal medis dalam 24 jam', 'Asesmen awal keperawatan dalam 24 jam', 'Asesmen gizi'],
        'Pemeriksaan penunjang' => ['Pemeriksaan penunjang sesuai clinical pathway'],
        'Tata laksana'          => ['Terapi sesuai clinical pathway', 'Konsultasi spesialis bila diperlukan'],
        'Edukasi & pulang'      => ['Edukasi penyakit kepada pasien dan keluarga', 'Kriteria pulang terpenuhi', 'Lama rawat sesuai clinical pathway'],
    ];

    /** [kategori => [nama form => [judul bagian => [butir, ...]] | null (pakai CP_UMUM)]] */
    private const FORM = [
        'ppi' => [
            'Bundle plebitis' => [
                'Pencegahan' => [
                    'Kebersihan tangan sebelum dan sesudah tindakan',
                    'Desinfeksi area insersi dengan antiseptik',
                    'Dressing transparan bersih dan kering',
                    'Penilaian skor plebitis (VIP) tiap shift',
                    'Kateter perifer diganti sesuai SPO',
                ],
            ],
            'Bundle ISK / CAUTI' => [
                'Pemasangan' => ['Indikasi pemasangan kateter sesuai', 'Pemasangan dengan teknik aseptik'],
                'Perawatan'  => [
                    'Kantong urin di bawah kandung kemih dan tidak menyentuh lantai',
                    'Perawatan kateter harian',
                    'Evaluasi harian kebutuhan kateter',
                ],
            ],
            'Bundle IDO' => [
                'Pra operasi'   => ['Mandi dengan antiseptik', 'Tidak mencukur dengan pisau cukur', 'Kadar gula darah terkontrol'],
                'Intra operasi' => ['Antibiotik profilaksis sesuai waktu SPO', 'Suhu tubuh pasien dijaga normal'],
            ],
            'Bundle VAP' => [
                'Pencegahan' => [
                    'Posisi kepala 30–45°',
                    'Oral hygiene sesuai SPO',
                    'Evaluasi sedasi harian',
                    'Kebersihan tangan sebelum kontak dengan ventilator',
                    'Manajemen sekresi',
                ],
            ],
            'Bundle IADP' => [
                'Pemasangan' => ['Kebersihan tangan', 'Alat pelindung diri lengkap', 'Antiseptik kulit sesuai SPO'],
                'Perawatan'  => ['Pemilihan lokasi insersi optimal', 'Evaluasi harian kebutuhan kateter'],
            ],
        ],
        'cp' => [
            'Demam tifoid' => [
                'Asesmen'               => ['Asesmen awal medis dalam 24 jam', 'Asesmen awal keperawatan dalam 24 jam', 'Asesmen gizi'],
                'Pemeriksaan penunjang' => ['Darah rutin', 'Tes Widal / Tubex', 'Fungsi hati bila diperlukan'],
                'Tata laksana'          => ['Antibiotik sesuai clinical pathway', 'Antipiretik', 'Cairan intravena sesuai kebutuhan', 'Diet lunak rendah serat'],
                'Edukasi & pulang'      => ['Edukasi kebersihan makanan dan cuci tangan', 'Bebas demam sesuai kriteria pulang', 'Lama rawat sesuai clinical pathway'],
            ],
            'TB paru'               => null,
            'Demam berdarah dengue' => null,
            'Stroke iskemik'        => null,
            'Stroke hemoragik'      => null,
            'Persalinan normal'     => null,
            'Sectio caesarea'       => null,
            'Pneumonia komunitas'   => null,
            'Gastroenteritis akut'  => null,
        ],
        'psikologi' => [
            'Pemeriksaan psikologi' => [
                'Observasi' => [
                    'Penampilan dan perawatan diri', 'Kontak mata dan komunikasi',
                    'Suasana hati (mood) dan afek', 'Orientasi waktu, tempat, dan orang', 'Proses pikir',
                ],
            ],
            'Kognitif CPM' => [
                'Pelaksanaan tes' => ['Memahami instruksi tes', 'Konsentrasi selama tes', 'Kecepatan mengerjakan', 'Ketelitian'],
            ],
            'Tes kesiapan masuk SD' => [
                'Aspek kesiapan' => [
                    'Kemandirian', 'Konsentrasi dan perhatian', 'Motorik halus (memegang pensil, menggunting)',
                    'Bahasa dan komunikasi', 'Sosial-emosional', 'Mengenal angka dan huruf',
                ],
            ],
        ],
    ];

    /** [['slug' => ..., 'nama' => ...], ...] */
    public static function daftar(string $kategori): array
    {
        return collect(self::FORM[$kategori])->keys()
            ->map(fn ($nama) => ['slug' => Str::slug($nama), 'nama' => $nama])
            ->all();
    }

    /** ['nama', 'slug', 'bagian' => [judul => [kode butir => teks]]] atau null */
    public static function form(string $kategori, string $slug): ?array
    {
        foreach (self::FORM[$kategori] as $nama => $bagian) {
            if (Str::slug($nama) !== $slug) {
                continue;
            }

            $bagian ??= self::CP_UMUM;
            $hasil = [];
            $b = 0;
            foreach ($bagian as $judul => $butir) {
                foreach ($butir as $i => $teks) {
                    $hasil[$judul]["$b-$i"] = $teks; // kode butir unik, mis. "0-2"
                }
                $b++;
            }

            return ['nama' => $nama, 'slug' => $slug, 'bagian' => $hasil];
        }

        return null;
    }

    /** Jumlah form per kategori, untuk angka di menu. */
    public static function jumlah(): array
    {
        return array_map('count', self::FORM);
    }

    /** Persentase jawaban "positif" (Ya / Baik) dari butir yang dinilai (N/A tidak dihitung). */
    public static function skor(string $kategori, array $jawab): ?int
    {
        $positif = array_key_first(self::KATEGORI[$kategori]['opsi']);
        $dinilai = array_filter($jawab, fn ($v) => $v !== 'na');

        return $dinilai
            ? (int) round(count(array_keys($dinilai, $positif, true)) / count($dinilai) * 100)
            : null;
    }
}