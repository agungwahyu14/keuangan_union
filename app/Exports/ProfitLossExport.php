<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromCollection, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    public function __construct(
        private readonly array $data,
        private readonly Carbon $dateFrom,
        private readonly Carbon $dateTo,
    ) {}

    public function title(): string
    {
        return 'Laba Rugi';
    }

    public function collection(): Collection
    {
        $rows = collect();

        // ── Header Dokumen ───────────────────────────────────────
        $rows->push(['LAPORAN LABA RUGI SEDERHANA', '', '', '']);
        $rows->push(['Union Authentic', '', '', '']);
        $rows->push(['Periode: ' . $this->dateFrom->format('d/m/Y') . ' s/d ' . $this->dateTo->format('d/m/Y'), '', '', '']);
        $rows->push(['Dicetak: ' . now()->format('d/m/Y H:i'), '', '', '']);
        $rows->push(['', '', '', '']);
        $rows->push(['Keterangan', 'Jml Transaksi', 'Jumlah (Rp)', 'Catatan']);

        // ── PENDAPATAN ───────────────────────────────────────────
        $rows->push(['I. PENDAPATAN', '', '', '']);
        foreach ($this->data['rincianPendapatan'] as $item) {
            $rows->push(['   ' . ($item->category?->name ?? 'Tanpa Kategori'), $item->jumlah, $item->total, '']);
        }
        $rows->push(['TOTAL PENDAPATAN', '', $this->data['totalPendapatan'], '']);
        $rows->push(['', '', '', '']);

        // ── HARGA POKOK PENJUALAN ────────────────────────────────
        $rows->push(['II. HARGA POKOK PENJUALAN (HPP)', '', '', '']);
        foreach ($this->data['rincianHPP'] as $item) {
            $rows->push(['   ' . ($item->category?->name ?? 'Tanpa Kategori'), $item->jumlah, $item->total, 'Pembelian Stok']);
        }
        $rows->push(['TOTAL HPP', '', $this->data['totalHPP'], '']);
        $rows->push(['', '', '', '']);

        $rows->push(['LABA KOTOR (Pendapatan - HPP)', '', $this->data['labaKotor'], '']);
        $rows->push(['', '', '', '']);

        // ── BEBAN OPERASIONAL ────────────────────────────────────
        $rows->push(['III. BEBAN OPERASIONAL', '', '', '']);
        foreach ($this->data['rincianOperasional'] as $item) {
            $rows->push(['   ' . ($item->category?->name ?? 'Tanpa Kategori'), $item->jumlah, $item->total, '']);
        }
        $rows->push(['TOTAL BEBAN OPERASIONAL', '', $this->data['totalBiayaOperasional'], '']);
        $rows->push(['', '', '', '']);

        // ── LABA BERSIH ──────────────────────────────────────────
        $rows->push(['LABA / RUGI BERSIH', '', $this->data['labaBersih'], $this->data['labaBersih'] >= 0 ? 'LABA' : 'RUGI']);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Format Rupiah kolom C
                $sheet->getStyle("C1:C{$highestRow}")
                    ->getNumberFormat()->setFormatCode('"Rp "#,##0');

                // Judul utama
                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                ]);

                foreach (['A2', 'A3', 'A4'] as $cell) {
                    $sheet->mergeCells("{$cell}:D" . substr($cell, 1));
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Header kolom
                $sheet->getStyle('A6:D6')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sectionColors = [
                    'I. PENDAPATAN'                      => ['font' => '1E40AF', 'fill' => 'DBEAFE'],
                    'II. HARGA POKOK PENJUALAN (HPP)'    => ['font' => '92400E', 'fill' => 'FEF3C7'],
                    'III. BEBAN OPERASIONAL'              => ['font' => '6B21A8', 'fill' => 'F3E8FF'],
                ];

                $totalColors = [
                    'TOTAL PENDAPATAN'        => ['font' => '065F46', 'fill' => 'D1FAE5'],
                    'TOTAL HPP'               => ['font' => '92400E', 'fill' => 'FEF9C3'],
                    'TOTAL BEBAN OPERASIONAL' => ['font' => '6B21A8', 'fill' => 'EDE9FE'],
                ];

                for ($row = 1; $row <= $highestRow; $row++) {
                    $val = $sheet->getCell("A{$row}")->getValue();

                    if (isset($sectionColors[$val])) {
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => $sectionColors[$val]['font']]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionColors[$val]['fill']]],
                        ]);
                    }

                    if (isset($totalColors[$val])) {
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => $totalColors[$val]['font']]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $totalColors[$val]['fill']]],
                        ]);
                    }

                    if ($val === 'LABA KOTOR (Pendapatan - HPP)') {
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font'    => ['bold' => true, 'size' => 11],
                            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                            'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);
                    }

                    if ($val === 'LABA / RUGI BERSIH') {
                        $bersih = $this->data['labaBersih'];
                        $color  = $bersih >= 0 ? '065F46' : '991B1B';
                        $bg     = $bersih >= 0 ? 'D1FAE5' : 'FEE2E2';
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font'    => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $color]],
                            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        ]);
                    }
                }

                $sheet->getStyle("A6:D{$highestRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            },
        ];
    }
}
