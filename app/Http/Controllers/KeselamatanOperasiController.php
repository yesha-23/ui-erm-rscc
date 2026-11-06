<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Checklist keselamatan operasi (WHO Surgical Safety Checklist) dalam 3 tahap.
 * Tiap tahap disimpan sendiri karena diisi pada jam yang berbeda, dan harus berurutan.
 * Baris checklist memakai tampilan yang sama dengan Checklist PPI.
 * Simulasi di session "ok_checklist.{noReg}". IT RSCC: tabel checklist_keselamatan_operasi.
 */
class KeselamatanOperasiController extends Controller
{
    /** [tahap => label, waktu, butir]. IT RSCC / Komite: sesuaikan butir dengan SPO RS. */
    public const TAHAP = [
        'sign-in' => [
            'label' => 'Sign in',
            'waktu' => 'Sebelum induksi anestesi',
            'butir' => [
                'Pasien mengonfirmasi identitas, lokasi operasi, prosedur, dan persetujuan tindakan',
                'Lokasi operasi sudah ditandai',
                'Mesin dan obat anestesi sudah dicek lengkap',
                'Pulse oximeter terpasang dan berfungsi',
                'Riwayat alergi pasien sudah dicek',
                'Risiko sulit jalan napas / aspirasi sudah dinilai, alat dan bantuan siap',
                'Risiko perdarahan > 500 ml (anak 7 ml/kgBB) sudah dinilai, akses IV dan cairan siap',
            ],
        ],
        'time-out' => [
            'label' => 'Time out',
            'waktu' => 'Sebelum insisi kulit',
            'butir' => [
                'Semua anggota tim menyebutkan nama dan perannya',
                'Operator, anestesi, dan perawat mengonfirmasi identitas pasien, lokasi, dan prosedur',
                'Antibiotik profilaksis diberikan dalam 60 menit terakhir',
                'Operator menyampaikan langkah kritis, lama operasi, dan antisipasi perdarahan',
                'Tim anestesi menyampaikan hal khusus terkait pasien',
                'Tim perawat mengonfirmasi sterilitas alat dan kesiapan peralatan',
                'Hasil pencitraan penting sudah ditampilkan',
            ],
        ],
        'sign-out' => [
            'label' => 'Sign out',
            'waktu' => 'Sebelum pasien keluar kamar operasi',
            'butir' => [
                'Nama prosedur yang dilakukan sudah dicatat',
                'Jumlah instrumen, kasa, dan jarum sudah lengkap',
                'Spesimen sudah diberi label (termasuk nama pasien)',
                'Masalah peralatan yang perlu ditangani sudah dicatat',
                'Operator, anestesi, dan perawat menyampaikan hal penting untuk pemulihan pasien',
            ],
        ],
    ];

    public const OPSI = ['ya' => 'Ya', 'tidak' => 'Tidak', 'na' => 'N/A'];

    public function index(string $noReg, string $tahap = 'sign-in')
    {
        $data = session("ok_checklist.$noReg", []);

        return view('rekam-medis.keselamatan-operasi', [
            'pasien'      => DataContoh::pasien($noReg),
            'tahap'       => $tahap,
            'daftarTahap' => self::TAHAP,
            'opsi'        => self::OPSI,
            'data'        => $data,
            'isi'         => $data[$tahap]['isi'] ?? [],
            'sebelumnya'  => self::tahapSebelumnyaBelum($tahap, $data),
            'skorTotal'   => self::skor(collect($data)->flatMap(fn ($t) => $t['isi']['jawab'])->all()),
            'crumb'       => 'Keselamatan operasi · ' . self::TAHAP[$tahap]['label'],
            'menuAktif'   => 'ok-checklist',
        ]);
    }

