<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #1a1a1a; }

    /* KOP SURAT */
    .kop { display: table; width: 100%; border-bottom: 3px solid #16a34a; padding-bottom: 12px; margin-bottom: 18px; }
    .kop-logo { display: table-cell; width: 60px; vertical-align: middle; }
    .kop-logo .icon { width: 50px; height: 50px; background: #16a34a; border-radius: 8px; text-align: center; line-height: 50px; color: white; font-size: 24pt; }
    .kop-text { display: table-cell; vertical-align: middle; padding-left: 12px; }
    .kop-text h1 { font-size: 14pt; font-weight: bold; color: #166534; }
    .kop-text p  { font-size: 8pt; color: #555; }

    /* INVOICE CONTENT */
    .title { text-align: center; font-size: 12pt; font-weight: bold; color: #166534; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px; }
    .number { text-align: center; font-size: 9pt; color: #888; margin-bottom: 20px; }

    /* Resident info box */
    .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; }
    .info-box table { width: 100%; }
    .info-box td { padding: 3px 0; font-size: 9.5pt; }
    .info-box td:first-child { width: 130px; color: #555; }
    .info-box td:last-child { font-weight: bold; color: #14532d; }

    /* Payment highlight */
    .payment-card { background: #166534; color: white; border-radius: 8px; padding: 14px 20px; margin-bottom: 20px; text-align: center; }
    .payment-card .label { font-size: 9pt; opacity: 0.8; margin-bottom: 4px; }
    .payment-card .amount { font-size: 18pt; font-weight: bold; }
    .payment-card .date { font-size: 8.5pt; opacity: 0.7; margin-top: 4px; }

    /* Recap table */
    .recap-title { font-size: 10pt; font-weight: bold; color: #166534; margin-bottom: 8px; }
    .recap-table { width: 100%; border-collapse: collapse; }
    .recap-table th { background: #166534; color: white; padding: 6px 10px; font-size: 9pt; text-align: left; }
    .recap-table td { padding: 5px 10px; font-size: 9pt; border-bottom: 1px solid #e5e7eb; }
    .recap-table tr:nth-child(even) td { background: #f9fafb; }
    .status-lunas { color: #16a34a; font-weight: bold; }
    .status-belum { color: #ef4444; font-weight: bold; }

    /* Footer */
    .footer { margin-top: 24px; }
    .signature { float: right; text-align: center; width: 180px; }
    .signature .city-date { font-size: 9pt; color: #555; margin-bottom: 40px; }
    .signature .sign-name { font-size: 9.5pt; font-weight: bold; border-top: 1px solid #333; padding-top: 4px; }
    .signature .sign-title { font-size: 8.5pt; color: #555; }
    .clearfix::after { content: ''; display: table; clear: both; }
    .watermark { text-align: center; margin-top: 20px; font-size: 8pt; color: #ccc; }
</style>
</head>
<body>

{{-- KOP SURAT --}}
<div class="kop">
    <div class="kop-logo">
        <div class="icon">🏡</div>
    </div>
    <div class="kop-text">
        <h1>PENGURUS RT – GRAND MADANI 2</h1>
        <p>Perumahan Grand Madani 2 | grandmadani2.xyz</p>
        <p>Sistem Informasi RT Digital</p>
    </div>
</div>

{{-- TITLE --}}
<p class="title">Kuitansi Pembayaran IPL</p>
<p class="number">Nomor: KW-{{ $resident->id }}-{{ sprintf('%02d', $transaction->month) }}-{{ $year }}</p>

{{-- RESIDENT INFO --}}
<div class="info-box">
    <table>
        <tr>
            <td>Nama Warga</td>
            <td>: {{ $resident->owner_name }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: Blok {{ $resident->block }} / No. {{ $resident->number }}</td>
        </tr>
        <tr>
            <td>Tanggal Bayar</td>
            <td>: {{ $transaction->paid_at?->format('d F Y') ?? now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>: {{ $transaction->month_name }} {{ $year }}</td>
        </tr>
    </table>
</div>

{{-- PAYMENT HIGHLIGHT --}}
<div class="payment-card">
    <p class="label">Nominal Pembayaran IPL</p>
    <p class="amount">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
    <p class="date">Diterima pada {{ $transaction->paid_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }} WIB</p>
</div>

{{-- YEAR RECAP TABLE --}}
<p class="recap-title">Rekap IPL Tahun {{ $year }}</p>
<table class="recap-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Bulan</th>
            <th>Status</th>
            <th>Nominal</th>
            <th>Tanggal Bayar</th>
        </tr>
    </thead>
    <tbody>
        @php
            $monthNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                           7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        @endphp
        @for($m = 1; $m <= 12; $m++)
        @php $tx = $yearTransactions->get($m); @endphp
        <tr>
            <td>{{ $m }}</td>
            <td>{{ $monthNames[$m] }}</td>
            <td class="{{ $tx && $tx->status === 'lunas' ? 'status-lunas' : 'status-belum' }}">
                {{ $tx && $tx->status === 'lunas' ? '✓ Lunas' : '✗ Belum' }}
            </td>
            <td>{{ $tx && $tx->status === 'lunas' ? 'Rp '.number_format($tx->amount,0,',','.') : '–' }}</td>
            <td>{{ $tx?->paid_at?->format('d M Y') ?? '–' }}</td>
        </tr>
        @endfor
    </tbody>
</table>

{{-- SIGNATURE --}}
<div class="footer clearfix">
    <div class="signature">
        <p class="city-date">Grand Madani 2, {{ now()->format('d F Y') }}</p>
        <p class="sign-name">Pengurus RT</p>
        <p class="sign-title">Ketua RT Grand Madani 2</p>
    </div>
</div>

<p class="watermark">Dokumen ini digenerate otomatis oleh Sistem RT Digital Grand Madani 2 · grandmadani2.xyz</p>

</body>
</html>
