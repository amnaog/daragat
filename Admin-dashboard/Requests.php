<?php
include 'db.php'; // الاتصال بقاعدة البيانات

$studentRequests = $conn->query("SELECT * FROM student_requests ORDER BY created_at DESC");
$teacherRequests = $conn->query("SELECT * FROM teacher_requests ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registration Requests</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
  <style>
    :root {
      --primary-bg: #f8f9fa;
      --sidebar-bg: #1a202c;
      --card-bg: #ffffff;
      --text-dark: #2d3748;
      --text-light: #718096;
      --accent-green: #48bb78;
      --danger-red: #e53e3e;
      --border-color: #eef2f7;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: var(--primary-bg);
      color: var(--text-dark);
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: var(--sidebar-bg);
      color: #e2e8f0;
      padding: 20px;
      flex-shrink: 0;
      height: 100vh;
      position: fixed;
      display: flex;
      flex-direction: column;
    }

    .sidebar h2 {
      font-size: 1.4em;
      font-weight: bold;
      margin-bottom: 30px;
      text-align: center;
      color: white;
    }

    .sidebar a {
      color: #cbd5e0;
      text-decoration: none;
      display: block;
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 10px;
      font-weight: 600;
      transition: background-color 0.3s;
    }

    .sidebar a.active, .sidebar a:hover {
      background-color: #2d3748;
      color: #fff;
    }

    .main-content {
      margin-left: 250px;
      padding: 50px 60px 60px;
      max-width: 1200px;
    }

    .main-content header h1 {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    .main-content header p {
      color: var(--text-light);
      margin-bottom: 40px;
      font-size: 1.1em;
    }

    .filter-search-bar {
      display: flex;
      gap: 20px;
      margin-bottom: 35px;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter-search-bar input,
    .filter-search-bar select {
      padding: 12px 18px;
      font-size: 1em;
      border-radius: 10px;
      border: 1px solid var(--border-color);
      outline: none;
      font-family: 'Poppins', sans-serif;
    }

    .filter-search-bar input {
      flex: 1;
      min-width: 280px;
    }

    .filter-search-bar select {
      min-width: 180px;
    }

    .requests-grid {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 40px;
    }

    .request-card {
      width: 100%;
      max-width: 1000px;
      background-color: var(--card-bg);
      border-radius: 12px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      transition: box-shadow 0.2s;
    }

    .request-card:hover {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .card-header, .card-body, .card-footer {
      padding: 25px;
    }

    .card-header {
      border-bottom: 1px solid var(--border-color);
    }

    .card-header h3 {
      margin: 0 0 8px;
      font-size: 1.35em;
    }

    .card-header p {
      margin: 0;
      font-size: 1em;
      color: var(--text-light);
    }

    .card-header p strong {
      color: var(--text-dark);
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 18px;
      font-size: 1em;
    }

    .info-item i {
      color: var(--text-light);
      width: 22px;
      text-align: center;
      font-size: 1.2em;
    }

    .info-item span {
      font-weight: 600;
      color: var(--text-dark);
    }

    .btn-download {
      background-color: transparent;
      color: var(--text-dark);
      text-decoration: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .btn-download .download-icon {
      color: var(--text-light);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      width: 38px;
      height: 38px;
      display: inline-grid;
      place-items: center;
      transition: all 0.2s;
    }

    .btn-download:hover .download-icon {
      background-color: #edf2f7;
      color: var(--text-dark);
    }

    .card-footer {
      background-color: #fdfdff;
      border-top: 1px solid var(--border-color);
      display: flex;
      gap: 15px;
      border-bottom-left-radius: 12px;
      border-bottom-right-radius: 12px;
    }

    .card-footer .btn {
      flex: 1;
      padding: 12px;
      border: 1px solid transparent;
      border-radius: 8px;
      font-weight: 700;
      font-size: 1em;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .btn-approve {
      background-color: var(--accent-green);
      color: #fff;
    }

    .btn-approve:hover {
      background-color: #38a169;
    }

    .btn-reject {
      background-color: var(--card-bg);
      color: var(--text-light);
      border: 1px solid var(--border-color);
    }

    .btn-reject:hover {
      background-color: var(--danger-red);
      color: #fff;
      border-color: var(--danger-red);
    }
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="teachers.php">Teachers</a>
    <a href="students.php">Students</a>
    <a href="halaqat.php">Halaqat</a>
    <a class="active" href="Requests.php">Registration Requests</a>
  </div>
  <main class="main-content">
    <header>
      <h1>Registration Requests</h1>
      <p>Manage new registration requests for students and teachers.</p>
    </header>
    <div class="filter-search-bar">
      <input type="text" id="searchInput" placeholder="Search by name or email..." />
      <select id="roleFilter">
        <option value="">All</option>
        <option value="Teacher">Teacher</option>
        <option value="Student">Student</option>
      </select>
    </div>
    <div class="requests-grid">
      <?php while($row = $teacherRequests->fetch_assoc()): ?>
        <div class="request-card">
          <div class="card-header">
            <h3><?= htmlspecialchars($row['full_name']) ?></h3>
            <p>Request to register as a <strong>Teacher</strong></p>
          </div>
          <div class="card-body">
            <div class="info-item"><i class="fas fa-envelope"></i><span><?= htmlspecialchars($row['email']) ?></span></div>
            <div class="info-item"><i class="fas fa-phone"></i><span><?= htmlspecialchars($row['phone']) ?></span></div>
            <div class="info-item"><i class="fas fa-calendar-alt"></i><span><?= date("F d, Y", strtotime($row['created_at'])) ?></span></div>
            <div class="info-item">
            <a href="certificates/<?php echo $row['certificate_file']; ?>" class="btn-download" target="_blank">
                <span>Download Certificate</span>
                <span class="download-icon"><i class="fas fa-download"></i></span>
              </a>
            </div>
          </div>
          <div class="card-footer">
            <form method="POST" action="handle_request.php">
              <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="role" value="Teacher">
              <input type="hidden" name="action" value="reject">
              <button class="btn btn-reject"><i class="fas fa-times"></i> Reject</button>
            </form>
            <form method="POST" action="handle_request.php">
              <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="role" value="Teacher">
              <input type="hidden" name="action" value="approve">
              <button class="btn btn-approve"><i class="fas fa-check"></i> Approve</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>

      <?php while($row = $studentRequests->fetch_assoc()): ?>
        <div class="request-card">
          <div class="card-header">
            <h3><?= htmlspecialchars($row['full_name']) ?></h3>
            <p>Request to register as a <strong>Student</strong></p>
          </div>
          <div class="card-body">
            <div class="info-item"><i class="fas fa-envelope"></i><span><?= htmlspecialchars($row['email']) ?></span></div>
            <div class="info-item"><i class="fas fa-phone"></i><span><?= htmlspecialchars($row['phone']) ?></span></div>
            <div class="info-item"><i class="fas fa-calendar-alt"></i><span><?= date("F d, Y", strtotime($row['created_at'])) ?></span></div>
            <div class="info-item"><i class="fas fa-layer-group"></i><span><?= htmlspecialchars($row['level'] ?: 'New to memorization') ?></span></div>
          </div>
          <div class="card-footer">
            <form method="POST" action="handle_request.php">
              <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="role" value="Student">
              <input type="hidden" name="action" value="reject">
              <button class="btn btn-reject"><i class="fas fa-times"></i> Reject</button>
            </form>
            <form method="POST" action="handle_request.php">
              <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="role" value="Student">
              <input type="hidden" name="action" value="approve">
              <button class="btn btn-approve"><i class="fas fa-check"></i> Approve</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </main>
</div>
<script>
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');
  const cards = document.querySelectorAll('.request-card');
  function filterCards() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedRole = roleFilter.value.toLowerCase();
    cards.forEach(card => {
      const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
      const email = card.querySelector('.fa-envelope')?.nextElementSibling?.textContent.toLowerCase() || '';
      const roleText = card.querySelector('p')?.textContent.toLowerCase() || '';
      const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
      const matchesRole = selectedRole === '' || roleText.includes(selectedRole);
      card.style.display = (matchesSearch && matchesRole) ? 'flex' : 'none';
    });
  }
  searchInput.addEventListener('input', filterCards);
  roleFilter.addEventListener('change', filterCards);
</script>
</body>
</html>