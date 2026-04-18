<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; color: #1a1a1a; }

    /* ── KOP SURAT ── */
    .kop-wrapper { border-bottom: 4px double #16a34a; padding-bottom: 14px; margin-bottom: 16px; }
    .kop { display: table; width: 100%; }
    .kop-logo  { display: table-cell; width: 70px; vertical-align: middle; text-align: center; }
    .kop-title { display: table-cell; vertical-align: middle; text-align: center; }
    .kop-right { display: table-cell; width: 70px; }
    .kop-title h1 { font-size: 13pt; font-weight: bold; color: #14532d; text-transform: uppercase; letter-spacing: 0.5px; }
    .kop-title p  { font-size: 8.5pt; color: #555; margin-top: 2px; }
    .logo-box { width: 55px; height: 55px; background: #16a34a; border-radius: 8px; margin: 0 auto; line-height: 55px; text-align: center; color: white; font-size: 22pt; }
    .thin-line { border: none; border-top: 1px solid #16a34a; margin-top: 2px; }

    /* ── LETTER META ── */
    .meta-table { width: 100%; margin: 16px 0; }
    .meta-table td { font-size: 10pt; padding: 2px 0; }
    .meta-table td:first-child { width: 160px; }
    .meta-table td:nth-child(2) { width: 10px; text-align: center; }

    /* ── SUBJECT ── */
    .subject-box { text-align: center; margin: 14px 0 20px; }
    .subject-box .label { font-size: 9pt; color: #888; }
    .subject-box .title { font-size: 12pt; font-weight: bold; text-decoration: underline; color: #166534; margin-top: 2px; }

    /* ── SALUTATION ── */
    .salutation { margin-bottom: 12px; font-size: 10.5pt; }

    /* ── BODY ── */
    .body-content { font-size: 10.5pt; line-height: 1.8; text-align: justify; white-space: pre-wrap; word-wrap: break-word; }

    /* ── CLOSING ── */
    .closing { margin-top: 22px; font-size: 10.5pt; }
    .signature-area { margin-top: 36px; float: right; text-align: center; width: 200px; }
    .signature-area .city-date { font-size: 10pt; margin-bottom: 52px; }
    .signature-area .name { font-weight: bold; font-size: 10.5pt; border-top: 1px solid #111; padding-top: 4px; }
    .signature-area .title { font-size: 9.5pt; color: #555; }
    .clearfix::after { content:''; display:table; clear:both; }
    .footer-stamp { text-align: center; margin-top: 30px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 8pt; color: #aaa; }
</style>
</head>
<body>

{{-- KOP SURAT --}}
<div class="kop-wrapper">
    <div class="kop">
        <div class="kop-logo">
            <div class="logo-box">🏡</div>
        </div>
        <div class="kop-title">
            <h1>Pengurus Rukun Tetangga</h1>
            <h1>Grand Madani 2</h1>
            <hr class="thin-line">
            <p>Perumahan Grand Madani 2 | grandmadani2.xyz</p>
        </div>
        <div class="kop-right"></div>
    </div>
</div>

{{-- LETTER META --}}
<table class="meta-table">
    <tr>
        <td>Nomor Surat</td>
        <td>:</td>
        <td><strong>{{ $letterNumber }}</strong></td>
    </tr>
    <tr>
        <td>Kategori</td>
        <td>:</td>
        <td>{{ $category }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>:</td>
        <td>{{ $date }}</td>
    </tr>
</table>

{{-- SUBJECT LINE --}}
<div class="subject-box">
    <p class="label">Perihal</p>
    <p class="title">{{ $subject }}</p>
</div>

{{-- SALUTATION --}}
<p class="salutation">Kepada Yth.,<br>Seluruh Warga Grand Madani 2<br>di Tempat</p>

{{-- BODY --}}
<div class="body-content">{{ $content }}</div>

{{-- CLOSING --}}
<div class="closing clearfix">
    <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>

    <div class="signature-area">
        <p class="city-date">Grand Madani 2, {{ $date }}</p>
        <p class="name">Pengurus RT</p>
        <p class="title">Ketua RT Grand Madani 2</p>
    </div>
</div>

{{-- FOOTER --}}
<div class="footer-stamp">
    Nomor: {{ $letterNumber }} · Digenerate otomatis oleh Sistem RT Digital Grand Madani 2 · grandmadani2.xyz
</div>

</body>
</html>
