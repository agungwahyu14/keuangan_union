<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Arus Kas — {{ config('app.name') }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }
  .header { text-align: center; padding: 24px 0 16px; border-bottom: 2px solid #4f46e5; margin-bottom: 20px; }
  .header .title { font-size: 18px; font-weight: bold; color: #4f46e5; letter-spacing: 0.5px; }
  .header .subtitle { font-size: 11px; color: #6b7280; margin-top: 3px; }
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10px; color: #6b7280; }
  .meta span { background: #f3f4f6; padding: 4px 10px; border-radius: 4px; }
  .section-title { font-size: 12px; font-weight: bold; color: #1e40af; background: #dbeafe; padding: 7px 12px; margin-top: 18px; margin-bottom: 0; border-radius: 4px 4px 0 0; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #374151; color: #fff; text-align: left; padding: 7px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
  th.right, td.right { text-align: right; }
  td { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; }
  tr:nth-child(even) td { background: #f9fafb; }
  .hpp-row td { background: #fff7ed !important; }
  .hpp-badge { display: inline-block; background: #fed7aa; color: #92400e; font-size: 9px; padding: 1px 6px; border-radius: 10px; font-weight: bold; }
  .subtotal td { background: #f3f4f6 !important; font-weight: bold; border-top: 1px solid #d1d5db; }
  .summary-box { margin-top: 24px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
  .summary-title { background: #374151; color: #fff; padding: 8px 14px; font-weight: bold; font-size: 11px; }
  .summary-row { display: flex; justify-content: space-between; padding: 8px 14px; border-bottom: 1px solid #f3f4f6; }
  .summary-row.total-in { background: #d1fae5; color: #065f46; font-weight: bold; }
  .summary-row.total-out { background: #fee2e2; color: #991b1b; font-weight: bold; }
  .summary-row.net.positive { background: #059669; color: #fff; font-size: 13px; font-weight: bold; }
  .summary-row.net.negative { background: #dc2626; color: #fff; font-size: 13px; font-weight: bold; }
  .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
  .signature-box { text-align: center; width: 200px; }
  .signature-box .label { font-size: 10px; color: #6b7280; margin-bottom: 60px; }
  .signature-box .name { font-weight: bold; border-top: 1px solid #374151; padding-top: 4px; font-size: 11px; }
  .page-info { text-align: center; margin-top: 30px; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>

  {{-- Header --}}
  <div class="header">
    <div class="title">LAPORAN ARUS KAS</div>
    <div class="subtitle">{{ config('app.name') }}</div>
    <div class="subtitle" style="margin-top:6px">
      Periode: {{ $dateFrom->translatedFormat('d F Y') }} s/d {{ $dateTo->translatedFormat('d F Y') }}
    </div>
  </div>

  {{-- Meta info --}}
  <div class="meta">
    <span>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
    <span>Dicetak oleh: {{ Auth::user()->name }}</span>
  </div>

  {{-- ARUS KAS MASUK --}}
  <div class="section-title">I. ARUS KAS MASUK (PEMASUKAN)</div>
  <table>
    <thead>
      <tr>
        <th>Kategori</th>
        <th class="right">Jml Transaksi</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pemasukanPerKategori as $item)
      <tr>
        <td>{{ $item->category?->name ?? 'Tanpa Kategori' }}</td>
        <td class="right">{{ $item->jumlah }}</td>
        <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
      </tr>
      @endforeach
      @if($pemasukanPerKategori->isEmpty())
      <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:12px">Tidak ada data pemasukan</td></tr>
      @endif
      <tr class="subtotal">
        <td>SUBTOTAL ARUS KAS MASUK</td>
        <td class="right">{{ $pemasukanPerKategori->sum('jumlah') }}</td>
        <td class="right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  {{-- ARUS KAS KELUAR --}}
  <div class="section-title" style="background:#fef3c7;color:#92400e;margin-top:16px">II. ARUS KAS KELUAR (PENGELUARAN)</div>
  <table>
    <thead>
      <tr>
        <th>Kategori</th>
        <th class="right">Jml Transaksi</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pengeluaranPerKategori as $item)
      <tr class="{{ $item->category?->is_hpp ? 'hpp-row' : '' }}">
        <td>
          {{ $item->category?->name ?? 'Tanpa Kategori' }}
          @if($item->category?->is_hpp)
            <span class="hpp-badge">HPP</span>
          @endif
        </td>
        <td class="right">{{ $item->jumlah }}</td>
        <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
      </tr>
      @endforeach
      @if($pengeluaranPerKategori->isEmpty())
      <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:12px">Tidak ada data pengeluaran</td></tr>
      @endif
      <tr class="subtotal">
        <td>SUBTOTAL ARUS KAS KELUAR</td>
        <td class="right">{{ $pengeluaranPerKategori->sum('jumlah') }}</td>
        <td class="right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  {{-- RINGKASAN --}}
  <div class="summary-box">
    <div class="summary-title">RINGKASAN ARUS KAS</div>
    <div class="summary-row total-in">
      <span>Total Arus Kas Masuk</span>
      <strong>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong>
    </div>
    <div class="summary-row total-out">
      <span>Total Arus Kas Keluar</span>
      <strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
    </div>
    <div class="summary-row net {{ $arusKasBersih >= 0 ? 'positive' : 'negative' }}">
      <span>ARUS KAS BERSIH</span>
      <strong>{{ $arusKasBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($arusKasBersih), 0, ',', '.') }}</strong>
    </div>
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
