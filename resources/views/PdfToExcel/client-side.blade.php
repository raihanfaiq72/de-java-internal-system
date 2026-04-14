@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">PDF to Excel Converter (Client-Side)</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">PDF Converter</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-laptop-code text-primary me-2"></i>
                                Convert PDF Kuitansi Penjualan ke Excel (Client-Side)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success mb-4">
                                <h6><i class="fas fa-info-circle me-2"></i>Convert PDF Kuitansi Penjualan ke Excel:</h6>
                                <ul class="mb-0">
                                    <li>📄 Upload file PDF kuitansi penjualan</li>
                                    <li>🔍 Extract data: Payment No, Invoice No, Amount, Payment Date</li>
                                    <li>📊 Generate Excel siap import ke sistem</li>
                                    <li>🚀 Proses 100% client-side (tidak perlu edit php.ini)</li>
                                    <li>🔒 Data aman, tidak terupload ke server</li>
                                </ul>
                            </div>

                            <!-- Step 1: Upload PDF -->
                            <div class="border rounded-3 p-4 bg-light mb-4">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-primary me-2">STEP 1</span>
                                    Upload File PDF Kuitansi Penjualan
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="file_pdf_client" class="form-label fw-bold">Pilih File PDF Kuitansi Penjualan</label>
                                    <input class="form-control" type="file" id="file_pdf_client" multiple required accept=".pdf" onchange="updateClientPdfCount()">
                                    <div class="form-text">
                                        <span id="client-pdf-count-info">Bisa pilih banyak file sekaligus (Tidak ada batasan!)</span>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button onclick="processClientSide()" class="btn btn-danger fw-bold text-white">
                                        <i class="fas fa-cogs me-1"></i> Convert PDF ke Excel
                                    </button>
                                </div>
                            </div>

                            <!-- Progress -->
                            <div id="client-progress" class="border rounded-3 p-4 bg-light mb-4" style="display: none;">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-warning me-2">PROCESSING</span>
                                    Convert PDF Kuitansi Penjualan ke Excel
                                </h6>
                                <div class="progress mb-3">
                                    <div id="client-progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="client-status">Mengconvert PDF kuitansi...</div>
                                <div id="client-current-file" class="small text-muted mt-2"></div>
                            </div>

                            <!-- Results -->
                            <div id="client-results" class="border rounded-3 p-4 bg-light" style="display: none;">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-success me-2">RESULTS</span>
                                    Hasil Convert PDF Kuitansi
                                </h6>
                                <div id="client-results-content"></div>
                            </div>

                            <!-- Step 2: Import Excel -->
                            <div class="border rounded-3 p-4 bg-light">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-success me-2">STEP 2</span>
                                    Import Excel ke Tab Kuitansi Penjualan
                                </h6>
                                
                                <p class="text-muted mb-3">
                                    Setelah mendownload file Excel dari Step 1, import ke sistem menggunakan tab "Import Kuitansi Penjualan (PDF)" di halaman Import.
                                </p>

                                <div class="d-grid">
                                    <a href="{{ route('import.index') }}" class="btn btn-success fw-bold text-white">
                                        <i class="fas fa-upload me-1"></i> Buka Halaman Import Kuitansi
                                    </a>
                                </div>
                            </div>

                            <!-- Libraries Info -->
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Teknologi yang Digunakan:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                                <h6 class="fw-bold">PDF.js</h6>
                                                <p class="small text-muted">PDF parsing di browser</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-excel fa-2x text-success mb-2"></i>
                                                <h6 class="fw-bold">SheetJS</h6>
                                                <p class="small text-muted">Excel generation di browser</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                                <h6 class="fw-bold">100% Client-Side</h6>
                                                <p class="small text-muted">Data tidak keluar browser</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include required libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@endsection

@push('js')
<script>
// Set PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let clientResults = [];

function updateClientPdfCount() {
    const fileInput = document.getElementById('file_pdf_client');
    const fileCountInfo = document.getElementById('client-pdf-count-info');
    const fileCount = fileInput.files.length;
    
    if (fileCount > 0) {
        fileCountInfo.textContent = `${fileCount} file dipilih (Tidak ada batasan - Client-Side!)`;
        fileCountInfo.style.color = 'green';
    } else {
        fileCountInfo.textContent = 'Bisa pilih banyak file sekaligus (Tidak ada batasan!)';
        fileCountInfo.style.color = '';
    }
    
    console.log('Client PDF Files selected:', fileCount);
}

