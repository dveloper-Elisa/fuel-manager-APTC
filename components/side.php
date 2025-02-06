<!-- Menu Toggle Button -->
<button id="menu-toggle" class="z-50 absolute text-blue-500 text-xl cursor-pointer top-5 left-5 md:hidden">
    <i class="fa-solid fa-bars"></i>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="z-30 w-0 bg-lime-700 text-white p-5 transition-all duration-300 md:w-fit md:block hidden">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Navigation -->
    <nav id="nav" class="ml-5">
        <h3 class="text-md font-bold mb-6 whitespace-nowrap">Fuel Management</h3>
        <ul>
            <li class="mb-2">
                <a href="./dashboard.php" class="block p-2 hover:bg-lime-600 rounded whitespace-nowrap">
                    <i class="fa-solid fa-home"></i> Dashboard
                </a>
            </li>
            <li class="mb-2">
                <a href="./requests.php" class="block p-2 hover:bg-lime-600 rounded">
                    <i class="fa-solid fa-file-alt"></i> Requests
                </a>
            </li>
            <?php
            echo (strtoupper($_SESSION['role']) === 'LOGISTICS') ? '<li class="mb-2">
                <a href="./quick_request.php" class="block hover:bg-lime-600 rounded">
                    <span class="text-white text-lg">⚡</span>QuickAct 
                </a>
            </li>' : '';
            ?>
            <li class="mb-2">
                <a href="./pannel/logout.php" class="block p-2 hover:bg-red-600 rounded">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- JavaScript for Sidebar Toggle -->
<script>
    const menuToggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");

    // Toggle Sidebar visibility
    function toggleSidebar() {
        sidebar.classList.toggle("w-0");
        sidebar.classList.toggle("w-fit");
        sidebar.classList.toggle("hidden");

        // Change background color based on visibility
        if (sidebar.classList.contains("hidden")) {
            sidebar.classList.remove("bg-lime-700", "text-white");
            sidebar.classList.add("bg-white", "text-black");
        } else {
            sidebar.classList.remove("bg-white", "text-black");
            sidebar.classList.add("bg-lime-700", "text-white");
        }
    }

    // Add event listener for toggling the sidebar
    menuToggle.addEventListener("click", toggleSidebar);

    // Check screen size and adjust sidebar visibility on page load and resizing
    function checkScreenSize() {
        if (window.innerWidth < 1024) {
            sidebar.classList.add("hidden");
            sidebar.classList.remove("w-fit");
            sidebar.classList.add("w-0");
        } else { // For larger screens
            sidebar.classList.remove("hidden");
            sidebar.classList.add("w-fit");
            sidebar.classList.remove("w-0");
        }
    }

    // Check screen size on page load and when resizing
    window.addEventListener("load", checkScreenSize);
    window.addEventListener("resize", checkScreenSize);

    // Ensure smooth transition for sidebar close on smaller screens
    sidebar.addEventListener('transitionend', () => {
        if (sidebar.classList.contains("hidden")) {
            sidebar.style.transition = "none";
            sidebar.style.transition = "";
        }
    });
</script>