<?php
session_start();
if (!isset($_SESSION["role"]) && (strtoupper($_SESSION["role"]) != 'D/CEO' || strtoupper($_SESSION["role"]) != 'CEO')) {
    header('Location: dashboard.php');
    exit();
}

require 'connection.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include("./components/side.php"); ?>
        <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700 flex flex-row items-center gap-2"><i class="fa-solid fa-home"></i> <span class="lg:flex md:flex sm:flex hidden">History</span></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"> <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    <?php echo (strtoupper($_SESSION['role']) == 'LOGISTICS') ?
                        '<button id="showFormBtn" class="bg-lime-700 text-white py-2 px-4 rounded-lg hover:bg-lime-800 transition" alt="Send Quick"> Quick Act </button>' : ''; ?>
                </div>
            </div>

            <!-- 
            DISPLAYING THE DATA FROM QUICK_ACTION IN CARDS AND ADD BUTTON FOR DOWNLOADING PDF 
            -->
            <?php
            $limit = 6;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Fetch total records for pagination
            $totalQuery = "SELECT COUNT(*) AS total FROM `quick_action`";
            $totalResult = $db->query($totalQuery);
            $totalRow = $totalResult->fetch_assoc();
            $totalPages = ceil($totalRow['total'] / $limit);

            // Fetch paginated records
            $sql = "SELECT * FROM `quick_action` ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
            $result = $db->query($sql);
            ?>

            <div class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 grid-cols-1 gap-4 p-4">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200 flex flex-row items-center justify-between">

                        <div class="w-fit">
                            <h3 class="text-md font-bold text-lime-700"><?php echo htmlspecialchars($row['head_mission']); ?></h3>
                            <p class="text-gray-700"><strong>Driver:</strong> <?php echo htmlspecialchars($row['driver']); ?></p>
                            <p class="text-gray-700"><strong>Plate No:</strong> <?php echo htmlspecialchars($row['plate_no']); ?></p>
                            <p class="text-gray-700"><strong>Fuel:</strong> <?php echo htmlspecialchars($row['fuel']); ?> Liters</p>
                            <p class="text-gray-700"><strong>From:</strong> <?php echo htmlspecialchars($row['origin']); ?></p>
                            <p class="text-gray-700"><strong>To:</strong> <?php echo htmlspecialchars($row['destination']); ?></p>
                            <p class="text-gray-700"><strong>Description:</strong>
                                <?php
                                $descript = (htmlspecialchars($row['description']));
                                echo (mb_strlen($descript > 300)) ? mb_substr($descript, 0, 100) . '...' : $descript;
                                ?>
                            </p>
                        </div>
                        <!-- Download PDF Button -->
                        <div class="text-center mt-6">
                            <a href="download.php?pdf_id=<?php echo $row['action_id'] ?>" target="_blank" class="bg-blue-900 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                ⬇️ PDF
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>

            <!-- Pagination Controls -->
            <div class="flex space-x-2 mt-4">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-500">⏮️</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="px-3 py-1 rounded-md <?php echo $i == $page ? 'bg-lime-700 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 bg-gray-700 text-white rounded-md hover:bg-gray-500">⏭️</a>
                <?php endif; ?>
            </div>
        </div>

        <style>
            .input-field {
                border: 1px solid #d1d5db;
                padding: 10px;
                border-radius: 8px;
                outline: none;
                width: 100%;
                transition: border-color 0.3s;
            }

            .input-field:focus {
                border-color: #84cc16;
            }
        </style>

        <script>
            document.getElementById("showFormBtn").addEventListener("click", function() {
                document.getElementById("popupForm").classList.remove("hidden");
            });

            document.getElementById("closeFormBtn").addEventListener("click", function() {
                document.getElementById("popupForm").classList.add("hidden");
            });

            // Close popup when clicking outside the form
            document.getElementById("popupForm").addEventListener("click", function(event) {
                if (event.target === this) {
                    this.classList.add("hidden");
                }
            });
        </script>
</body>

</html>