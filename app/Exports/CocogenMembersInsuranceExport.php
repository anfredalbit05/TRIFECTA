<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CocogenMembersInsuranceExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected string $search;
    protected string $statusFilter;

    protected int $rowNumber = 0;

    public function __construct(
        string $search = '',
        string $statusFilter = ''
    ) {
        $this->search = $search;
        $this->statusFilter = $statusFilter;
    }

    public function collection(): Collection
    {
        $query = DB::table('cocogen_members_insurance');

        $query->when(filled($this->search), function ($query) {
            $term = '%' . trim($this->search) . '%';

            $query->where(function ($q) use ($term) {
                $q->where('member_name', 'like', $term)
                    ->orWhere('beneficiary_name', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('contact_number', 'like', $term)
                    ->orWhere('plan', 'like', $term);
            });
        });

        $query->when(
            $this->statusFilter !== '',
            fn ($query) => $query->where(
                'civil_status',
                $this->statusFilter
            )
        );

        return $query
            ->orderBy('CocogenID', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'NAME',
            'Name of Beneficiary',
            'Address',
            'B-Date',
            'Status',
            'Gender',
            'Occupation',
            'CP #',
            'Plan',
            'Amount',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->member_name ?? '',
            $row->beneficiary_name ?? '',
            $row->address ?? '',
            $row->birthdate
                ? date('d-m-Y', strtotime($row->birthdate))
                : '',
            $row->civil_status ?? '',
            $row->gender ?? '',
            $row->occupation ?? '',
            $row->contact_number ?? '',
            $row->plan ?? '',
            (float) ($row->amount ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => '000000',
                    ],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => 'FFC000',
                    ],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }
}
