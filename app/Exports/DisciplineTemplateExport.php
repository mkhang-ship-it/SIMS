<?php
// Đã đổi "Tên" thành "Tên kỷ luật" trong heading và hướng dẫn

namespace App\Exports;

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

class DisciplineTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents
{
    /**
     * Return sample data array
     */
    public function array(): array
    {
        return [
            [
                'Vi phạm quy định câu lạc bộ',
                -5,
                'Không tuân thủ nội quy câu lạc bộ, vi phạm các quy định đã được đặt ra'
            ],
            [
                'Tham gia không đầy đủ',
                -3,
                'Vắng mặt không phép tại các hoạt động bắt buộc của câu lạc bộ'
            ],
            [
                'Hoàn thành xuất sắc nhiệm vụ',
                10,
                'Thể hiện tinh thần trách nhiệm cao, hoàn thành tốt các nhiệm vụ được giao'
            ],
            [
                'Đóng góp tích cực',
                5,
                'Có nhiều đóng góp tích cực cho hoạt động câu lạc bộ'
            ],
            [
                'Vi phạm nghiêm trọng',
                -10,
                'Có hành vi vi phạm nghiêm trọng ảnh hưởng đến uy tín câu lạc bộ'
            ]
        ];
    }

    /**
     * Return headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Tên kỷ luật',
            'Điểm',
            'Mô tả'
        ];
    }

    /**
     * Apply styles to the worksheet
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
                        'argb' => 'ff4CAF50'
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
     * Return sheet title
     */
    public function title(): string
    {
        return 'Mẫu Import Kỷ Luật';
    }

    /**
     * Register events for after sheet creation
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set default font for the entire sheet to Times New Roman
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');

                // Auto-fit all columns to content
                foreach (range('A', 'C') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set minimum width for description column (column C)
                $sheet->getColumnDimension('C')->setWidth(50);

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

                // Center align specific columns (STT and Points)
                $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add instructions above the table
                $sheet->insertNewRowBefore(1, 3);

                // Title
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', 'FILE MẪU IMPORT KỶ LUẬT');
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
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'ff2196F3'
                        ]
                    ],
                ]);

                // Instructions
                $sheet->mergeCells('A2:C2');
                $sheet->setCellValue('A2', 'Hướng dẫn: Tên kỷ luật (bắt buộc) | Điểm (số nguyên, bắt buộc) | Mô tả (không bắt buộc).');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 11,
                        'name' => 'Times New Roman'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'ffFFF3E0'
                        ]
                    ],
                ]);

                // Empty row
                $sheet->mergeCells('A3:C3');
                $sheet->setCellValue('A3', '');

                // Set specific row heights for instruction rows
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(35);
                $sheet->getRowDimension('3')->setRowHeight(10);
            },
        ];
    }
}
