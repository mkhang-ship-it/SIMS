<?php

namespace App\Exports;

use App\Models\EventPlan;
use App\Models\User;
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

class EventPlanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $eventPlan;
    protected $currentRow = 1;

    public function __construct(EventPlan $eventPlan)
    {
        $this->eventPlan = $eventPlan;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = collect();

        // Title - single row with proper UTF-8 encoding
        $eventName = $this->eventPlan->event->name ?? '';
        $eventName = mb_strtoupper($eventName, 'UTF-8'); // Use mb_strtoupper for proper UTF-8 handling
        $data->push(['', 'KẾ HOẠCH TỔ CHỨC "' . $eventName . '"', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // I. Thông Tin Chung
        $data->push(['', 'I. Thông Tin Chung', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push([
            '',
            'Câu lạc bộ:',
            '',
            $this->eventPlan->event->club->name ?? '',
            '',
            '',
            'Thời gian triển khai:',
            $this->eventPlan->implementation_time ? $this->eventPlan->implementation_time->format('d/m/Y') : '',
            '',
            ''
        ]);
        $data->push([
            '',
            'Người lập:',
            '',
            $this->eventPlan->preparedBy->name ?? '',
            '',
            '',
            'Phê duyệt:',
            $this->eventPlan->approved_by ?? '',
            '',
            ''
        ]);
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // II. Nội Dung Tổ Chức
        $data->push(['', 'II. Nội Dung Tổ Chức', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push([
            '',
            'Thời gian tổ chức sự kiện:',
            '',
            ($this->eventPlan->event->event_date ? $this->eventPlan->event->event_date->format('d/m/Y') : '') .
                ($this->eventPlan->event->slot ? ' - ' . $this->eventPlan->event->slot->start_time . ' đến ' . $this->eventPlan->event->slot->end_time : ''),
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        $data->push([
            '',
            'Địa điểm tổ chức sự kiện:',
            '',
            $this->eventPlan->event->classRoom->name ?? '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        $data->push([
            '',
            'Đối tượng tham gia:',
            '',
            $this->eventPlan->participants ?? '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        // Process description to handle line breaks
        $description = $this->eventPlan->description ?? '';
        // Convert HTML line breaks and escaped newlines to proper newlines
        $description = str_replace(['<br>', '<br/>', '<br />', '\\n', '\n'], "\n", $description);
        // Remove any HTML tags that might be present
        $description = strip_tags($description);

        $data->push([
            '',
            'Hình thức tổ chức:',
            '',
            $description,
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // III. Thành Phần Tổ Chức
        $data->push(['', 'III. Thành Phần Tổ Chức', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push([
            '',
            'Chịu trách nhiệm chính:',
            '',
            $this->eventPlan->responsible_organizer ?? '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        $data->push([
            '',
            'Hỗ trợ cố vấn và phối hợp tổ chức:',
            '',
            $this->eventPlan->advisor_coordinator ?? '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // IV. Mục Tiêu và Các Chỉ Số Đo Lường
        $data->push(['', 'IV. Mục Tiêu và Các Chỉ Số Đo Lường', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push(['', 'STT', 'Mục tiêu', 'Kết quả cần đạt', 'Chịu trách nhiệm']);

        $objectives = $this->eventPlan->objectives ?? collect();
        if ($objectives->count() > 0) {
            foreach ($objectives as $index => $objective) {
                $data->push([
                    '',
                    $index + 1,
                    $objective->objective ?? '',
                    $objective->expected_result ?? '',
                    $objective->responsible_party ?? ''
                ]);
            }
        } else {
            $data->push(['', '', 'Chưa có mục tiêu nào', '', '']);
        }
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // V. Kế Hoạch Hành Động
        $data->push(['', 'V. Kế Hoạch Hành Động', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push(['', 'STT', 'Hoạt động', 'Kết quả mong đợi', 'Thời gian bắt đầu', 'Thời gian kết thúc', 'Chịu trách nhiệm', 'Thành viên phối hợp', 'Rủi ro phát sinh', 'Phòng ngừa rủi ro']);

        $actionPlans = $this->eventPlan->actionPlans ?? collect();
        if ($actionPlans->count() > 0) {
            foreach ($actionPlans as $index => $actionPlan) {
                // Process support_members to show user names instead of UUIDs
                $supportMembersNames = '';
                if (!empty($actionPlan->support_members)) {
                    $supportMemberIds = is_string($actionPlan->support_members)
                        ? json_decode($actionPlan->support_members, true)
                        : $actionPlan->support_members;

                    if (is_array($supportMemberIds)) {
                        $userNames = [];
                        foreach ($supportMemberIds as $userId) {
                            $user = User::find($userId);
                            if ($user) {
                                $userNames[] = $user->name;
                            }
                        }
                        $supportMembersNames = implode("\n", $userNames);
                    }
                }

                $data->push([
                    '',
                    $index + 1,
                    $actionPlan->activity_item ?? '',
                    $actionPlan->expected_result ?? '',
                    $actionPlan->start_time ? $actionPlan->start_time->format('d/m/Y H:i') : '',
                    $actionPlan->end_time ? $actionPlan->end_time->format('d/m/Y H:i') : '',
                    $actionPlan->primaryResponsible->name ?? '',
                    $supportMembersNames,
                    $actionPlan->potential_risks ?? '',
                    $actionPlan->risk_prevention_actions ?? ''
                ]);
            }
        } else {
            $data->push(['', '', 'Chưa có kế hoạch hành động', '', '', '', '', '', '', '']);
        }
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // VI. Dự Toán Chi Phí
        $data->push(['', 'VI. Dự Toán Chi Phí', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push(['', 'STT', 'Tên hạng mục', 'Số lượng', 'Đơn vị', 'Đơn giá', 'Thành tiền', 'Phụ trách', 'Ghi chú']);

        $budgetItems = $this->eventPlan->budgetItems ?? collect();
        $totalBudget = 0;
        if ($budgetItems->count() > 0) {
            foreach ($budgetItems as $index => $budgetItem) {
                $total = ($budgetItem->quantity ?? 0) * ($budgetItem->unit_price ?? 0);
                $totalBudget += $total;

                $data->push([
                    '',
                    $index + 1,
                    $budgetItem->content ?? '',
                    $budgetItem->quantity ?? 0,
                    $budgetItem->unit ?? '',
                    number_format($budgetItem->unit_price ?? 0, 0, ',', '.') . ' đ',
                    number_format($total, 0, ',', '.') . ' đ',
                    $budgetItem->responsible_person ?? '',
                    $budgetItem->note ?? ''
                ]);
            }
            $data->push(['', '', '', '', '', 'Tổng tiền:', number_format($totalBudget, 0, ',', '.') . ' đ', '', '']);
        } else {
            $data->push(['', '', 'Chưa có dự toán chi phí', '', '', '', '', '', '']);
        }
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // VII. Quản Lý Vật Dụng
        $data->push(['', 'VII. Quản Lý Vật Dụng', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);
        $data->push(['', 'STT', 'Tên vật dụng', 'Số lượng', 'Nguồn cung cấp']);

        $equipment = $this->eventPlan->equipment ?? collect();
        if ($equipment->count() > 0) {
            foreach ($equipment as $index => $item) {
                $typeLabel = $item->type === 'club' ? 'Câu lạc bộ' : 'Trường học';

                $data->push([
                    '',
                    $index + 1,
                    $item->name ?? '',
                    $item->quantity ?? 0,
                    $typeLabel
                ]);
            }
        } else {
            $data->push(['', '', 'Chưa có vật dụng nào', '', '']);
        }
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        // VIII. Timeline Sự Kiện (grouped by date)
        $data->push(['', 'VIII. Timeline Sự Kiện', '', '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '', '']);

        $timeline = $this->eventPlan->timeline ?? collect();
        if ($timeline->count() > 0) {
            // Group timeline by date field
            $timelineByDate = $timeline->groupBy(function ($item) {
                return $item->date ? $item->date->format('Y-m-d') : 'unknown';
            });

            // Sort dates by distance from today (gần nhất đến xa nhất theo ngày hiện tại)
            $today = now()->format('Y-m-d');
            $sortedTimelineByDate = $timelineByDate->sortKeysUsing(function ($dateA, $dateB) use ($today) {
                // Handle 'unknown' dates - put them at the end
                if ($dateA === 'unknown' && $dateB !== 'unknown') return 1;
                if ($dateB === 'unknown' && $dateA !== 'unknown') return -1;
                if ($dateA === 'unknown' && $dateB === 'unknown') return 0;

                // Calculate distance from today for both dates
                $distanceA = abs(strtotime($dateA) - strtotime($today));
                $distanceB = abs(strtotime($dateB) - strtotime($today));

                // Sort by distance (closest first)
                return $distanceA <=> $distanceB;
            });

            $isFirstDate = true;
            foreach ($sortedTimelineByDate as $date => $timelineItems) {
                if (!$isFirstDate) {
                    // Add empty separator row between different dates
                    $data->push(['', '', '', '', '', '', '', '', '', '']);
                }

                // Date label merged across 3 columns above table header
                $dateLabel = $date !== 'unknown' ? 'Timeline Sự Kiện - ' . date('d/m/Y', strtotime($date)) : 'Timeline sự kiện chưa xác định ngày';
                $data->push(['', $dateLabel, '', '', '', '', '', '', '', '']);

                // Table header for this date
                $data->push(['', 'STT', 'Thời gian', 'Hoạt động']);

                // Sort timeline items by order_index for this date
                $sortedTimelineItems = is_array($timelineItems) ? collect($timelineItems) : $timelineItems;
                $sortedTimelineItems = $sortedTimelineItems->sortBy(function ($item) {
                    // Handle both object and array access
                    if (is_object($item)) {
                        return $item->order_index ?? 999;
                    } else {
                        return $item['order_index'] ?? 999;
                    }
                });

                // Timeline items for this date
                $sttCounter = 1;
                foreach ($sortedTimelineItems as $timelineItem) {
                    $timeDisplay = '';

                    // Handle both object and array access
                    if (is_object($timelineItem)) {
                        if ($timelineItem->start_time) {
                            $timeDisplay = $timelineItem->start_time->format('H:i');
                            if ($timelineItem->end_time) {
                                $timeDisplay .= ' - ' . $timelineItem->end_time->format('H:i');
                            }
                        }
                        $content = $timelineItem->content ?? '';
                    } else {
                        if (isset($timelineItem['start_time']) && $timelineItem['start_time']) {
                            $timeDisplay = $timelineItem['start_time']->format('H:i');
                            if (isset($timelineItem['end_time']) && $timelineItem['end_time']) {
                                $timeDisplay .= ' - ' . $timelineItem['end_time']->format('H:i');
                            }
                        }
                        $content = $timelineItem['content'] ?? '';
                    }

                    $data->push([
                        '',
                        $sttCounter,
                        $timeDisplay,
                        $content
                    ]);
                    $sttCounter++;
                }
                $isFirstDate = false;
            }
        } else {
            // No timeline data
            $data->push(['', 'STT', 'Thời gian', 'Hoạt động']);
            $data->push(['', '', 'Chưa có timeline', '']);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return []; // No fixed headings as we have dynamic sections
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return $row;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [


            // Apply Times New Roman font to all cells with size 12
            'A:Z' => [
                'font' => ['name' => 'Times New Roman', 'size' => 12],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],

            // Center align STT column (now in column B)
            'B:B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Kế Hoạch Sự Kiện';
    }

    /**
     * Get status label in Vietnamese
     */
    private function getStatusLabel($status): string
    {
        $statusLabels = [
            'pending' => 'Chờ Duyệt',
            'active' => 'Hoạt Động',
            'cancelled' => 'Đã Hủy',
            'completed' => 'Đã Hoàn Thành'
        ];

        return $statusLabels[$status] ?? $status;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Find the maximum column width used in any table (largest table has 9 columns)
                $maxTableCols = 0;
                for ($row = 1; $row <= $highestRow; $row++) {
                    if ($sheet->getCell('B' . $row)->getValue() === 'STT') {
                        $tableCols = 0;
                        for ($col = 'B'; $col <= 'Z'; $col++) {
                            if (!empty($sheet->getCell($col . $row)->getValue())) {
                                $tableCols++;
                            }
                        }
                        $maxTableCols = max($maxTableCols, $tableCols);
                    }
                }

                // Calculate the last column for title (B + maxTableCols - 1)
                $titleLastCol = chr(ord('B') + $maxTableCols - 1);

                // Merge title across single row with dynamic width
                $sheet->mergeCells('B1:' . $titleLastCol . '1');

                // Set auto height for title to prevent too much white space
                $sheet->getRowDimension(1)->setRowHeight(-1);

                // Center align the merged title and apply title styling with UTF-8 compatible font
                $sheet->getStyle('B1:' . $titleLastCol . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'name' => 'Times New Roman'],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => false
                    ]
                ]);

                // Set column A width to minimum (empty column)
                $sheet->getColumnDimension('A')->setWidth(2);

                // Find and style section headers, table headers, and apply borders
                $tableStartRows = [];

                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('B' . $row)->getValue(); // Check column B now (A is empty)

                    // Check if this row contains a section header (starts with Roman numeral)
                    if (preg_match('/^(I{1,3}V?|IV|V|VI{1,3}|IX|X)\.\s/', $cellValue)) {
                        // Merge this section header to match the title width
                        $sheet->mergeCells('B' . $row . ':' . $titleLastCol . $row);

                        // Apply styling to section headers
                        $sheet->getStyle('B' . $row . ':' . $titleLastCol . $row)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E8F5E8']
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => false
                            ]
                        ]);
                    }

                    // Check if this row contains table headers (STT column)
                    if ($cellValue === 'STT') {
                        $tableStartRows[] = $row;

                        // Find the actual last column with data in this header row
                        $headerLastCol = 'B';
                        for ($col = 'C'; $col <= 'Z'; $col++) {
                            if (!empty($sheet->getCell($col . $row)->getValue())) {
                                $headerLastCol = $col;
                            }
                        }

                        // Apply bold styling to table headers only to actual columns
                        $sheet->getStyle('B' . $row . ':' . $headerLastCol . $row)->applyFromArray([
                            'font' => ['bold' => true, 'name' => 'Times New Roman'],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5']
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => false
                            ]
                        ]);

                        // Special alignment for section IV table headers
                        $cellValueB = $sheet->getCell('B' . $row)->getValue();
                        $cellValueC = $sheet->getCell('C' . $row)->getValue();
                        if ($cellValueB === 'STT' && $cellValueC === 'Mục tiêu') {
                            // STT column - center both ways
                            $sheet->getStyle('B' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Mục tiêu column - center both ways
                            $sheet->getStyle('C' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Kết quả cần đạt column - center both ways
                            $sheet->getStyle('D' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Chịu trách nhiệm column - center both ways
                            $sheet->getStyle('E' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);
                        }

                        // Special alignment for section V table headers
                        if ($cellValueB === 'STT' && $cellValueC === 'Hoạt động') {
                            // STT column - center both ways
                            $sheet->getStyle('B' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Hoạt động column - center both ways
                            $sheet->getStyle('C' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Kết quả mong đợi column - center both ways
                            $sheet->getStyle('D' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Thời gian bắt đầu column - center both ways
                            $sheet->getStyle('E' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Thời gian kết thúc column - center both ways
                            $sheet->getStyle('F' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Chịu trách nhiệm column - center both ways
                            $sheet->getStyle('G' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Thành viên phối hợp column - center both ways
                            $sheet->getStyle('H' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Rủi ro phát sinh column - center both ways
                            $sheet->getStyle('I' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Phòng ngừa rủi ro column - center both ways
                            $sheet->getStyle('J' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);
                        }

                        // Special alignment for section VI table headers (Dự Toán Chi Phí)
                        if ($cellValueB === 'STT' && $cellValueC === 'Tên hạng mục') {
                            // STT column - center both ways
                            $sheet->getStyle('B' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Tên hạng mục column - center both ways
                            $sheet->getStyle('C' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Số lượng column - center both ways
                            $sheet->getStyle('D' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Đơn vị column - center both ways
                            $sheet->getStyle('E' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Đơn giá column - center both ways
                            $sheet->getStyle('F' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Thành tiền column - center both ways
                            $sheet->getStyle('G' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Phụ trách column - center both ways
                            $sheet->getStyle('H' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Ghi chú column - center both ways
                            $sheet->getStyle('I' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);
                        }

                        // Special alignment for section VII table headers (Quản Lý Vật Dụng)
                        if ($cellValueB === 'STT' && $cellValueC === 'Tên vật dụng') {
                            // STT column - center both ways
                            $sheet->getStyle('B' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Tên vật dụng column - center both ways
                            $sheet->getStyle('C' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Số lượng column - center both ways
                            $sheet->getStyle('D' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Nguồn cung cấp column - center both ways
                            $sheet->getStyle('E' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);
                        }

                        // Special alignment for section VIII table headers (Timeline Sự Kiện)
                        if ($cellValueB === 'STT' && $cellValueC === 'Thời gian') {
                            // STT column - center both ways
                            $sheet->getStyle('B' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Thời gian column - center both ways
                            $sheet->getStyle('C' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);

                            // Hoạt động column - center both ways
                            $sheet->getStyle('D' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => false
                                ]
                            ]);
                        }

                        // Set auto height for header row
                        $sheet->getRowDimension($row)->setRowHeight(-1);
                    }

                    // Apply bold styling to labels (cells that end with ":")
                    for ($col = 'B'; $col <= 'J'; $col++) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        if (is_string($cellValue) && str_ends_with($cellValue, ':')) {
                            $sheet->getStyle($col . $row)->applyFromArray([
                                'font' => ['bold' => true, 'name' => 'Times New Roman'],
                                'alignment' => ['wrapText' => false]
                            ]);
                        }
                    }
                }

                // Apply wrap text to all data cells (will be overridden by specific styles later)
                $sheet->getStyle('B:Z')->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                        'vertical' => Alignment::VERTICAL_TOP,
                    ]
                ]);

                // Apply borders to tables and configure sizing
                foreach ($tableStartRows as $tableStartRow) {
                    // Find the actual last column with data in this table
                    $tableLastCol = 'B';
                    for ($col = 'C'; $col <= 'Z'; $col++) {
                        if (!empty($sheet->getCell($col . $tableStartRow)->getValue())) {
                            $tableLastCol = $col;
                        }
                    }

                    // Find the end of this table (next empty row or end of sheet)
                    $tableEndRow = $tableStartRow;
                    for ($row = $tableStartRow + 1; $row <= $highestRow; $row++) {
                        $cellValue = $sheet->getCell('B' . $row)->getValue(); // Check column B (A is empty)
                        if (empty($cellValue) && empty($sheet->getCell('C' . $row)->getValue())) {
                            $tableEndRow = $row - 1;
                            break;
                        }
                        $tableEndRow = $row;
                    }

                    // Apply borders to the actual table size only
                    $sheet->getStyle('B' . $tableStartRow . ':' . $tableLastCol . $tableEndRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);

                    // Set column widths with limits to force wrap text
                    for ($col = 'B'; $col <= $tableLastCol; $col++) {
                        $colIndex = ord($col) - ord('B');

                        // Set different widths based on column type
                        switch ($colIndex) {
                            case 0: // STT column
                                $sheet->getColumnDimension($col)->setWidth(8);
                                break;
                            case 1: // Main content column (usually longest)
                                $sheet->getColumnDimension($col)->setWidth(30);
                                break;
                            case 2: // Second content column
                                $sheet->getColumnDimension($col)->setWidth(30);
                                break;
                            default: // Other columns
                                $sheet->getColumnDimension($col)->setWidth(30);
                                break;
                        }
                    }

                    // Apply wrap text to table content (exclude header row)
                    if ($tableEndRow > $tableStartRow) {
                        $contentStartRow = $tableStartRow + 1;
                        $sheet->getStyle('B' . $contentStartRow . ':' . $tableLastCol . $tableEndRow)->applyFromArray([
                            'alignment' => [
                                'wrapText' => true,
                                'vertical' => Alignment::VERTICAL_TOP
                            ]
                        ]);

                        // Special alignment for section IV table data rows
                        $headerCellB = $sheet->getCell('B' . $tableStartRow)->getValue();
                        $headerCellC = $sheet->getCell('C' . $tableStartRow)->getValue();
                        if ($headerCellB === 'STT' && $headerCellC === 'Mục tiêu') {
                            for ($dataRow = $contentStartRow; $dataRow <= $tableEndRow; $dataRow++) {
                                // STT column - center both ways
                                $sheet->getStyle('B' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => false
                                    ]
                                ]);

                                // Mục tiêu column - center vertically, left horizontally
                                $sheet->getStyle('C' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Kết quả cần đạt column - center vertically, left horizontally
                                $sheet->getStyle('D' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Chịu trách nhiệm column - center both ways
                                $sheet->getStyle('E' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);
                            }
                        }

                        // Special alignment for section V table data rows
                        if ($headerCellB === 'STT' && $headerCellC === 'Hoạt động') {
                            for ($dataRow = $contentStartRow; $dataRow <= $tableEndRow; $dataRow++) {
                                // STT column - center both ways
                                $sheet->getStyle('B' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => false
                                    ]
                                ]);

                                // Hoạt động column - center vertically, left horizontally
                                $sheet->getStyle('C' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Kết quả mong đợi column - center vertically, left horizontally
                                $sheet->getStyle('D' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Thời gian bắt đầu column - center both ways
                                $sheet->getStyle('E' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Thời gian kết thúc column - center both ways
                                $sheet->getStyle('F' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Chịu trách nhiệm column - center both ways
                                $sheet->getStyle('G' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Thành viên phối hợp column - center both ways
                                $sheet->getStyle('H' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Rủi ro phát sinh column - center vertically, left horizontally
                                $sheet->getStyle('I' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Phòng ngừa rủi ro column - center vertically, left horizontally
                                $sheet->getStyle('J' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);
                            }
                        }

                        // Special alignment for section VI table data rows (Dự Toán Chi Phí)
                        if ($headerCellB === 'STT' && $headerCellC === 'Tên hạng mục') {
                            for ($dataRow = $contentStartRow; $dataRow <= $tableEndRow; $dataRow++) {
                                // Check if this is the "Tổng tiền:" row
                                $totalRowCheck = $sheet->getCell('F' . $dataRow)->getValue();

                                if ($totalRowCheck === 'Tổng tiền:') {
                                    // Special styling for "Tổng tiền:" row
                                    // Tổng tiền: label - center both ways
                                    $sheet->getStyle('F' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => false
                                        ],
                                        'font' => ['bold' => true, 'name' => 'Times New Roman', 'size' => 12]
                                    ]);

                                    // Tổng tiền value - center both ways
                                    $sheet->getStyle('G' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => false
                                        ],
                                        'font' => ['bold' => true, 'name' => 'Times New Roman', 'size' => 12]
                                    ]);
                                } else {
                                    // Regular data row styling
                                    // STT column - center both ways
                                    $sheet->getStyle('B' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => false
                                        ]
                                    ]);

                                    // Tên hạng mục column - center vertically, left horizontally
                                    $sheet->getStyle('C' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);

                                    // Số lượng column - center both ways
                                    $sheet->getStyle('D' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);

                                    // Đơn vị column - center both ways
                                    $sheet->getStyle('E' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);

                                    // Đơn giá column - center both ways
                                    $sheet->getStyle('F' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);

                                    // Thành tiền column - center both ways
                                    $sheet->getStyle('G' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);

                                    // Phụ trách column - center both ways
                                    $sheet->getStyle('H' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);

                                    // Ghi chú column - center vertically, left horizontally
                                    $sheet->getStyle('I' . $dataRow)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                            'wrapText' => true
                                        ]
                                    ]);
                                }
                            }
                        }

                        // Special alignment for section VII table data rows (Quản Lý Vật Dụng)
                        if ($headerCellB === 'STT' && $headerCellC === 'Tên vật dụng') {
                            for ($dataRow = $contentStartRow; $dataRow <= $tableEndRow; $dataRow++) {
                                // STT column - center both ways
                                $sheet->getStyle('B' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => false
                                    ]
                                ]);

                                // Tên vật dụng column - center vertically, left horizontally
                                $sheet->getStyle('C' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Số lượng column - center both ways
                                $sheet->getStyle('D' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Nguồn cung cấp column - center both ways
                                $sheet->getStyle('E' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);
                            }
                        }

                        // Special alignment for section VIII table data rows (Timeline Sự Kiện)
                        if ($headerCellB === 'STT' && $headerCellC === 'Thời gian') {
                            for ($dataRow = $contentStartRow; $dataRow <= $tableEndRow; $dataRow++) {
                                // STT column - center both ways
                                $sheet->getStyle('B' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => false
                                    ]
                                ]);

                                // Thời gian column - center both ways
                                $sheet->getStyle('C' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);

                                // Hoạt động column - center vertically, left horizontally
                                $sheet->getStyle('D' . $dataRow)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => true
                                    ]
                                ]);
                            }
                        }

                        // Process line breaks for multi-line content in table rows
                        for ($row = $contentStartRow; $row <= $tableEndRow; $row++) {
                            for ($col = 'B'; $col <= $tableLastCol; $col++) {
                                $cellValue = $sheet->getCell($col . $row)->getValue();
                                if (!empty($cellValue) && strpos($cellValue, "\n") !== false) {
                                    // Convert newlines to Excel format
                                    $processedValue = str_replace("\n", "\r\n", $cellValue);
                                    $sheet->setCellValue($col . $row, $processedValue);
                                }
                            }
                            // Set auto height for content rows
                            $sheet->getRowDimension($row)->setRowHeight(-1);
                        }
                    }
                }

                // Merge specific labels with next column for better display
                $labelsToMerge = [
                    'Câu lạc bộ:',
                    'Người lập:',
                    'Thời gian tổ chức sự kiện:',
                    'Địa điểm tổ chức sự kiện:',
                    'Đối tượng tham gia:',
                    'Hình thức tổ chức:',
                    'Chịu trách nhiệm chính:',
                    'Hỗ trợ cố vấn và phối hợp tổ chức:'
                ];

                // Merge timeline date labels across 3 columns
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('B' . $row)->getValue();
                    if (is_string($cellValue) && (strpos($cellValue, 'Timeline Sự Kiện - ') === 0 || strpos($cellValue, 'Timeline sự kiện chưa xác định') === 0)) {
                        // Merge across 3 columns (B, C, D)
                        $sheet->mergeCells('B' . $row . ':D' . $row);

                        // Apply styling to merged timeline date label with center alignment
                        $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                            'font' => ['bold' => true, 'name' => 'Times New Roman', 'size' => 12],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => false
                            ]
                        ]);
                    }
                }

                // Scan all rows to find and merge labels with empty next column
                for ($row = 1; $row <= $highestRow; $row++) {
                    for ($col = 'B'; $col <= 'J'; $col++) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        if (in_array($cellValue, $labelsToMerge)) {
                            // Merge with next column (which should be empty due to data structure)
                            $nextCol = chr(ord($col) + 1);
                            if ($nextCol <= 'Z') {
                                $sheet->mergeCells($col . $row . ':' . $nextCol . $row);

                                // Set alignment to left for merged label
                                $sheet->getStyle($col . $row . ':' . $nextCol . $row)->applyFromArray([
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                        'vertical' => Alignment::VERTICAL_CENTER,
                                        'wrapText' => false
                                    ],
                                    'font' => ['bold' => true, 'name' => 'Times New Roman', 'size' => 12]
                                ]);
                            }
                        }
                    }
                }

                // Special merge for content fields from D to J
                $fieldsToMergeContent = [
                    'Thời gian tổ chức sự kiện:',
                    'Địa điểm tổ chức sự kiện:',
                    'Đối tượng tham gia:',
                    'Hình thức tổ chức:',
                    'Chịu trách nhiệm chính:',
                    'Hỗ trợ cố vấn và phối hợp tổ chức:'
                ];

                for ($row = 1; $row <= $highestRow; $row++) {
                    $labelValue = $sheet->getCell('B' . $row)->getValue();

                    if (in_array($labelValue, $fieldsToMergeContent)) {
                        // Check if there's content in column D
                        $contentValue = $sheet->getCell('D' . $row)->getValue();
                        if (!empty($contentValue)) {
                            // Merge content from D to J
                            $sheet->mergeCells('D' . $row . ':J' . $row);

                            // Convert newlines to Excel format for proper display
                            $processedContent = str_replace("\n", "\r\n", $contentValue);

                            // Set the processed content first
                            $sheet->setCellValue('D' . $row, $processedContent);

                            // Set alignment and wrap text for content
                            $sheet->getStyle('D' . $row . ':J' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical' => Alignment::VERTICAL_TOP,
                                    'wrapText' => true
                                ],
                                'font' => ['name' => 'Times New Roman', 'size' => 12]
                            ]);

                            // Calculate height based on content lines and text wrapping
                            $explicitLines = substr_count($processedContent, "\r\n") + 1;

                            // Estimate wrapped lines (7 columns merged, roughly 30 chars per column = 210 chars total)
                            $mergedWidth = 7 * 30; // 7 columns * 30 width = 210 characters approximate
                            $charactersPerLine = $mergedWidth;
                            $wrappedLines = max(1, ceil(strlen($processedContent) / $charactersPerLine));

                            // Use the higher of explicit lines or wrapped lines
                            $totalLines = max($explicitLines, $wrappedLines);
                            $calculatedHeight = max(25, $totalLines * 20); // 20 points per line
                            $sheet->getRowDimension($row)->setRowHeight($calculatedHeight);
                        }
                    }
                }

                // Merge content values for specific information fields with next column
                $fieldsContentToMerge = [
                    'Câu lạc bộ:',
                    'Người lập:',
                    'Thời gian triển khai:',
                    'Phê duyệt:'
                ];

                for ($row = 1; $row <= $highestRow; $row++) {
                    $labelValue = $sheet->getCell('B' . $row)->getValue();

                    if (in_array($labelValue, $fieldsContentToMerge)) {
                        // For these fields, merge the content in column D with column E
                        // and merge the content in column H with column I (if exists)

                        // Merge content in column D with E
                        $contentD = $sheet->getCell('D' . $row)->getValue();
                        if (!empty($contentD)) {
                            $sheet->mergeCells('D' . $row . ':E' . $row);

                            $sheet->getStyle('D' . $row . ':E' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => true
                                ],
                                'font' => ['name' => 'Times New Roman', 'size' => 12]
                            ]);
                        }

                        // Merge content in column H with I (for second field in same row)
                        $contentH = $sheet->getCell('H' . $row)->getValue();
                        if (!empty($contentH)) {
                            $sheet->mergeCells('H' . $row . ':I' . $row);

                            $sheet->getStyle('H' . $row . ':I' . $row)->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'wrapText' => true
                                ],
                                'font' => ['name' => 'Times New Roman', 'size' => 12]
                            ]);
                        }
                    }
                }
            },
        ];
    }
}
