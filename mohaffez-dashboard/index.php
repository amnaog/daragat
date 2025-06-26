<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QuranFlow Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
      <h1>Welcome back, Sheikh Abdullah!</h1>
      <p>Here's your dashboard for today.</p>
      <input type="text" class="search" placeholder="Search students or surahs...">
    </header>

    <section class="halqat-overview">
      <div class="halqa active">
        <h3>Fajr Halqa</h3>
        <p>🕒 Daily, 5:30 AM - 7:00 AM</p>
        <p>👥 4 Students</p>
        <button class="edit-btn">Edit</button>
        <button class="view-btn">View Students</button>
      </div>
      <div class="halqa">
        <h3>Asr Children's Class</h3>
        <p>🕒 Mon, Wed, Fri, 4:00 PM - 5:00 PM</p>
        <p>👥 8 Students</p>
        <button class="edit-btn">Edit</button>
        <button class="view-btn">View Students</button>
      </div>
      <div class="halqa">
        <h3>Isha Advanced Group</h3>
        <p>🕒 Tue, Thu, 9:00 PM - 10:30 PM</p>
        <p>👥 5 Students</p>
        <button class="edit-btn">Edit</button>
        <button class="view-btn">View Students</button>
      </div>
    </section>

    <section class="students-section">
      <h2>Students in Fajr Halqa</h2>
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
          <tr>
            <td><div class="avatar-sm"></div> Yusuf Ahmed</td>
            <td>Juz Amma</td>
            <td><div class="progress"><div class="bar" style="width:85%"></div></div>85%</td>
            <td>Surah Al-Asr on 2025-06-13</td>
            <td>🗒</td>
          </tr>
          <tr>
            <td><div class="avatar-sm"></div> Fatima Al-Fihri</td>
            <td>Juz Tabarak</td>
            <td><div class="progress"><div class="bar" style="width:45%"></div></div>45%</td>
            <td>Surah Al-Mulk on 2025-06-12</td>
            <td>🗒</td>
          </tr>
          <tr>
            <td><div class="avatar-sm"></div> Ali ibn Abi Talib</td>
            <td>Full Quran Review</td>
            <td><div class="progress"><div class="bar" style="width:95%"></div></div>95%</td>
            <td>Surah Al-Baqarah on 2025-06-14</td>
            <td>🗒</td>
          </tr>
          <tr>
            <td><div class="avatar-sm"></div> Aisha bint Abu Bakr</td>
            <td>5 Juz</td>
            <td><div class="progress"><div class="bar" style="width:60%"></div></div>60%</td>
            <td>Surah An-Nisa on 2025-06-10</td>
            <td>🗒</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>