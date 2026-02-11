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
        $jenisRealisasi = $request->input('jenis_realisasi'); // Tambahan Baru

        // Filter Tanggal
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        // --- INISIALISASI QUERY UTAMA ---
        $q = Realisasi::with([
            'satker',
            'coaItem.mak.akun',
            'coaItem.subKomponen.komponen.rincianOutput'
        ]);

        // MODIFIKASI: Filter berdasarkan Role
        if (auth()->user()->role === 'PLO') {
            // PLO melihat semua data miliknya (LS & GUP)
            $q->where('created_by', auth()->id());
        } elseif (auth()->user()->role === 'PPBJ') {
            // PPBJ hanya melihat data miliknya DAN harus jenis LS
            $q->where('created_by', auth()->id())
                ->where('jenis_realisasi', 'LS');
        }

        if ($coaItemId) {
            $q->where('coa_item_id', $coaItemId);
        }

        // --- LOGIC COUNT STATUS ---
        $countQuery = clone $q;
        $counts = $countQuery->select('status_berkas', \DB::raw('count(*) as total'))
            ->groupBy('status_berkas')
            ->pluck('total', 'status_berkas')
            ->toArray();

        // -- APPLY FILTERS --
        if ($statusBerkas) {
            $q->where('status_berkas', $statusBerkas);
        }

        // Filter Jenis Realisasi (UI dropdown)
        if ($jenisRealisasi) {
            // Jika Superadmin/PPSPM/Bendahara, mereka bebas filter apa saja
            if (in_array(auth()->user()->role, ['PPSPM', 'Bendahara', 'Superadmin'])) {
                $q->where('jenis_realisasi', $jenisRealisasi);
            }
            // Jika PLO, mereka boleh filter LS/GUP sesuai input dropdown
            elseif (auth()->user()->role === 'PLO') {
                $q->where('jenis_realisasi', $jenisRealisasi);
            }
            // Jika PPBJ, abaikan input dropdown dan tetap kunci ke LS (sudah ditangani di query utama di atas)
        }

        if ($filterGup) {
            $q->where('gup', $filterGup);
        }

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
        $listGupQuery = Realisasi::whereNotNull('gup')->where('gup', '!=', '');
        if (auth()->user()->role === 'PLO') {
            $listGupQuery->where('created_by', auth()->id());
        }
        $listGup = $listGupQuery->distinct()->pluck('gup');

        $listRo = \DB::table('rincian_outputs')->whereNotNull('kode_ro')->distinct()->pluck('kode_ro');
        $listAkun = \DB::table('master_akuns')->select('kode_akun', 'nama_akun')->get();

        // List Jenis Realisasi untuk Dropdown
        $listJenisRealisasi = ['LS', 'GUP'];

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
            'totalRealisasiFiltered',
            'listJenisRealisasi' // Tambahan Baru
        ));
    }

    public function create(Request $request)
    {
        $coaItemId = $request->query('coa_item_id');
        $selectedCoa = null;

        if ($coaItemId) {
            // Memuat silsilah anggaran + relasi MAK dan AKUN
            $selectedCoa = CoaItem::with([
                'mak.akun', // Relasi ke MAK lalu ke MasterAkun
                'subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker'
            ])->findOrFail($coaItemId);
        }

        $satkers = Satker::orderBy('nama_satker')->get();
        $coaItems = CoaItem::orderBy('kode_coa_item')->get();
        $maks = Mak::with('akun')->get(); // Muat akun untuk dropdown mak juga

        return view('realisasiv2.create', compact('satkers', 'coaItems', 'maks', 'selectedCoa'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Hardened File Validation)
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

            // Tambahan Kolom Baru untuk PPBJ & LS
            'no_spp'                    => 'nullable|string',
            'tgl_spp'                   => 'nullable|date',

            // Gunakan mimetypes untuk cek isi asli file (bukan cuma ekstensi)
            'dokumen.*'                 => 'nullable|file|mimes:pdf,jpg,png,jpeg|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
            'custom_dokumen.*'          => 'nullable|file|mimes:pdf,jpg,png,jpeg|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
            'custom_nama_berkas.*'      => 'nullable|string',
        ]);

        // 2. Logika Nomor Urut Otomatis
        $sumber = $request->sumber_anggaran;
        $bidang = $request->bidang;
        $middlePart = ($sumber === 'GUP') ? $bidang : (($sumber === 'BGN') ? 'MG' : $sumber);

        $lastRecord = Realisasi::where('sumber_anggaran', $sumber)
            ->where('kode_unik_plo', 'like', "%.$middlePart.%")
            ->orderBy('no_urut', 'desc')
            ->first();

        $nextNoUrut = $lastRecord ? ($lastRecord->no_urut + 1) : 1;

        // 3. Logika Upload Berkas (Secure Upload)
        $filePaths = [];
        $uploadDir = 'uploads/realisasi/' . date('Y/m');

        // A. Handle Berkas dari List (Statis)
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $nama_dokumen => $file) {
                if ($file && $file->isValid()) {
                    // store() secara otomatis memberikan nama unik (hashName)
                    $path = $file->store($uploadDir, 'public');
                    $filePaths[] = [
                        'nama_berkas' => $nama_dokumen,
                        'path'         => $path,
                        'uploaded_at' => now()->toDateTimeString()
                    ];
                }
            }
        }

        // B. Handle Berkas Tambahan (Kustom)
        if ($request->hasFile('custom_dokumen')) {
            $customFiles = $request->file('custom_dokumen');
            $customNames = $request->input('custom_nama_berkas');

            foreach ($customFiles as $key => $file) {
                if ($file && $file->isValid() && isset($customNames[$key])) {
                    $path = $file->store($uploadDir, 'public');
                    $filePaths[] = [
                        'nama_berkas' => strip_tags($customNames[$key]), // Sanitasi input teks
                        'path'         => $path,
                        'uploaded_at' => now()->toDateTimeString()
                    ];
                }
            }
        }

        // 4. Penggabungan Data Akhir
        $data = $request->except(['dokumen', 'custom_dokumen', 'custom_nama_berkas']);
        $data['no_urut'] = $nextNoUrut;

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
            "User (PLO) membuat realisasi baru: {$realisasi->nama_kegiatan}",
            'NULL',
            $realisasi->status_berkas ?? 'Draft',
            $realisasi->id
        );

        return redirect()->route('realisasi-v2.index')
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

        // Proteksi Role: PLO, Bendahara, dan PPBJ diizinkan
        $allowedRoles = ['PLO', 'Bendahara', 'PPBJ', 'Superadmin', 'PPSPM'];
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

        // 1. Validasi Hardened
        $rules = [
            'status_berkas'        => 'required',
            'custom_dokumen.*'     => 'nullable|file|mimes:pdf,jpg,png,jpeg|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
            'custom_nama_berkas.*' => 'nullable|string',
        ];

        // Validasi khusus berdasarkan Role
        if (Auth::user()->role == 'Bendahara') {
            $rules['gup'] = 'required';
        } elseif (Auth::user()->role == 'PPSPM') {
            // PPSPM bisa mengisi SPM dan Faktur Pajak (Opsional/Nullable tergantung kebijakan)
            $rules['no_spm'] = 'nullable|string';
            $rules['tgl_spm'] = 'nullable|date';
            $rules['no_faktur_pajak'] = 'nullable|string';
            $rules['tgl_faktur_pajak'] = 'nullable|date';
        } else {
            $rules += [
                'nama_kegiatan'     => 'required',
                'penerima_penyedia' => 'required',
                'jumlah'            => 'required|numeric',
                'dokumen.*'         => 'nullable|file|mimes:pdf,jpg,png,jpeg|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
            ];
        }

        $request->validate($rules);

        // 2. Olah Data
        $uploadDir = 'uploads/realisasi/' . date('Y/m');

        if (Auth::user()->role == 'Bendahara') {
            $data = $request->only(['gup', 'no_urut_arsip_spby', 'status_berkas', 'status_digitalisasi', 'status_sp2d']);
            $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;
            $data['status_sp2d']         = $request->has('status_sp2d') ? 1 : 0;
            $aktivitasLog = 'Update Kelengkapan & Digitalisasi Bendahara';
        } elseif (Auth::user()->role == 'PPSPM') {
            // PPSPM Update Data SPM & Faktur
            $data = $request->only(['no_spm', 'tgl_spm', 'no_faktur_pajak', 'tgl_faktur_pajak', 'status_berkas', 'status_sp2d', 'status_digitalisasi']);
            $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;
            $data['status_sp2d']         = $request->has('status_sp2d') ? 1 : 0;
            $aktivitasLog = 'Update Data SPM & Faktur Pajak oleh PPSPM';
        } else {
            $data = $request->except(['dokumen', 'custom_dokumen', 'custom_nama_berkas']);
            $aktivitasLog = 'Update Data Realisasi';

            $currentFiles = is_array($realisasi->lampiran)
                ? $realisasi->lampiran
                : json_decode($realisasi->lampiran, true) ?? [];

            // A. Update berkas dari list
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $nama_berkas => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store($uploadDir, 'public');

                        $foundKey = -1;
                        foreach ($currentFiles as $key => $existing) {
                            if ($existing['nama_berkas'] === $nama_berkas) {
                                $foundKey = $key;
                                break;
                            }
                        }

                        $entryData = [
                            'nama_berkas' => $nama_berkas,
                            'path'         => $path,
                            'uploaded_at' => now()->toDateTimeString()
                        ];

                        if ($foundKey !== -1) {
                            $currentFiles[$foundKey] = $entryData;
                        } else {
                            $currentFiles[] = $entryData;
                        }
                    }
                }
            }

            // B. Tambah berkas kustom baru
            if ($request->hasFile('custom_dokumen')) {
                $customFiles = $request->file('custom_dokumen');
                $customNames = $request->input('custom_nama_berkas');

                foreach ($customFiles as $key => $file) {
                    if ($file && $file->isValid() && isset($customNames[$key])) {
                        $path = $file->store($uploadDir, 'public');
                        $currentFiles[] = [
                            'nama_berkas' => strip_tags($customNames[$key]),
                            'path'         => $path,
                            'uploaded_at' => now()->toDateTimeString()
                        ];
                    }
                }
            }

            $data['lampiran'] = json_encode($currentFiles);
            $data['total'] = $request->jumlah;
            $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;
            $data['status_sp2d']         = $request->has('status_sp2d') ? 1 : 0;
        }

        $data['updated_by'] = Auth::id();
        $realisasi->update($data);

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

    public function verifyPPSPM(Request $request, $id)
    {
        $realisasi = Realisasi::findOrFail($id);

        // 1. Logika Jika Tombol Reject Ditekan
        if ($request->action == 'reject') {
            if ($realisasi->jenis_realisasi != 'LS') {
                return redirect()->route('realisasi-v2.index')->with('error', 'Hanya jenis LS yang dapat dikembalikan ke PPBJ.');
            }

            $request->validate([
                'keterangan' => 'required|string|min:5'
            ], [
                'keterangan.required' => 'Alasan revisi wajib diisi!',
                'keterangan.min' => 'Alasan revisi minimal 5 karakter.'
            ]);

            $realisasi->update([
                'status_berkas' => 'Ditolak/Revisi',
                'catatan_ppspm' => $request->keterangan,
            ]);

            return redirect()->route('realisasi-v2.index')->with('success', 'Berkas telah ditolak dan dikembalikan ke PPBJ.');
        }

        // 2. Logika Jika Approve (Setuju)
        if ($realisasi->jenis_realisasi == 'GUP') {
            // Jika GUP: Status ke Bendahara
            $realisasi->update([
                'status_berkas' => 'Menunggu Finalisasi Bendahara',
                'tgl_verifikasi_ppspm' => now(),
            ]);

            $message = 'Berkas GUP berhasil disetujui dan diteruskan ke Bendahara.';
        } elseif ($realisasi->jenis_realisasi == 'LS') {
            // Jika LS: Langsung Selesai dan Potong Pagu
            $realisasi->update([
                'status_berkas' => 'Selesai',
                'tgl_verifikasi_ppspm' => now(),
            ]);

            // Logika Potong Pagu (Asumsi relasi ke model Pagu ada)
            // Sesuaikan nama field 'nilai_realisasi' dengan database Anda
            $pagu = $realisasi->pagu;
            if ($pagu) {
                $pagu->decrement('sisa_pagu', $realisasi->nilai_realisasi);
            }

            $message = 'Berkas LS berhasil disetujui, status selesai, dan pagu telah terpotong.';
        }

        return redirect()->route('realisasi-v2.index')->with('success', $message);
    }

    public function finalize($id)
    {
        try {
            \DB::transaction(function () use ($id) {
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

                $this->logActivity('Finalisasi Realisasi', "Bendahara menutup transaksi ID #{$id}", $statusAwal, 'Selesai');
            });

            return redirect()->route('realisasi-v2.index')
                ->with('success', 'Pagu Berhasil Terpotong.');
        } catch (\Exception $e) {
            // Ini akan memicu alert "Gagal" di JavaScript Anda
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
