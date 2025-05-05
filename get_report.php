<?php
error_reporting(0);
ini_set("display_errors", 0);

define('ALLOW_INCLUDE', true);

session_start();

if (isset($_SESSION["role"]) && strtoupper($_SESSION["role"]) == 'LOGISTICS' || (isset($_SESSION["role"]) && strtoupper($_SESSION["role"]) == 'CEO' || strtoupper($_SESSION["role"]) == 'D/CEO')) {

    /**
     * FUNCTION FOR DISPLAYING RANGE REPORT
     * FROM DIFFRENT DATES
     */


    $role = strtoupper($_SESSION['role']);
    include "./connection.php";



    /**
     * UPDATING OPERATION REPORT
     * 
     */
    $errors = [];
    $success = "";
    if (isset($_POST['update'])) {
        $driver = $_POST['op_driver'];
        $plate = $_POST['op_car'];
        $from = $_POST['op_origin'];
        $to = $_POST['op_destin'];
        $date = $_POST['op_date'];
        $description = $_POST['op_description'];

        $id = (int)$_POST['op_id'];


        if (empty($driver) || $driver == "" || empty($from) || $from == "" || empty($to) || $to == "" || empty($date) || $date == "" || empty($description) || $description == "") {
            $errors[] = "Fill All fields please";
        } else {
            /**
             * INSERTING DATA INTO DATABASE
             */
            try {

                $sql = "UPDATE `operation_report` SET `driver` = ?, `op_car` = ?, `op_from` = ? , `op_to` =? , `date`= ?, `description` = ? WHERE op_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssssssi", $driver, $plate, $from, $to, $date, $description, $id);
                if ($stmt->execute()) {
                    $success = "Reported Updated";
                } else {
                    $errors[] = "Report not Updated";
                }
            } catch (PDOException $e) {
                return $errors[] = $e->getMessage();
            }
        }
    }

    /**
     * DELETING OPERATION REPORT
     * 
     */

    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];

        $isExist = mysqli_num_rows(mysqli_query($db, "SELECT * FROM operation_report WHERE op_id = '$id'"));
        if ($isExist > 0) {
            $deleteQuery = "DELETE FROM operation_report WHERE op_id = '$id'";
            $dele = mysqli_query($db, $deleteQuery);
            if ($dele) {
                $message = "Report deleted successfully!";
                $messageType = "success"; // Used to define popup color
            }
        } else {
            $message = "Report ID not found!";
            $messageType = "error";
        }
