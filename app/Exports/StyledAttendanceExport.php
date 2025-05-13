<?php
namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StyledAttendanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithDrawings
{
    protected $user_id, $start_date, $end_date;

    public function __construct($user_id = null, $start_date = null, $end_date = null)
    {
        $this->user_id = $user_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection(): Collection
    {
        $query = Attendance::with('user')->orderBy('date');

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }
        if ($this->start_date) {
            $query->where('date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->where('date', '<=', $this->end_date);
        }

        return $query->get()->map(function ($a) {
            return [
                $a->user->name,
                $a->date,
                optional($a->check_in_time)->format('H:i:s'),
                optional($a->check_out_time)->format('H:i:s'),
                $a->check_in_lat . ', ' . $a->check_in_long,
                $a->check_out_lat . ', ' . $a->check_out_long,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Tanggal',
            'Check-In',
            'Check-Out',
            'Koordinat Masuk',
            'Koordinat Keluar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'PEMERINTAH KANBUPATEN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Subjudul
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Laporan Absensi Pegawai');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Tanggal cetak
        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'Dicetak pada: ' . now()->format('d-m-Y'));
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

        // Header tabel (baris ke-5)
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:F5')->getBorders()->getAllBorders()->setBorderStyle('thin');

        // Data rows
        $rowCount = Attendance::count() + 5;
        $sheet->getStyle("A6:F$rowCount")->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle("A6:F$rowCount")->getAlignment()->setWrapText(true);

        return [];
    }

    public function title(): string
    {
        return 'Absensi';
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Pemerintah');
        $drawing->setPath(public_path('logo.png')); // Path logo
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetY(5);
        $drawing->setOffsetX(10);

        return [$drawing];
    }
}
