@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">PDF to Excel Converter</h4>
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
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                Konversi PDF ke Excel
                                <div class="btn-group float-end" role="group">
                                    <a href="{{ route('pdf.to.excel') }}" class="btn btn-sm btn-outline-primary active">
                                        <i class="fas fa-server me-1"></i> Server-Side
                                    </a>
                                    <a href="{{ route('pdf.to.excel.client') }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-laptop-code me-1"></i> Client-Side
                                    </a>
                                </div>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Step 1: Upload PDF -->
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-4 bg-light">
                                        <h6 class="fw-bold mb-3">
                                            <span class="badge bg-primary me-2">STEP 1</span>
                                            Upload File PDF
                                        </h6>
                                        
                                        <form action="{{ route('pdf.to.excel.convert') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file_pdf" class="form-label fw-bold">Pilih File PDF Kuitansi</label>
                                                <input class="form-control" type="file" id="file_pdf" name="file[]" multiple required accept=".pdf" onchange="updatePdfCount()">
                                                <div class="form-text">
                                                    <span id="pdf-count-info">Bisa pilih banyak file sekaligus (Max 5.000 file per upload).</span>
                                                </div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-danger fw-bold text-white">
                                                    <i class="fas fa-file-excel me-1"></i> Konversi ke Excel
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-3 alert alert-info small">
                                            <strong>Hasil Konversi:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Payment No (No. Pembayaran)</li>
                                                <li>Invoice No (No. Invoice)</li>
                                                <li>Amount (Jumlah)</li>
                                                <li>Payment Date (Tanggal)</li>
                                                <li>Description (Deskripsi)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Import Excel -->
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-4 bg-light">
                                        <h6 class="fw-bold mb-3">
                                            <span class="badge bg-success me-2">STEP 2</span>
                                            Import Excel ke Sistem
                                        </h6>
                                        
                                        <p class="text-muted mb-3">
                                            Setelah mendownload file Excel dari Step 1, Anda bisa mengedit data jika perlu, kemudian import ke sistem menggunakan halaman Import.
                                        </p>

                                        <div class="d-grid">
                                            <a href="{{ route('import.index') }}" class="btn btn-success fw-bold text-white">
                                                <i class="fas fa-upload me-1"></i> Buka Halaman Import
                                            </a>
                                        </div>

                                        <div class="mt-3 alert alert-success small">
                                            <strong>Keuntungan:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>✅ Bisa edit data sebelum import</li>
                                                <li>✅ Validasi data lebih mudah</li>
                                                <li>✅ Tidak ada batasan file</li>
                                                <li>✅ Proses lebih reliable</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Process Flow -->
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Alur Proses:</h6>
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-file-pdf fa-lg"></i>
                                        </div>
                                        <p class="mb-0 mt-2 small fw-bold">Upload PDF</p>
                                    </div>
                                    <div class="mx-3">
                                        <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-cogs fa-lg"></i>
                                        </div>
                                        <p class="mb-0 mt-2 small fw-bold">Convert</p>
                                    </div>
                                    <div class="mx-3">
                                        <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-file-excel fa-lg"></i>
                                        </div>
                                        <p class="mb-0 mt-2 small fw-bold">Download Excel</p>
                                    </div>
                                    <div class="mx-3">
                                        <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-database fa-lg"></i>
                                        </div>
                                        <p class="mb-0 mt-2 small fw-bold">Import ke Sistem</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Fitur Konversi:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="fas fa-bolt fa-2x text-warning mb-2"></i>
                                                <h6 class="fw-bold">Cepat</h6>
                                                <p class="small text-muted">Konversi ribuan PDF dalam hitungan menit</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="fas fa-robot fa-2x text-primary mb-2"></i>
                                                <h6 class="fw-bold">Otomatis</h6>
                                                <p class="small text-muted">Ekstrak data Payment No, Invoice, Amount otomatis</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                                <h6 class="fw-bold">Aman</h6>
                                                <p class="small text-muted">Validasi data dan error handling yang baik</p>
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
@endsection

