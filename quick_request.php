<?php
session_start();
if (!isset($_SESSION["role"]) || strtoupper($_SESSION["role"]) != 'LOGISTICS') {
    header('Location: dashboard.php');
    exit();
}

require 'connection.php';

$errors = [];
if (isset($_POST['quick_request'])) {
    $header = trim($_POST['header']);
    $driver = trim($_POST['driver']);
    $plate = trim($_POST['plate']);
    $fuel_littel = trim($_POST['fuel_littel']);
    $origin = trim($_POST['origin']);
    $destination = trim($_POST['destin']);
    $description = trim($_POST['description']);
    $prepared_by = $_SESSION['name'];

    // Input validation
    if (empty($header) || empty($driver) || empty($plate) || empty($fuel_littel) || empty($origin) || empty($destination) || empty($description)) {
        $errors[] = "All fields are required.";
    } elseif (!is_numeric($fuel_littel) || $fuel_littel <= 0) {
        $errors[] = "Fuel quantity must be a positive number.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO `quick_action`(`head_mission`, `driver`, `plate_no`, `fuel`, `origin`, `destination`, `description`, `prepared_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("sssissss", $header, $driver, $plate, $fuel_littel, $origin, $destination, $description, $prepared_by);

        if ($statement->execute()) {
            $success = "Request submitted successfully.";
        } else {
            $errors[] = "Failed to submit request.";
        }
    }
}
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
                <h1 class="text-xl font-semibold text-lime-700"><i class="fa-solid fa-home"></i> Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo "<b>" . $_SESSION["name"] . "</b>"; ?></span>
                    <?php echo (strtoupper($_SESSION['role']) == 'LOGISTICS') ?
                        '<a href="#" class="bg-lime-700 text-white p-1 rounded-md" alt="Send Quick"> Quick Act </a>' : ''; ?>
                </div>
            </div>

            <!-- DISPLAYING THE DATA FROM QUICK_ACTION IN CARDS AND ADD BUTTON FOR DOWNLOADING PDF -->
            <?php
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Fetch total records for pagination
            $totalQuery = "SELECT COUNT(*) AS total FROM `quick_action`";
            $totalResult = $db->query($totalQuery);
            $totalRow = $totalResult->fetch_assoc();
            $totalPages = ceil($totalRow['total'] / $limit);

            // Fetch paginated records
            $sql = "SELECT * FROM `quick_action` LIMIT $limit OFFSET $offset";
            $result = $db->query($sql);
            ?>

            <div class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 grid-cols-1 gap-4 p-4">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="bg-white shadow-lg rounded-lg p-4 border border-gray-200 flex flex-row items-center">

                        <div class="w-fit">
                            <h3 class="text-md font-bold text-lime-700"><?php echo htmlspecialchars($row['head_mission']); ?></h3>
                            <p class="text-gray-700"><strong>Driver:</strong> <?php echo htmlspecialchars($row['driver']); ?></p>
                            <p class="text-gray-700"><strong>Plate No:</strong> <?php echo htmlspecialchars($row['plate_no']); ?></p>
                            <p class="text-gray-700"><strong>Fuel:</strong> <?php echo htmlspecialchars($row['fuel']); ?> Liters</p>
                            <p class="text-gray-700"><strong>From:</strong> <?php echo htmlspecialchars($row['origin']); ?></p>
                            <p class="text-gray-700"><strong>To:</strong> <?php echo htmlspecialchars($row['destination']); ?></p>
                            <p class="text-gray-700"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        </div>
                        <!-- Download PDF Button -->
                        <div class="text-center mt-6">
                            <a href="download.php?id=<?php echo $row['action_id'] ?>" class="bg-blue-900 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                ⬇️ PDF
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>

            <!-- Pagination Controls -->
            <div class="flex justify-center space-x-2 mt-4">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-lime-700 text-white rounded-lg hover:bg-lime-800">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded-lg <?php echo $i == $page ? 'bg-lime-700 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-lime-700 text-white rounded-lg hover:bg-lime-800">Next</a>
                <?php endif; ?>
            </div>



            <!-- FORM CONTENT AHEAD -->
            <!-- Main Content -->
            <div class="flex-1 flex items-center justify-center p-6 relative overflow-y-scroll">
                <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-lg">
                    <h2 class="text-2xl font-semibold text-lime-700 text-center mb-6">Quick Fuel Request Form</h2>

                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                            <?php foreach ($errors as $error) {
                                echo "<p>$error</p>";
                            } ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"> <?php echo $success; ?> </div>
                    <?php endif; ?>

                    <form action="" method="post" class="flex flex-col gap-4">
                        <input type="text" name="header" placeholder="Event Header" class="input-field">
                        <input type="text" name="driver" placeholder="Driver Name" class="input-field">
                        <input type="text" name="plate" placeholder="Plate Number" class="input-field">
                        <input type="number" name="fuel_littel" placeholder="Fuel Litter" class="input-field">
                        <input type="text" name="origin" placeholder="From" class="input-field">
                        <input type="text" name="destin" placeholder="Destination" class="input-field">
                        <textarea name="description" placeholder="Write Description" class="input-field h-24"></textarea>
                        <button type="submit" name="quick_request" class="bg-lime-700 text-white py-2 rounded-lg hover:bg-lime-800 transition">Send Request</button>
                    </form>
                </div>
            </div>
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
</body>

</html>