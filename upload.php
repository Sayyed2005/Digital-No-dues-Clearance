<?php
include "config.php";
?>

<form action="process_upload.php" method="post" enctype="multipart/form-data">

Department:
<select name="dept">
<?php
$q=mysqli_query($conn,"SELECT * FROM departments");
while($d=mysqli_fetch_assoc($q))
{
 echo "<option value='".$d['dept_id']."'>".$d['dept_name']."</option>";
}
?>
</select><br><br>

Year:
<select name="year">
<?php
$q=mysqli_query($conn,"SELECT * FROM years");
while($d=mysqli_fetch_assoc($q))
{
 echo "<option value='".$d['year_id']."'>".$d['year_name']."</option>";
}
?>
</select><br><br>

Division:
<select name="division">
<?php
$q=mysqli_query($conn,"SELECT * FROM divisions");
while($d=mysqli_fetch_assoc($q))
{
 echo "<option value='".$d['division_id']."'>".$d['division_name']."</option>";
}
?>
</select><br><br>

Upload CSV:
<input type="file" name="file" required><br><br>

<button name="upload">Upload</button>

</form>