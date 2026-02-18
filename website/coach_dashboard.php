<?php
require_once __DIR__ . '/config/config.php';

require_any_role(['coach', 'admin']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sportsplay - Coach Dashboard</title>
    <link rel="stylesheet" href="assets/css/coach.css">
</head>

<body>

    <header class="coach-header">
        <div class="coach-header-inner">
            <div class="coach-logo">Sportsplay</div>
            <nav class="coach-nav">
                <a href="#">Home</a>
                <a href="#" class="active">Teams</a>
                <a href="#">Coach</a>
                <a href="#">Enroll</a>
            </nav>
            <div class="coach-right">
                <span class="coach-user">
                    Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button class="btn-login" type="submit">Logout</button>
                </form>
                <div class="menu-icon">☰</div>
            </div>

        </div>

        <div class="coach-hero">
            <div class="coach-hero-overlay">
                <h1>Coach Dashboard</h1>
                <p>Select Team</p>
                <div class="team-select">
                    <select>
                        <option>All teams</option>
                        <option>U14 Blue</option>
                        <option>U16 Girls</option>
                        <option>U18 Elite</option>
                    </select>
                </div>
            </div>
        </div>
    </header>

    <main class="coach-main">
        <section class="top-cards">
            <div class="card roster-card">
                <h2>Team Roster</h2>
                <table class="roster-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Position</th>
                            <th>Parent Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>10</td>
                            <td>Alex Johnson</td>
                            <td>13</td>
                            <td>Forward</td>
                            <td>(555) 111-2222</td>
                            <td>✎ 🗑</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Sarah Williams</td>
                            <td>12</td>
                            <td>Midfielder</td>
                            <td>(555) 222-3333</td>
                            <td>✎ 🗑</td>
                        </tr>
                        <tr>
                            <td>15</td>
                            <td>Mike Brown</td>
                            <td>13</td>
                            <td>Defender</td>
                            <td>(555) 333-4444</td>
                            <td>✎ 🗑</td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Emily Davis</td>
                            <td>15</td>
                            <td>Goalkeeper</td>
                            <td>(555) 444-5555</td>
                            <td>✎ 🗑</td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn-add">+ Add Player</button>
            </div>

            <div class="card overview-card">
                <h2>Overview</h2>
                <ul>
                    <li><span class="badge red">Active Players: 4</span></li>
                    <li><span class="badge orange">Upcoming Games: 2</span></li>
                    <li><span class="badge yellow">Practice Sessions: 2</span></li>
                </ul>
                <p class="next-events">Next Events…</p>
            </div>
        </section>

        <section class="schedule-section">
            <div class="card schedule-card">
                <h2>Team Schedule</h2>

                <div class="schedule-item">
                    <button class="pill practice">Practice</button>
                    <div class="schedule-text">
                        <strong>Team Practice</strong><br>
                        12/01/2025 18:00 – Central Stadium Field 1
                    </div>
                    <div class="trash">🗑</div>
                </div>

                <div class="schedule-item">
                    <button class="pill game">Game</button>
                    <div class="schedule-text">
                        <strong>Vs Lightning U13</strong><br>
                        15/01/2025 16:00 – Central Stadium
                    </div>
                    <div class="trash">🗑</div>
                </div>

                <div class="schedule-item">
                    <button class="pill practice">Practice</button>
                    <div class="schedule-text">
                        <strong>Team Practice</strong><br>
                        19/01/2025 18:00 – Central Stadium Field 1
                    </div>
                    <div class="trash">🗑</div>
                </div>

                <div class="schedule-item">
                    <button class="pill game">Game</button>
                    <div class="schedule-text">
                        <strong>Vs Dragons U15</strong><br>
                        25/01/2025 17:00 – Central Stadium
                    </div>
                    <div class="trash">🗑</div>
                </div>

                <button class="btn-add center">+ Add Event</button>
            </div>
        </section>
    </main>

    <footer class="coach-footer">
        <div class="footer-inner">
            <div class="footer-col">
                <h4>Sportsplay</h4>
                <p>Youth sports team management platform.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <p>About<br>Support<br>Terms</p>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>Email: info@sportsplay.com<br>Phone: (555) 123-4567</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> Sportsplay. All rights reserved.
        </div>
    </footer>

</body>

</html>