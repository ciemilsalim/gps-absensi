<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Izin;

class HomeController extends Controller
{
    /**
     * Middleware auth agar user harus login.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan dashboard home dengan data absensi dan salam.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Ambil data absensi user hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Tentukan status masuk dan pulang
        $absenMasuk = null;
        $absenPulang = null;
        $statusMasuk = null;
        $statusPulang = null;

        if ($attendance) {
            if ($attendance->check_in_time) {
                $absenMasuk = Carbon::parse($attendance->check_in_time)->format('H:i');
                $statusMasuk = $attendance->status_masuk ?? 'tepat_waktu';
            }

            if ($attendance->check_out_time) {
                $absenPulang = Carbon::parse($attendance->check_out_time)->format('H:i');
                $statusPulang = $attendance->status_pulang ?? 'tepat_waktu';
            }
        }

        // Tentukan ucapan berdasarkan waktu
        $hour = Carbon::now()->format('H');
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 12 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        // Ambil data presensi selama 1 minggu terakhir (Senin s.d Minggu ini)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $presensiMingguan = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date', 'desc')
            ->get();

        // Data untuk Pie Chart
        $hadir = Attendance::where('user_id', $user->id)
            ->where('status_masuk', 'tepat_waktu')
            ->count();

        $terlambat = Attendance::where('user_id', $user->id)
            ->where('status_masuk', 'terlambat')
            ->count();

        $izin = Izin::where('user_id', $user->id)
            ->where('jenis', 'izin')
            ->count();

        $cuti = Izin::where('user_id', $user->id)
            ->where('jenis', 'cuti')
            ->count();

        // Hitung total hari kerja dari awal bulan sampai hari ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();
        $workingDays = collect();
        for ($date = $startOfMonth->copy(); $date <= $today; $date->addDay()) {
            if (!$date->isWeekend()) {
                $workingDays->push($date->format('Y-m-d'));
            }
        }

        // Tanggal absensi user (hadir/terlambat)
        $absensiTanggal = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $today])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'));

        // Tanggal izin dan cuti
        $izinCutiTanggal = Izin::where('user_id', $user->id)
            ->whereBetween('start_date', [$startOfMonth, $today])
            ->pluck('start_date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'));

        // Hitung Alpa = Hari kerja - (hadir/terlambat + izin + cuti)
        $alpa = $workingDays
            ->diff($absensiTanggal)
            ->diff($izinCutiTanggal)
            ->count();

        return view('home', compact(
            'user',
            'greeting',
            'absenMasuk',
            'absenPulang',
            'attendance',
            'statusMasuk',
            'statusPulang',
            'presensiMingguan',
            'hadir',
            'terlambat',
            'izin',
            'cuti',
            'alpa'
        ));
    }
}
