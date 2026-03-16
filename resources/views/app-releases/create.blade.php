@extends('Layout.main')

@section('title', 'Create App Release')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="page-title fw-bold">
                                    <i class="fa fa-plus me-2"></i>Create App Release
                                </h4>
                                <p class="text-muted mb-0 small">Upload new version of DeJava Mobile app</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('app-releases.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('app-releases.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="version" class="form-label">Version <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                                       id="version" name="version" value="{{ old('version') }}" 
                                                       placeholder="e.g., 1.0.0" required>
                                                @error('version')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="platform" class="form-label">Platform <span class="text-danger">*</span></label>
                                                <select class="form-select @error('platform') is-invalid @enderror" 
                                                        id="platform" name="platform" required>
                                                    <option value="">Select Platform</option>
                                                    <option value="android" {{ old('platform') == 'android' ? 'selected' : '' }}>Android</option>
                                                    <option value="ios" {{ old('platform') == 'ios' ? 'selected' : '' }}>iOS</option>
                                                </select>
                                                @error('platform')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="build_number" class="form-label">Build Number</label>
                                                <input type="text" class="form-control @error('build_number') is-invalid @enderror" 
                                                       id="build_number" name="build_number" value="{{ old('build_number') }}" 
                                                       placeholder="e.g., 100">
                                                @error('build_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="release_date" class="form-label">Release Date</label>
                                                <input type="date" class="form-control @error('release_date') is-invalid @enderror" 
                                                       id="release_date" name="release_date" value="{{ old('release_date') ?? now()->format('Y-m-d') }}">
                                                @error('release_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="3" placeholder="Describe this release...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="file" class="form-label">App File <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                               id="file" name="file" accept=".apk,.ipa" required>
                                        <div class="form-text">
                                            Supported formats: .apk for Android, .ipa for iOS. Max size: 50MB
                                        </div>
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="changelog" class="form-label">Changelog</label>
                                        <div id="changelog-container">
                                            <div class="changelog-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="changelog[]" placeholder="New feature or bug fix...">
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeChangelogItem(this)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addChangelogItem()">
                                            <i class="fa fa-plus me-1"></i> Add Item
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" 
                                                        id="status" name="status" required>
                                                    <option value="">Select Status</option>
                                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input @error('is_force_update') is-invalid @enderror" 
                                                           type="checkbox" id="is_force_update" name="is_force_update" value="1">
                                                    <label class="form-check-label" for="is_force_update">
                                                        Force Update
                                                    </label>
                                                    <div class="form-text">
                                                        Users will be required to update to this version
                                                    </div>
                                                    @error('is_force_update')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('app-releases.index') }}" class="btn btn-secondary">
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save me-1"></i> Create Release
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fa fa-info-circle me-2"></i>Release Guidelines
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>Version Format</h6>
                                <p class="text-muted small">Use semantic versioning: MAJOR.MINOR.PATCH (e.g., 1.0.0)</p>
                                
                                <h6>Platform Support</h6>
                                <p class="text-muted small">Android (.apk) and iOS (.ipa) files are supported</p>
                                
                                <h6>Status Options</h6>
                                <ul class="small text-muted">
                                    <li><strong>Draft:</strong> Not visible to users</li>
                                    <li><strong>Published:</strong> Available for download</li>
                                    <li><strong>Archived:</strong> No longer available</li>
                                </ul>
                                
                                <h6>Force Update</h6>
                                <p class="text-muted small">When enabled, users must update to this version</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function addChangelogItem() {
            const container = document.getElementById('changelog-container');
            const newItem = document.createElement('div');
            newItem.className = 'changelog-item mb-2';
            newItem.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="changelog[]" placeholder="New feature or bug fix...">
                    <button type="button" class="btn btn-outline-danger" onclick="removeChangelogItem(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
        }

        function removeChangelogItem(button) {
            const container = document.getElementById('changelog-container');
            if (container.children.length > 1) {
                button.closest('.changelog-item').remove();
            }
        }
    </script>
@endsection
