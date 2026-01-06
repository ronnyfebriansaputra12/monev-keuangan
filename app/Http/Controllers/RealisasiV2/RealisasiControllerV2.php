<?php

namespace App\Http\Controllers\RealisasiV2;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Realisasi;
use App\Models\Satker;
use App\Models\CoaItem;
use App\Models\Mak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Tambahkan baris ini
// use App\Notifications\RealisasiCreatedNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RealisasiNotification;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class RealisasiControllerV2 extends Controller
{
    private function logActivity($activity, $description, $statusAwal = null, $statusAkhir = null, $realisasiId = null)
    {
        ActivityLog::create([
            'user_id'      => Auth::id(),
            'realisasi_id' => $realisasiId, // Memperbaiki penamaan key agar sesuai DB
            'activity'     => $activity,
            'description'  => $description,
            'role'         => Auth::user()->role,
            'status_awal'  => $statusAwal,
            'status_akhir' => $statusAkhir,
            'ip_address'   => request()->ip(),
        ]);
    }

    public function index(Request $request)
    {
        $coaItemId = $request->input('coa_item_id');
        $search = $request->input('search');
        $statusBerkas = $request->input('status_berkas');

        // Parameter filter tambahan
        $filterGup = $request->input('filter_gup');
        $filterRo = $request->input('filter_ro');
        $filterAkun = $request->input('filter_akun');

        // Filter Tanggal
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        // --- INISIALISASI QUERY UTAMA ---
        $q = Realisasi::with([
            'satker',
            'coaItem.mak.akun',
            'coaItem.subKomponen.komponen.rincianOutput'
        ]);

        // MODIFIKASI: Cek Role User
        // Jika role adalah 'PLO', batasi data hanya miliknya sendiri
        // Jika role lain (Superadmin, Bendahara, dll), biarkan tanpa filter created_by
        if (auth()->user()->role === 'PLO') {
            $q->where('created_by', auth()->id());
        }

        if ($coaItemId) {
            $q->where('coa_item_id', $coaItemId);
        }

        // --- LOGIC COUNT STATUS (Otomatis mengikuti filter Role di atas) ---
        $countQuery = clone $q;
        $counts = $countQuery->select('status_berkas', \DB::raw('count(*) as total'))
            ->groupBy('status_berkas')
            ->pluck('total', 'status_berkas')
            ->toArray();

        // -- APPLY FILTERS --
        if ($statusBerkas) {
            $q->where('status_berkas', $statusBerkas);
        }

        if ($filterGup) {
            $q->where('gup', $filterGup);
        }

        // Filter Tanggal Kuitansi
        if ($tgl_awal && $tgl_akhir) {
            $q->whereBetween('tgl_kuitansi', [$tgl_awal, $tgl_akhir]);
        }

        if ($filterRo) {
            $q->whereHas('coaItem.subKomponen.komponen.rincianOutput', function ($query) use ($filterRo) {
                $query->where('kode_ro', $filterRo);
            });
        }

        if ($filterAkun) {
            $q->whereHas('coaItem.mak.akun', function ($query) use ($filterAkun) {
                $query->where('kode_akun', $filterAkun);
            });
        }

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('uraian', 'like', "%{$search}%")
                    ->orWhere('penerima_penyedia', 'like', "%{$search}%")
                    ->orWhere('kode_unik_plo', 'like', "%{$search}%");
            });
        }

        $totalRealisasiFiltered = $q->sum('jumlah');

        $items = $q->orderBy('no_urut', 'desc')->get();
        $selectedCoa = $coaItemId ? \App\Models\CoaItem::find($coaItemId) : null;

        // -- DATA DROPDOWN --
        // Filter dropdown GUP juga disesuaikan dengan role
        $listGupQuery = Realisasi::whereNotNull('gup')->where('gup', '!=', '');
        if (auth()->user()->role === 'PLO') {
            $listGupQuery->where('created_by', auth()->id());
        }
        $listGup = $listGupQuery->distinct()->pluck('gup');

        $listRo = \DB::table('rincian_outputs')->whereNotNull('kode_ro')->distinct()->pluck('kode_ro');
        $listAkun = \DB::table('master_akuns')->select('kode_akun', 'nama_akun')->get();

        return view('realisasiv2.index', compact(
            'items',
            'selectedCoa',
            'coaItemId',
            'search',
            'counts',
            'statusBerkas',
            'listGup',
            'listRo',
            'listAkun',
            'tgl_awal',
            'tgl_akhir',
            'totalRealisasiFiltered'
        ));
    }

    public function create(Request $request)
    {
        $coaItemId = $request->query('coa_item_id');
        $selectedCoa = null;

        if ($coaItemId) {
            // Memuat silsilah anggaran lengkap untuk ditampilkan di side panel
            $selectedCoa = CoaItem::with(['subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker'])
                ->findOrFail($coaItemId);
        }

        $satkers = Satker::orderBy('nama_satker')->get();
        $coaItems = CoaItem::orderBy('kode_coa_item')->get();
        $maks = Mak::all();

        return view('realisasiv2.create', compact('satkers', 'coaItems', 'maks', 'selectedCoa'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'coa_item_id'               => 'required|exists:coa_items,id',
            'satker_id'                 => 'required|exists:satkers,id',
            'tahun_anggaran'            => 'required|integer',
            'mak_id'                    => 'required|exists:maks,id',
            'kode_unik_plo'             => 'required|string',
            'sumber_anggaran'           => 'required|string',
            'nama_kegiatan'             => 'required|string',
            'penerima_penyedia'         => 'required|string',
            'uraian'                    => 'nullable|string',
            'jumlah'                    => 'required|numeric',
            'nomor_kuitansi'            => 'nullable|string',
            'pph21'                     => 'nullable|numeric',
            'pph22'                     => 'nullable|numeric',
            'pph23'                     => 'nullable|numeric',
            'pph_final'                 => 'nullable|numeric',
            'ppn'                       => 'nullable|numeric',
            'npwp'                      => 'nullable|string',
            'tgl_kuitansi'              => 'required|date',
            'bidang'                    => 'nullable|string',
            'tanggal_penyerahan_berkas' => 'nullable|date',
            'status_berkas'             => 'nullable|string',
            'dokumen.*'                 => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        // 2. Logika Nomor Urut Otomatis (RESET per kategori, format angka biasa)
        $sumber = $request->sumber_anggaran;
        $bidang = $request->bidang;

        // Tentukan inisial tengah (Middle Part)
        $middlePart = ($sumber === 'GUP') ? $bidang : (($sumber === 'BGN') ? 'MG' : $sumber);

        // Cari record terakhir yang memiliki middle part yang sama
        $lastRecord = Realisasi::where('sumber_anggaran', $sumber)
            ->where('kode_unik_plo', 'like', "%.$middlePart.%")
            ->orderBy('no_urut', 'desc')
            ->first();

        // Mulai dari 1
        $nextNoUrut = $lastRecord ? ($lastRecord->no_urut + 1) : 1;

        // 3. Logika Upload Berkas
        $filePaths = [];
        if ($request->has('dokumen')) {
            foreach ($request->file('dokumen') as $nama_dokumen => $file) {
                if ($file) {
                    $path = $file->store('uploads/realisasi/' . date('Y/m'), 'public');
                    $filePaths[] = [
                        'nama_berkas' => $nama_dokumen,
                        'path'        => $path,
                        'uploaded_at' => now()->toDateTimeString()
                    ];
                }
            }
        }

        // 4. Penggabungan Data Akhir
        $data = $request->except('dokumen');
        $data['no_urut'] = $nextNoUrut; // Tersimpan sebagai integer (1, 2, dst)

        // Generate kode_unik_plo tanpa padding nol (Contoh: K.TU.1)
        $userInitial = Auth::user()->plo_code ?? 'U';
        $data['kode_unik_plo'] = $userInitial . '.' . $middlePart . '.' . $nextNoUrut;

        $data['lampiran'] = json_encode($filePaths);
        $data['total'] = $request->jumlah;
        $data['created_by'] = Auth::id();
        $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;

        // 5. Eksekusi Simpan
        $realisasi = Realisasi::create($data);

        // --- NOTIFIKASI ---
        $verifikators = User::where('role', 'verifikator')->get();
        if ($verifikators->count() > 0) {
            Notification::send($verifikators, new RealisasiNotification($realisasi));
        }

        // --- LOGGING ---
        $this->logActivity(
            'Tambah Realisasi',
            "User (PLO) membuat realisasi baru: {$realisasi->nama_kegiatan} dengan nominal Rp " . number_format($realisasi->jumlah, 0, ',', '.'),
            'NULL',
            $realisasi->status_berkas ?? 'Draft',
            $realisasi->id
        );

        return redirect()->route('realisasi-v2.index', ['coa_item_id' => $realisasi->coa_item_id])
            ->with('success', "Data berhasil disimpan dengan Kode: " . $realisasi->kode_unik_plo);
    }



    public function show($id)
    {
        // 1. Memuat data realisasi dengan Eager Loading yang optimal
        // Kita memuat relasi silsilah anggaran secara mendalam untuk kebutuhan "Hierarchy Anggaran" di View
        $realisasi = Realisasi::with([
            'mak',
            'coaItem.subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker',
            'logs' => function ($query) {
                $query->latest(); // Mengurutkan log dari yang terbaru
            },
            'logs.user' // User yang melakukan aktivitas di log
        ])->findOrFail($id);

        // 2. Tambahan: Memastikan array lampiran tidak null untuk menghindari error di View
        // Karena kita menggunakan cast 'array' di Model, Laravel otomatis mengubah JSON menjadi array.
        // Jika kolom lampiran kosong, kita beri array kosong.
        if (!$realisasi->lampiran) {
            $realisasi->lampiran = [];
        }

        // 3. Mengarahkan ke view detail
        return view('realisasiv2.show', compact('realisasi'));
    }
    public function getNextNoUrut(Request $request)
    {
        $sumber = $request->query('sumber');
        $bidang = $request->query('bidang');

        $middlePart = ($sumber === 'GUP') ? $bidang : (($sumber === 'BGN') ? 'MG' : $sumber);

        $lastRecord = Realisasi::where('sumber_anggaran', $sumber)
            ->where('kode_unik_plo', 'like', "%.$middlePart.%")
            ->orderBy('no_urut', 'desc')
            ->first();

        $nextNo = $lastRecord ? ($lastRecord->no_urut + 1) : 1;

        // Return angka murni tanpa padding str_pad
        return response()->json(['next_no_urut' => $nextNo]);
    }

    public function edit($id)
    {
        $realisasiV2 = Realisasi::with([
            'coaItem.subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker'
        ])->findOrFail($id);

        // Proteksi Role: PLO dan Bendahara diizinkan
        $allowedRoles = ['PLO', 'Bendahara'];
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        // Proteksi Status untuk PLO:
        // Izinkan edit jika status Draft, Ditolak, atau Menunggu Finalisasi Bendahara
        if (Auth::user()->role === 'PLO') {
            $allowedPloStatus = ['Draft', 'Ditolak/Revisi', 'Menunggu Finalisasi Bendahara'];
            if (!in_array($realisasiV2->status_berkas, $allowedPloStatus)) {
                return redirect()->back()->with('error', 'Berkas sedang dalam proses verifikasi PPK/PPSPM.');
            }
        }

        $satkers = Satker::orderBy('nama_satker')->get();
        $coaItems = CoaItem::orderBy('kode_coa_item')->get();
        $maks = Mak::all();

        return view('realisasiv2.edit', compact('realisasiV2', 'satkers', 'coaItems', 'maks'));
    }
    public function update(Request $request, $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        // 1. Validasi
        $rules = ['status_berkas' => 'required'];
        if (Auth::user()->role == 'Bendahara') {
            $rules['gup'] = 'required';
            $rules['no_urut_arsip_spby'] = 'required';
        } else {
            $rules += [
                'nama_kegiatan' => 'required',
                'penerima_penyedia' => 'required',
                'jumlah' => 'required|numeric',
                'dokumen.*' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            ];
        }
        $request->validate($rules);

        // 2. Olah Data
        if (Auth::user()->role == 'Bendahara') {
            // PERBAIKAN: Tambahkan status_digitalisasi agar diizinkan masuk ke $data
            $data = $request->only(['gup', 'no_urut_arsip_spby', 'status_berkas', 'status_digitalisasi']);

            // Pastikan nilai boolean (1 atau 0) tersimpan
            $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;

            $aktivitasLog = 'Update Kelengkapan & Digitalisasi Bendahara';
        } else {
            $data = $request->except('dokumen');
            $aktivitasLog = 'Update Data Realisasi';

            // Ambil lampiran lama
            $currentFiles = is_array($realisasi->lampiran) ? $realisasi->lampiran : json_decode($realisasi->lampiran, true) ?? [];

            // Update Berkas Baru secara spesifik
            if ($request->has('dokumen')) {
                foreach ($request->file('dokumen') as $nama_dokumen => $file) {
                    if ($file) {
                        $path = $file->store('uploads/realisasi/' . date('Y/m'), 'public');

                        // Cari index jika sudah ada nama berkas yang sama
                        $foundKey = -1;
                        foreach ($currentFiles as $key => $existing) {
                            if ($existing['nama_berkas'] === $nama_dokumen) {
                                $foundKey = $key;
                                break;
                            }
                        }

                        $newEntry = [
                            'nama_berkas' => $nama_dokumen,
                            'path'        => $path,
                            'uploaded_at' => now()->toDateTimeString()
                        ];

                        if ($foundKey !== -1) {
                            $currentFiles[$foundKey] = $newEntry; // Timpa
                        } else {
                            $currentFiles[] = $newEntry; // Tambah Baru
                        }
                    }
                }
            }

            $data['lampiran'] = $currentFiles;
            $data['total'] = $request->jumlah;
            $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;

            // Bersihkan catatan revisi jika status dikirim kembali ke verifikator
            if ($request->status_berkas == 'Proses Verifikasi' && \Str::contains($realisasi->uraian, '[CATATAN')) {
                $data['uraian'] = \Str::before($request->uraian ?? $realisasi->uraian, "\n\n[CATATAN");
            }
        }

        $data['updated_by'] = Auth::id();
        $realisasi->update($data);

        // Logging
        $this->logActivity($aktivitasLog, "User memperbarui data ID #{$id}", $statusAwal, $realisasi->status_berkas, $realisasi->id);

        return redirect()->route('realisasi-v2.index', ['coa_item_id' => $realisasi->coa_item_id])
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $realisasiV2 = Realisasi::findOrFail($id);
        $coaId = $realisasiV2->coa_item_id;
        $statusTerakhir = $realisasiV2->status_berkas; // Simpan status untuk log
        $namaKegiatan = $realisasiV2->nama_kegiatan;

        // --- LOGGING AKTIVITAS: Penghapusan Data ---
        // Mencatat penghapusan data agar jejak audit tetap ada meskipun data fisik sudah tidak ada
        $this->logActivity(
            'Hapus Realisasi',
            "User menghapus data Realisasi ID #{$id} ({$namaKegiatan}) dengan nominal Rp " . number_format($realisasiV2->jumlah, 0, ',', '.'),
            $statusTerakhir,
            'DELETED' // Status akhir ditandai sebagai terhapus
        );

        // Hapus file fisik jika ada lampiran
        if (!empty($realisasiV2->lampiran)) {
            foreach ($realisasiV2->lampiran as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $realisasiV2->delete();

        return redirect()->route('realisasi-v2.index', ['coa_item_id' => $coaId])
            ->with('success', 'Data Realisasi berhasil dihapus.');
    }

    // Tambahkan di RealisasiControllerV2.php

    // --- WORKFLOW VERIFIKASI & LOGGING ---

    public function returnToPlo(Request $request, $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string|min:5'
        ]);

        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        $realisasi->update([
            'status_berkas' => 'Ditolak/Revisi',
            'status_flow'   => 'PLO',
            'updated_by'    => Auth::id(),
            'uraian'        => $realisasi->uraian . "\n\nCatatan Verifikator: " . $request->catatan_revisi
        ]);
        $ploUser = User::find($realisasi->created_by); // Pastikan fieldnya sesuai (created_by atau user_id)
        if ($ploUser) {
            $ploUser->notify(new RealisasiNotification($realisasi, 'revisi', [
                'catatan' => $request->catatan_revisi
            ]));
        }

        // LOGGING: Pengembalian Berkas
        $this->logActivity('Kembalikan ke PLO', "User mengembalikan berkas ID #{$id} untuk revisi. Catatan: {$request->catatan_revisi}", $statusAwal, 'Ditolak/Revisi');

        return redirect()->route('realisasi-v2.index')
            ->with('success', 'Berkas berhasil dikembalikan ke PLO untuk direvisi.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string']);

        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        $realisasi->update([
            'status_berkas' => 'Ditolak/Revisi',
            'uraian' => $realisasi->uraian . "\n\n[CATATAN VERIFIKATOR]: " . $request->catatan,
            'updated_by' => Auth::id()
        ]);

        // LOGGING: Penolakan
        $this->logActivity('Tolak Berkas', "Verifikator menolak berkas ID #{$id}. Alasan: {$request->catatan}", $statusAwal, 'Ditolak/Revisi');

        return redirect()->route('realisasi-v2.index')->with('success', 'Berkas dikembalikan ke PLO.');
    }

    public function approve($id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        $realisasi->update([
            'status_berkas' => 'Terverifikasi',
            'updated_by' => Auth::id()
        ]);

        // LOGGING: Persetujuan Verifikator
        $this->logActivity('Setujui Berkas', "Verifikator menyetujui berkas ID #{$id} dan meneruskan ke Bendahara", $statusAwal, 'Terverifikasi', $id);

        return redirect()->route('realisasi-v2.index')->with('success', 'Berkas berhasil diverifikasi.');
    }

    public function submitToPpk(Request $request, $id)
    {
        $request->validate([
            'gup' => 'required|string',
            'no_urut_arsip_spby' => 'required|string',
        ]);

        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        $realisasi->update([
            'gup' => $request->gup,
            'no_urut_arsip_spby' => $request->no_urut_arsip_spby,
            'status_berkas' => 'Proses PPK',
            'updated_by' => Auth::id()
        ]);

        // LOGGING: Penyerahan ke PPK
        $this->logActivity('Kirim ke PPK', "Bendahara melengkapi data GUP ({$request->gup}) dan meneruskan berkas ID #{$id} ke PPK", $statusAwal, 'Proses PPK');

        return redirect()->route('realisasi-v2.index')->with('success', 'Data GUP/SPBY disimpan, berkas diteruskan ke PPK.');
    }

    public function verifyPpk($id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        if (Auth::user()->role !== 'PPK') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk verifikasi PPK.');
        }

        $realisasi->update([
            'status_berkas' => 'Proses PPSPM',
            'updated_by' => Auth::id()
        ]);

        // LOGGING: Verifikasi PPK
        $this->logActivity('Verifikasi PPK', "Pejabat PPK menandatangani/menyetujui berkas ID #{$id}", $statusAwal, 'Proses PPSPM');

        return redirect()->route('realisasi-v2.index', ['status_berkas' => 'Proses PPK'])
            ->with('success', 'Verifikasi PPK Berhasil, berkas diteruskan ke PPSPM.');
    }

    public function verifyPpspm($id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        if (Auth::user()->role !== 'PPSPM') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk verifikasi PPSPM.');
        }

        $realisasi->update([
            'status_berkas' => 'Menunggu Finalisasi Bendahara',
            'updated_by' => Auth::id()
        ]);

        // LOGGING: Verifikasi PPSPM
        $this->logActivity('Verifikasi PPSPM', "Pejabat PPSPM menyetujui berkas ID #{$id}. Berkas kembali ke Bendahara untuk tahap akhir", $statusAwal, 'Menunggu Finalisasi Bendahara');

        return redirect()->route('realisasi-v2.index', ['status_berkas' => 'Proses PPSPM'])
            ->with('success', 'Verifikasi PPSPM Berhasil, berkas siap difinalisasi oleh Bendahara.');
    }

    public function finalize($id)
    {
        $realisasi = Realisasi::with('coaItem')->findOrFail($id);
        $statusAwal = $realisasi->status_berkas;

        $coaItem = $realisasi->coaItem;
        if ($coaItem) {
            $coaItem->realisasi_total += $realisasi->jumlah;
            $coaItem->sisa_realisasi = $coaItem->pagu_item - $coaItem->realisasi_total;
            $coaItem->save();
        }

        $realisasi->update([
            'status_berkas' => 'Selesai',
            'finalized_at' => now(),
            'updated_by' => Auth::id()
        ]);

        // LOGGING: Finalisasi Akhir
        $this->logActivity('Finalisasi Realisasi', "Bendahara menutup transaksi ID #{$id}. Pagu COA terpotong senilai Rp " . number_format($realisasi->jumlah, 0, ',', '.'), $statusAwal, 'Selesai');

        return redirect()->route('realisasi-v2.index')->with('success', 'Pagu Berhasil Terpotong.');
    }
    public function exportExcel(Request $request)
    {
        // Menggunakan RealisasiViewExport yang baru
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RealisasiViewExport($request),
            'SPTB_GUP_' . ($request->filter_gup ?? 'Semua') . '_' . date('Ymd') . '.xlsx'
        );
    }
}
