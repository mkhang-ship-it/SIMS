<?php
// Updated: Added dropdown validation for easier data entry in Excel template
// Added: Birthday (Ngày sinh) and Identity Card Code (CCCD/CMND) fields to member import template
// Updated: Made birthday field mandatory with clear validation messages
// Fixed: CCCD column formatted as text to prevent scientific notation display (1.23457E+11)

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\Major;
use App\Models\Course;
use App\Models\Role;

class MemberTemplateExport implements WithMultipleSheets
{
    private $clubId;

    public function __construct($clubId)
    {
        $this->clubId = $clubId;
    }

    public function sheets(): array
    {
        return [
            new MemberTemplateSheet(),
            new MajorsReferenceSheet(),
            new CoursesReferenceSheet(),
            new RolesReferenceSheet($this->clubId),
        ];
    }
}

// Main template sheet
class MemberTemplateSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents
{
    public function array(): array
    {
        return [
            [
                'Nguyễn Văn A',
                'nguyenvana@example.com',
                'Nam',
                '0901234567',
                '1990-05-15',
                "023456789012",
                'SV001',
                'Công nghệ thông tin',
                'K64',
                'Thành viên',
                'Hoạt động',
                '123456@'
            ],
            [
                'Trần Thị B',
                'tranthib@example.com',
                'Nữ',
                '0987654321',
                '1995-08-20',
                "087654321098",
                'SV002',
                'Quản trị kinh doanh',
                'K63',
                'Phó ban',
                'Hoạt động',
                'password123'
            ],
            [
                'Lê Văn C',
                'levanc@example.com',
                'Nam',
                '0123456789',
                '1992-12-10',
                '',
                'SV003',
                'Kế toán',
                'K65',
                'Thành viên',
                'Hoạt động',
                'abc123'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Tên thành viên',
            'Email',
            'Giới tính',
            'Số điện thoại',
            'Ngày sinh',
            'CCCD/CMND',
            'Mã sinh viên',
            'Chuyên ngành',
            'Khóa học',
            'Vai trò',
            'Trạng thái',
            'Mật khẩu'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'name' => 'Times New Roman'
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffFF9800']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Mẫu Import Thành Viên';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

                // Auto-fit columns
                foreach (range('A', 'L') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set minimum widths
                $sheet->getColumnDimension('A')->setWidth(20); // Tên thành viên
                $sheet->getColumnDimension('B')->setWidth(25); // Email
                $sheet->getColumnDimension('E')->setWidth(12); // Ngày sinh
                $sheet->getColumnDimension('F')->setWidth(15); // CCCD/CMND
                $sheet->getColumnDimension('H')->setWidth(20); // Chuyên ngành
                $sheet->getColumnDimension('J')->setWidth(15); // Vai trò

                // Add borders and styling
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'font' => ['name' => 'Times New Roman'],
                ]);

                // Center align specific columns
                $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Giới tính
                $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Ngày sinh
                $sheet->getStyle('F:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // CCCD/CMND
                $sheet->getStyle('K:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Trạng thái

                // Format CCCD column as text to prevent scientific notation
                $sheet->getStyle('F:F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Add instructions above
                $sheet->insertNewRowBefore(1, 3);

                // Title
                $sheet->mergeCells('A1:L1');
                $sheet->setCellValue('A1', 'FILE MẪU IMPORT THÀNH VIÊN CÂU LẠC BỘ');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'ff2196F3']
                    ],
                ]);

                // Instructions
                $sheet->mergeCells('A2:L2');
                $instructionText = 'HƯỚNG DẪN: Chỉ có CCCD/CMND có thể để trống, TẤT CẢ CÁC CỘT KHÁC ĐỀU BẮT BUỘC! Ngày sinh BẮT BUỘC nhập theo định dạng YYYY-MM-DD (VD: 1990-05-15). CCCD/CMND không bắt buộc, nhập 12 số nếu có (cột đã được format dạng text để tránh hiển thị khoa học). Các cột Giới tính, Chuyên ngành, Khóa học, Vai trò, Trạng thái có dropdown - chỉ cần click vào ô và chọn từ danh sách. Xem các sheet tham chiếu để biết thêm chi tiết. Mật khẩu mặc định: 123456';
                $sheet->setCellValue('A2', $instructionText);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 11, 'name' => 'Times New Roman', 'color' => ['argb' => 'ffD32F2F']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'ffFFF3E0']
                    ],
                ]);

                // Empty row
                $sheet->mergeCells('A3:L3');
                $sheet->setCellValue('A3', '');

                // Set row heights
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(60);
                $sheet->getRowDimension('3')->setRowHeight(10);

                // Add data validation dropdowns for easier data entry
                $this->addDataValidations($sheet);
            },
        ];
    }

    /**
     * Add data validation dropdowns to make data entry easier
     */
    private function addDataValidations(Worksheet $sheet)
    {
        // Gender dropdown (Column C) - Giới tính
        $genderValidation = $sheet->getCell('C4')->getDataValidation();
        $genderValidation->setType(DataValidation::TYPE_LIST);
        $genderValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $genderValidation->setAllowBlank(false);
        $genderValidation->setShowInputMessage(true);
        $genderValidation->setShowErrorMessage(true);
        $genderValidation->setShowDropDown(true);
        $genderValidation->setErrorTitle('Lỗi giới tính');
        $genderValidation->setError('Chỉ được chọn: Nam hoặc Nữ');
        $genderValidation->setPromptTitle('Chọn giới tính');
        $genderValidation->setPrompt('Chọn giới tính từ danh sách dropdown');
        $genderValidation->setFormula1('"Nam,Nữ"');

        // Copy gender validation to more rows (assuming max 1000 rows)
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('C' . $row)->setDataValidation(clone $genderValidation);
        }

        // Date of birth validation (Column E) - Ngày sinh
        $birthdayValidation = $sheet->getCell('E4')->getDataValidation();
        $birthdayValidation->setType(DataValidation::TYPE_DATE);
        $birthdayValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $birthdayValidation->setAllowBlank(false);
        $birthdayValidation->setShowInputMessage(true);
        $birthdayValidation->setShowErrorMessage(true);
        $birthdayValidation->setErrorTitle('Lỗi ngày sinh');
        $birthdayValidation->setError('Ngày sinh là bắt buộc! Vui lòng nhập đúng định dạng YYYY-MM-DD (VD: 1990-05-15)');
        $birthdayValidation->setPromptTitle('Nhập ngày sinh (BẮT BUỘC)');
        $birthdayValidation->setPrompt('Ngày sinh bắt buộc nhập. Định dạng: YYYY-MM-DD (VD: 1990-05-15)');
        $birthdayValidation->setFormula1('DATE(1950,1,1)');
        $birthdayValidation->setFormula2('DATE(2010,12,31)');
        $birthdayValidation->setOperator(DataValidation::OPERATOR_BETWEEN);

        // Copy birthday validation to more rows
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('E' . $row)->setDataValidation(clone $birthdayValidation);
        }

        // CCCD/CMND validation (Column F) - CCCD/CMND
        $cccdValidation = $sheet->getCell('F4')->getDataValidation();
        $cccdValidation->setType(DataValidation::TYPE_TEXTLENGTH);
        $cccdValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $cccdValidation->setAllowBlank(true); // CCCD có thể để trống
        $cccdValidation->setShowInputMessage(true);
        $cccdValidation->setShowErrorMessage(true);
        $cccdValidation->setErrorTitle('Lỗi CCCD/CMND');
        $cccdValidation->setError('CCCD/CMND phải có đúng 12 số hoặc để trống. Cột này được format dạng text để tránh hiển thị khoa học');
        $cccdValidation->setPromptTitle('Nhập CCCD/CMND');
        $cccdValidation->setPrompt('Nhập 12 số CCCD/CMND hoặc để trống (không bắt buộc). Cột được format dạng text tránh lỗi hiển thị');
        $cccdValidation->setFormula1('12');
        $cccdValidation->setFormula2('12');
        $cccdValidation->setOperator(DataValidation::OPERATOR_BETWEEN);

        // Copy CCCD validation to more rows
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('F' . $row)->setDataValidation(clone $cccdValidation);
        }

        // Major dropdown (Column H) - Chuyên ngành
        $majorValidation = $sheet->getCell('H4')->getDataValidation();
        $majorValidation->setType(DataValidation::TYPE_LIST);
        $majorValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $majorValidation->setAllowBlank(false);
        $majorValidation->setShowInputMessage(true);
        $majorValidation->setShowErrorMessage(true);
        $majorValidation->setShowDropDown(true);
        $majorValidation->setErrorTitle('Lỗi chuyên ngành');
        $majorValidation->setError('Vui lòng chọn chuyên ngành từ sheet "Chuyên Ngành"');
        $majorValidation->setPromptTitle('Chọn chuyên ngành');
        $majorValidation->setPrompt('Chọn chuyên ngành từ danh sách. Xem sheet "Chuyên Ngành" để biết các tùy chọn có sẵn.');
        $majorValidation->setFormula1("'Chuyên Ngành'!\$A:\$A");

        // Copy major validation to more rows
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('H' . $row)->setDataValidation(clone $majorValidation);
        }

        // Course dropdown (Column I) - Khóa học
        $courseValidation = $sheet->getCell('I4')->getDataValidation();
        $courseValidation->setType(DataValidation::TYPE_LIST);
        $courseValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $courseValidation->setAllowBlank(false);
        $courseValidation->setShowInputMessage(true);
        $courseValidation->setShowErrorMessage(true);
        $courseValidation->setShowDropDown(true);
        $courseValidation->setErrorTitle('Lỗi khóa học');
        $courseValidation->setError('Vui lòng chọn khóa học từ sheet "Khóa Học"');
        $courseValidation->setPromptTitle('Chọn khóa học');
        $courseValidation->setPrompt('Chọn khóa học từ danh sách. Xem sheet "Khóa Học" để biết các tùy chọn có sẵn.');
        $courseValidation->setFormula1("'Khóa Học'!\$A:\$A");

        // Copy course validation to more rows
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('I' . $row)->setDataValidation(clone $courseValidation);
        }

        // Role dropdown (Column J) - Vai trò
        $roleValidation = $sheet->getCell('J4')->getDataValidation();
        $roleValidation->setType(DataValidation::TYPE_LIST);
        $roleValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $roleValidation->setAllowBlank(false);
        $roleValidation->setShowInputMessage(true);
        $roleValidation->setShowErrorMessage(true);
        $roleValidation->setShowDropDown(true);
        $roleValidation->setErrorTitle('Lỗi vai trò');
        $roleValidation->setError('Vui lòng chọn vai trò từ sheet "Vai Trò"');
        $roleValidation->setPromptTitle('Chọn vai trò');
        $roleValidation->setPrompt('Chọn vai trò từ danh sách. Xem sheet "Vai Trò" để biết các tùy chọn có sẵn.');
        $roleValidation->setFormula1("'Vai Trò'!\$A:\$A");

        // Copy role validation to more rows
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('J' . $row)->setDataValidation(clone $roleValidation);
        }

        // Status dropdown (Column K) - Trạng thái
        $statusValidation = $sheet->getCell('K4')->getDataValidation();
        $statusValidation->setType(DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowInputMessage(true);
        $statusValidation->setShowErrorMessage(true);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setErrorTitle('Lỗi trạng thái');
        $statusValidation->setError('Chỉ được chọn: Hoạt động hoặc Chờ phê duyệt hoặc Không hoạt động');
        $statusValidation->setPromptTitle('Chọn trạng thái');
        $statusValidation->setPrompt('Chọn trạng thái hoạt động của thành viên');
        $statusValidation->setFormula1('"Hoạt động,Chờ phê duyệt,Không hoạt động"');

        // Copy status validation to more rows
        for ($row = 4; $row <= 1000; $row++) {
            $sheet->getCell('K' . $row)->setDataValidation(clone $statusValidation);
        }
    }
}

