<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchedulingExport
{
    protected $schedulings;

    const HEADERS = [
        'TIPO',
        'VALOR',
        'DATA DE AGENDAMENTO',
        'DESCRIÇÃO',
        'CATEGORIA',
        'CONTA',
        'NÚMERO CONTA',
        'AGÊNCIA',
        'CRIADO EM',
        'ATUALIZADO EM',
    ];

    public function __construct(Collection $schedulings)
    {
        $this->schedulings = $schedulings;
    }

    public function download($fileName): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(self::HEADERS, null, 'A1');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->schedulings as $scheduling) {
            $isEntrada = strtolower($scheduling->type) === 'entrada';

            $sheet->fromArray([
                $scheduling->type,
                'R$ '.number_format($scheduling->value, 2, ',', '.'),
                Carbon::parse($scheduling->date_movement)->format('d/m/Y'),
                $scheduling->description,
                $scheduling->category->name ?? '',
                $scheduling->account->name ?? '',
                $scheduling->account->account_number ?? '',
                $scheduling->account->agency_number ?? '',
                Carbon::parse($scheduling->created_at)->format('d/m/Y H:i:s'),
                Carbon::parse($scheduling->updated_at)->format('d/m/Y H:i:s'),
            ], null, 'A'.$row);

            // VALOR = coluna B
            $sheet->getStyle('B'.$row)->getFont()->setColor(
                new Color($isEntrada ? '00AA00' : 'CC0000')
            );

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
