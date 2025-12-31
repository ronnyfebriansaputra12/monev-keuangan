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

class RealisasiControllerV2 extends Controller
{
    private function logActivity($activity, $description, $statusAwal = null, $statusAkhir = null, $idRealisasi = null)
    {
        ActivityLog::create([
            'user_id'       => Auth::id(),
            'id_realisasi'  => $idRealisasi, // ID Realisasi disimpan di sini
            'activity'      => $activity,
            'description'   => $description,
            'role'          => Auth::user()->role,
            'status_awal'   => $statusAwal,
            'status_akhir'  => $statusAkhir,
            'ip_address'    => request()->ip(),
        ]);
    }

    public function index(Request $request)
    {
        $coaItemId = $request->input('coa_item_id');
        $search = $request->input('search');

        // Menggunakan jalur relasi yang valid sesuai skema database
        $q = Realisasi::with([
            'satker',
            'coaItem.subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker'
        ]);

        if ($coaItemId) {
            $q->where('coa_item_id', $coaItemId);
        }

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('penerima_penyedia', 'like', "%{$search}%")
                    ->orWhere('kode_unik_plo', 'like', "%{$search}%");
            });
        }

        $items = $q->latest()->paginate(20);
        $selectedCoa = $coaItemId ? CoaItem::find($coaItemId) : null;

        return view('realisasiv2.index', compact('items', 'selectedCoa', 'coaItemId', 'search'));
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
        // 1. Validasi Input sesuai dengan field di Model Realisasi
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
            'files.*'                   => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        // 2. Logika Nomor Urut Otomatis 0001
        $lastRecord = Realisasi::where('kode_unik_plo', $request->kode_unik_plo)
            ->where('sumber_anggaran', $request->sumber_anggaran)
            ->orderBy('no_urut', 'desc')
            ->first();
        $nextNoUrut = $lastRecord ? ($lastRecord->no_urut + 1) : 1;

        // 3. Logika Upload Banyak Berkas Dinamis
        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads/realisasi', 'public');
                $filePaths[] = $path;
            }
        }

        // 4. Penggabungan Data Akhir
        $data = $request->all();
        $data['no_urut'] = $nextNoUrut;
        $data['lampiran'] = $filePaths;
        $data['total'] = $request->jumlah;
        $data['created_by'] = Auth::id();
        $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;

        // 5. Eksekusi Simpan ke Database
        $realisasi = Realisasi::create($data);

        // --- LOGGING AKTIVITAS: Tambah Data Baru ---
        // Mencatat pembuatan data realisasi baru
        $this->logActivity(
            'Tambah Realisasi',
            "User (PLO) membuat realisasi baru: {$realisasi->nama_kegiatan} dengan nominal Rp " . number_format($realisasi->jumlah, 0, ',', '.'),
            'NULL', // Status awal memang belum ada
            $realisasi->status_berkas ?? 'Draft',
            $realisasi->id
        );

        return redirect()->route('realisasi-v2.index', ['coa_item_id' => $realisasi->coa_item_id])
            ->with('success', "Data berhasil disimpan dengan No Urut: " . str_pad($nextNoUrut, 4, '0', STR_PAD_LEFT));
    }

    public function show($id)
    {
        // Memuat data realisasi beserta silsilah anggaran dan riwayat log-nya
        $realisasi = Realisasi::with([
            'mak',
            'coaItem.subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker',
            'logs.user' // Menambahkan relasi logs dan user yang melakukan aksi
        ])->findOrFail($id);

        return view('realisasiv2.show', compact('realisasi'));
    }

    public function getNextNoUrut(Request $request)
    {
        $plo = $request->query('plo');
        $sumber = $request->query('sumber');

        $lastRecord = Realisasi::where('kode_unik_plo', $plo)
            ->where('sumber_anggaran', $sumber)
            ->orderBy('no_urut', 'desc')
            ->first();

        $nextNo = $lastRecord ? ($lastRecord->no_urut + 1) : 1;
        $formattedNo = str_pad($nextNo, 4, '0', STR_PAD_LEFT);

        return response()->json(['next_no_urut' => $formattedNo]);
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
        $statusAwal = $realisasi->status_berkas; // Simpan status sebelum diupdate untuk log

        // 1. Validasi Dinamis Berdasarkan Role
        $rules = ['status_berkas' => 'required'];
        if (Auth::user()->role == 'Bendahara') {
            $rules['gup'] = 'required';
            $rules['no_urut_arsip_spby'] = 'required';
        } else {
            $rules += [
                'nama_kegiatan' => 'required',
                'penerima_penyedia' => 'required',
                'jumlah' => 'required|numeric',
            ];
        }
        $request->validate($rules);

        // 2. Olah Data Berdasarkan Role
        if (Auth::user()->role == 'Bendahara') {
            // Bendahara hanya mengupdate field kelengkapan bayar
            $data = $request->only(['gup', 'no_urut_arsip_spby', 'status_berkas']);
            $aktivitasLog = 'Update Kelengkapan Bendahara'; // Label log khusus
        } else {
            // PLO mengupdate data transaksi
            $data = $request->all();
            $aktivitasLog = 'Update Data Realisasi';

            // Gabung Lampiran Berkas Baru dengan yang sudah ada
            $currentFiles = $realisasi->lampiran ?? [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $currentFiles[] = $file->store('uploads/realisasi', 'public');
                }
            }
            $data['lampiran'] = $currentFiles;
            $data['total'] = $request->jumlah;

            // UPDATE STATUS DIGITALISASI
            $data['status_digitalisasi'] = $request->has('status_digitalisasi') ? 1 : 0;

            // PROTEKSI STATUS FINAL: Jika PLO update saat di tahap Bendahara, status tidak boleh berubah
            if ($realisasi->status_berkas == 'Menunggu Finalisasi Bendahara') {
                $data['status_berkas'] = 'Menunggu Finalisasi Bendahara';
                $aktivitasLog = 'Update Lampiran/Digitalisasi';
            }

            // Bersihkan catatan revisi lama jika mengajukan ulang ke verifikator
            if ($request->status_berkas == 'Proses Verifikasi' && Str::contains($realisasi->uraian, '[CATATAN')) {
                $data['uraian'] = Str::before($realisasi->uraian, "\n\n[CATATAN");
            }
        }

        $data['updated_by'] = Auth::id();
        $realisasi->update($data);

        // --- LOGGING AKTIVITAS: Perubahan Data & Status ---
        // Mencatat perubahan yang dilakukan serta transisi status berkasnya
        $this->logActivity(
            $aktivitasLog,
            "User memperbarui data pada ID #{$id}." . (isset($data['gup']) ? " (GUP: {$data['gup']})" : ""),
            $statusAwal,
            $realisasi->status_berkas,
            $realisasi->id
        );

        return redirect()->route('realisasi-v2.index', ['status_berkas' => $realisasi->status_berkas])
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
}
