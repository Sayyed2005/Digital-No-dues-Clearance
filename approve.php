<?php
include "config.php";

$q = "SELECT s.student_id, s.name, d.*
      FROM students s
      JOIN dues d ON s.student_id = d.student_id";

$r = mysqli_query($conn,$q);
?>

<h2>Approval Panel</h2>

<table border="1">
<tr>
<th>Name</th>
<th>Library</th>
<th>Lab</th>
<th>Account</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($r)) { ?>

<tr>
<td><?php echo $row['name']; ?></td>

<td>
<?php echo $row['library_status']; ?>
<br>
<a href="update_status.php?id=<?php echo $row['student_id']; ?>&type=library">Approve</a>
</td>

<td>
<?php echo $row['lab_status']; ?>
<br>
<a href="update_status.php?id=<?php echo $row['student_id']; ?>&type=lab">Approve</a>
</td>

<td>
<?php echo $row['account_status']; ?>
<br>
<a href="update_status.php?id=<?php echo $row['student_id']; ?>&type=account">Approve</a>
</td>

<td><?php echo $row['status']; ?></td>

</tr>

<?php } ?>

</table>