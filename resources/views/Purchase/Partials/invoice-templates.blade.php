<template id="row-template">
    <tr class="clickable-row">
        <td class="ps-3" onclick="event.stopPropagation(); updateSelection()">
            <input type="checkbox" class="form-check-input invoice-checkbox">
        </td>
        <td class="text-center small text-muted">
            <span class="row-index"></span>
        </td>
        <td>
            <div class="fw-bold text-dark row-nomor" style="font-size: 13px;"></div>
            <div class="small text-muted row-ref" style="font-size: 11px;"></div>
        </td>
        <td>
            <div class="fw-semibold text-truncate row-mitra" style="max-width: 180px;"></div>
            <div class="small text-muted row-mitra-no" style="font-size: 11px;"></div>
        </td>
        <td class="text-center">
            <div class="row-status-dok-badge"></div>
            <div class="mt-1"><span class="f-badge row-status-bayar-badge"></span></div>
        </td>
        <td class="text-end fw-bold f-mono text-dark row-total"></td>
        <td class="text-end fw-bold f-mono row-terhutang"></td>
        <td class="text-center">
            <div class="text-dark fw-medium small row-tgl"></div>
            <div class="text-danger row-jatuh-tempo" style="font-size: 10px;"></div>
        </td>
        <td class="text-end pe-3" onclick="event.stopPropagation()">
            <div class="dropdown">
                <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-muted" data-bs-toggle="dropdown">
                    <i class="fa fa-ellipsis-h"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-finance">
                    <li>
                        <a class="dropdown-item btn-show" href="javascript:void(0)">
                            <i class="fa fa-eye text-warning me-2"></i> Lihat Data
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-edit" href="javascript:void(0)"><i
                                class="fa fa-pencil text-primary me-2"></i> Edit Data</a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-send-invoice" href="javascript:void(0)">
                            <i class="fa fa-paper-plane text-success me-2"></i> Kirim Invoice
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-create-receipt" href="javascript:void(0)">
                            <i class="fa fa-receipt text-dark me-2"></i> Buat Kuitansi
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-print" href=""><i class="fa fa-print text-dark me-2"></i> Cetak
                            Nota</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)"><i
                                class="fa fa-trash me-2"></i> Hapus</a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
</template>

<template id="item-row-template">
    <tr style="border-bottom: 1px dashed #e2e8f0;">
        <td class="py-2 text-muted text-center item-index" style="width: 40px;"></td>
        <td class="py-2 fw-medium item-nama"></td>
        <td class="py-2 text-center item-qty"></td>
        <td class="py-2 text-end f-mono text-muted item-harga"></td>
        <td class="py-2 text-end fw-bold f-mono item-total"></td>
    </tr>
</template>