<?php
session_start();
include "config.php";

if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $q = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $r = mysqli_query($conn,$q);

    if(mysqli_num_rows($r) > 0)
    {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
    }
    else
    {
        echo "Invalid Login";
    }
}
?>

<form method="post">
Username:
<input type="text" name="username" required><br>

Password:
<input type="password" name="password" required><br>

<button name="login">Login</button>
</form>