// Majors reference sheet
class MajorsReferenceSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents
{
    public function array(): array
    {
        $majors = Major::where('status', 'active')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get(['name']);

        $data = [];
        foreach ($majors as $major) {
            $data[] = [
                $major->name
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Tên Chuyên Ngành'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'name' => 'Times New Roman'],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffFF9800']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Chuyên Ngành';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('A')->setWidth(40);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:A' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => ['name' => 'Times New Roman'],
                ]);
            },
        ];
    }
}

// Courses reference sheet
class CoursesReferenceSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents
{
    public function array(): array
    {
        $courses = Course::where('status', 'active')
            ->orderBy('name')
            ->get(['name']);

        $data = [];
        foreach ($courses as $course) {
            $data[] = [
                $course->name
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Tên Khóa Học'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'name' => 'Times New Roman'],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffFF9800']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Khóa Học';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('A')->setWidth(30);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:A' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => ['name' => 'Times New Roman'],
                ]);
            },
        ];
    }
}

// Roles reference sheet
class RolesReferenceSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents
{
    private $clubId;

    public function __construct($clubId)
    {
        $this->clubId = $clubId;
    }

    public function array(): array
    {
        $roles = Role::where('club_id', $this->clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['name', 'is_leader']);

        $data = [];
        foreach ($roles as $role) {
            $leaderType = $role->is_leader === 'leader' ? 'Ban chủ nhiệm' : 'Thành viên';
            $data[] = [
                $role->name,
                $leaderType
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Tên Vai Trò', 'Loại Vai Trò'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'name' => 'Times New Roman'],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffFF9800']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Vai Trò';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

                foreach (range('A', 'B') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(40);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:B' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => ['name' => 'Times New Roman'],
                ]);
            },
        ];
    }
}