@push('js')
<script>
function updatePdfCount() {
    const fileInput = document.getElementById('file_pdf');
    const fileCountInfo = document.getElementById('pdf-count-info');
    const fileCount = fileInput.files.length;
    
    if (fileCount > 0) {
        fileCountInfo.textContent = `${fileCount} file dipilih (Max 5.000 file per upload).`;
        
        if (fileCount > 5000) {
            fileCountInfo.textContent = `⚠️ ${fileCount} file dipilih - Melebihi batas maksimal 5.000 file!`;
            fileCountInfo.style.color = 'red';
        } else if (fileCount > 2000) {
            fileCountInfo.style.color = 'orange';
        } else {
            fileCountInfo.style.color = 'green';
        }
    } else {
        fileCountInfo.textContent = 'Bisa pilih banyak file sekaligus (Max 5.000 file per upload).';
        fileCountInfo.style.color = '';
    }
    
    console.log('PDF Files selected:', fileCount);
    console.log('Files:', fileInput.files);
}

// Batch upload function for large file counts
function uploadInBatches() {
    const fileInput = document.getElementById('file_pdf');
    const files = Array.from(fileInput.files);
    const batchSize = 500; // Process 500 files at a time to avoid multipart limit
    const totalBatches = Math.ceil(files.length / batchSize);
    
    if (files.length <= 1000) {
        // Use normal form for small batches
        document.querySelector('form[action*="pdf.to.excel"]').submit();
        return;
    }
    
    // Show progress
    const progressHtml = `
        <div class="alert alert-info">
            <h6>Processing ${files.length} files in ${totalBatches} batches...</h6>
            <div class="progress mb-2">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <div class="batch-info">Starting batch 1 of ${totalBatches}...</div>
        </div>
    `;
    
    const form = document.querySelector('form[action*="pdf.to.excel"]');
    form.insertAdjacentHTML('beforebegin', progressHtml);
    
    let currentBatch = 0;
    let processedFiles = 0;
    
    function processBatch() {
        if (currentBatch >= totalBatches) {
            // All batches processed
            document.querySelector('.alert-info').innerHTML = `
                <h6>✅ All ${files.length} files processed!</h6>
                <p>Download links for each batch will appear below.</p>
            `;
            return;
        }
        
        const startIdx = currentBatch * batchSize;
        const endIdx = Math.min(startIdx + batchSize, files.length);
        const batchFiles = files.slice(startIdx, endIdx);
        
        // Update progress
        const progress = Math.round((processedFiles / files.length) * 100);
        document.querySelector('.progress-bar').style.width = progress + '%';
        document.querySelector('.batch-info').textContent = `Processing batch ${currentBatch + 1} of ${totalBatches} (${batchFiles.length} files)...`;
        
        // Create FormData for this batch
        const formData = new FormData();
        batchFiles.forEach(file => {
            formData.append('file[]', file);
        });
        
        // Add CSRF token
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
        
        // Send batch
        fetch('/pdf-to-excel/convert', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Batch upload failed');
        })
        .then(blob => {
            // Create download link for this batch
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `PDF_Conversion_Batch_${currentBatch + 1}_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.querySelector('.alert-info').insertAdjacentHTML('beforeend', 
                `<p><a href="${url}" download="${a.download}" class="btn btn-success btn-sm">Download Batch ${currentBatch + 1}</a></p>`
            );
            
            processedFiles += batchFiles.length;
            currentBatch++;
            
            // Process next batch
            setTimeout(processBatch, 1000);
        })
        .catch(error => {
            console.error('Batch error:', error);
            document.querySelector('.batch-info').textContent = `Error in batch ${currentBatch + 1}: ${error.message}`;
            currentBatch++;
            setTimeout(processBatch, 2000);
        });
    }
    
    processBatch();
}

// Modify form submission for large files
document.querySelector('form[action*="pdf.to.excel"]').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('file_pdf');
    const fileCount = fileInput.files.length;
    
    if (fileCount > 1000) {
        e.preventDefault();
        if (confirm(`You have ${fileCount} files. This will be processed in ${Math.ceil(fileCount / 500)} batches. Continue?`)) {
            uploadInBatches();
        }
    }
});
</script>
@endpush
