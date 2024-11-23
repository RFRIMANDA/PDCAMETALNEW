<header id="header" class="header fixed-top d-flex align-items-center" style="background: linear-gradient(90deg, #6a9df2, #6abf69);">
    <div class="d-flex align-items-center justify-content-between px-4 w-100">


        <!-- Logo -->
        <a href="/" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block" style="color: white; font-size: 1.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Tata Metal Lestari</span>
        </a>
        <!-- Sidebar Toggle Button (Left side) -->
        <i class="bi bi-list toggle-sidebar-btn text-white fs-3" title="Full Screen / Small Screen"></i>

        <!-- Center Text -->
        <div class="center-text mx-auto text-center">
            <span style="font-size: 1.5rem; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
                DOCUMENT CONTROL MANAGEMENT SYSTEM
            </span>
        </div>

    </div>

    <!-- Navbar -->
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <!-- Profile Picture -->
                    <img src="{{ asset('admin/img/TML3LOGO.png') }}" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; border: 2px solid #fff; transition: transform 0.3s;">
                    <span class="d-none d-md-block dropdown-toggle ps-2 text-light" style="font-weight: 600; font-size: 1rem; letter-spacing: 0.5px;">{{ Auth::user()->nama_user }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <li class="dropdown-header text-center">
                        <h6 class="text-dark" style="font-weight: 600;">{{ Auth::user()->nama_user }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ Auth::user()->role }}</p>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Email: {{ Auth::user()->email }}</p>
                    </li>
                    <hr class="dropdown-divider">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/password">
                            <i class="ri-lock-password-fill text-primary me-2"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                    <hr class="dropdown-divider">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/logout">
                            <i class="bi bi-box-arrow-right text-danger me-2"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<!-- Add a Smooth Hover Animation for the Profile Picture -->
<style>
    .nav-profile img:hover {
        transform: scale(1.1);
    }
    .center-text {
        flex-grow: 1;
    }
</style>
