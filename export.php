<?php
include "config.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=students.csv');

$output = fopen("php://output", "w");

fputcsv($output, ['Name','PRN','Library','Lab','Account','Status']);

$q = mysqli_query($conn,"SELECT s.name, s.prn, d.library_due, d.lab_due, d.account_due, d.status
                         FROM students s
                         LEFT JOIN dues d ON s.student_id=d.student_id");

while($row=mysqli_fetch_assoc($q))
{
    fputcsv($output,$row);
}

fclose($output);
?>