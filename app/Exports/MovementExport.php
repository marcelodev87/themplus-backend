<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MovementExport
{
    protected $movements;

    // Índice da coluna VALOR (1-based: A=1, B=2...)
    const VALOR_COL = 3; // coluna C

    const HEADERS = [
        'DATA DE MOVIMENTAÇÃO',
        'DESCRIÇÃO',
        'VALOR',
        'CATEGORIA',
        'TIPO',
        'CODIGO CREDITO',
        'CODIGO DEBITO',
        'CONTA',
        'NÚMERO CONTA',
        'AGÊNCIA',
        'CRIADO EM',
        'ATUALIZADO EM',
    ];

    public function __construct(Collection $movements)
    {
        $this->movements = $movements;
    }

    public function download($fileName): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalho
        $sheet->fromArray(self::HEADERS, null, 'A1');

        // Estilo negrito no cabeçalho
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')
            ->getFont()->setBold(true);

        // Dados
        $row = 2;
        foreach ($this->movements as $movement) {
            $isEntrada = strtolower($movement->type) === 'entrada';

            $sheet->fromArray([
                Carbon::parse($movement->date_movement)->format('d/m/Y'),
                $movement->description ?? '',
                'R$ '.number_format($movement->value, 2, ',', '.'),
                $movement->category->name ?? '',
                $movement->type,
                $movement->category->code_credit ?? '',
                $movement->category->code_debt ?? '',
                $movement->account->name ?? '',
                $movement->account->account_number ?? '',
                $movement->account->agency_number ?? '',
                Carbon::parse($movement->created_at)->format('d/m/Y H:i:s'),
                Carbon::parse($movement->updated_at)->format('d/m/Y H:i:s'),
            ], null, 'A'.$row);

            // Cor da célula VALOR (coluna C)
            $valorCell = 'C'.$row;
            $sheet->getStyle($valorCell)->getFont()->setColor(
                new Color($isEntrada ? '00AA00' : 'CC0000') // verde escuro / vermelho
            );

            $row++;
        }

        // Auto-width nas colunas
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
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
