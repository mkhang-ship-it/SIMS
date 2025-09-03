<?php

/**
 * Updated: DisciplineRecordsSummaryExport - Excel export for discipline records summary
 * - Exports member points summary with individual discipline records
 * - Each discipline record appears as a separate row
 * - Includes member info, club points, school points, and discipline descriptions
 * - Removed OpenAI evaluation column and all AI-related logic
 * - Auto-fit column widths and row heights to content for better readability
 * - Minimum 40-character width for description column
 * - All fonts set to Times New Roman
 */

namespace App\Exports;

use App\Models\UserClubRole;
use App\Models\DisciplineRecord;
use App\Models\SchoolPoint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DisciplineRecordsSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $clubId;
    protected $semesterId;
    protected $semesterName;
    protected $clubName;

    public function __construct($clubId, $semesterId, $semesterName, $clubName)
    {
        $this->clubId = $clubId;
        $this->semesterId = $semesterId;
        $this->semesterName = $semesterName;
        $this->clubName = $clubName;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = collect();

        // Get all club members
        $clubMembers = UserClubRole::with(['user', 'role'])
            ->byClub($this->clubId)
            ->where('status', 'active')
            ->get();

        $rowNumber = 1;

        foreach ($clubMembers as $member) {
            // Get all discipline records for this member
            $disciplineRecords = DisciplineRecord::with(['discipline'])
                ->byUser($member->user_id)
                ->bySemester($this->semesterId)
                ->byClub($this->clubId)
                ->active()
                ->orderBy('applied_date', 'desc')
                ->get();

            // Calculate total points
            $totalClubPoints = DisciplineRecord::calculateTotalPoints(
                $member->user_id,
                $this->semesterId,
                $this->clubId
            );

            // Get school points
            $schoolPoint = SchoolPoint::getSchoolPoint(
                $member->user_id,
                $this->clubId,
                $this->semesterId
            );
            $schoolPoints = $schoolPoint ? $schoolPoint->school_points : 0;

            if ($disciplineRecords->count() > 0) {
                // If member has discipline records, add each record as a separate row
                foreach ($disciplineRecords as $record) {
                    $data->push([
                        'stt' => $rowNumber,
                        'name' => $member->user->name,
                        'student_id' => $member->user->student_code ?? '',
                        'role' => $member->role->name ?? '-',
                        'email' => $member->user->email,
                        'description' => $record->discipline->name . ' (' .
                            ($record->discipline->discipline_points > 0 ? '+' : '') .
                            $record->discipline->discipline_points . ' điểm) - ' .
                            'Ngày: ' . $record->applied_date->format('d/m/Y') .
                            ($record->notes ? ' - Ghi chú: ' . $record->notes : ''),
                        'club_points' => $totalClubPoints,
                        'school_points' => $schoolPoints,
                        'record_date' => $record->applied_date->format('d/m/Y'),
                        'record_points' => $record->discipline->discipline_points,
                        'member' => $member
                    ]);
                    $rowNumber++;
                }
            } else {
                // If member has no discipline records, add one row with empty description
                $data->push([
                    'stt' => $rowNumber,
                    'name' => $member->user->name,
                    'student_id' => $member->user->student_code ?? '',
                    'role' => $member->role->name ?? '-',
                    'email' => $member->user->email,
                    'description' => '-',
                    'club_points' => $totalClubPoints,
                    'school_points' => $schoolPoints,
                    'record_date' => '',
                    'record_points' => 0,
                    'member' => $member
                ]);
                $rowNumber++;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'STT',
            'HỌ VÀ TÊN',
            'MSSV',
            'CHỨC VỤ',
            'EMAIL',
            'MÔ TẢ',
            'ĐIỂM CLUB',
            'ĐIỂM TRƯỜNG'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['stt'],
            $row['name'],
            $row['student_id'],
            $row['role'],
            $row['email'],
            $row['description'],
            $row['club_points'] > 0 ? '+' . $row['club_points'] : $row['club_points'],
            number_format($row['school_points'], 1)
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Set default font for the entire sheet to Times New Roman
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'name' => 'Times New Roman'
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'fff1511b'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Tổng Hợp Điểm';
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set default font for the entire sheet to Times New Roman
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

                // Auto-fit all columns to content
                foreach (range('A', 'H') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set minimum width for description column (column F)
                $sheet->getColumnDimension('F')->setWidth(40);

                // Auto-fit row heights to content
                for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1); // -1 means auto-fit
                }

                // Add borders to all cells with data
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'font' => [
                        'name' => 'Times New Roman'
                    ],
                ]);

                // Center align specific columns
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add title above the table
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'TỔNG HỢP ĐIỂM THÀNH VIÊN CLB - ' . strtoupper($this->semesterName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'name' => 'Times New Roman'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Add club name
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Câu lạc bộ: ' . $this->clubName);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'name' => 'Times New Roman'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Set specific row heights for title rows
                $sheet->getRowDimension('1')->setRowHeight(35);
                $sheet->getRowDimension('2')->setRowHeight(30);
                // $sheet->getRowDimension('3')->setRowHeight(30);
            },
        ];
    }
}