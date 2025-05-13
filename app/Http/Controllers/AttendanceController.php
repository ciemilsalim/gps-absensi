<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Auth::user()->attendances()->orderByDesc('date')->get();
        return view('attendance.index', compact('attendances'));
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
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', Carbon::today());
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



    // Koordinat kantor (ganti sesuai lokasi kantor Anda)
    protected $officeLat = 1.1719015;  // Contoh: Jakarta
    protected $officeLng = 121.4259835;
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

        // Cek jarak lokasi
        $distance = $this->distanceInMeters($request->latitude, $request->longitude, $this->officeLat, $this->officeLng);
        if ($distance > $this->radius) {
            return back()->withErrors(['Lokasi Anda di luar radius kantor.']);
        }

        // Cek apakah sudah absen hari ini
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($attendance && $attendance->check_in_time) {
            return back()->withErrors(['Anda sudah check-in hari ini.']);
        }

        // Simpan data check-in
        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->date = $today;
        }

        $attendance->check_in_time = Carbon::now();
        $attendance->check_in_lat = $request->latitude;
        $attendance->check_in_long = $request->longitude;
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

        // Cek jarak lokasi
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

        $attendance->check_out_time = Carbon::now();
        $attendance->check_out_lat = $request->latitude;
        $attendance->check_out_long = $request->longitude;
        $attendance->save();

        return back()->with('success', 'Check-out berhasil.');
    }
}
