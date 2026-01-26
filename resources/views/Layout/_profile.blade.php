<li class="dropdown topbar-item">
    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
        role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
        @if(auth()->user()->avatar)
            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
        @else
            <div class="user-avatar">
                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
            </div>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end py-0">
        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
            <div class="flex-shrink-0">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <div class="user-avatar" style="width: 40px; height: 40px; font-size: 18px;">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                <h6 class="my-0 fw-medium text-dark fs-13">{{ auth()->user()->name ?? 'Guest' }}</h6>
                <small class="text-muted mb-0">{{ auth()->user()->username ?? '' }}</small>
            </div>
        </div>
        <div class="dropdown-divider mt-0"></div>
        <a class="dropdown-item" href="{{ route('profile.edit') }}">
            <i class="iconoir-user fs-18 me-1 align-text-bottom"></i> Profile
        </a>
        <div class="dropdown-divider mb-0"></div>
        <a class="dropdown-item text-danger" href="#" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="iconoir-log-out fs-18 me-1 align-text-bottom"></i> Logout
        </a>
    </div>
</li>
