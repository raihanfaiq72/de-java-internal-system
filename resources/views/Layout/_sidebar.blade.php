<div class="startbar-menu">
    <div class="startbar-collapse" id="startbarCollapse" style="flex: 1; overflow-y: auto; padding: 15px;">
        <div class="d-flex align-items-start flex-column w-100">
            <ul class="navbar-nav mb-auto w-100">
                @foreach(config('menu') as $menu)
                    @if($menu['type'] == 'label')
                        <li class="menu-label {{ $menu['class'] ?? 'mt-2' }}">
                            @if(isset($menu['is_html']) && $menu['is_html'])
                                {!! $menu['title'] !!}
                            @else
                                <span>{{ $menu['title'] }}</span>
                            @endif
                        </li>
                    @elseif($menu['type'] == 'item')
                        @if(isset($menu['submenu']))
                            <li class="nav-item">
                                <a class="nav-link {{ $menu['class'] ?? '' }}" href="#{{ $menu['id'] }}" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="{{ $menu['id'] }}">
                                    <i class="{{ $menu['icon'] }} menu-icon {{ isset($menu['class']) ? 'text-danger' : '' }}"></i>
                                    <span class="{{ $menu['class'] ?? '' }}">{{ $menu['title'] }}</span>
                                </a>
                                <div class="collapse" id="{{ $menu['id'] }}">
                                    <ul class="nav flex-column">
                                        @foreach($menu['submenu'] as $sub)
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ Route::has($sub['route']) ? route($sub['route']) : $sub['route'] }}">
                                                    <i class="{{ $sub['icon'] }} me-2"></i> {{ $sub['title'] }}
                                                    @if(isset($sub['badge']))
                                                        <span class="badge {{ $sub['badge']['class'] }}">{{ $sub['badge']['text'] }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ $menu['class'] ?? '' }}" href="{{ Route::has($menu['route']) ? route($menu['route']) : $menu['route'] }}">
                                    <i class="{{ $menu['icon'] }} menu-icon {{ isset($menu['class']) ? 'text-danger' : '' }}"></i>
                                    <span class="{{ $menu['class'] ?? '' }}">{{ $menu['title'] }}</span>
                                    @if(isset($menu['badge']))
                                        <span class="badge {{ $menu['badge']['class'] }}">{{ $menu['badge']['text'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach

                <li class="nav-item mt-3">
                    <a href="#" class="nav-link text-danger"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="iconoir-log-out menu-icon"></i>
                        <span>Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
