<?php
include "config.php";

$id = $_GET['id'];

$q = mysqli_query($conn,"SELECT * FROM dues WHERE student_id='$id'");
$data = mysqli_fetch_assoc($q);

if(isset($_POST['update']))
{
    $lib = $_POST['library'];
    $lab = $_POST['lab'];
    $acc = $_POST['account'];

    mysqli_query($conn,"UPDATE dues SET 
    library_due='$lib',
    lab_due='$lab',
    account_due='$acc'
    WHERE student_id='$id'");

    header("Location: admin_dashboard.php");
}
?>

<h2>Edit Dues</h2>

<form method="post">

Library:
<input type="number" name="library" value="<?php echo $data['library_due']; ?>"><br>

Lab:
<input type="number" name="lab" value="<?php echo $data['lab_due']; ?>"><br>

Account:
<input type="number" name="account" value="<?php echo $data['account_due']; ?>"><br>

<button name="update">Update</button>

</form>