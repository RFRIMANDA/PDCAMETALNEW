<aside id="sidebar" class="sidebar" style="background: linear-gradient(90deg, #e6e6fa, #b4e7b4);">

    <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link" href="/">
          <i class="ri-home-4-fill"></i>
          <span>Dashboard</span>
      </a>
    </li>
<!-- End Dashboard Nav -->

      <!-- End Components Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Risk & Opportunity Register</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/riskregister">
              <i class="bi bi-circle"></i><span>Create Risk & Opportunity Register</span>
            </a>
            <a href="/bigrisk">
              <i class="bi bi-circle"></i><span>Report</span>
            </a>
          </li>

          <li>

          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-file-earmark-bar-graph"></i><span>Proses Peningkatan Kinerja</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/formppk">
              <i class="bi bi-circle"></i><span>Create Proses Peningkatan Kinerja (PPK)</span>
            </a>
          </li>
        <li>
            <a href="#">
              <i class="bi bi-circle"></i><span></span>
            </a>
        </li>
        </ul>
      </li><!-- End Components Nav -->
       <br>
       @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manajemen')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="bx bx-run"></i><span>Action</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                `   <a href="/divisi">
                        <i class="bi bi-circle"></i><span>Kelola Departemen</span>
                    </a>
                    <a href="/kelolaakun">
                        <i class="bi bi-circle"></i><span>Kelola Akun</span>
                    </a>
                    <a href="/kriteria">
                        <i class="bi bi-circle"></i><span>Kelola Kriteria</span>
                    </a>
                </li>
            </li>
        @endif



      <!-- End Tables Nav -->

      <!-- End Charts Nav -->

      <!-- End Icons Nav -->

      <!-- <li class="nav-heading bg-blue-800 text-white font-bold text-center py-2" style="background-color: #FF8C00;">
          PAGES
      </li> -->


      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li> -->
      <!-- End Profile Page Nav -->

      <!-- <li class="nav-item"><br>
        <a class="nav-link collapsed" href="pages-contact.html">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li> -->
      <!-- End Contact Page Nav -->

      <!-- <li class="nav-item"><br>
        <a class="nav-link collapsed" href="pages-register.html">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li>End Register Page Nav -->


      <!-- End Login Page Nav -->


      <!-- End Login Page Nav -->




  </aside>
