<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Izin;
use Yajra\DataTables\DataTables;

class IzinController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin user (bukan admin)
     */
    public function index()
    {
        $izinList = Izin::where('user_id', Auth::id())->latest()->get();
        return view('izin.index', compact('izinList'));
    }

    /**
     * Menampilkan form pengajuan izin
     */
    public function create()
    {
        return view('izin.create');
    }

    /**
     * Menyimpan pengajuan izin ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'document' => 'nullable|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        // Menentukan path penyimpanan
        $path = null;
        if ($request->hasFile('document')) {
            // Membuat nama file custom: nama_nomor(random5digit).tipefile
            $user = Auth::user();
            $randomNumber = rand(10000, 99999); // Angka acak 5 digit
            $fileName = $user->name . '_' . $user->id . '_' . $randomNumber . '.' . $request->file('document')->getClientOriginalExtension();

            // Menyimpan file ke folder public/uploads/izin/
            $path = $request->file('document')->storeAs('uploads/izin', $fileName, 'public');
        }

        // Menyimpan data pengajuan izin ke database
        Izin::create([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin/cuti berhasil diajukan.');
    }

    /**
     * Menampilkan detail pengajuan izin
     */
    public function show($id)
    {
        // Ambil data izin berdasarkan ID
        $izin = Izin::with('user')->findOrFail($id);

        // Tampilkan view dengan data izin
        return view('admin.izin.show', compact('izin'));
    }

    /**
     * Menampilkan semua pengajuan izin (untuk admin)
     */
    public function adminIndex(Request $request)
    {
        $query = Izin::with('user')->latest();

        if ($request->has('search_name') && $request->search_name !== '') {
            $searchName = $request->search_name;
            $query->whereHas('user', function ($q) use ($searchName) {
                $q->where('name', 'like', '%' . $searchName . '%');
            });
        }

        $izins = $query->get();

        return view('admin.izin.index', compact('izins'));
    }


    /**
     * Menyetujui pengajuan izin
     */
    public function approve($id)
    {
        $izin = Izin::findOrFail($id);
        $izin->status = 'approve';
        $izin->save();

        return redirect()->route('admin.izin.index')->with('status', 'Pengajuan izin telah disetujui.');
    }

    /**
     * Menolak pengajuan izin
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255'
        ]);

        $izin = Izin::findOrFail($id);
        $izin->status = 'reject';
        $izin->reject_reason = $request->reject_reason;
        $izin->save();

        return redirect()->route('admin.izin.index')->with('status', 'Pengajuan izin telah ditolak.');
    }

    /**
     * Mengunduh dokumen pengajuan izin
     */
    public function download($id)
    {
        $izin = Izin::findOrFail($id);
        $filePath = storage_path('app/public/' . $izin->document_path);

        // Periksa apakah file ada
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function getData()
{
    $izins = Izin::with('user');

    return DataTables::of($izins)
        ->addIndexColumn()
        ->addColumn('nama', fn($row) => optional($row->user)->name ?? '-')
        ->addColumn('jenis', fn($row) => ucfirst($row->jenis))
        ->addColumn('tanggal', fn($row) => $row->start_date . ' s/d ' . $row->end_date)
        ->addColumn('status', fn($row) => ucfirst($row->status))
        ->addColumn('aksi', function ($row) {
            $btn = '';

            if ($row->status == 'pending') {
                $btn .= '<form action="' . route('admin.izin.approve', $row->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form> ';
                $btn .= '<button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal' . $row->id . '">Reject</button> ';
            }

            $btn .= '<a href="' . route('admin.izin.download', $row->id) . '" class="btn btn-primary btn-sm">Download</a>';

            return $btn;
        })
        ->rawColumns(['aksi'])
        ->make(true);
}

}
