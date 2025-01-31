<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="./styles/login.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class='bg-green-400 flex items-center justify-center min-h-screen'>
    <div id="conatiner" class="text-lime-700 bg-white font-bold">
        <form action="./pannel/login.php" method="POST" class="bg-white flex flex-col gap-5 p-10 rounded-lg text-center">
            <p>Welcome Login Page</p>
            <div class="loginform flex flex-col gap-10">
                <input type="number" name="phone" placeholder="Phone Number" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none">
                <input type="password" name="password" placeholder="Password" class="w-full border-2 border-blue-500 rounded-md p-[3px] focus:ring focus:outline-none">
                <input type="submit" name="login" value="Login" class='w-full border-0 bg-green-800 text-white font-bold rounded-md p-2 hover:cursor-pointer hover:bg-green-700'>
            </div>
        </form>
    </div>
    
</body>
</html>