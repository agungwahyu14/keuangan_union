<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Laba Rugi — {{ config('app.name') }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
  .header { text-align: center; padding: 24px 0 16px; border-bottom: 2px solid #4f46e5; margin-bottom: 20px; }
  .header .title { font-size: 18px; font-weight: bold; color: #4f46e5; }
  .header .subtitle { font-size: 11px; color: #6b7280; margin-top: 3px; }
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10px; color: #6b7280; }
  .meta span { background: #f3f4f6; padding: 4px 10px; border-radius: 4px; }
  .section-title { font-size: 12px; font-weight: bold; padding: 7px 12px; margin-top: 14px; margin-bottom: 0; border-radius: 4px 4px 0 0; }
  .sec-pendapatan { background: #dbeafe; color: #1e40af; }
  .sec-hpp        { background: #fef3c7; color: #92400e; }
  .sec-operasional{ background: #ede9fe; color: #6b21a8; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #374151; color: #fff; text-align: left; padding: 7px 10px; font-size: 10px; text-transform: uppercase; }
  th.right, td.right { text-align: right; }
  td { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; }
  tr:nth-child(even) td { background: #f9fafb; }
  .subtotal td { font-weight: bold; border-top: 1px solid #d1d5db; }
  .sub-pendapatan td { background: #d1fae5 !important; color: #065f46; }
  .sub-hpp        td { background: #fef9c3 !important; color: #92400e; }
  .sub-operasional td{ background: #ede9fe !important; color: #6b21a8; }
  .intermediate-box { margin-top: 10px; padding: 10px 14px; border-radius: 6px; display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; }
  .laba-kotor { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }
  .final-box { margin-top: 16px; padding: 14px; border-radius: 8px; display: flex; justify-content: space-between; font-size: 15px; font-weight: bold; }
  .final-laba { background: #059669; color: #fff; }
  .final-rugi { background: #dc2626; color: #fff; }
  .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
  .signature-box { text-align: center; width: 200px; }
  .signature-box .label { font-size: 10px; color: #6b7280; margin-bottom: 55px; }
  .signature-box .name { font-weight: bold; border-top: 1px solid #374151; padding-top: 4px; }
  .page-info { text-align: center; margin-top: 30px; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>

  <div class="header">
    <div class="title">LAPORAN LABA RUGI SEDERHANA</div>
    <div class="subtitle">{{ config('app.name') }}</div>
    <div class="subtitle" style="margin-top:6px">
      Periode: {{ $dateFrom->translatedFormat('d F Y') }} s/d {{ $dateTo->translatedFormat('d F Y') }}
    </div>
  </div>

  <div class="meta">
    <span>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
    <span>Dicetak oleh: {{ Auth::user()->name }}</span>
  </div>

  {{-- I. PENDAPATAN --}}
  <div class="section-title sec-pendapatan">I. PENDAPATAN</div>
  <table>
    <thead><tr><th>Kategori</th><th class="right">Jml Transaksi</th><th class="right">Jumlah</th></tr></thead>
    <tbody>
      @foreach($rincianPendapatan as $item)
      <tr>
        <td>{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
        <td class="right">{{ $item->jumlah }}</td>
        <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
      </tr>
      @endforeach
      @if($rincianPendapatan->isEmpty())
      <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:10px">Tidak ada data</td></tr>
      @endif
      <tr class="subtotal sub-pendapatan">
        <td>TOTAL PENDAPATAN</td>
        <td class="right">{{ $rincianPendapatan->sum('jumlah') }}</td>
        <td class="right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  {{-- II. HPP --}}
  <div class="section-title sec-hpp">II. HARGA POKOK PENJUALAN (HPP)</div>
  <table>
    <thead><tr><th>Kategori</th><th class="right">Jml Transaksi</th><th class="right">Jumlah</th></tr></thead>
    <tbody>
      @foreach($rincianHPP as $item)
      <tr>
        <td>{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
        <td class="right">{{ $item->jumlah }}</td>
        <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
      </tr>
      @endforeach
      @if($rincianHPP->isEmpty())
      <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:10px">Tidak ada data HPP</td></tr>
      @endif
      <tr class="subtotal sub-hpp">
        <td>TOTAL HPP</td>
        <td class="right">{{ $rincianHPP->sum('jumlah') }}</td>
        <td class="right">Rp {{ number_format($totalHPP, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  {{-- LABA KOTOR --}}
  <div class="intermediate-box laba-kotor">
    <span>LABA KOTOR &nbsp;=&nbsp; Pendapatan − HPP</span>
    <span>{{ $labaKotor < 0 ? '-' : '' }}Rp {{ number_format(abs($labaKotor), 0, ',', '.') }}</span>
  </div>

  {{-- III. BEBAN OPERASIONAL --}}
  <div class="section-title sec-operasional">III. BEBAN OPERASIONAL</div>
  <table>
    <thead><tr><th>Kategori</th><th class="right">Jml Transaksi</th><th class="right">Jumlah</th></tr></thead>
    <tbody>
      @foreach($rincianOperasional as $item)
      <tr>
        <td>{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
        <td class="right">{{ $item->jumlah }}</td>
        <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
      </tr>
      @endforeach
      @if($rincianOperasional->isEmpty())
      <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:10px">Tidak ada beban operasional</td></tr>
      @endif
      <tr class="subtotal sub-operasional">
        <td>TOTAL BEBAN OPERASIONAL</td>
        <td class="right">{{ $rincianOperasional->sum('jumlah') }}</td>
        <td class="right">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  {{-- LABA / RUGI BERSIH --}}
  <div class="final-box {{ $labaBersih >= 0 ? 'final-laba' : 'final-rugi' }}">
    <span>{{ $labaBersih >= 0 ? '✓ LABA BERSIH' : '✗ RUGI BERSIH' }}</span>
    <span>{{ $labaBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}</span>
  </div>

  {{-- Tanda Tangan --}}
  <div class="footer">
    <div class="signature-box">
      <div class="label">Diketahui oleh,</div>
      <div class="name">{{ Auth::user()->name }}</div>
      <div style="font-size:9px;color:#6b7280">Administrator</div>
    </div>
  </div>

  <div class="page-info">{{ config('app.name') }} — Laporan digenerate otomatis pada {{ now()->format('d/m/Y H:i:s') }}</div>

</body>
</html>
