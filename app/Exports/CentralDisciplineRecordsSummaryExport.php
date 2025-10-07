<?php

/**
 * CentralDisciplineRecordsSummaryExport - Excel export for central discipline records summary
 * - Exports discipline records summary for all clubs or specific club from central admin perspective
 * - Includes club information in the export
 * - Format: STT, Họ tên, MSSV, Email, Câu lạc bộ, Chức vụ, Điểm CLB, Điểm trường
 * - Sử dụng font Times New Roman cho toàn bộ file Excel export
 */

namespace App\Exports;

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
use App\Models\DisciplineRecord;
use App\Models\UserClubRole;
use App\Models\SchoolPoint;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CentralDisciplineRecordsSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $clubId;
    protected $semesterId;
    protected $semesterName;
    protected $description;

    public function __construct($clubId, $semesterId, $semesterName, $description = null)
    {
        $this->clubId = $clubId;
        $this->semesterId = $semesterId;
        $this->semesterName = $semesterName;
        $this->description = $description ?? 'Tất cả câu lạc bộ';
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $membersSummary = collect();

        if ($this->clubId) {
            // Export for specific club
            $clubMembers = UserClubRole::with(['user', 'role', 'club'])
                ->byClub($this->clubId)
                ->where('status', 'active')
                ->get();

            // Auto-calculate school points for the club
            SchoolPoint::calculateAndUpdateSchoolPoints($this->clubId, $this->semesterId);

            foreach ($clubMembers as $member) {
                $totalClubPoints = DisciplineRecord::calculateTotalPoints(
                    $member->user_id,
                    $this->semesterId,
                    $this->clubId
                );

                $schoolPoint = SchoolPoint::getSchoolPoint(
                    $member->user_id,
                    $this->clubId,
                    $this->semesterId
                );

                // Get all discipline records for description
                $disciplineRecords = DisciplineRecord::with(['discipline'])
                    ->byUser($member->user_id)
                    ->bySemester($this->semesterId)
                    ->byClub($this->clubId)
                    ->active()
                    ->orderBy('applied_date', 'desc')
                    ->get();

                // Create combined description
                $descriptions = [];
                if ($disciplineRecords->count() > 0) {
                    foreach ($disciplineRecords as $record) {
                        $appliedDate = Carbon::parse($record->applied_date);
                        $descriptions[] = $record->discipline->name . ' (' .
                            ($record->discipline->discipline_points > 0 ? '+' : '') .
                            $record->discipline->discipline_points . ' điểm) - ' .
                            'Ngày: ' . $appliedDate->format('d/m/Y') .
                            ($record->notes ? ' - Ghi chú: ' . $record->notes : '');
                    }
                    $combinedDescription = implode("\n", $descriptions);
                } else {
                    $combinedDescription = '-';
                }

                $membersSummary->push([
                    'user' => $member->user,
                    'club' => $member->club,
                    'role' => $member->role,
                    'total_points' => $totalClubPoints,
                    'school_points' => $schoolPoint ? $schoolPoint->school_points : 0,
                    'is_leader' => $schoolPoint ? $schoolPoint->is_leader : false,
                    'description' => $combinedDescription,
                ]);
            }
        } else {
            // Export for all clubs
            $allClubMembers = UserClubRole::with(['user', 'role', 'club'])
                ->where('status', 'active')
                ->get();

            // Group by club for batch processing
            $membersByClub = $allClubMembers->groupBy('club_id');

            foreach ($membersByClub as $clubId => $clubMembers) {
                // Auto-calculate school points for each club
                SchoolPoint::calculateAndUpdateSchoolPoints($clubId, $this->semesterId);

                foreach ($clubMembers as $member) {
                    $totalClubPoints = DisciplineRecord::calculateTotalPoints(
                        $member->user_id,
                        $this->semesterId,
                        $clubId
                    );

                    $schoolPoint = SchoolPoint::getSchoolPoint(
                        $member->user_id,
                        $clubId,
                        $this->semesterId
                    );

                    // Get all discipline records for description
                    $disciplineRecords = DisciplineRecord::with(['discipline'])
                        ->byUser($member->user_id)
                        ->bySemester($this->semesterId)
                        ->byClub($clubId)
                        ->active()
                        ->orderBy('applied_date', 'desc')
                        ->get();

                    // Create combined description
                    $descriptions = [];
                    if ($disciplineRecords->count() > 0) {
                        foreach ($disciplineRecords as $record) {
                            $appliedDate = Carbon::parse($record->applied_date);
                            $descriptions[] = $record->discipline->name . ' (' .
                                ($record->discipline->discipline_points > 0 ? '+' : '') .
                                $record->discipline->discipline_points . ' điểm) - ' .
                                'Ngày: ' . $appliedDate->format('d/m/Y') .
                                ($record->notes ? ' - Ghi chú: ' . $record->notes : '');
                        }
                        $combinedDescription = implode("\n", $descriptions);
                    } else {
                        $combinedDescription = '-';
                    }

                    $membersSummary->push([
                        'user' => $member->user,
                        'club' => $member->club,
                        'role' => $member->role,
                        'total_points' => $totalClubPoints,
                        'school_points' => $schoolPoint ? $schoolPoint->school_points : 0,
                        'is_leader' => $schoolPoint ? $schoolPoint->is_leader : false,
                        'description' => $combinedDescription,
                    ]);
                }
            }
        }

        // Sort by total points descending
        return $membersSummary->sortByDesc('total_points')->values();
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row['user']->name ?? '-',
            $row['user']->student_code ?? '-',
            $row['user']->email ?? '-',
            $row['club']->name ?? '-',
            $row['role']->name ?? '-',
            $row['description'] ?? '-',
            $row['total_points'] > 0 ? '+' . $row['total_points'] : (string) $row['total_points'],
            number_format($row['school_points'], 1),
        ];
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và Tên',
            'MSSV',
            'Email',
            'Câu Lạc Bộ',
            'Chức Vụ',
            'Mô Tả',
            'Điểm Club',
            'Điểm Trường',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Sử dụng font Times New Roman cho toàn bộ sheet
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Áp dụng font cho toàn bộ sheet (tất cả các ô)
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getFont()->setName('Times New Roman');

        return [
            // Header row styling (data starts at row 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                    'name' => 'Times New Roman',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'fff1511b'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Add title above the data
                $sheet->insertNewRowBefore(1, 2);

                // Set title in row 1
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'TỔNG HỢP ĐIỂM THÀNH VIÊN - ' . strtoupper($this->description) . ' - ' . strtoupper($this->semesterName));

                // Style the title
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1f4e79'],
                        'name' => 'Times New Roman',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Đặt font Times New Roman cho toàn bộ sheet (bao gồm cả dữ liệu, header, title)
                $newHighestRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:' . $highestColumn . $newHighestRow)->getFont()->setName('Times New Roman');

                // Auto-size columns
                foreach (range('A', $highestColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Auto-fit row heights to content for better display of wrapped text
                for ($row = 1; $row <= $newHighestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1); // -1 means auto-fit
                }

                // Set minimum column widths
                $sheet->getColumnDimension('A')->setWidth(5);   // STT
                $sheet->getColumnDimension('B')->setWidth(20);  // Họ tên
                $sheet->getColumnDimension('C')->setWidth(12);  // MSSV
                $sheet->getColumnDimension('D')->setWidth(25);  // Email
                $sheet->getColumnDimension('E')->setWidth(20);  // Câu lạc bộ
                $sheet->getColumnDimension('F')->setWidth(15);  // Chức vụ
                $sheet->getColumnDimension('G')->setWidth(40);  // Mô tả
                $sheet->getColumnDimension('H')->setWidth(12);  // Điểm CLB
                $sheet->getColumnDimension('I')->setWidth(12);  // Điểm Trường
    
                // Apply borders to all data cells (now starting from row 3)
                $newHighestRow = $sheet->getHighestRow();
                if ($newHighestRow > 3) {
                    $sheet->getStyle('A3:' . $highestColumn . $newHighestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'font' => [
                            'name' => 'Times New Roman',
                        ],
                    ]);

                    // Center align specific columns (data starts from row 4)
                    $sheet->getStyle('A4:A' . $newHighestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // STT
                    $sheet->getStyle('H4:I' . $newHighestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Points columns
    
                    // Set wrap text for description column
                    $sheet->getStyle('G4:G' . $newHighestRow)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('G4:G' . $newHighestRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                }

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30); // Title row
                $sheet->getRowDimension(2)->setRowHeight(10); // Spacing row
                $sheet->getRowDimension(3)->setRowHeight(25); // Header row
            },
        ];
    }

    public function title(): string
    {
        return 'Tổng Hợp Điểm Thành Viên';
    }
}