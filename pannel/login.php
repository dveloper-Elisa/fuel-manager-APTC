<?php
include "../connection.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    if (empty($phone) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location='../login.php';</script>";
        exit();
    }

    // Append country code properly
    $full_phone = '25' . $phone;

    // Use a prepared statement for security
    $stmt = $db->prepare("SELECT stf_phoneno, stf_pwd_vis, stf_status, stf_code, stf_names, stf_position FROM staff WHERE stf_phoneno = ?");
    $stmt->bind_param("s", $full_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password (assuming `stf_pwd_vis` is hashed using password_hash)
        if (strtolower($row['stf_status']) !== 'inactive') {

            if (($password === $row['stf_pwd_vis'])) {
                // Store user details in session
                $_SESSION['phone'] = $row['stf_phoneno'];
                $_SESSION['staff_code'] = $row['stf_code'];
                $_SESSION['name'] = $row['stf_names'];
                $_SESSION['role'] = $row['stf_position'];
                echo "<script>alert('Login Successful'); window.location='../dashboard.php';</script>";
            } else {
                echo "<script>alert('Invalid password. Please try again.'); window.location='../login.php';</script>";
            }
        } else {
            echo "<script>alert('Account Innactive'); window.location='../login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found. Please check your phone number.'); window.location='../login.php';</script>";
    }

    $stmt->close();
    $db->close();
}
