<?php
error_reporting(E_ALL); // Changed to see errors if they occur
ini_set("display_errors", 1);

define('ALLOW_INCLUDE', true);
session_start();

if (isset($_SESSION["role"]) && (strtoupper($_SESSION["role"]) == 'LOGISTICS' || strtoupper($_SESSION["role"]) == 'CEO' || strtoupper($_SESSION["role"]) == 'D/CEO')) {

    include "./connection.php";
    $role = strtoupper($_SESSION['role']);
    $errors = [];
    $success = "";

    // --- 1. HANDLE UPDATES ---
    if (isset($_POST['update'])) {
        $driver = $_POST['op_driver'];
        $plate = $_POST['op_car'];
        $from = $_POST['op_origin'];
        $to = $_POST['op_destin'];
        $date = $_POST['op_date'];
        $description = $_POST['op_description'];
        $id = (int) $_POST['op_id'];

        if (empty($driver) || empty($from) || empty($to) || empty($date)) {
            $errors[] = "Fill All fields please";
        } else {
            $sql = "UPDATE `operation_report` SET `driver` = ?, `op_car` = ?, `op_from` = ? , `op_to` =? , `date`= ?, `description` = ? WHERE op_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssssssi", $driver, $plate, $from, $to, $date, $description, $id);
            if ($stmt->execute()) {
                $success = "Report Updated Successfully";
            }
        }
    }

    // --- 2. HANDLE DELETES ---
    if (isset($_GET['delete'])) {
        $id = (int) $_GET['delete'];
        mysqli_query($db, "DELETE FROM operation_report WHERE op_id = '$id'");
        header("Location: get_report.php");
        exit();
    }

    // --- 3. FILTER LOGIC (THE FIX) ---
    // Get dates from POST (search button) or GET (pagination links)
    $start_date = isset($_REQUEST['from']) ? mysqli_real_escape_string($db, $_REQUEST['from']) : '';
    $end_date = isset($_REQUEST['to']) ? mysqli_real_escape_string($db, $_REQUEST['to']) : '';

    $where_clause = " WHERE 1=1 ";
    if (!empty($start_date) && !empty($end_date)) {
        $where_clause .= " AND date BETWEEN '$start_date' AND '$end_date' ";
    }

    // --- 4. PAGINATION SETUP ---
    $items_per_page = 6;
    $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $current_page = max(1, $current_page);
    $offset = ($current_page - 1) * $items_per_page;

    // Total count for filtered data
    $total_result = mysqli_query($db, "SELECT COUNT(*) as total FROM operation_report $where_clause");
    $total_row = mysqli_fetch_assoc($total_result);
    $total_items = $total_row['total'];
    $total_pages = ceil($total_items / $items_per_page);

    // Final Query
    $query = "SELECT * FROM operation_report $where_clause ORDER BY date DESC LIMIT $items_per_page OFFSET $offset";
    $result = mysqli_query($db, $query);
}

