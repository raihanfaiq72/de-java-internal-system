<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // AKTIVA (1000)
            ['1000', 'Aktiva', 'Aktiva', false],
            ['1101', 'Kas', 'Aktiva', true],
            ['1201', 'Bank', 'Aktiva', true],
            ['1251', 'Rekening Bersama Digital Payment Paper.id', 'Aktiva', true],
            ['1301', 'Piutang Usaha', 'Aktiva', false],
            ['1501', 'Persediaan', 'Aktiva', false],
            ['1502', 'Persediaan dalam Perjalanan', 'Aktiva', false],
            ['1503', 'Persediaan Konsinyasi', 'Aktiva', false],
            ['1701', 'PPN Masukan', 'Aktiva', false],
            ['1702', 'PPH Pasal 23 Dibayar Dimuka', 'Aktiva', false],
            ['1801', 'Bangunan', 'Aktiva', false],
            ['1802', 'Peralatan', 'Aktiva', false],
            ['1803', 'Kendaraan', 'Aktiva', false],
            ['1851', 'Akumulasi Penyusutan Bangunan', 'Aktiva', false],
            ['1852', 'Akumulasi Penyusutan Peralatan', 'Aktiva', false],
            ['1853', 'Akumulasi Penyusutan Kendaraan', 'Aktiva', false],

            // KEWAJIBAN (2000)
            ['2000', 'Kewajiban', 'Kewajiban', false],
            ['2101', 'Hutang Usaha', 'Kewajiban', false],
            ['2201', 'Pendapatan Diterima Di Muka', 'Kewajiban', false],
            ['2203', 'Penjualan dimuka in transit', 'Kewajiban', false],
            ['2301', 'Hutang PPH Pasal 21', 'Kewajiban', false],
            ['2302', 'Hutang PPH Pasal 23', 'Kewajiban', false],
            ['2303', 'Hutang PPH Pasal 4(2)', 'Kewajiban', false],
            ['2304', 'Hutang PPH Pasal 25', 'Kewajiban', false],
            ['2305', 'PPN Keluaran', 'Kewajiban', false],
            ['2306', 'Hutang PPN', 'Kewajiban', false],
            ['2401', 'Hutang Bank', 'Kewajiban', false],

            // MODAL (3000)
            ['3000', 'Modal', 'Modal', false],
            ['3101', 'Modal Disetor', 'Modal', false],
            ['3201', 'Laba Ditahan', 'Modal', false],
            ['3202', 'Laba Tahun Berjalan', 'Modal', false],
            ['3301', 'Dividen', 'Modal', false],
            ['3991', 'Saldo Ekuitas Awal', 'Modal', false],

            // PENDAPATAN (4000 & 7000)
            ['4000', 'Pendapatan', 'Pendapatan', false],
            ['4101', 'Penjualan Umum', 'Pendapatan', false],
            ['4102', 'Pendapatan Jasa', 'Pendapatan', false],
            ['4103', 'Penjualan Produk', 'Pendapatan', false],
            ['4110', 'Pendapatan Pengiriman', 'Pendapatan', false],
            ['4201', 'Diskon Penjualan', 'Pendapatan', false],
            ['4301', 'Retur Penjualan', 'Pendapatan', false],
            ['7001', 'Pendapatan Lain-lain', 'Pendapatan', false],
            ['7101', 'Pendapatan Bunga', 'Pendapatan', false],
            ['7120', 'Keuntungan dari Selisih Kurs', 'Pendapatan', false],
            ['7201', 'Keuntungan Dari Penjualan Aktiva Tetap', 'Pendapatan', false],

            // HARGA POKOK PENJUALAN & BEBAN (5000, 6000, 8000, 9000)
            ['5000', 'Harga Pokok Penjualan', 'Beban', false],
            ['5101', 'Harga Pokok Penjualan', 'Beban', false],
            ['5102', 'Beban Pengiriman', 'Beban', false],
            ['5103', 'Beban Pembelian', 'Beban', false],
            ['5201', 'Diskon Pembelian', 'Beban', false],
            ['5301', 'Retur Pembelian', 'Beban', false],
            ['6000', 'Beban', 'Beban', false],
            ['6101', 'Beban Gaji Operasional', 'Beban', false],
            ['6102', 'Beban Gaji Administrasi', 'Beban', false],
            ['6121', 'Biaya Pencairan Digital Payment', 'Beban', false],
            ['6199', 'Biaya Pembayaran Keluar', 'Beban', false],
            ['6201', 'Beban Listrik dan Air', 'Beban', false],
            ['6202', 'Beban Kendaraan dan Transportasi', 'Beban', false],
            ['6203', 'Beban Komunikasi', 'Beban', false],
            ['6204', 'Beban Perlengkapan Kantor', 'Beban', false],
            ['6301', 'Beban Komisi Penjualan', 'Beban', false],
            ['6302', 'Beban Entertainment', 'Beban', false],
            ['6303', 'Beban Iklan dan Promosi', 'Beban', false],
            ['6401', 'Beban Perbaikan dan Pemeliharaan', 'Beban', false],
            ['6501', 'Beban Sewa', 'Beban', false],
            ['6502', 'Beban Asuransi', 'Beban', false],
            ['6601', 'Beban Penyesuaian Persediaan', 'Beban', false],
            ['6602', 'Beban Cacat Produksi', 'Beban', false],
            ['6701', 'Beban Perijinan dan Lisensi', 'Beban', false],
            ['6801', 'Beban Penyusutan Bangunan', 'Beban', false],
            ['6802', 'Beban Penyusutan Peralatan', 'Beban', false],
            ['6803', 'Beban Penyusutan Kendaraan', 'Beban', false],
            ['6901', 'Beban Piutang Tak Tertagih', 'Beban', false],
            ['8001', 'Beban Lain-lain', 'Beban', false],
            ['8101', 'Beban Bunga', 'Beban', false],
            ['8102', 'Beban Administrasi Bank', 'Beban', false],
            ['8120', 'Kerugian Selisih Kurs', 'Beban', false],
            ['8201', 'Kerugian Dari Penjualan Aktiva Tetap', 'Beban', false],
            ['9000', 'Beban Pajak Penghasilan', 'Beban', false],
        ];

        foreach ($accounts as $account) {
            DB::table('chart_of_accounts')->updateOrInsert(
                ['kode_akun' => $account[0]],
                [
                    'nama_akun' => $account[1],
                    'kelompok_akun' => $account[2],
                    'is_kas_bank' => $account[3],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}