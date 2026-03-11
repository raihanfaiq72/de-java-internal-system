@extends('Layout.main')

@section('title', 'Edit Profil')

@section('main')

<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Profil Saya</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <div id="avatarContainer">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 120px; height: 120px; font-size: 48px;">
                                    <span id="avatarInitial">-</span>
                                </div>
                            </div>
                            <label for="avatar" class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow cursor-pointer" style="cursor: pointer;">
                                <i class="iconoir-camera text-primary"></i>
                            </label>
                            <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-muted mt-2 small">Klik ikon kamera untuk mengganti foto</p>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Ganti Password (Opsional)</h5>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
        </div>

    </div>

</div>

@push('js')
<script>
    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Find the image element or create a new one if it was an initial div
                var img = document.getElementById('avatarContainer').querySelector('img');
                var initialDiv = document.getElementById('avatarContainer').querySelector('div.rounded-circle');
                
                if (img) {
                    img.src = e.target.result;
                } else if (initialDiv) {
                    // Replace div with img
                    img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded-circle img-thumbnail';
                    img.style.width = '120px';
                    img.style.height = '120px';
                    img.style.objectFit = 'cover';
                    initialDiv.replaceWith(img);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', async function () {
        try {
            const res = await fetch("{{ route('profile-api.me') }}", { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!json.success) return;
            const u = json.data || {};
            const name = u.name || '';
            const email = u.email || '';

            if (!document.getElementById('name').value) document.getElementById('name').value = name;
            if (!document.getElementById('email').value) document.getElementById('email').value = email;

            const initial = name ? name.trim().charAt(0).toUpperCase() : '-';
            document.getElementById('avatarInitial').textContent = initial;

            if (u.avatar_url) {
                const wrap = document.getElementById('avatarContainer');
                wrap.innerHTML = '';
                const img = document.createElement('img');
                img.src = u.avatar_url;
                img.alt = 'Avatar';
                img.className = 'rounded-circle img-thumbnail';
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                wrap.appendChild(img);
            }
        } catch (e) {
            console.error(e);
        }
    });
</script>
@endpush
@endsection