    public function simpan(Request $request, string $noReg, string $tahap)
    {
        $data = session("ok_checklist.$noReg", []);

        // Urutan wajib: Time out setelah Sign in, Sign out setelah Time out
        if ($belum = self::tahapSebelumnyaBelum($tahap, $data)) {
            return redirect()->route('rekam-medis.ok-checklist', [$noReg, $tahap])
                ->withErrors(['urutan' => self::TAHAP[$belum]['label'] . ' harus disimpan lebih dulu.']);
        }

        $kode   = array_keys(self::TAHAP[$tahap]['butir']);
        $aturan = [
            'jam'     => ['required', 'date_format:H:i'],
            'jawab'   => ['required', 'array'],
            // Ada jawaban "Tidak" → catatan wajib, supaya tim mencatat tindak lanjutnya
            'catatan' => ['nullable', 'string', 'max:1000',
                          Rule::requiredIf(in_array('tidak', (array) $request->input('jawab'), true))],
        ];
        foreach ($kode as $k) {
            $aturan["jawab.$k"] = ['required', Rule::in(array_keys(self::OPSI))];
        }

        $request->validate($aturan, [
            'jam.required'     => 'Jam wajib diisi.',
            'jam.date_format'  => 'Format jam harus JJ:MM.',
            'jawab.required'   => 'Masih ada butir yang belum dijawab.',
            'jawab.*.required' => 'Masih ada butir yang belum dijawab.',
            'jawab.*.in'       => 'Ada jawaban yang tidak valid.',
            'catatan.required' => 'Ada butir dijawab "Tidak". Tulis tindak lanjutnya di catatan.',
        ]);

        // Hanya butir milik tahap ini yang disimpan
        $jawab = array_intersect_key($request->input('jawab'), array_flip($kode));

        // TODO (IT RSCC): simpan ke tabel checklist_keselamatan_operasi (no_reg, tahap, jawaban, jam, petugas).
        $data[$tahap] = [
            'isi'   => ['jawab' => $jawab, 'jam' => $request->input('jam'), 'catatan' => $request->input('catatan')],
            'waktu' => now()->format('H:i'),
        ];
        session()->put("ok_checklist.$noReg", $data);

        $berikut = array_keys(self::TAHAP)[array_search($tahap, array_keys(self::TAHAP), true) + 1] ?? null;

        return redirect()
            ->route('rekam-medis.ok-checklist', [$noReg, $berikut ?? $tahap])
            ->with('sukses', self::TAHAP[$tahap]['label'] . ' tersimpan'
                . ($berikut ? ' · lanjut ' . self::TAHAP[$berikut]['label'] . '.' : ' · checklist lengkap.'));
    }

    /** Tahap sebelumnya yang belum disimpan, atau null bila urutan sudah benar. */
    public static function tahapSebelumnyaBelum(string $tahap, array $data): ?string
    {
        foreach (array_keys(self::TAHAP) as $k) {
            if ($k === $tahap) {
                return null;
            }
            if (! isset($data[$k])) {
                return $k;
            }
        }

        return null;
    }

    /** Persentase jawaban "Ya" dari butir yang dinilai (N/A tidak dihitung). */
    public static function skor(array $jawab): ?int
    {
        $dinilai = array_filter($jawab, fn ($v) => $v !== 'na');

        return $dinilai
            ? (int) round(count(array_keys($dinilai, 'ya', true)) / count($dinilai) * 100)
            : null;
    }

    /** Untuk "Perlu dikerjakan" di Ringkasan: [status, keterangan]. */
    public static function ringkas(string $noReg): array
    {
        $data = session("ok_checklist.$noReg", []);

        $selesai = $belum = [];
        foreach (self::TAHAP as $k => $t) {
            isset($data[$k]) ? $selesai[] = $t['label'] : $belum[] = $t['label'];
        }

        if (! $data) {
            return ['belum', 'Sign in, Time out, Sign out belum diisi'];
        }
        if (count($data) === count(self::TAHAP)) {
            $skor = self::skor(collect($data)->flatMap(fn ($t) => $t['isi']['jawab'])->all());

            return ['selesai', 'Lengkap · kepatuhan ' . ($skor === null ? '–' : "$skor%")];
        }

        return ['draf', implode(' & ', $selesai) . ' selesai · ' . implode(' & ', $belum) . ' belum'];
    }
}