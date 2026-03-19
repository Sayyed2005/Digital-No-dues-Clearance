<?php
session_start();
include "config.php";

if(isset($_POST['login']))
{
    $prn = $_POST['prn'];

    $q = "SELECT * FROM students WHERE prn='$prn'";
    $r = mysqli_query($conn,$q);

    if(mysqli_num_rows($r)>0)
    {
        $row = mysqli_fetch_assoc($r);

        $_SESSION['student_id'] = $row['student_id'];
        $_SESSION['prn'] = $row['prn'];

        header("Location: student_dashboard.php");
    }
    else
    {
        echo "Invalid PRN";
    }
}
?>

<h2>Student Login</h2>

<form method="post">
PRN:
<input type="text" name="prn" required>

<button name="login">Login</button>
</form>