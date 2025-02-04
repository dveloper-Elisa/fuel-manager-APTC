<?php

error_reporting(E_ALL);
ini_set("display_errors",1);

session_start();

if((!isset($_SESSION["phone"]) || !isset( $_SESSION["name"])) && (strtoupper($_SESSION['role']) !== 'D/CEO' || strtoupper($_SESSION['role']) !== 'CEO')){
    header("Location: login.php");
}

include("./connection.php");

if(isset($_GET["approve"])){

    // HANDLE FORM SUBMISSION
    if(isset($_POST['approve'])){
        $granted_quantinty = $_POST['received'];

        $id = $_GET["approve"];
        $verified_by = $_SESSION['name'];
        $approved_by = $_SESSION['name'];

        $approve = mysqli_query($db, "UPDATE fuel_request SET received_qty = '$granted_quantinty', approved_by = '$approved_by', status = 'approved' WHERE req_id = '$id'");

        if($approve){
            ?>
            <script>
                alert("Request Approved Successfully")
                window.location = "./requests.php"
            </script>
            <?php
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve request</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- <body class="bg-gray-100 flex items-center justify-center min-h-screen"> -->
<body class="bg-gray-100">
<div class="flex h-screen">
    <!-- including side bar -->
    <?php
    include "./components/side.php"

    ?>

    <!-- Main Content -->
    <div class="flex-1 p-6">
            <!-- Top Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded shadow-md">
                <h1 class="text-xl font-semibold text-lime-700">Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo "<b>".$_SESSION["name"]. "</b>" ?></span>
                </div>
            </div>


    <form action="" method="post" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <?php
        $id = $_GET["approve"];
        $sql = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM fuel_request WHERE req_id = '$id' "));
        ?>

        <label for="requested" class="block text-gray-700 font-semibold">Requested Quantity (L)</label>
        <input type="number" name="" id="requested" value=<?php echo $sql['requested_qty'] ?> disabled class="w-full p-2 border border-black rounded mb-4">
        
        <label for="received" class="block text-gray-700 font-semibold">Granted Quantity (L)</label>
        <input type="number" name="received" id="received" placeholder="Quantity in Liters (L)" required class="w-full p-2 border border-black rounded mb-4">
        
        <button type="submit" name="approve" class="w-full bg-lime-700 text-white p-2 rounded hover:bg-lime-800">Approve</button>
    </form>
    
    <?php

}else{
    header("Location: ./requests.php");
}

?>
</div>
</div>
</body>
</html>
