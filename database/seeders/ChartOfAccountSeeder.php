<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    public function run()
    {
        $baseAccounts = [
            ['1000', 'Aktiva', 'Aktiva', 'Aktiva', false],
            
            ['1101', 'Kas', 'Aktiva', 'Kas', true],
            
            ['1201', 'Bank', 'Aktiva', 'Bank', true],
            
            ['1251', 'Rekening Bersama Digital Payment Paper.id', 'Aktiva', 'Rekening Bersama Digital Payment Paper.id', false],
            
            ['1301', 'Piutang Usaha', 'Aktiva', 'Piutang Usaha', false],
            
            ['1501', 'Persediaan', 'Aktiva', 'Persediaan', false],
            ['1502', 'Persediaan dalam Perjalanan', 'Aktiva', 'Persediaan', false],
            ['1503', 'Persediaan Konsinyasi', 'Aktiva', 'Persediaan', false],
            
            ['1701', 'PPN Masukan', 'Aktiva', 'Pajak Dibayar Di Muka', false],
            ['1702', 'PPH Pasal 23 Dibayar Dimuka', 'Aktiva', 'Pajak Dibayar Di Muka', false],
            
            ['1801', 'Bangunan', 'Aktiva', 'Aktiva Tetap', false],
            ['1802', 'Peralatan', 'Aktiva', 'Aktiva Tetap', false],
            ['1803', 'Kendaraan', 'Aktiva', 'Aktiva Tetap', false],

            ['1851', 'Akumulasi Penyusutan Bangunan', 'Aktiva', 'Akumulasi Penyusutan', false],
            ['1852', 'Akumulasi Penyusutan Peralatan', 'Aktiva', 'Akumulasi Penyusutan', false],
            ['1853', 'Akumulasi Penyusutan Kendaraan', 'Aktiva', 'Akumulasi Penyusutan', false],

            ['2000', 'Kewajiban', 'Kewajiban', 'Kewajiban', false],
            
            ['2101', 'Hutang Usaha', 'Kewajiban', 'Hutang Usaha', false],

            ['2201', 'Pendapatan Diterima Di Muka', 'Kewajiban', 'Hutang Non-Usaha', false],
            ['2203', 'Penjualan dimuka in transit', 'Kewajiban', 'Hutang Non-Usaha', false],

            ['2301', 'Hutang PPH Pasal 21', 'Kewajiban', 'Hutang Pajak', false],
            ['2302', 'Hutang PPH Pasal 22', 'Kewajiban', 'Hutang Pajak', false],
            ['2303', 'Hutang PPH Pasal 4(2)', 'Kewajiban', 'Hutang Pajak', false],
            ['2304', 'Hutang PPH Pasal 25', 'Kewajiban', 'Hutang Pajak', false],
            ['2305', 'PPN Keluaran', 'Kewajiban', 'Hutang Pajak', false],
            ['2306', 'Hutang PPN', 'Kewajiban', 'Hutang Pajak', false],

            ['2401', 'Hutang Bank', 'Kewajiban', 'Kewajiban Jangka Panjang', false],

            ['3000', 'Modal', 'Modal', 'Modal', false],

            ['3101', 'Modal Disetor', 'Modal', 'Modal', false],
            ['3301', 'Dividen', 'Modal', 'Modal', false],
            ['3991', 'Saldo Ekuitas Awal', 'Modal', 'Modal', false],

            ['3201', 'Laba Ditahan', 'Modal', 'Laba', false],
            ['3202', 'Laba Tahun Berjalan', 'Modal', 'Laba', false],

            ['4000', 'Pendapatan', 'Pendapatan', 'Pendapatan', false],

            ['4101', 'Penjualan Umum', 'Pendapatan', 'Pendapatan', false],
            ['4102', 'Pendapatan Jasa', 'Pendapatan', 'Pendapatan', false],
            ['4103', 'Penjualan Produk', 'Pendapatan', 'Pendapatan', false],
            ['4110', 'Pendapatan Pengiriman', 'Pendapatan', 'Pendapatan', false],
            ['4201', 'Diskon Penjualan', 'Pendapatan', 'Pendapatan', false],
            ['4301', 'Retur Penjualan', 'Pendapatan', 'Pendapatan', false],

            ['5000', 'Beban Pokok', 'Beban Pokok', 'Beban Pokok', false],

            ['5101', 'Harga Pokok Penjualan', 'Beban Pokok', 'Harga Pokok Penjualan', false],
            ['5102', 'Beban Pengiriman', 'Beban Pokok', 'Harga Pokok Penjualan', false],
            ['5103', 'Beban Pembelian', 'Beban Pokok', 'Harga Pokok Penjualan', false],
            ['5201', 'Diskon Pembelian', 'Beban Pokok', 'Harga Pokok Penjualan', false],
            ['5301', 'Retur Pembelian', 'Beban Pokok', 'Harga Pokok Penjualan', false],

            ['6000', 'Beban Operasional', 'Beban', 'Beban', false],

            ['6101', 'Beban Gaji Operasional', 'Beban', 'Beban Operasional', false],
            ['6102', 'Beban Gaji Administrasi', 'Beban', 'Beban Operasional', false],
            ['6121', 'Biaya Pencairan Digital Payment', 'Beban', 'Beban Operasional', false],
            ['6199', 'Biaya Pembayaran Keluar', 'Beban', 'Beban Operasional', false],
            ['6201', 'Beban Listrik dan Air', 'Beban', 'Beban Operasional', false],
            ['6202', 'Beban Kendaraan dan Transportasi', 'Beban', 'Beban Operasional', false],
            ['6203', 'Beban Komunikasi', 'Beban', 'Beban Operasional', false],
            ['6204', 'Beban Perlengkapan Kantor', 'Beban', 'Beban Operasional', false],
            ['6301', 'Beban Komisi Penjualan', 'Beban', 'Beban Operasional', false],
            ['6302', 'Beban Entertainment', 'Beban', 'Beban Operasional', false],
            ['6303', 'Beban Iklan dan Promosi', 'Beban', 'Beban Operasional', false],
            ['6401', 'Beban Perbaikan dan Pemeliharaan', 'Beban', 'Beban Operasional', false],
            ['6501', 'Beban Sewa', 'Beban', 'Beban Operasional', false],
            ['6502', 'Beban Asuransi', 'Beban', 'Beban Operasional', false],
            ['6601', 'Beban Penyesuaian Persediaan', 'Beban', 'Beban Operasional', false],
            ['6602', 'Beban Cacat Produksi', 'Beban', 'Beban Operasional', false],
            ['6701', 'Beban Perijinan dan Lisensi', 'Beban', 'Beban Operasional', false],
            ['6901', 'Beban Piutang Tak Tertagih', 'Beban', 'Beban Operasional', false],

            ['6800', 'Beban Penyusutan', 'Beban Penyusutan', 'Beban Penyusutan', false],

            ['6801', 'Beban Penyusutan Bangunan', 'Beban Penyusutan', 'Beban Penyusutan', false],
            ['6802', 'Beban Penyusutan Peralatan', 'Beban Penyusutan', 'Beban Penyusutan', false],
            ['6803', 'Beban Penyusutan Kendaraan', 'Beban Penyusutan', 'Beban Penyusutan', false],
            
            ['7000', 'Pendapatan Lain', 'Pendapatan Lain', 'Pendapatan Lain', false],

            ['7001', 'Pendapatan Lain-lain', 'Pendapatan Lain', 'Pendapatan Lain-lain', false],
            ['7101', 'Pendapatan Bunga', 'Pendapatan Lain', 'Pendapatan Lain-lain', false],
            ['7120', 'Keuntungan dari Selisih Kurs', 'Pendapatan Lain', 'Pendapatan Lain-lain', false],

            ['7201', 'Keuntungan Dari Penjualan Aktiva Tetap', 'Pendapatan Lain', 'Keuntungan dari Penjualan Aktiva', false],

            ['8000', 'Beban Lain', 'Beban Lain', 'Beban Lain', false],

            ['8001', 'Beban Lain-lain', 'Beban Lain', 'Beban Lain-lain', false],
            ['8101', 'Beban Bunga', 'Beban Lain', 'Beban Lain-lain', false],
            ['8102', 'Beban Administrasi Bank', 'Beban Lain', 'Beban Lain-lain', false],
            ['8120', 'Kerugian Selisih Kurs', 'Beban Lain', 'Beban Lain-lain', false],

            ['8201', 'Kerugian Dari Penjualan Aktiva Tetap', 'Beban Lain', 'Kerugian Penjualan Aktiva', false],

            ['90000', 'Beban Pajak', 'Beban Pajak', 'Beban Pajak', false],

            ['9000', 'Beban Pajak Penghasilan', 'Beban Pajak', 'Beban Pajak', false],

        ];

        $data = [];
        foreach ($baseAccounts as $acc) {
            $data[] = [
                'office_id' => 1,
                'kode_akun' => $acc[0],
                'nama_akun' => $acc[1],
                'kelompok_akun' => $acc[2],
                'tipe_akun' => $acc[3],
                'is_kas_bank' => $acc[4],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('chart_of_accounts')->insert($chunk);
        }
    }
}