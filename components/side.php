<!-- Menu Toggle Button -->
<button id="menu-toggle" class="absolute text-blue-500 text-xl cursor-pointer top-5 left-5 md:hidden">
    <i class="fa-solid fa-bars"></i>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="w-0 bg-lime-700 text-white p-5 transition-all duration-300 md:w-fit md:block hidden">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Navigation -->
    <nav id="nav" class="ml-5">
        <h3 class="text-lg font-bold mb-6">Fuel Management</h3>
        <ul>
            <li class="mb-4">
                <a href="./dashboard.php" class="block p-2 hover:bg-lime-600 rounded">
                    <i class="fa-solid fa-home"></i> Dashboard
                </a>
            </li>
            <li class="mb-4">
                <a href="./requests.php" class="block p-2 hover:bg-lime-600 rounded">
                    <i class="fa-solid fa-file-alt"></i> Requests
                </a>
            </li>
            <li class="mb-4">
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
        sidebar.classList.toggle("w-0");  // Collapse sidebar
        sidebar.classList.toggle("w-fit"); // Expand sidebar
        sidebar.classList.toggle("hidden"); // Hide sidebar on smaller screens

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
        if (window.innerWidth < 1024) {  // For medium and small screens
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
            sidebar.style.transition = "none"; // Disable transition on close
            sidebar.style.transition = ""; // Re-enable transition for future actions
        }
    });
</script>
