<?php
session_start();
include "config.php";

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

/* FILTER VALUES */
$dept = $_GET['dept'] ?? '';
$year = $_GET['year'] ?? '';
$division = $_GET['division'] ?? '';
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$query = "SELECT s.*, d.*
          FROM students s
          LEFT JOIN dues d ON s.student_id = d.student_id
          WHERE 1";

/* APPLY FILTERS */

if($dept != '')
{
    $query .= " AND s.dept_id='$dept'";
}

if($year != '')
{
    $query .= " AND s.year_id='$year'";
}

if($division != '')
{
    $query .= " AND s.division_id='$division'";
}

if($search != '')
{
    $query .= " AND (s.name LIKE '%$search%' OR s.prn LIKE '%$search%')";
}

/* FIXED STATUS FILTER */
if($status != '')
{
    if($status == "Pending")
    {
        $query .= " AND (d.status='Pending' OR d.status IS NULL)";
    }
    else
    {
        $query .= " AND d.status='Cleared'";
    }
}

$result = mysqli_query($conn,$query);
?>
<h2>Admin Dashboard</h2>

<form method="get">

Search:
<input type="text" name="search">

Department:
<select name="dept">
<option value="">All</option>
<?php
$q=mysqli_query($conn,"SELECT * FROM departments");
while($d=mysqli_fetch_assoc($q))
{
 echo "<option value='".$d['dept_id']."'>".$d['dept_name']."</option>";
}
?>
</select>

Year:
<select name="year">
<option value="">All</option>
<?php
$q=mysqli_query($conn,"SELECT * FROM years");
while($d=mysqli_fetch_assoc($q))
{
 echo "<option value='".$d['year_id']."'>".$d['year_name']."</option>";
}
?>
</select>

Division:
<select name="division">
<option value="">All</option>
<?php
$q=mysqli_query($conn,"SELECT * FROM divisions");
while($d=mysqli_fetch_assoc($q))
{
 echo "<option value='".$d['division_id']."'>".$d['division_name']."</option>";
}
?>
</select>
Status:
<select name="status">
<option value="">All</option>
<option value="Pending">Pending</option>
<option value="Cleared">Cleared</option>
</select>
<button type="submit">Filter</button>
<a href="export.php">Export CSV</a>
</form>

<br>

<table border="1">
<tr>
<th>Name</th>
<th>PRN</th>
<th>Library</th>
<th>Lab</th>
<th>Account</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)) { ?>

<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['prn']; ?></td>

<td><?php echo $row['library_due']; ?></td>
<td><?php echo $row['lab_due']; ?></td>
<td><?php echo $row['account_due']; ?></td>

<td><?php echo $row['status']; ?></td>

<td>
<a href="update_dues.php?id=<?php echo $row['student_id']; ?>">Edit</a>
<a href="report.php?id=<?php echo $row['student_id']; ?>">Report</a>
</td>

</tr>

<?php } ?>

</table>