<?php
session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}
?>

<h2>Admin Dashboard</h2>

<a href="upload.php">Upload Students</a><br><br>
<a href="logout.php">Logout</a>