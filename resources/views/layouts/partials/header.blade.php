<header id="header" class="header fixed-top d-flex align-items-center" style="background: linear-gradient(90deg, #87ceeb, #98FB98);">
    <div class="d-flex align-items-center justify-content-between">
        <a href="https://www.instagram.com/tata_metal_lestari/" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block" style="color: white; font-size: 1.5rem; font-weight: 700; margin-left: 10px; text-transform: uppercase; letter-spacing: 1px; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">Tata Metal Lestari</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn text-light fs-3"></i>
    </div>
    
<nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="" data-bs-toggle="dropdown">
                <img src="{{ asset('admin/img/TML3LOGO.png') }}" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; border: 2px solid #fff;">
                <span class="d-none d-md-block dropdown-toggle ps-2 text-dark">{{ Auth::user()->nama_user }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-header">
                    <h6>Email: {{ Auth::user()->email }}</h6>
                    <span>Role: {{ Auth::user()->role }}</span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <!-- Tambahkan item lainnya di sini jika diperlukan -->
            </ul>
        </li>
    </ul>
</nav>

</header>
