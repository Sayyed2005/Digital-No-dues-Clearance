<?php
include "config.php";

$id = $_GET['id'];

$q = mysqli_query($conn,"SELECT s.*, d.*
                         FROM students s
                         JOIN dues d ON s.student_id=d.student_id
                         WHERE s.student_id='$id'");

$data = mysqli_fetch_assoc($q);
?>

<h2>Clearance Report</h2>

Name: <?php echo $data['name']; ?><br>
PRN: <?php echo $data['prn']; ?><br>

<hr>

Library: <?php echo $data['library_status']; ?><br>
Lab: <?php echo $data['lab_status']; ?><br>
Account: <?php echo $data['account_status']; ?><br>

<hr>

Final Status: <b><?php echo $data['status']; ?></b>

<br><br>
<a href="report.php?id=<?php echo $row['student_id']; ?>">Report</a>
<button onclick="window.print()">Print</button>