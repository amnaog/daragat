<?php
include 'db.php'; // الاتصال بقاعدة البيانات
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Students - QuranFlow</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <h2 class="logo">QuranFlow</h2>
    <ul class="menu">
      <li><a href="index.php">Dashboard</a></li>
      <li><a href="students.php">Students</a></li>
      <li><a href="progress.php">Progress</a></li>
      <li><a href="messages.php">Messages <span class="badge">3</span></a></li>
    </ul>
    <div class="user-info">
      <div class="avatar"></div>
      <div class="email">Sheikh Abdullah<br><small>sheikh.abdullah@quran.com</small></div>
    </div>
  </div>

  <div class="main">
    <header>
      <h2>Students</h2>
    </header>

    <section class="students-section">
      <h3>All Students</h3>
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Level</th>
            <th>Progress</th>
            <th>Last Memorized</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT * FROM students";
          $result = mysqli_query($conn, $query);

          if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)) :
          ?>
              <tr>
                <td><div class="avatar-sm"></div> <?= htmlspecialchars($row['full_name']) ?></td>
                <td>--</td> <!-- يمكنك تحديثها لاحقًا ببيانات المستوى -->
                <td>
                  <div class="progress">
                    <div class="bar" style="width: 0%"></div>
                  </div>
                  0%
                </td>
                <td>--</td>
                <td>🗒</td>
              </tr>
          <?php
            endwhile;
          else:
          ?>
            <tr><td colspan="5">No students found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>