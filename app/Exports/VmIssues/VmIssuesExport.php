<?php

namespace App\Exports\VmIssues;

use App\Models\VisualMerchandising;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VmIssuesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithEvents
{
    public function __construct(
        private readonly int    $creatorId,
        private readonly ?string $fixStatus = null,
        private readonly ?int    $storeId   = null,
    ) {}

    public function collection(): Collection
    {
        return VisualMerchandising::query()
            ->with([
                'store:id,title,code',
                'asset:id,name,asset_code,asset_type_id',
                'asset.assetType:id,name',
            ])
            ->where('creator_id', $this->creatorId)
            ->when($this->fixStatus, fn ($q) => $q->where('issue_fix_status', $this->fixStatus))
            ->when($this->storeId,   fn ($q) => $q->where('store_id', $this->storeId))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Store',
            'Store Code',
            'Asset',
            'Asset Type',
            'Asset Code',
            'Issue Details',
            'Fix Status',
            'Created At',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->store?->title   ?? 'N/A',
            $row->store?->code    ?? 'N/A',
            $row->asset?->name    ?? 'N/A',
            $row->asset?->assetType?->name ?? 'N/A',
            $row->asset?->asset_code       ?? 'N/A',
            strip_tags($row->issue_text ?? ''),
            ucfirst($row->issue_fix_status ?? ''),
            $row->created_at?->format('d M Y H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — bold, background, centered
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $lastRow    = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Border around all cells
                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                ]);

                // Wrap text and vertical align for data rows
                $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'wrapText'   => true,
                        'vertical'   => Alignment::VERTICAL_TOP,
                    ],
                ]);

                // Fix column widths
                $sheet->getColumnDimension('G')->setWidth(50); // Issue Details
                $sheet->getColumnDimension('H')->setWidth(14); // Fix Status

                // Freeze header row
                $sheet->freezePane('A2');

                // Header row height
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Alternate row background for readability
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF9FAFB'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }

    public function title(): string
    {
        return 'VM Issues';
    }
}
