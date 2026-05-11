<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashFlowExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    public function __construct(
        private readonly array $data,
        private readonly Carbon $dateFrom,
        private readonly Carbon $dateTo,
    ) {}

    public function title(): string
    {
        return 'Arus Kas';
    }

    public function headings(): array
    {
        return []; // Header dibuat manual via WithEvents
    }

    public function collection(): Collection
    {
        $rows = collect();

        // ── Section Header ───────────────────────────────────────
        $rows->push(['LAPORAN ARUS KAS', '', '', '']);
        $rows->push(['Union Authentic', '', '', '']);
        $rows->push(['Periode: ' . $this->dateFrom->format('d/m/Y') . ' s/d ' . $this->dateTo->format('d/m/Y'), '', '', '']);
        $rows->push(['Dicetak: ' . now()->format('d/m/Y H:i'), '', '', '']);
        $rows->push(['', '', '', '']);

        // ── Kolom Header ─────────────────────────────────────────
        $rows->push(['Kategori', 'Jml Transaksi', 'Total', 'Keterangan']);

        // ── ARUS KAS MASUK ───────────────────────────────────────
        $rows->push(['ARUS KAS MASUK (PEMASUKAN)', '', '', '']);
        foreach ($this->data['pemasukanPerKategori'] as $item) {
            $rows->push([
                $item->category?->name ?? 'Tanpa Kategori',
                $item->jumlah,
                $item->total,
                '',
            ]);
        }
        $rows->push(['SUBTOTAL PEMASUKAN', '', $this->data['totalPemasukan'], '']);
        $rows->push(['', '', '', '']);

        // ── ARUS KAS KELUAR ──────────────────────────────────────
        $rows->push(['ARUS KAS KELUAR (PENGELUARAN)', '', '', '']);
        foreach ($this->data['pengeluaranPerKategori'] as $item) {
            $isHpp = $item->category?->is_hpp ?? false;
            $rows->push([
                $item->category?->name ?? 'Tanpa Kategori',
                $item->jumlah,
                $item->total,
                $isHpp ? 'HPP / Pembelian Stok' : '',
            ]);
        }
        $rows->push(['SUBTOTAL PENGELUARAN', '', $this->data['totalPengeluaran'], '']);
        $rows->push(['', '', '', '']);

        // ── RINGKASAN ────────────────────────────────────────────
        $rows->push(['RINGKASAN ARUS KAS', '', '', '']);
        $rows->push(['Total Arus Kas Masuk', '', $this->data['totalPemasukan'], '']);
        $rows->push(['Total Arus Kas Keluar', '', $this->data['totalPengeluaran'], '']);
        $rows->push(['ARUS KAS BERSIH', '', $this->data['arusKasBersih'], '']);

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
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Format kolom C (Total) sebagai Rupiah
                $sheet->getStyle("C1:C{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0');

                // Judul utama
                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                ]);

                foreach (['A2', 'A3', 'A4'] as $cell) {
                    $sheet->mergeCells("{$cell}:D" . substr($cell, 1));
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Header kolom (baris 6)
                $sheet->getStyle('A6:D6')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Cari dan style section headers + totals
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellVal = $sheet->getCell("A{$row}")->getValue();

                    if (in_array($cellVal, ['ARUS KAS MASUK (PEMASUKAN)', 'ARUS KAS KELUAR (PENGELUARAN)', 'RINGKASAN ARUS KAS'])) {
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                        ]);
                    }

                    if (in_array($cellVal, ['SUBTOTAL PEMASUKAN', 'SUBTOTAL PENGELUARAN'])) {
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                        ]);
                    }

                    if ($cellVal === 'ARUS KAS BERSIH') {
                        $bersih = $this->data['arusKasBersih'];
                        $color  = $bersih >= 0 ? '065F46' : '991B1B';
                        $bg     = $bersih >= 0 ? 'D1FAE5' : 'FEE2E2';
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $color]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]],
                        ]);
                    }
                }

                // Border seluruh tabel
                $sheet->getStyle("A6:D{$highestRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            },
        ];
    }
}
