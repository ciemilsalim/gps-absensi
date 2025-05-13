<?php
namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;

class AttendanceExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithDrawings

{
    protected $user_id;
    protected $start_date;
    protected $end_date;

    public function __construct($user_id = null, $start_date = null, $end_date = null)
    {
        $this->user_id = $user_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function view(): View
    {
        $query = \App\Models\Attendance::with('user')->latest();

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        if ($this->start_date) {
            $query->whereDate('date', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('date', '<=', $this->end_date);
        }

        return view('admin.attendance.excel', [
            'attendances' => $query->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Styling untuk header
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Absensi';
    }

    public function shouldAutoSize(): bool
    {
        return true;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Instansi');
        $drawing->setPath(public_path('logo.png')); // Lokasi logo
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1'); // Tampilkan di sel A1
        $drawing->setOffsetY(10);

        return [$drawing];
    }

}
