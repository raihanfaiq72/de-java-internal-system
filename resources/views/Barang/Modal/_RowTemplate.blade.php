<template id="massal-row-template">
    <tr>
        <td class="text-center align-middle">
            <span class="row-number"></span>
        </td>
        <td class="in-sku-wrap">
            <input type="text" class="form-control form-control-sm in-sku" placeholder="Auto generate" disabled>
        </td>
        <td>
            <select class="form-select form-select-sm in-supplier">
                <option value="">-- Pilih --</option>
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm in-brand">
                <option value="">-- Pilih --</option>
            </select>
        </td>
        <td class="in-nama-produk-wrap">
            <input type="text" class="form-control form-control-sm required-field in-nama" placeholder="Nama produk wajib diisi">
        </td>
        <td>
            <select class="form-select form-select-sm in-kategori">
                <option value="">-- Pilih --</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-kemasan" placeholder="Contoh: Box, Botol">
        </td>
        <td>
            <select class="form-select form-select-sm in-unit">
                <option value="">-- Pilih --</option>
            </select>
        </td>
        <td class="in-qty-wrap">
            <input type="number" class="form-control form-control-sm in-qty" placeholder="0" value="0" min="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-beli" placeholder="0" value="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-jual" placeholder="0" value="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-tempo" placeholder="0" value="0">
        </td>
        <td>
            <select class="form-select form-select-sm in-coa">
                <option value="">-- Pilih --</option>
            </select>
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn-premium btn-premium-delete" title="Hapus Baris">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>
