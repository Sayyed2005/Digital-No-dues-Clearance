<?php
include "config.php";

$id = $_GET['id'];
$type = $_GET['type'];

if($type == "library")
{
    mysqli_query($conn,"UPDATE dues SET library_status='Approved', library_due=0 WHERE student_id='$id'");
}
elseif($type == "lab")
{
    mysqli_query($conn,"UPDATE dues SET lab_status='Approved', lab_due=0 WHERE student_id='$id'");
}
elseif($type == "account")
{
    mysqli_query($conn,"UPDATE dues SET account_status='Approved', account_due=0 WHERE student_id='$id'");
}

/* CHECK FINAL STATUS */

$q = mysqli_query($conn,"SELECT * FROM dues WHERE student_id='$id'");
$d = mysqli_fetch_assoc($q);

if(
$d['library_status']=='Approved' &&
$d['lab_status']=='Approved' &&
$d['account_status']=='Approved'
)
{
    mysqli_query($conn,"UPDATE dues SET status='Cleared' WHERE student_id='$id'");
}

header("Location: approve.php");
?>