async function extractPdfData(pdfFile) {
    try {
        // Read file secara bertahap untuk hemat memory
        const arrayBuffer = await pdfFile.arrayBuffer();
        
        // Cleanup reference untuk free memory
        const pdf = await pdfjsLib.getDocument({data: arrayBuffer}).promise;
        let fullText = '';
        
        // Bisa baca lebih banyak halaman untuk mendapatkan data lengkap
        const numPages = Math.min(pdf.numPages, 5);
        
        for (let pageNum = 1; pageNum <= numPages; pageNum++) {
            const page = await pdf.getPage(pageNum);
            const textContent = await page.getTextContent();
            const pageText = textContent.items.map(item => item.str).join(' ');
            fullText += pageText + ' ';
            
            // Cleanup page reference
            page.cleanup();
        }
        
        // Cleanup PDF reference
        pdf.destroy();
        
        // Extract data menggunakan regex yang sesuai dengan ImportReceiptJob
        const data = {
            fileName: pdfFile.name,
            paymentNo: '',
            invoiceNo: '',
            amount: '',
            paymentDate: '',
            description: fullText.substring(0, 100) + '...',
            status: 'EXTRACTED'
        };
        
        // Payment No patterns - sesuai dengan ImportReceiptJob
        const paymentPatterns = [
            /Payment No\.?\s*:?\s*([A-Z0-9\/-]+)/i,
            /No\.?\s*Payment[:\s]*([A-Z0-9\-\/]+)/i,
            /PAYMENT\s+([A-Z0-9\-\/]+)/i,
            /PYE[_\s]*([0-9]+)/i,
            /Receipt\s+No[:\s]*([A-Z0-9\-\/]+)/i
        ];
        
        for (const pattern of paymentPatterns) {
            const match = fullText.match(pattern);
            if (match) {
                data.paymentNo = match[1];
                break;
            }
        }
        
        // Invoice No patterns - sesuai dengan ImportReceiptJob
        const invoicePatterns = [
            /Invoice No\.?\s*:?\s*([A-Z0-9\/-]+)/i,
            /No\.?\s*Invoice[:\s]*([A-Z0-9\-\/]+)/i,
            /INV[_\s]*([0-9]+)/i,
            /Sales\s+Invoice[:\s]*([A-Z0-9\-\/]+)/i
        ];
        
        for (const pattern of invoicePatterns) {
            const match = fullText.match(pattern);
            if (match) {
                data.invoiceNo = match[1];
                break;
            }
        }
        
        // Amount patterns - sesuai dengan ImportReceiptJob
        const amountPatterns = [
            /Jumlah Rp\s*:?\s*([\d,.]+)/i,
            /Rp\s*([\d,\.]+)/i,
            /IDR\s*([\d,\.]+)/i,
            /\$\s*([\d,\.]+)/,
            /Amount[:\s]*([\d,\.]+)/i,
            /Total[:\s]*([\d,\.]+)/i
        ];
        
        for (const pattern of amountPatterns) {
            const match = fullText.match(pattern);
            if (match) {
                // Clean amount - sesuai dengan ImportReceiptJob logic
                const amountString = match[1];
                const cleanAmount = amountString.replace(',', ''); // Remove comma separators
                const amount = parseFloat(cleanAmount);
                if (!isNaN(amount)) {
                    data.amount = amount;
                    break;
                }
            }
        }
        
        // Date patterns - sesuai dengan ImportReceiptJob
        const datePatterns = [
            /([A-Za-z]{3}\s+\d{1,2},\s+\d{4})/, // "Jan 7, 2025" format
            /(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/,
            /(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/,
            /Date[:\s]*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i
        ];
        
        for (const pattern of datePatterns) {
            const match = fullText.match(pattern);
            if (match) {
                data.paymentDate = match[1];
                break;
            }
        }
        
        if (!data.invoiceNo) {
            data.status = 'NO_INVOICE_FOUND';
        }
        
        // Cleanup text variable
        fullText = null;
        
        return data;
        
    } catch (error) {
        console.error('Error processing PDF:', error);
        return {
            fileName: pdfFile.name,
            paymentNo: 'ERROR',
            invoiceNo: '',
            amount: '',
            paymentDate: '',
            description: error.message,
            status: 'FAILED'
        };
    }
}

async function processClientSide() {
    const fileInput = document.getElementById('file_pdf_client');
    const files = Array.from(fileInput.files);
    
    if (files.length === 0) {
        alert('Silakan pilih file PDF terlebih dahulu.');
        return;
    }
    
    // Show progress
    document.getElementById('client-progress').style.display = 'block';
    document.getElementById('client-results').style.display = 'none';
    
    clientResults = [];
    const totalFiles = files.length;
    
    // Sequential processing - satu file per satu
    for (let i = 0; i < totalFiles; i++) {
        const file = files[i];
        const progress = Math.round(((i + 1) / totalFiles) * 100);
        
        // Update progress
        document.getElementById('client-progress-bar').style.width = progress + '%';
        document.getElementById('client-status').textContent = `Mengconvert ${i + 1} dari ${totalFiles} file PDF kuitansi...`;
        document.getElementById('client-current-file').textContent = `Sedang diproses: ${file.name}`;
        
        try {
            // Process satu file saja
            const data = await extractPdfData(file);
            clientResults.push(data);
            
            console.log(`✅ Processed: ${file.name} - Status: ${data.status}`);
            
        } catch (error) {
            console.error(`❌ Error processing ${file.name}:`, error);
            clientResults.push({
                fileName: file.name,
                paymentNo: 'ERROR',
                invoiceNo: '',
                amount: '',
                paymentDate: '',
                description: error.message,
                status: 'FAILED'
            });
        }
        
        // Force garbage collection untuk free memory
        if (i % 50 === 0) {
            await new Promise(resolve => setTimeout(resolve, 500));
            if (window.gc) window.gc(); // Force GC jika available
        }
    }
    
    // Generate Excel setelah semua selesai
    document.getElementById('client-status').textContent = 'Membuat file Excel dari data kuitansi...';
    document.getElementById('client-current-file').textContent = 'Generate worksheet: Payment No, Invoice No, Amount, Payment Date...';
    
    await new Promise(resolve => setTimeout(resolve, 1000));
    generateClientExcel();
}

function generateClientExcel() {
    document.getElementById('client-status').textContent = 'Generating Excel file...';
    
    // Create workbook
    const wb = XLSX.utils.book_new();
    
    // Create worksheet data - Format sesuai import kuitansi PDF
    const wsData = [
        ['Payment No', 'Invoice No', 'Amount', 'Payment Date', 'Description', 'Status']
    ];
    
    clientResults.forEach((data, index) => {
        wsData.push([
            data.paymentNo,
            data.invoiceNo,
            data.amount,
            data.paymentDate,
            data.description,
            data.status
        ]);
    });
    
    // Add summary
    wsData.push([]);
    wsData.push(['SUMMARY']);
    wsData.push(['Total Files Processed', clientResults.length]);
    wsData.push(['Successfully Extracted', clientResults.filter(r => r.status === 'EXTRACTED').length]);
    wsData.push(['No Invoice Found', clientResults.filter(r => r.status === 'NO_INVOICE_FOUND').length]);
    wsData.push(['Errors', clientResults.filter(r => r.status === 'FAILED').length]);
    
    // Create worksheet
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    
    // Style headers
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let C = range.s.c; C <= range.e.c; ++C) {
        const address = XLSX.utils.encode_col(C) + '1';
        if (!ws[address]) continue;
        ws[address].s = {
            font: { bold: true },
            fill: { fgColor: { rgb: "FFFF0000" } },
            alignment: { horizontal: "center" }
        };
    }
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, "PDF Receipt Data");
    
    // Generate file
    const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    
    // Download
    const fileName = `PDF_Receipt_Data_${new Date().toISOString().split('T')[0]}.xlsx`;
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    // Show results
    showClientResults();
}

