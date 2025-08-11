<?php

/**
 * MemberExport - Excel export for club member management
 * - Exports member data for a specific club from club admin perspective
 * - Includes role, points, and personal details for club members
 * - Format: STT, Họ tên, MSSV, Email, Chức vụ, Điểm CLB, Điểm trường, Giới tính, Ngày sinh, CCCD/CMND, SĐT, Chuyên ngành, Khóa học, Trạng thái
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
use App\Models\UserClubRole;
use App\Models\SchoolPoint;
use App\Models\DisciplineRecord;
use App\Models\Semester;
use Illuminate\Support\Collection;

class MemberExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $clubId;
    protected $semesterId;
    protected $clubName;

    public function __construct($clubId, $semesterId = null, $clubName = '')
    {
        $this->clubId = $clubId;
        $this->semesterId = $semesterId ?? Semester::getCurrentSemester()?->id;
        $this->clubName = $clubName;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $members = UserClubRole::with(['user.major', 'user.course', 'role'])
            ->byClub($this->clubId)
            ->where('status', 'active')
            ->get();

        // Calculate points for each member if semester is available
        if ($this->semesterId) {
            // Auto-calculate school points for the club
            SchoolPoint::calculateAndUpdateSchoolPoints($this->clubId, $this->semesterId);
        }

        return $members->sortBy('user.name');
    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        static $stt = 0;
        $stt++;

        $genderLabel = $row->user->gender ?
            ($row->user->gender === 'male' ? 'Nam' : 'Nữ') : '-';

        $birthdayFormatted = '-';
        if ($row->user->birthday) {
            try {
                if (is_string($row->user->birthday)) {
                    $birthdayFormatted = \Carbon\Carbon::parse($row->user->birthday)->format('d/m/Y');
                } else {
                    $birthdayFormatted = $row->user->birthday->format('d/m/Y');
                }
            } catch (\Exception $e) {
                $birthdayFormatted = '-';
            }
        }

        $statusLabel = $row->user->status === 'active' ? 'Hoạt động' : 'Vô hiệu hóa';

        return [
            $stt,
            $row->user->name,
            $row->user->student_code ?? '-',
            $row->user->email,
            $row->role->name ?? '-',
            $genderLabel,
            $birthdayFormatted,
            $row->user->identity_card_code ?? '-',
            $row->user->phone ?? '-',
            $row->user->major->name ?? '-',
            $row->user->course->name ?? '-',
            $statusLabel
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'STT',
            'Họ tên',
            'MSSV',
            'Email',
            'Chức vụ',
            'Giới tính',
            'Ngày sinh',
            'CCCD/CMND',
            'Số điện thoại',
            'Chuyên ngành',
            'Khóa học',
            'Trạng thái'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Set font for entire sheet
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'name' => 'Times New Roman',
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'fff1511b']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'ff000000']
                    ]
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Danh sách thành viên ' . ($this->clubName ? '- ' . $this->clubName : '');
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set font for entire sheet
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(11);

                // Auto-fit column widths
                foreach (range('A', 'L') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set minimum widths for some columns
                $sheet->getColumnDimension('A')->setWidth(8);  // STT
                $sheet->getColumnDimension('B')->setWidth(25); // Họ tên
                $sheet->getColumnDimension('D')->setWidth(30); // Email
                $sheet->getColumnDimension('J')->setWidth(25); // Chuyên ngành

                // Apply borders to all data
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'ff000000']
                        ]
                    ],
                    'font' => [
                        'name' => 'Times New Roman',
                        'size' => 11
                    ]
                ]);

                // Center align certain columns
                $sheet->getStyle('A1:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // STT
                $sheet->getStyle('C1:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // MSSV
                $sheet->getStyle('F1:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Giới tính
                $sheet->getStyle('G1:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Ngày sinh
                $sheet->getStyle('L1:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Trạng thái

                // Auto-fit row heights
                foreach (range(1, $highestRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            }
        ];
    }
}
