<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class AttendanceController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $today = Carbon::today()->toDateString();

    // Ambil absensi hari ini
    $attendance = Attendance::where('user_id', $user->id)
        ->where('date', $today)
        ->first();

    $hasCheckedIn = $attendance && $attendance->check_in_time !== null;
    $hasCheckedOut = $attendance && $attendance->check_out_time !== null;

    // Ambil semua riwayat jika ingin ditampilkan nanti
    $attendances = $user->attendances()->orderByDesc('date')->get();

    return view('attendance.index', compact('attendances', 'hasCheckedIn', 'hasCheckedOut'));
}


    public function adminIndex(Request $request)
    {
        // Cek apakah user adalah admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('attendance.index')->withErrors(['Anda tidak memiliki akses ke halaman ini.']);
        }

        // $query = Attendance::with('user')->latest();
        $query = Attendance::with('user')->latest();

        //filter berdasarkan tanggal hari ini
        // if ($request->filled('date')) {
        //     $query->whereDate('date', $request->date);
        // } else {
        //     $query->whereDate('date', Carbon::today());
        // }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        // Filter user jika dipilih
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // $attendances = $query->get();
        $attendances = $query->orderByDesc('date')->get();
        $users = User::orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'users'));
    }

    //export pdf
    public function exportPdf(Request $request)
    {
        $query = Attendance::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $attendances = $query->get();

        $pdf = pdf::loadView('admin.attendance.pdf', compact('attendances'));
        return $pdf->download('laporan-absensi.pdf');
    }

    //eksport excel
    public function exportExcel(Request $request)
    {
        $user_id = $request->user_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $export = new AttendanceExport($user_id, $start_date, $end_date);
        return Excel::download($export, 'laporan-absensi.xlsx');
    }



    // Koordinat kantor (ganti sesuai lokasi kantor Anda)
    protected $officeLat = 1.1558476230747408;  // Contoh: Jakarta
    protected $officeLng = 121.43746097373258;
    protected $radius = 100; // meter

    // Rumus Haversine untuk hitung jarak GPS dalam meter
    protected function distanceInMeters($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meter
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLat = $lat2 - $lat1;
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLng/2) * sin($deltaLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $distance = $this->distanceInMeters($request->latitude, $request->longitude, $this->officeLat, $this->officeLng);
        if ($distance > $this->radius) {
            return back()->withErrors(['Lokasi Anda di luar radius kantor.']);
        }

        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => $today,
        ]);

        if ($attendance->check_in_time) {
            return back()->withErrors(['Anda sudah check-in hari ini.']);
        }

        $now = Carbon::now();
        $attendance->check_in_time = $now;
        $attendance->check_in_lat = $request->latitude;
        $attendance->check_in_long = $request->longitude;

        // Logika status masuk
        $cutoffMasuk = Carbon::createFromTime(7, 0, 0); // 07:00 pagi
        $attendance->status_masuk = $now->greaterThan($cutoffMasuk) ? 'Terlambat' : 'Tepat Waktu';

        $attendance->save();

        return back()->with('success', 'Check-in berhasil.');
    }


    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $distance = $this->distanceInMeters($request->latitude, $request->longitude, $this->officeLat, $this->officeLng);
        if ($distance > $this->radius) {
            return back()->withErrors(['Lokasi Anda di luar radius kantor.']);
        }

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if (!$attendance || !$attendance->check_in_time) {
            return back()->withErrors(['Anda belum check-in hari ini.']);
        }
        if ($attendance->check_out_time) {
            return back()->withErrors(['Anda sudah check-out hari ini.']);
        }

        $now = Carbon::now();
        $attendance->check_out_time = $now;
        $attendance->check_out_lat = $request->latitude;
        $attendance->check_out_long = $request->longitude;

        // Logika status pulang
        $cutoffPulang = Carbon::createFromTime(15, 0, 0); // 15:00 siang
        $attendance->status_pulang = $now->lessThan($cutoffPulang) ? 'Pulang Awal' : 'Tepat Waktu';

        $attendance->save();

        return back()->with('success', 'Check-out berhasil.');
    }

    public function getData(Request $request)
{
    $query = Attendance::with('user');

    if ($request->user_id) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->start_date) {
        $query->whereDate('date', '>=', $request->start_date);
    }

    if ($request->end_date) {
        $query->whereDate('date', '<=', $request->end_date);
    }

    return DataTables::of($query)
        ->addColumn('user_name', fn($row) => $row->user->name)
        ->editColumn('check_in', fn($row) => $row->check_in_time 
            ? "{$row->check_in_time}<br><small class='text-muted'>({$row->check_in_lat}, {$row->check_in_long})</small>" 
            : '-')
        ->editColumn('check_out', fn($row) => $row->check_out_time 
            ? "{$row->check_out_time}<br><small class='text-muted'>({$row->check_out_lat}, {$row->check_out_long})</small>" 
            : '-')
        ->rawColumns(['check_in', 'check_out']) // agar HTML tidak di-escape
        ->make(true);
}

}
