<?php

// app/Http/Controllers/RiwayatController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil tanggal mulai dan tanggal selesai dari filter, jika ada
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = Attendance::where('user_id', $user->id);

        // Jika tanggal mulai dan tanggal selesai ada, filter berdasarkan rentang tanggal
        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('date', [$tanggalMulai, $tanggalSelesai]);
        } else {
            // Jika tidak ada filter, tampilkan presensi minggu terakhir
            $query->whereDate('date', '>=', Carbon::now()->subWeek());
        }

        $riwayat = $query->orderBy('date', 'desc')->get();

        return view('riwayat.index', compact('riwayat', 'tanggalMulai', 'tanggalSelesai'));
    }
}
