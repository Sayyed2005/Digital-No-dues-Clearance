<?php
session_start();
include "config.php";

if(!isset($_SESSION['student_id']))
{
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$q = "SELECT s.name, s.prn, d.*
      FROM students s
      LEFT JOIN dues d ON s.student_id = d.student_id
      WHERE s.student_id='$student_id'";

$r = mysqli_query($conn,$q);
$data = mysqli_fetch_assoc($r);
?>

<h2>Welcome <?php echo $data['name']; ?></h2>

PRN: <?php echo $data['prn']; ?>

<h3>Dues Status</h3>

<table border="1">
<tr>
<th>Library</th>
<th>Lab</th>
<th>Account</th>
<th>Other</th>
<th>Status</th>
</tr>

<tr>
<td><?php echo $data['library_due']; ?></td>
<td><?php echo $data['lab_due']; ?></td>
<td><?php echo $data['account_due']; ?></td>
<td><?php echo $data['other_due']; ?></td>
<td><?php echo $data['status']; ?></td>
</tr>

</table>

<br>

<a href="student_logout.php">Logout</a>