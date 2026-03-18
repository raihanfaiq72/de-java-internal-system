<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class PdfToExcelController extends Controller
{
    public function index()
    {
        return view('PdfToExcel.index');
    }

    public function clientSide()
    {
        return view('PdfToExcel.client-side');
    }

    public function convert(Request $request)
    {
        // Increase PHP limits for large batch uploads
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);
        
        // Increase upload limits
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '2048M');
        ini_set('max_file_uploads', 5000);
        ini_set('max_multipart_body_parts', 10000); // Increase multipart limit

        $request->validate([
            'file.*' => 'required|file|mimes:pdf|max:102400', // Max 100MB per file
        ], [
            'file.*.max' => 'File PDF maksimal 100MB per file',
            'file.*.mimes' => 'Hanya file PDF yang diperbolehkan',
        ]);

        $files = $request->file('file');
        $officeId = session('active_office_id');
        $userId = auth()->id();

        if (! $files) {
            return redirect()->back()->with('error', 'Tidak ada file yang dipilih.');
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        // Limit to 5000 files
        if (count($files) > 5000) {
            return redirect()->back()->with('error', 'Maksimal 5.000 file per batch upload.');
        }

        Log::info('Starting PDF to Excel conversion: Total files = ' . count($files));

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'File Name');
        $sheet->setCellValue('C1', 'Payment No');
        $sheet->setCellValue('D1', 'Invoice No');
        $sheet->setCellValue('E1', 'Amount');
        $sheet->setCellValue('F1', 'Payment Date');
        $sheet->setCellValue('G1', 'Description');
        $sheet->setCellValue('H1', 'Status');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE5E5']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $parser = new Parser();
        $row = 2;
        $processed = 0;
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                $fileName = $file->getClientOriginalName();
                Log::info('Processing PDF ' . ($index + 1) . ': ' . $fileName);

                // Parse PDF
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();

                // Extract data using regex patterns
                $data = $this->extractPdfData($text, $fileName);

                // Add to spreadsheet
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $fileName);
                $sheet->setCellValue('C' . $row, $data['payment_no']);
                $sheet->setCellValue('D' . $row, $data['invoice_no']);
                $sheet->setCellValue('E' . $row, $data['amount']);
                $sheet->setCellValue('F' . $row, $data['payment_date']);
                $sheet->setCellValue('G' . $row, $data['description']);
                $sheet->setCellValue('H' . $row, $data['status']);

                $row++;
                $processed++;

                Log::info('Successfully processed: ' . $fileName);

            } catch (\Exception $e) {
                Log::error('Failed to process PDF: ' . $file->getClientOriginalName() . ' - ' . $e->getMessage());
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();

                // Add error row
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $file->getClientOriginalName());
                $sheet->setCellValue('C' . $row, 'ERROR');
                $sheet->setCellValue('D' . $row, '');
                $sheet->setCellValue('E' . $row, '');
                $sheet->setCellValue('F' . $row, '');
                $sheet->setCellValue('G' . $row, $e->getMessage());
                $sheet->setCellValue('H' . $row, 'FAILED');

                $row++;
            }
        }

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create summary
        $sheet->setCellValue('A' . ($row + 2), 'SUMMARY');
        $sheet->setCellValue('A' . ($row + 3), 'Total Files Processed: ' . count($files));
        $sheet->setCellValue('A' . ($row + 4), 'Successfully Converted: ' . $processed);
        $sheet->setCellValue('A' . ($row + 5), 'Errors: ' . count($errors));

        // Style summary
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5F5E5']]
        ];
        $sheet->getStyle('A' . ($row + 2) . ':A' . ($row + 5))->applyFromArray($summaryStyle);

        // Generate filename
        $excelFileName = 'PDF_Conversion_' . date('Y-m-d_H-i-s') . '.xlsx';
        $excelPath = storage_path('app/public/' . $excelFileName);

        // Save Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save($excelPath);

        Log::info('PDF to Excel conversion completed: ' . $processed . ' files processed');

        // Create download response
        return response()->download($excelPath, $excelFileName)->deleteFileAfterSend(true);
    }

    private function extractPdfData($text, $fileName)
    {
        $data = [
            'payment_no' => '',
            'invoice_no' => '',
            'amount' => '',
            'payment_date' => '',
            'description' => '',
            'status' => 'PENDING'
        ];

        // Common patterns for payment numbers
        $paymentPatterns = [
            '/Payment\s+No[:\s]*([A-Z0-9\-\/]+)/i',
            '/No\.?\s*Payment[:\s]*([A-Z0-9\-\/]+)/i',
            '/PAYMENT\s+([A-Z0-9\-\/]+)/i',
            '/PYE[_\s]*([0-9]+)/i',
            '/Receipt\s+No[:\s]*([A-Z0-9\-\/]+)/i'
        ];

        foreach ($paymentPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['payment_no'] = trim($matches[1]);
                break;
            }
        }

        // Common patterns for invoice numbers
        $invoicePatterns = [
            '/Invoice\s+No[:\s]*([A-Z0-9\-\/]+)/i',
            '/No\.?\s*Invoice[:\s]*([A-Z0-9\-\/]+)/i',
            '/INV[_\s]*([0-9]+)/i',
            '/Sales\s+Invoice[:\s]*([A-Z0-9\-\/]+)/i'
        ];

        foreach ($invoicePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['invoice_no'] = trim($matches[1]);
                break;
            }
        }

        // Extract amounts (look for currency patterns)
        $amountPatterns = [
            '/Rp\s*([\d,\.]+)/i',
            '/IDR\s*([\d,\.]+)/i',
            '/\$\s*([\d,\.]+)/',
            '/Amount[:\s]*([\d,\.]+)/i',
            '/Total[:\s]*([\d,\.]+)/i'
        ];

        foreach ($amountPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = str_replace(['.', ','], ['', ''], $matches[1]);
                if (is_numeric($amount)) {
                    $data['amount'] = (float) $amount;
                    break;
                }
            }
        }

        // Extract dates
        $datePatterns = [
            '/(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/',
            '/(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/',
            '/Date[:\s]*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i'
        ];

        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['payment_date'] = trim($matches[1]);
                break;
            }
        }

        // Set status based on data extraction
        if (!empty($data['payment_no']) || !empty($data['invoice_no'])) {
            $data['status'] = 'EXTRACTED';
        }

        // Add description (first 100 chars of text)
        $data['description'] = substr(trim($text), 0, 100) . '...';

        return $data;
    }
}
