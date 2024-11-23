<aside id="sidebar" class="sidebar" style="background: linear-gradient(90deg, #c6d9f1, #c9f6d6); border-right: 2px solid #ddd;">

    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Dashboard Nav -->
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="ri-home-4-fill"></i>
                <span class="text-dark">Dashboard</span>
            </a>
        </li>
        <!-- End Dashboard Nav -->

        <!-- Risk & Opportunity Register Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-layout-text-window-reverse"></i><span class="text-dark">Risk & Opportunity Register</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tables-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="/riskregister">
                        <i class="bi bi-circle"></i><span>Create Risk & Opportunity Register</span>
                    </a>
                    <a href="/bigrisk">
                        <i class="bi bi-circle"></i><span>Report</span>
                    </a>
                </li>
            </ul>
        </li>
        <!-- End Risk & Opportunity Register Nav -->

        <!-- Proses Peningkatan Kinerja Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-file-earmark-bar-graph"></i><span class="text-dark">Proses Peningkatan Kinerja</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="/formppk">
                        <i class="bi bi-circle"></i><span>Create Proses Peningkatan Kinerja (PPK)</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- End Proses Peningkatan Kinerja Nav -->

        <!-- Admin/Management Actions -->
        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manajemen')
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="bx bx-run"></i><span class="text-dark">Action</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="/divisi">
                        <i class="bi bi-circle"></i><span>Kelola Departemen</span>
                    </a>
                    <a href="/kelolaakun">
                        <i class="bi bi-circle"></i><span>Kelola Akun</span>
                    </a>
                    <a href="/kriteria">
                        <i class="bi bi-circle"></i><span>Kelola Kriteria</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        <!-- End Admin/Management Actions -->

    </ul>

</aside>

<style>
    /* Sidebar Styling */
    .sidebar {
        background: linear-gradient(90deg, #c6d9f1, #c9f6d6);
        border-right: 2px solid #ddd;
        transition: all 0.3s ease;
    }

    .sidebar-nav {
        padding-top: 20px;
    }

    .nav-item {
        margin-bottom: 15px;
    }

    .nav-link {
        font-size: 1rem;
        font-weight: 500;
        color: #4f4f4f;
        display: flex;
        align-items: center;
        padding: 10px 20px;
        border-radius: 10px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .nav-link:hover {
        background-color: #5b9bd5;
        color: #fff;
    }

    .nav-content a {
        padding-left: 40px;
        font-size: 0.95rem;
    }

    .nav-content a:hover {
        background-color: #8ebdf5;
    }

    .nav-item i {
        margin-right: 10px;
        font-size: 1.2rem;
        transition: color 0.3s ease;
    }

    .nav-link:hover i {
        color: #fff;
    }

    /* Collapse icon animation */
    .bi-chevron-down {
        transition: transform 0.3s ease;
    }

    .collapse.show + .bi-chevron-down {
        transform: rotate(180deg);
    }

    .collapse .nav-link {
        padding-left: 40px;
    }

    .text-dark {
        color: #4f4f4f;
    }
</style>
