<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>


<body>
    <script src="https://cdn.tailwindcss.com"></script>


    <!-- Modal -->
    <div id="statusModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h2 class="text-center text-5 font-bold text-gray-700">✅</h2>
            <p id="status" class="text-center text-10 capitalize mt-4 text-gray-600"></p>
        </div>
    </div>

</body>


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
?>
                <script>
                    document.getElementById("statusModal").classList.remove("hidden")
                    document.getElementById("status").innerHTML = "Login Successful"

                    setTimeout(() => {
                        window.location = '../dashboard.php'
                    }, 3000)
                </script>
            <?php
            } else {
            ?>
                <script>
                    document.getElementById("statusModal").classList.remove("hidden")
                    document.getElementById("status").innerHTML = "Invalid password. Please try again."
                    setTimeout(() => {
                        window.location = '../login.php';
                    })
                </script>
            <?php
            }
        } else {
            ?>
            <script>
                document.getElementById("statusModal").classList.remove("hidden")
                document.getElementById("status").innerHTML = "Account Innactive"
                setTimeout(() => {
                    window.location = '../login.php';
                })
            </script>
        <?php
        }
    } else {
        ?>
        <script>
            document.getElementById("statusModal").classList.remove("hidden")
            document.getElementById("status").innerHTML = "User not found. Please check your phone number."
            setTimeout(() => {
                window.location = '../login.php';
            })
        </script>
<?php
    }

    $stmt->close();
    $db->close();
}
