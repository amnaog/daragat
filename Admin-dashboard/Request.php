<?php
// Include the database connection file.
include 'db.php';

// SQL query to fetch all pending requests with the new fields.
$sql = "SELECT id, full_name, email, phone, memorization_level, certificate_path, request_date 
        FROM teachers 
       /*WHERE status = 'pending' */
        ORDER BY created_at DESC";
/*$result = $conn->query($sql); */
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Requests</title>
    <!-- Link to your main CSS file -->
    <link rel="stylesheet" href="style.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">Quran Circle<span class="admin-badge">Admin</span></div>
            <nav>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt fa-fw"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-chalkboard-teacher fa-fw"></i> Teachers</a></li>
                    <li><a href="#"><i class="fas fa-user-graduate fa-fw"></i> Students</a></li>
                    <li><a href="#"><i class="fas fa-ring fa-fw"></i> Halaqat</a></li>
                    <li class="active"><a href="requests.php"><i class="fas fa-inbox fa-fw"></i> Requests</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar fa-fw"></i> Reports</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Registration Requests</h1>
                <p>Manage new registration requests for students and teachers.</p>
            </header>
            
            <div class="requests-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="request-card">
                            <div class="card-header">
                                <div class="role-icon <?php echo htmlspecialchars($row['role']); ?>">
                                    <i class="fas <?php echo ($row['role'] == 'teacher') ? 'fa-chalkboard-user' : 'fa-user-graduate'; ?>"></i>
                                </div>
                                <div class="applicant-info">
                                    <h3><?php echo htmlspecialchars($row['full_name']); ?></h3>
                                    <p>Request to register as a <strong><?php echo ucfirst(htmlspecialchars($row['role'])); ?></strong></p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <i class="fas fa-envelope fa-fw"></i>
                                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-phone fa-fw"></i>
                                    <span><?php echo htmlspecialchars($row['phone']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt fa-fw"></i>
                                    <span><?php echo date('F d, Y', strtotime($row['request_date'])); ?></span>
                                </div>

                                <?php if ($row['role'] == 'student'): ?>
                                    <div class="info-item">
                                        <i class="fas fa-layer-group fa-fw"></i>
                                        <?php 
                                            $level = htmlspecialchars($row['memorization_level']);
                                            $level_class = (strpos($level, 'Juz') !== false) ? 'juz' : 'new';
                                        ?>
                                        <span class="level <?php echo $level_class; ?>"><?php echo $level; ?></span>
                                    </div>
                                <?php else: // Teacher ?>
                                    <div class="info-item">
                                        <i class="fas fa-file-alt fa-fw"></i>
                                        <a href="<?php echo htmlspecialchars($row['certificate_path']); ?>" class="btn-download" target="_blank">
                                            <i class="fas fa-download"></i> Download Certificate
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="handle_request.php?id=<?php echo $row['id']; ?>&action=reject" class="btn btn-reject"><i class="fas fa-times"></i> Reject</a>
                                <a href="handle_request.php?id=<?php echo $row['id']; ?>&action=approve" class="btn btn-approve"><i class="fas fa-check"></i> Approve</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-requests">No pending requests at the moment.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
