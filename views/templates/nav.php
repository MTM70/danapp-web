<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link cursor-select" onclick="showOption(this, 'main')">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <?php 
            if (isset($_SESSION["idRol"]) AND $_SESSION["idRol"] == 1) {
                echo '
                    <li class="nav-heading">Admin</li>

                    <li class="nav-item">
                        <a class="nav-link cursor-select collapsed" onclick="showOption(this, '."'".'main-parameters'."'".')">
                            <i class="bi bi-card-list"></i>
                            <span>Parameters</span>
                        </a>
                    </li><!-- End Profile Page Nav -->
            
                    <li class="nav-item">
                        <a class="nav-link cursor-select collapsed" onclick="showOption(this, '."'".'main-users'."'".')">
                            <i class="bi bi-people"></i>
                            <span>Users</span>
                        </a>
                    </li><!-- End Profile Page Nav -->
                ';
            }
        ?>

    </ul>

</aside><!-- End Sidebar-->