// --- HANDLE EDIT FETCH ---
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_query = mysqli_query($db, "SELECT * FROM operation_report WHERE id = $edit_id");
    if ($edit_query && mysqli_num_rows($edit_query) > 0) {
        $edit_row = mysqli_fetch_assoc($edit_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation Report | Fleet Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .table-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="flex h-screen overflow-hidden">
        <!-- <div class="hidden md:block"> -->
            <?php include("./components/side.php"); ?>
        <!-- </div> -->

        <div class="flex-1 flex flex-col overflow-hidden">

            <header
                class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm z-10">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-slate-600"><i class="fa-solid fa-bars text-xl"></i></button>
                    <h1 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <span class="p-2 bg-lime-100 text-lime-700 rounded-lg"><i
                                class="fa-solid fa-gas-pump"></i></span>
                        Operation Fuel Report
                    </h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Signed in as</p>
                        <p class="text-sm font-bold text-slate-800"><?php echo $_SESSION["name"]; ?></p>
                    </div>
                    <div
                        class="h-10 w-10 bg-lime-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                        <?php echo substr($_SESSION["name"], 0, 1); ?>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between group hover:border-lime-500 transition-colors">
                        <div>
                            <p class="text-slate-500 text-xs font-bold uppercase">Monthly Summary</p>
                            <a href="./report/monthlyOpReport.php"
                                class="text-slate-800 font-semibold group-hover:text-lime-600">View Full Report →</a>
                        </div>
                        <i
                            class="fa-solid fa-calendar-check text-2xl text-slate-300 group-hover:text-lime-500 transition-colors"></i>
                    </div>
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between group hover:border-blue-500 transition-colors">
                        <div>
                            <p class="text-slate-500 text-xs font-bold uppercase">Mission Tracking</p>
                            <a href="./report/missionReport.php"
                                class="text-slate-800 font-semibold group-hover:text-blue-600">Mission Reports →</a>
                        </div>
                        <i
                            class="fa-solid fa-map-location-dot text-2xl text-slate-300 group-hover:text-blue-500 transition-colors"></i>
                    </div>
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between group hover:border-red-500 transition-colors">
                        <div>
                            <p class="text-slate-500 text-xs font-bold uppercase">Export Data</p>
                            <a target="_blank" href="reportPdf.php"
                                class="text-slate-800 font-semibold group-hover:text-red-600 flex items-center gap-2">
                                <i class="fa-solid fa-file-pdf"></i> Download PDF
                            </a>
                        </div>
                        <i
                            class="fa-solid fa-cloud-arrow-down text-2xl text-slate-300 group-hover:text-red-500 transition-colors"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                    <form method="POST" class="flex flex-wrap items-end gap-6">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Start Date</label>
                            <input type="date" name="from" required
                                class="w-full border border-slate-200 rounded-lg p-2.5 focus:ring-2 focus:ring-lime-500 focus:outline-none transition">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">End Date</label>
                            <input type="date" name="to" required
                                class="w-full border border-slate-200 rounded-lg p-2.5 focus:ring-2 focus:ring-lime-500 focus:outline-none transition">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="search"
                                class="bg-slate-800 text-white px-6 py-2.5 rounded-lg hover:bg-black transition shadow-md flex items-center gap-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="get_report.php"
                                class="bg-slate-100 text-slate-600 px-4 py-2.5 rounded-lg hover:bg-slate-200 transition">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                    <div class="table-container overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[11px] font-bold tracking-widest">
                                    <th class="px-6 py-4">Mission Header</th>
                                    <th class="px-6 py-4">Car Plate</th>
                                    <th class="px-6 py-4">Origin</th>
                                    <th class="px-6 py-4">Destination</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Description</th>
                                    <!-- <?php if (strtolower($_SESSION['role']) === 'logistics'): ?>
                                        <th class="px-6 py-4 text-center">Actions</th>
                                    <?php endif; ?> -->
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr class="hover:bg-slate-50 transition-colors group">
                                            <td class="px-6 py-4 font-semibold text-slate-800">
                                                <?php echo htmlspecialchars($row['driver']); ?></td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded text-xs font-mono font-bold border border-slate-200">
                                                    <?php echo htmlspecialchars($row['op_car']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600">
                                                <?php echo htmlspecialchars($row['op_from']); ?></td>
                                            <td class="px-6 py-4 text-slate-600"><?php echo htmlspecialchars($row['op_to']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                                <i
                                                    class="fa-regular fa-calendar-days text-lime-600 mr-2"></i><?php echo $row['date']; ?>
                                            </td>
                                            <td class="px-6 py-4 text-slate-500 text-sm max-w-xs truncate"
                                                title="<?php echo htmlspecialchars($row['description']); ?>">
                                                <?php echo htmlspecialchars($row['description']); ?>
                                            </td>
                                            <!-- <?php if (strtolower($_SESSION['role']) === 'logistics'): ?>
                                                <td class="px-6 py-4">
                                                    <div class="flex justify-center gap-3">
                                                        <a href="?edit=<?php echo $row['id']; ?>"
                                                            class="h-8 w-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                                            <i class="fas fa-edit text-xs"></i>

                                                        </a>
                                                        <a href="?delete=<?php echo $row['id']; ?>"
                                                            onclick="return confirm('Delete this report?')"
                                                            class="h-8 w-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            <?php endif; ?> -->
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="p-12 text-center text-slate-400 italic">No records found matching your criteria.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-1">
                        <?php
                        // Build the string to keep filters in the URL
                        $filter_params = "";
                        if (!empty($start_date) && !empty($end_date)) {
                            $filter_params = "&from=$start_date&to=$end_date";
                        }

                        if ($current_page > 1): ?>
                            <a href="?page=<?php echo ($current_page - 1) . $filter_params; ?>"
                                class="px-3 py-1.5 rounded-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition text-sm">Prev</a>
                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                            <a href="?page=<?php echo $page . $filter_params; ?>"
                                class="px-3 py-1.5 rounded-md <?php echo $page == $current_page ? 'bg-lime-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'; ?> transition text-sm">
                                <?php echo $page; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?php echo ($current_page + 1) . $filter_params; ?>"
                                class="px-3 py-1.5 rounded-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition text-sm">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php if ($edit_row): ?>
        <div id="popupForm"
            class="fixed inset-1 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
                <div class="bg-lime-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold">Edit Operation Report</h3>
                    <a href="get_report.php" class="text-white/80 hover:text-white"><i
                            class="fa-solid fa-xmark text-xl"></i></a>
                </div>
                <form action="" method="post" class="p-6 space-y-4">
                    <input type="hidden" name="op_id" value="<?php echo $edit_row['id'] ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Driver</label>
                            <input type="text" name="op_driver" value="<?php echo $edit_row['driver'] ?>"
                                class="w-full border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-lime-500 outline-none">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Plate</label>
                            <input type="text" name="op_car" value="<?php echo $edit_row['op_car'] ?>"
                                class="w-full border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-lime-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Origin</label>
                            <input type="text" name="op_origin" value="<?php echo $edit_row['op_from'] ?>"
                                class="w-full border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-lime-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Destination</label>
                            <input type="text" name="op_destin" value="<?php echo $edit_row['op_to'] ?>"
                                class="w-full border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-lime-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Date</label>
                        <input type="date" name="op_date" value="<?php echo $edit_row['date'] ?>"
                            class="w-full border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-lime-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Description</label>
                        <textarea name="op_description" rows="3"
                            class="w-full border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-lime-500 outline-none"><?php echo $edit_row['description'] ?></textarea>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="submit" name="update"
                            class="flex-1 bg-lime-700 text-white py-2.5 rounded-lg font-bold hover:bg-lime-800 transition shadow-lg">Save
                            Changes</button>
                        <a href="get_report.php"
                            class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-lg font-bold text-center hover:bg-slate-200 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

</body>

</html>