?>

        <!-- Popup Message -->
        <div id="popupMessage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 md:p-8 w-full max-w-md text-center 
                    <?php echo ($messageType === 'success') ? 'text-green-700' : 'text-red-700'; ?>">
                <p><?php echo $message; ?></p>
            </div>
        </div>

        <!-- JavaScript to Hide Popup After 3 Seconds -->
        <script>
            setTimeout(function() {
                document.getElementById("popupMessage").classList.add("hidden");
                window.location.href = "get_report.php"; // Redirect if needed
            }, 3000);
        </script>

    <?php
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Operation report</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    </head>

    <body>
        <div class="flex h-screen">
            <!-- Sidebar -->
            <?php
            include("./components/side.php");
            ?>
            <div class="flex-1 p-6">
                <!-- Top Bar -->
                <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                    <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">Operation Report</span></h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    </div>

                </div>

                <!-- 
                DISPLAYING OPEARTION REPORT
                -->
                <h2 class="text-capitalize font-bold text-[15px] md:text-[20px] lg:text-[30px] sm:text-[15px] py-5 bg-slate-200 flex items-center justify-center gap-10" style="font-family:Bodoni MT Black;"><span>OPERATION FUEL REPORT </span>
                    <a target="_blank" href="reportPdf.php"> <span class="material-icons text-[20px text-red-500" title="Download Pdf">picture_as_pdf</span></a>
                </h2>
                <form method="POST" class="flex flex-col sm:flex-row justify-center items-center gap-4 bg-white shadow-md rounded-lg">
                    <!-- From Date -->
                    <div class="flex flex-row justify-center items-center gap-5">
                        <label for="from" class="text-gray-700 font-semibold text-sm">From</label>
                        <input type="date" name="from" id="from" require class="border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-lime-700 text-5">
                    </div>

                    <!-- To Date -->
                    <div class="flex flex-row justify-center items-center gap-5">
                        <label for="to" class="text-gray-700 font-semibold text-sm">To</label>
                        <input type="date" name="to" id="to" require class="border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-lime-700 text-5">
                    </div>

                    <!-- Search Button -->
                    <div class="flex flex-row gap-5 items-center">
                        <button type="submit" name="search" class="bg-blue-700 w-5 h-5 p-1 text-center text-white rounded-sm flex items-center hover:bg-blue-800 transition">
                            <i class="fas fa-search"></i>
                        </button>
                        <!-- <button type="submit" name="weekreport"> <span class="material-icons text-[20px text-blue-500" title="Download Pdf">picture_as_pdf</span></button> -->
                    </div>
                </form>

                <?php
                // Number of items per page
                $items_per_page = 6;
                // Get the current page from the URL, defaulting to 1 if not set
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                // Ensure the current page is at least 1
                $current_page = max(1, $current_page);
                // Calculate the offset (number of items to skip)
                $offset = ($current_page - 1) * $items_per_page;
                // Fetch the total number of rows in the operation_report table
                $total_result = mysqli_query($db, "SELECT COUNT(*) as total FROM operation_report");
                $total_row = mysqli_fetch_assoc($total_result);
                $total_items = $total_row['total'];

                $result = "";

                // Check if search is submitted
                if (isset($_POST['search'])) {
                    $start = mysqli_real_escape_string($db, $_POST["from"]);
                    $end = mysqli_real_escape_string($db, $_POST["to"]);

                    // Validate date format (YYYY-MM-DD)
                    if (!empty($start) && !empty($end) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
                        $query = "SELECT * FROM operation_report 
                  WHERE date BETWEEN '$start' AND '$end' 
                  ORDER BY date DESC 
                  LIMIT $items_per_page OFFSET $offset";
                    } else {
                        $query = "SELECT * FROM operation_report ORDER BY date DESC LIMIT $items_per_page OFFSET $offset";
                    }
                } else {
                    $query = "SELECT * FROM operation_report ORDER BY date DESC LIMIT $items_per_page OFFSET $offset";
                }

                /**
                 * DOWLOADING PDF
                 * FROM THE PDF ICON
                 * FROM SEARCH
                 */
                if (isset($_POST['weekreport'])) {
                    $from = mysqli_real_escape_string($db, $_POST["from"]);
                    $to = mysqli_real_escape_string($db, $_POST["to"]);

                    if (!empty($from) && !empty($to)) {
                        $_SESSION['from'] = $from;
                        $_SESSION['to'] = $to;
                        header("Location: report/rangeReport.php");
                        exit(); // Prevents further execution
                    } else {
                ?>
                        <script>
                            alert("Please fill all dates field")
                        </script>
                <?php
                    }
                }


                // Execute the query
                $result = mysqli_query($db, $query);

                // Check for query execution errors
                if (!$result) {
                    die("Query Failed: " . mysqli_error($db));
                }

                // Render the HTML table with overflow-x-scroll
                echo '<div class="w-[100%] overflow-x-auto border border-gray-300">';
                echo '<table class="min-w-max w-full border-collapse border border-gray-400 text-left text-gray-700">';
                echo '<thead class="bg-lime-700 text-white">';
                echo '<tr>';
                echo '<th class="p-2 border text-[14px] text-bold">Mission Header</th>';
                echo '<th class="p-2 border text-[14px] text-bold">Car</th>';
                echo '<th class="p-2 border text-[14px] text-bold">From</th>';
                echo '<th class="p-2 border text-[14px] text-bold">To</th>';
                echo '<th class="p-2 border text-[14px] text-bold">Date</th>';
                echo '<th class="p-2 border text-[14px] text-bold">Description</th>';

                if (strtoupper($_SESSION['role']) === 'D/CEO' || strtoupper($_SESSION['role']) === 'CEO') {
                    echo "";
                }
                if (strtolower($_SESSION['role']) === 'logistics') {
                    echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? '<th class="p-2 border text-[14px] text-bold">Status</th>' : '<th class="p-2 border text-[12px]">Actions</th>';
                }

                echo '</tr></thead><tbody class="bg-white">';
                $i = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    $strip = ($i % 2 == 0) ? 'bg-gray-100' : ' bg-gray-200';
                    echo '<tr class="border border-b ' .  $strip . ' p-5 hover:bg-zinc-300">';
                    echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['driver']) . '</td>';
                    echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['op_car']) . '</td>';
                    echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['op_from']) . '</td>';
                    echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['op_to']) . '</td>';
                    echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['date']) . '</td>';
                    echo '<td class="text-[15px] border p-2 text-black">' . htmlspecialchars($row['description']) . '</td>';

                    if (strtolower($_SESSION['role']) === 'logistics') {
                        echo '<td class="flex flex-col items-center gap-2">
            <a href="?edit=' . urlencode($row['op_id']) . '" class="text-blue-500 hover:underline"><i class="fas fa-edit" title="Edit"></i></a> 
            <a href="?delete=' . urlencode($row['op_id']) . '" class="text-red-500 hover:underline"><i class="fas fa-trash-alt" title="Delete"></i></a> 
            </td>';
                    }

                    echo '</tr>';
                    $i++;
                }

                echo '</tbody></table> </div>';

                // Pagination: Calculate total pages
                $total_pages = ceil($total_items / $items_per_page);

                // Pagination controls
                echo '<div class="pagination">';
                echo '<ul class="flex justify-center gap-4">';
                if ($current_page > 1) {
                    echo '<li><a href="?page=' . ($current_page - 1) . '" class="text-blue-500 hover:underline">Previous</a></li>';
                }
                for ($page = 1; $page <= $total_pages; $page++) {
                    if ($page == $current_page) {
                        echo '<li class="font-bold">' . $page . '</li>';
                    } else {
                        echo '<li><a href="?page=' . $page . '" class="text-blue-500 hover:underline">' . $page . '</a></li>';
                    }
                }
                if ($current_page < $total_pages) {
                    echo '<li><a href="?page=' . ($current_page + 1) . '" class="text-blue-500 hover:underline">Next</a></li>';
                }
                echo '</ul>';
                echo '</div>';
                ?>

                <!-- 
                FORM FOR PERFORMING UPDATE
                FOR UPDATING REQUEST 
                -->

                <div id="popupForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center <?php echo isset($_GET['edit']) ? "" : "hidden" ?> p-4">
                    <?php

                    $sql = "SELECT * FROM operation_report WHERE op_id = '" . (int)$_GET['edit'] . "'";
                    $result = mysqli_query($db, $sql);

                    $row = mysqli_fetch_assoc($result);
                    if ($row):
                    ?>
                        <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 md:p-8 w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl relative max-h-[90vh] overflow-y-auto">
                            <button id="closeFormBtn" class="absolute top-2 right-2 text-gray-600 hover:font-bold hover:text-red-600 text-xl">&times;</button>
                            <h2 class="text-lg sm:text-xl md:text-2xl font-semibold text-lime-700 text-center mb-4 sm:mb-6">
                                Update The Report
                            </h2>

                            <?php if (!empty($errors)): ?>
                                <div class="bg-red-100 text-red-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                    <?php foreach ($errors as $error) {
                                        echo "<p>$error</p>";
                                    } ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($success)): ?>
                                <div class="bg-green-100 text-green-700 p-2 sm:p-3 rounded mb-4 text-sm sm:text-base">
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>

                            <form action="" method="post" class="flex flex-col gap-3 sm:gap-4">
                                <input type="hidden" name="op_id" value="<?php echo $row['op_id'] ?>">
                                <input type="text" name="op_driver" value="<?php echo $row['driver'] ?>" class="capitalize input-field text-sm sm:text-base p-2 border border-lime-300 rounded-md focus:outline-none">
                                <input type="text" name="op_car" value="<?php echo $row['op_car'] ?>" class="capitalize input-field text-sm sm:text-base p-2 border border-lime-300 rounded-md focus:outline-none">
                                <input type="text" name="op_origin" value="<?php echo $row['op_from'] ?>" class="input-field text-sm sm:text-base p-2 border border-lime-300 rounded-md focus:outline-none">
                                <input type="text" name="op_destin" value="<?php echo $row['op_to'] ?>" class="input-field text-sm sm:text-base p-2 border border-lime-300 rounded-md focus:outline-none">
                                <input type="date" name="op_date" value="<?php echo $row['date'] ?>" class="input-field text-sm sm:text-base p-2 border border-lime-300 rounded-md focus:outline-none">
                                <textarea name="op_description" class="input-field h-20 sm:h-24 text-sm sm:text-base p-2 border border-lime-300 rounded-md focus:outline-none"><?php echo $row['description'] ?></textarea>
                                <button type="submit" name="update" class="bg-lime-700 text-white py-2 rounded-lg hover:bg-lime-800 transition text-sm sm:text-base">
                                    Update
                                </button>
                            </form>
                        </div>
                    <?php
                    endif;

                    if (isset($_GET['edit'])) {
                    ?>
                        <script>
                            document.getElementById("closeFormBtn").addEventListener("click", function() {
                                document.getElementById("popupForm").classList.add("hidden");
                                window.location = "get_report.php";
                            });

                            document.addEventListener("click", function(event) {
                                let popup = document.getElementById("popupForm");
                                let form = popup.querySelector("div");

                                if (event.target === popup && !form.contains(event.target)) {
                                    popup.classList.add("hidden");
                                    window.location = "get_report.php";
                                }
                            });
                        </script>
                    <?php
                    }
                    ?>
                </div>
            </div>
    </body>

    </html>
<?php
}
?>