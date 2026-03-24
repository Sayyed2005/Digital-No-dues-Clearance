<?php
include "config.php";

$q = "SELECT s.student_id, s.name, d.*
      FROM students s
      JOIN dues d ON s.student_id = d.student_id";

$r = mysqli_query($conn,$q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Panel | Student Dues</title>
    <!-- Google Fonts + Font Awesome -->
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
            background: #f0f9ff;
            color: #1e293b;
            padding: 2rem 1.5rem;
            line-height: 1.5;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* header */
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
        .back-link {
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
        }
        .back-link:hover {
            background: #eef6fc;
            border-color: #7ab3c8;
            transform: translateY(-1px);
        }

        /* stats row */
        .stats-row {
            margin-bottom: 1.2rem;
        }
        .record-count {
            background: #e7f3fc;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #136b9c;
            display: inline-block;
        }
        .record-count i {
            margin-right: 5px;
        }

        /* table wrapper */
        .table-wrapper {
            background: white;
            border-radius: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid #e9f0f5;
            overflow-x: auto;
        }
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 800px;
        }
        .approval-table th {
            text-align: left;
            padding: 1rem 1rem;
            background-color: #fafdff;
            border-bottom: 1px solid #e2edf7;
            font-weight: 600;
            color: #2b5c7c;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }
        .approval-table td {
            padding: 1rem 1rem;
            border-bottom: 1px solid #eff4fa;
            vertical-align: middle;
            color: #1e374b;
        }
        .approval-table tr:hover td {
            background-color: #f6fbfe;
        }

        /* status badge */
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
        .status-badge.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .due-cell {
            min-width: 110px;
        }
        .approve-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #eef2ff;
            color: #2563eb;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            border: 1px solid #cddcff;
            transition: 0.1s;
            margin-top: 6px;
        }
        .approve-link:hover {
            background: #e0e7ff;
            transform: translateY(-1px);
        }
        .overall-status {
            font-weight: 600;
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
            body { padding: 1rem; }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Header with back link (optional) -->
    <div class="dashboard-header">
        <div class="title-section">
            <h1><i class="fas fa-check-circle" style="color:#2c7cb6; margin-right: 8px;"></i> Approval Panel</h1>
            <p>Review and approve individual dues per student</p>
        </div>
        <a href="admin_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <?php
    $total_rows = mysqli_num_rows($r);
    ?>
    <div class="stats-row">
        <div class="record-count"><i class="fas fa-users"></i> <?php echo $total_rows; ?> student(s) pending/approved</div>
    </div>

    <div class="table-wrapper">
        <table class="approval-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Library Due</th>
                    <th>Lab Due</th>
                    <th>Account Due</th>
                    <th>Overall Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($r) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($r)): 
                    // Determine status classes
                    $lib_status = $row['library_status'] ?? '';
                    $lab_status = $row['lab_status'] ?? '';
                    $acc_status = $row['account_status'] ?? '';
                    $overall = $row['status'] ?? 'Pending';
                    
                    $lib_badge_class = ($lib_status == 'Approved') ? 'approved' : '';
                    $lab_badge_class = ($lab_status == 'Approved') ? 'approved' : '';
                    $acc_badge_class = ($acc_status == 'Approved') ? 'approved' : '';
                    $overall_badge_class = ($overall == 'Cleared') ? 'approved' : '';
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    
                    <!-- Library column: status + approve link -->
                    <td class="due-cell">
                        <span class="status-badge <?php echo $lib_badge_class; ?>">
                            <?php echo $lib_status ?: 'Pending'; ?>
                        </span>
                        <br>
                        <a href="update_status.php?id=<?php echo $row['student_id']; ?>&type=library" class="approve-link">
                            <i class="fas fa-thumbs-up"></i> Approve
                        </a>
                    </td>
                    
                    <!-- Lab column -->
                    <td class="due-cell">
                        <span class="status-badge <?php echo $lab_badge_class; ?>">
                            <?php echo $lab_status ?: 'Pending'; ?>
                        </span>
                        <br>
                        <a href="update_status.php?id=<?php echo $row['student_id']; ?>&type=lab" class="approve-link">
                            <i class="fas fa-thumbs-up"></i> Approve
                        </a>
                    </td>
                    
                    <!-- Account column -->
                    <td class="due-cell">
                        <span class="status-badge <?php echo $acc_badge_class; ?>">
                            <?php echo $acc_status ?: 'Pending'; ?>
                        </span>
                        <br>
                        <a href="update_status.php?id=<?php echo $row['student_id']; ?>&type=account" class="approve-link">
                            <i class="fas fa-thumbs-up"></i> Approve
                        </a>
                    </td>
                    
                    <!-- Overall status -->
                    <td class="overall-status">
                        <span class="status-badge <?php echo $overall_badge_class; ?>">
                            <?php echo $overall; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr class="empty-row">
                    <td colspan="5"><i class="fas fa-info-circle"></i> No records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
        <i class="fas fa-shield-alt"></i> Approval Panel — Secure admin actions
    </footer>
</div>

<script>
    // Optional: any JS enhancements
    document.querySelectorAll('.approve-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Just to confirm – you can add a confirmation dialog if needed, but original functionality remains
            if(!confirm('Approve this due? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>
