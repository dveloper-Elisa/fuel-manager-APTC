<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

if (!isset($_SESSION["phone"])) {
    header("Location:./login.php");
    exit;
}

include("./connection.php");

require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Request Management System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="./fuel.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="flex h-screen">
<?php
include "./components/side.php";
include "./measures/districts.php";

// Display vehicles for missions
$missionedCars = ["RAE450P","RAE449P","RAF553E","RAC887L","RAD036D","RDF198P"];
$plateNumbers = "'" . implode("','", $missionedCars) . "'";
$query = "SELECT plateno, vname FROM vehicles WHERE plateno IN ($plateNumbers)";
$result = mysqli_query($db, $query);

$vehicles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $vehicles[$row['plateno']] = $row['vname'];
}
?>

<form method="post" enctype="multipart/form-data" class="overflow-y-scroll" onsubmit="showLoader()">
    <h2>Fuel Request Management System</h2>
    <div><p id="response" class="text-red-500"></p></div>
    <input type="text" style="text-transform: capitalize;" placeholder="Driver Name" name="Dnames" id="driverName" required>

    <select id="plateSelect" name="Pnumber" onchange="updateVehicleType()">
        <option value="">-- Select Plate Number --</option>
        <?php foreach ($vehicles as $plate => $type) {
            echo "<option value='$plate'>$plate</option>";
        } ?>
    </select>

    <input type="text" id="vehicleType" name="Vtype" placeholder="Vehicle Type" readonly>
    <select name="from" id="select" class="input-field text-sm sm:text-base" required>
        <option value="">-- District Of Origin --</option>
        <?php foreach ($districts as $district): ?>
            <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
        <?php endforeach; ?>
    </select>

    <select name="Destination" id="select" class="input-field text-sm sm:text-base" required>
        <option value="">-- Select a Destination --</option>
        <?php foreach ($districts as $district): ?>
            <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
        <?php endforeach; ?>
    </select>

    <label for="departure">Date of Departure</label>
    <input type="date" name="departure" id="departure" required>
    <label for="return">Date of Return</label>
    <input type="date" name="return" id="return" required>
    <label for="select">Fuel Type</label>
    <select name="fuel" id="select" class="input-field text-sm sm:text-base" required>
        <option value="">Select Fuel</option>
        <option value="Diesel">Diesel</option>
        <option value="Petrol">Petrol</option>
    </select>
    <label for="signature">Head of Mission's Signature (Optional)</label>
    <input type="file" name="signature" id="signature" accept=".jpg, .jpeg, .png">
    <input type="submit" value="Submit" name="btn">

    <!-- Loader -->
    <div id="loader" class="hidden mt-4 flex justify-center">
        <div class="w-8 h-8 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
    </div>
</form>

<!-- Modal -->
<div id="statusModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg w-1/3">
        <h2 class="text-center text-5 font-bold text-gray-700">✅</h2>
        <p id="status" class="mt-4 text-gray-600"></p>
    </div>
</div>

<?php
if (isset($_POST["btn"])) {
    $Hnames = $_SESSION["name"];
    $Dnames = mysqli_real_escape_string($db, $_POST["Dnames"]);
    $Vtype = mysqli_real_escape_string($db, $_POST["Vtype"]);
    $Pnumber = mysqli_real_escape_string($db, $_POST["Pnumber"]);
    $from = mysqli_real_escape_string($db, $_POST["from"]);
    $Destination = mysqli_real_escape_string($db, $_POST["Destination"]);
    $departure = mysqli_real_escape_string($db, $_POST["departure"]);
    $return = mysqli_real_escape_string($db, $_POST["return"]);
    $fuelType = mysqli_real_escape_string($db, $_POST["fuel"]);

    include "./measures/distance.php";

    $quantinty = 0;
    function rounding($distance) {
        $decimal = $distance - floor((float)$distance);
        return ($decimal >= 0.5) ? ceil($distance) : floor($distance);
    }

    $quantinty = ($Pnumber !== 'RDF198P') ? rounding(($distance / 8) * 2) : rounding(($distance / 7) * 2);

    $fileDestination = null; // default null if no file uploaded

    if (isset($_FILES["signature"]) && $_FILES["signature"]["error"] === 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = $_FILES["signature"]["name"];
        $fileTmpName = $_FILES["signature"]["tmp_name"];
        $fileSize = $_FILES["signature"]["size"];
        $allowedTypes = ["image/jpeg","image/png","image/jpg"];
        $fileType = mime_content_type($fileTmpName);

        if (in_array($fileType, $allowedTypes) && $fileSize <= 30*1024*1024) {
            $uniqueName = uniqid("signature_", true) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
            $fileDestination = $uploadDir . $uniqueName;
            move_uploaded_file($fileTmpName, $fileDestination);
        }
    }

    $staff_code = $_SESSION['staff_code'];
    $sqlPrice = "SELECT uplt FROM fuel WHERE type = ? AND status = 'active'";
    $resultPrice = $db->prepare($sqlPrice);
    $resultPrice->bind_param("s", $fuelType);
    $resultPrice->execute();
    $result = $resultPrice->get_result();
    $row = $result->fetch_assoc();
    $realPrice = $row["uplt"] * $quantinty;

    $sql = "INSERT INTO fuel_request (
        stf_code, requested_date, location_from, location_to, kilometer, date_from, date_to, 
        head_mission, vehicle_type, requested_qty, received_qty, price, driver_name, fuel_type, 
        plate_number, verified_by, approved_by, `signature`, `status`, created_at
    ) VALUES (
        '$staff_code', now(), '$from', '$Destination', $distance,'$departure','$return',
        '$Hnames','$Vtype',$quantinty,0,$realPrice,'$Dnames','$fuelType','$Pnumber','-','-'," 
        . ($fileDestination ? "'$fileDestination'" : "NULL") . ",'pending', now()
    )";

    if (mysqli_query($db, $sql)) {
        include "./messages/sendSms.php";
        echo '<script>
            document.getElementById("statusModal").classList.remove("hidden");
            document.getElementById("status").innerText = "Request sent successfully!";
            window.location="./requests.php";
        </script>';
    } else {
        echo '<script>document.getElementById("response").innerText = "Error: '.mysqli_error($db).'";</script>';
    }
}
?>
</div>

<style>
#select,#plateSelect { width:100%;padding:12px;margin:6px 0;border:1px solid #2b2929;border-radius:5px;font-size:14px;background-color:#fff;color:#333;cursor:pointer;outline:none;transition:all 0.3s ease-in-out; }
#select:hover,#plateSelect:hover{border-color:#1e1e1e;}
#select:focus,#plateSelect:focus{border-color:#4caf50;box-shadow:0 0 5px rgba(76,175,80,0.5);}
#select::-ms-expand,#plateSelect::-ms-expand{display:none;}
#select,#plateSelect{appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%232b2929' d='M2 0L0 2h4z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-size:8px;}
</style>

<script>
let today = new Date().toISOString().split("T")[0];
document.getElementById("departure").min = today;
document.getElementById("return").min = today;

function updateVehicleType() {
    let selectedPlate = document.getElementById("plateSelect").value;
    let vehicleTypes = <?php echo json_encode($vehicles); ?>;
    document.getElementById("vehicleType").value = vehicleTypes[selectedPlate] || "";
}

function showLoader() {
    document.querySelector("#select").classList.add("hidden");
    document.querySelector("input").classList.add("hidden");
    document.querySelector("#plateSelect").classList.add("hidden");
    document.getElementById("loader").classList.remove("hidden");
}
</script>
</body>
</html>
