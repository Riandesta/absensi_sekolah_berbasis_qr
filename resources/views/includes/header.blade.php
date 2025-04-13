<header class="mb-3">
    <div class="d-flex justify-content-between align-items-center px-3 py-3 bg-white shadow-sm">
        <!-- Kiri: Judul + Breadcrumb -->
        <div>
            <h5 class="mb-0">@yield('header')</h5>
            <small class="text-muted">@yield('breadcrumb')</small>
        </div>

        <!-- Kanan: Profil Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser"
               data-bs-toggle="dropdown" aria-expanded="false">

                @if(Auth::user()->profile_picture_url)
                    <img src="{{ Auth::user()->profile_picture_url }}"
                         alt="Avatar" width="32" height="32" class="rounded-circle me-2">
                @else
                    <i class="bi bi-person-circle fs-3 me-2 d-flex align-items-center"></i>
                @endif

                <span class="d-none d-sm-inline fw-bold">{{ Auth::user()->name }}</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                {{-- <li><a class="dropdown-item" href="{{ route('profile') }}">Profil</a></li> --}}
                <li><a class="dropdown-item" href="#">Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="dropdown-item" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