function showClientResults() {
    document.getElementById('client-progress').style.display = 'none';
    document.getElementById('client-results').style.display = 'block';
    
    const successCount = clientResults.filter(r => r.status === 'EXTRACTED').length;
    const errorCount = clientResults.filter(r => r.status === 'FAILED').length;
    const noInvoiceCount = clientResults.filter(r => r.status === 'NO_INVOICE_FOUND').length;
    
    const resultsHtml = `
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h4 class="text-success">${clientResults.length}</h4>
                        <small>Total Files</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h4 class="text-success">${successCount}</h4>
                        <small>Success</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h4 class="text-warning">${noInvoiceCount}</h4>
                        <small>No Invoice</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h4 class="text-danger">${errorCount}</h4>
                        <small>Errors</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <h6>Excel file berhasil di-generate dan di-download!</h6>
            <p class="mb-0">File: <strong>PDF_Receipt_Data_${new Date().toISOString().split('T')[0]}.xlsx</strong></p>
            <p class="mb-0">Format: <strong>Payment No | Invoice No | Amount | Payment Date | Description | Status</strong></p>
        </div>
        
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i>Cara Import:</h6>
            <ol class="mb-0">
                <li>Buka halaman <a href="{{ route('import.index') }}" target="_blank">Import Data</a></li>
                <li>Pilih tab "Import Kuitansi Penjualan (PDF)"</li>
                <li>Upload file Excel yang baru di-download</li>
                <li>System akan memproses data dan membuat payment records</li>
            </ol>
        </div>
        
        <div class="d-grid">
            <a href="{{ route('import.index') }}" class="btn btn-success fw-bold text-white">
                <i class="fas fa-upload me-1"></i> Lanjut Import ke Sistem
            </a>
        </div>
    `;
    
    document.getElementById('client-results-content').innerHTML = resultsHtml;
}
</script>
@endpush
