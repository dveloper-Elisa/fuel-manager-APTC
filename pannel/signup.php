<?php
include "../connection.php";
$success = '';
$errors = [];

function registerStaff($db, $names, $phone, $password, $sex, $email, $degree, $province, $district, $sector)
{
    global $errors, $success;

    try {
        $insertQuery = "INSERT INTO `staff`(`stf_names`, `stf_phoneno`, `stf_pwd_vis`, `stf_gender`, `stf_email`, `stf_degree`, `stf_prov`, `stf_dist`, `stf_sect`, `stf_status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')";

        $stmt = $db->prepare($insertQuery);
        $stmt->bind_param('sssssssss', $names, $phone, $password, $sex, $email, $degree, $province, $district, $sector);

        if ($stmt->execute()) {
            $success = 'Staff Registered Successfully!';
        } else {
            $errors[] = 'Error: Staff not registered';
        }
    } catch (Exception $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $names = trim($_POST['names']);
    $email = trim($_POST['email']);
    $sex = $_POST['gender'];
    $phone = "25" . trim($_POST['phone']);
    $degree = trim($_POST['degree']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $sector = trim($_POST['sector']);
    $password = trim($_POST['password']);

    if (empty($names) || empty($email) || empty($sex) || empty($phone) || empty($degree) || empty($province) || empty($district) || empty($sector) || empty($password)) {
        $errors[] = "Please fill in all fields.";
    } else {
        registerStaff($db, $names, $phone, $password, $sex, $email, $degree, $province, $district, $sector);
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="./styles/login.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class='bg-green-400 flex items-center justify-center min-h-screen'>
    <div id="conatiner" class="text-lime-700 bg-white font-bold">


        <form action="" method="POST" class="bg-white flex flex-col gap-5 p-10 rounded-lg text-center" onsubmit="showLoader()">
            <p>Signup Page</p>
            <?php if (!empty($success)): ?>
                <div class='text-green-500 bg-green-100 p-2 rounded-md'>
                    <?= $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class='text-red-500 bg-red-100 p-2 rounded-md'>
                        <?= $error; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <input type="text" name="names" placeholder="Names" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
            <div class="flex gap-5 flex-col sm:flex-col xsm:flex-col md:flex-row lg:flex-row xl:flex-row">

                <div class="loginform flex flex-col gap-5">
                    <input type="text" name="email" placeholder="Your Email" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
                    <div class="flex flex-col items-left">
                        <div>
                            <p class="flex flex-row gap-5">
                                <input type="radio" name="gender" value="M" class="" required><span>Male</span>
                                <input type="radio" name="gender" value="F" class="" required><span>Female</span>
                            </p>
                        </div>
                    </div>
                    <input type="number" name="phone" placeholder="Phone Number" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
                    <input type="text" name="degree" placeholder="Your Qualification" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
                </div>
                <div class="loginform flex flex-col gap-5">
                    <input type="text" name="province" placeholder="Province" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
                    <input type="text" name="district" placeholder="District" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
                    <input type="text" name="sector" placeholder="Sector" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>
                    <input type="password" name="password" placeholder="Password" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none" required>


                </div>
            </div>
            <input type="submit" name="signup" value="Signup" class='w-full border-0 bg-green-800 text-white font-bold rounded-md p-2 hover:cursor-pointer hover:bg-green-700'>

            <p>If have an account <a href="../login.php" class="hover text-blue-500 hover:font-bold"> Login </a> </p>
        </form>

        <!-- Loader -->
        <div id="loader" class="hidden mt-4 flex justify-center">
            <div class="w-8 h-8 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
        </div>
    </div>


</body>

</html>