<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Surat Jalan</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
  .page { width: 210mm; min-height: 148mm; margin: 0 auto; padding: 15mm 15mm 10mm; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 2px solid #1a1a1a; padding-bottom: 10px; }
  .company-name { font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; }
  .doc-title { font-size: 22px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: #1a1a1a; text-align: right; }
  .doc-number { font-size: 13px; font-weight: 700; color: #555; text-align: right; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
  .info-block label { font-size: 9px; text-transform: uppercase; letter-spacing: .8px; color: #888; display: block; margin-bottom: 2px; }
  .info-block .value { font-weight: 700; font-size: 12px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  thead th { background: #1a1a1a; color: #fff; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; text-align: left; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; vertical-align: top; }
  tbody tr:nth-child(even) td { background: #f9fafb; }
  .text-center { text-align: center; }
  .text-right  { text-align: right; }
  .sign-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 20px; }
  .sign-box { text-align: center; }
  .sign-box .role { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #888; margin-bottom: 40px; }
  .sign-box .line { border-top: 1px solid #1a1a1a; padding-top: 4px; font-size: 10px; }
  .notes { font-size: 10px; color: #666; margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 8px; }
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .no-print { display: none !important; }
  }
</style>
</head>
<body>
<div class="page" id="print-area">
  <div class="header">
    <div>
      <div class="company-name" id="p-company">Perusahaan</div>
      <div style="font-size:10px;color:#666;" id="p-company-addr"></div>
    </div>
    <div>
      <div class="doc-title">Surat Jalan</div>
      <div class="doc-number" id="p-nomor">-</div>
    </div>
  </div>

  <div class="info-grid">
    <div>
      <div class="info-block">
        <label>Kepada</label>
        <div class="value" id="p-mitra-nama">-</div>
        <div style="font-size:10px;color:#666;" id="p-mitra-alamat"></div>
        <div style="font-size:10px;color:#666;" id="p-mitra-telp"></div>
      </div>
    </div>
    <div>
      <div class="info-block" style="margin-bottom:6px;">
        <label>Tanggal</label>
        <div class="value" id="p-tgl">-</div>
      </div>
      <div class="info-block" style="margin-bottom:6px;">
        <label>Referensi / PO</label>
        <div class="value" id="p-ref">-</div>
      </div>
      <div class="info-block">
        <label>Sales Person</label>
        <div class="value" id="p-sales">-</div>
      </div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th width="5%">No</th>
        <th width="45%">Nama Barang</th>
        <th class="text-center" width="15%">Qty</th>
        <th class="text-center" width="15%">Satuan</th>
        <th width="20%">Keterangan</th>
      </tr>
    </thead>
    <tbody id="p-items">
      <tr><td colspan="5" class="text-center" style="padding:20px;color:#999;">Memuat...</td></tr>
    </tbody>
  </table>

  <div class="notes" id="p-notes"></div>

  <div class="sign-grid">
    <div class="sign-box">
      <div class="role">Pengirim</div>
      <div class="line">( ________________ )</div>
    </div>
    <div class="sign-box">
      <div class="role">Pengemudi</div>
      <div class="line">( ________________ )</div>
    </div>
    <div class="sign-box">
      <div class="role">Penerima</div>
      <div class="line">( ________________ )</div>
    </div>
  </div>
</div>

<div class="no-print" style="text-align:center;padding:16px;">
  <button onclick="window.print()" style="padding:8px 24px;background:#1a1a1a;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;font-weight:700;">
    🖨 Cetak Sekarang
  </button>
</div>

<script>
const SJ_ID = {{ $id }};

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res    = await fetch(`/api/surat-jalan-api/${SJ_ID}`);
        const result = await res.json();
        if (!result.success) { document.body.innerHTML = '<p style="padding:20px;color:red;">Data tidak ditemukan.</p>'; return; }
        render(result.data);
    } catch (e) {
        document.body.innerHTML = '<p style="padding:20px;color:red;">Gagal memuat data.</p>';
    }
});

function fmtDate(d) {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
}

function render(sj) {
    document.title = 'Surat Jalan - ' + sj.nomor_invoice;
    document.getElementById('p-nomor').textContent = sj.nomor_invoice;
    document.getElementById('p-tgl').textContent   = fmtDate(sj.tgl_invoice);
    document.getElementById('p-ref').textContent   = sj.ref_no || '-';
    document.getElementById('p-sales').textContent = sj.sales ? sj.sales.name : '-';

    if (sj.mitra) {
        document.getElementById('p-mitra-nama').textContent   = sj.mitra.nama;
        document.getElementById('p-mitra-alamat').textContent = sj.mitra.alamat || '';
        document.getElementById('p-mitra-telp').textContent   = sj.mitra.no_hp  || '';
    }

    if (sj.keterangan) {
        document.getElementById('p-notes').textContent = 'Catatan: ' + sj.keterangan;
    }

    const tbody = document.getElementById('p-items');
    if (!sj.items || sj.items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center" style="padding:20px;color:#999;">Tidak ada barang</td></tr>';
        return;
    }

    tbody.innerHTML = sj.items.map((item, i) => `
        <tr>
            <td class="text-center">${i + 1}</td>
            <td>
                <strong>${item.nama_produk_manual || item.product?.nama_produk || '-'}</strong>
                ${item.deskripsi_produk ? `<br><span style="font-size:10px;color:#666;">${item.deskripsi_produk}</span>` : ''}
            </td>
            <td class="text-center"><strong>${item.qty}</strong></td>
            <td class="text-center">${item.product?.satuan || 'Pcs'}</td>
            <td style="color:#666;">${item.deskripsi_produk || ''}</td>
        </tr>
    `).join('');
}
</script>
</body>
</html>
