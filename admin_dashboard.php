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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Dashboard | Student Dues Manager</title>
    <!-- Google Fonts + Font Awesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f9ff;   /* soft sky background */
            color: #1e293b;
            padding: 2rem 1.5rem;
            line-height: 1.5;
        }

        /* main container */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* header section */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .title-section h1 {
            font-size: 1.9rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0b4f6c, #1e88e5);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.3px;
        }
        .title-section p {
            color: #4b6a8b;
            font-size: 0.9rem;
            margin-top: 0.2rem;
        }
        .export-btn {
            background: white;
            padding: 0.6rem 1.4rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f6b9e;
            border: 1px solid #cbdff2;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .export-btn i {
            font-size: 0.9rem;
        }
        .export-btn:hover {
            background: #eef6fc;
            border-color: #7ab3c8;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.03);
        }

        /* filter card */
        .filter-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02), 0 2px 6px rgba(0, 32, 64, 0.05);
            margin-bottom: 2rem;
            padding: 1.5rem 1.8rem;
            border: 1px solid #e2edf7;
            transition: box-shadow 0.2s;
        }
        .filter-grid {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 1rem;
            row-gap: 1.2rem;
        }
        .filter-group {
            flex: 1 1 160px;
            min-width: 140px;
        }
        .filter-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #527a9b;
            margin-bottom: 0.4rem;
        }
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border-radius: 14px;
            border: 1px solid #cfdfed;
            background: #fefefe;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            transition: 0.2s;
            outline: none;
            color: #1e2f3f;
        }
        .filter-group input:focus,
        .filter-group select:focus {
            border-color: #4aa3d4;
            box-shadow: 0 0 0 3px rgba(74, 163, 212, 0.15);
        }
        .search-group {
            flex: 2 1 220px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-left: auto;
        }
        .btn-primary, .btn-secondary {
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 36px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #1e88e5;
            color: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-primary:hover {
            background: #0f6b9e;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #2c5a7a;
            border: 1px solid #dce5ed;
        }
        .btn-secondary:hover {
            background: #e6edf4;
            transform: translateY(-1px);
        }

        /* stats summary row */
        .stats-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
            gap: 0.8rem;
        }
        .record-count {
            background: #e7f3fc;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #136b9c;
        }
        .record-count i {
            margin-right: 5px;
        }

        /* table container */
        .table-wrapper {
            background: white;
            border-radius: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid #e9f0f5;
            overflow-x: auto;
            padding: 0;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 680px;
        }
        .student-table th {
            text-align: left;
            padding: 1rem 1rem;
            background-color: #fafdff;
            border-bottom: 1px solid #e2edf7;
            font-weight: 600;
            color: #2b5c7c;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }
        .student-table td {
            padding: 1rem 1rem;
            border-bottom: 1px solid #eff4fa;
            vertical-align: middle;
            color: #1e374b;
        }
        .student-table tr:hover td {
            background-color: #f6fbfe;
        }
        /* badge style */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.8rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.7rem;
            text-align: center;
            background: #fef3c7;
            color: #b45309;
        }
        .status-badge.cleared {
            background: #dcfce7;
            color: #15803d;
        }
        .due-number {
            font-weight: 500;
            font-family: monospace;
            font-size: 0.85rem;
        }
        .action-links a {
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-block;
            margin-right: 8px;
            transition: 0.1s;
        }
        .edit-link {
            background: #eef2ff;
            color: #2563eb;
            border: 1px solid #cddcff;
        }
        .report-link {
            background: #f0f9ff;
            color: #0f6b9e;
            border: 1px solid #cae3f2;
        }
        .edit-link:hover, .report-link:hover {
            transform: translateY(-1px);
            filter: brightness(0.96);
        }
        .empty-row td {
            text-align: center;
            padding: 2rem;
            color: #6c8eae;
        }
        footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.7rem;
            color: #6c8eae;
        }

        @media (max-width: 760px) {
            body {
                padding: 1rem;
            }
            .filter-card {
                padding: 1.2rem;
            }
            .action-buttons {
                margin-left: 0;
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    
    <!-- HEADER with export -->
    <div class="dashboard-header">
        <div class="title-section">
            <h1><i class="fas fa-graduation-cap" style="color:#2c7cb6; margin-right: 8px;"></i> Dues Manager</h1>
            <p>Track & manage student dues — library, lab, account</p>
        </div>
        <a href="export.php" class="export-btn"><i class="fas fa-file-csv"></i> Export CSV</a>
    </div>

    <!-- FILTER CARD (modern) -->
    <div class="filter-card">
        <form method="get" id="filterForm">
            <div class="filter-grid">
                <div class="filter-group search-group">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" placeholder="Name or PRN" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-building"></i> Department</label>
                    <select name="dept">
                        <option value="">All Departments</option>
                        <?php
                        $dept_q = mysqli_query($conn,"SELECT * FROM departments");
                        while($d = mysqli_fetch_assoc($dept_q)){
                            $selected = ($dept == $d['dept_id']) ? 'selected' : '';
                            echo "<option value='".$d['dept_id']."' $selected>".htmlspecialchars($d['dept_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Year</label>
                    <select name="year">
                        <option value="">All Years</option>
                        <?php
                        $year_q = mysqli_query($conn,"SELECT * FROM years");
                        while($y = mysqli_fetch_assoc($year_q)){
                            $selected = ($year == $y['year_id']) ? 'selected' : '';
                            echo "<option value='".$y['year_id']."' $selected>".htmlspecialchars($y['year_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-layer-group"></i> Division</label>
                    <select name="division">
                        <option value="">All Divisions</option>
                        <?php
                        $div_q = mysqli_query($conn,"SELECT * FROM divisions");
                        while($div = mysqli_fetch_assoc($div_q)){
                            $selected = ($division == $div['division_id']) ? 'selected' : '';
                            echo "<option value='".$div['division_id']."' $selected>".htmlspecialchars($div['division_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-flag-checkered"></i> Status</label>
                    <select name="status">
                        <option value="">All</option>
                        <option value="Pending" <?php if($status == "Pending") echo "selected"; ?>>Pending</option>
                        <option value="Cleared" <?php if($status == "Cleared") echo "selected"; ?>>Cleared</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Apply</button>
                    <a href="?<?php echo htmlspecialchars(strtok($_SERVER["QUERY_STRING"],'&')); ?>" class="btn-secondary"><i class="fas fa-undo-alt"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>

    <?php
    // Count total rows for information
    $count_result = mysqli_query($conn, $query);
    $total_rows = mysqli_num_rows($count_result);
    ?>
    <div class="stats-row">
        <div class="record-count"><i class="fas fa-users"></i> <?php echo $total_rows; ?> student(s) found</div>
        <div style="font-size:0.75rem; color:#5382a0;"><i class="fas fa-info-circle"></i> Click "Edit" to update dues</div>
    </div>

    <!-- TABLE SECTION -->
    <div class="table-wrapper">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>PRN</th>
                    <th>Library Due (₹)</th>
                    <th>Lab Due (₹)</th>
                    <th>Account Due (₹)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): 
                    // format due values
                    $lib_due = isset($row['library_due']) && $row['library_due'] != '' ? number_format((float)$row['library_due'], 2) : '0.00';
                    $lab_due = isset($row['lab_due']) && $row['lab_due'] != '' ? number_format((float)$row['lab_due'], 2) : '0.00';
                    $acc_due = isset($row['account_due']) && $row['account_due'] != '' ? number_format((float)$row['account_due'], 2) : '0.00';
                    
                    // derive status display: null or 'Pending' => Pending, else Cleared
                    $raw_status = $row['status'] ?? '';
                    $display_status = ($raw_status == 'Cleared') ? 'Cleared' : 'Pending';
                    $badge_class = ($display_status == 'Cleared') ? 'cleared' : '';
            ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['prn']); ?></td>
                    <td class="due-number">₹ <?php echo $lib_due; ?></td>
                    <td class="due-number">₹ <?php echo $lab_due; ?></td>
                    <td class="due-number">₹ <?php echo $acc_due; ?></td>
                    <td><span class="status-badge <?php echo $badge_class; ?>"><?php echo $display_status; ?></span></td>
                    <td class="action-links">
                        <a href="update_dues.php?id=<?php echo $row['student_id']; ?>" class="edit-link"><i class="fas fa-edit"></i> Edit</a>
                        <a href="report.php?id=<?php echo $row['student_id']; ?>" class="report-link"><i class="fas fa-chart-line"></i> Report</a>
                    </td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr class="empty-row">
                    <td colspan="7">📭 No students match the selected filters. Try resetting or changing criteria.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <i class="fas fa-shield-alt"></i> Secure Admin Panel | Dues Management System
    </footer>
</div>

<!-- optional JS for subtle interactions -->
<script>
    (function() {
        // keep placeholder interactions smooth
        const filterForm = document.getElementById('filterForm');
        if(filterForm) {
            // optional: prevent double submit, just normal get
        }
        // add small hover feedback (just to meet js requirement)
        const rows = document.querySelectorAll('.student-table tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transition = 'background 0.1s';
            });
        });
        console.log("Modern dashboard ready — sky blue theme active");
    })();
</script>
</body>
</html>
