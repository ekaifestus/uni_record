<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/uni_record/database/database.php";

$dbo = new Database();
$student_id = $password = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($student_id) || empty($password)) {
        $error_message = "Student ID and password are required!";
    } else {

        $checkQuery = "SELECT student_id, password FROM faculty_details WHERE student_id = :student_id";
        $stmt = $dbo->conn->prepare($checkQuery);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // password_verify() works with hashed passwords
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['student_id'] = $student_id;
                header("Location: stu_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "Student ID not found.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9f9fb;
      color: #333;
      line-height: 1.6;
    }

    .hed {
      background: #fff;
      padding: 20px 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      flex-wrap: wrap;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2c3e50;
    }

    .menu-toggle {
      display: none;
      font-size: 1.5rem;
      cursor: pointer;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    nav a {
      text-decoration: none;
      color: #555;
      font-weight: 500;
      transition: color 0.3s;
    }

    nav a:hover {
      color: #1abc9c;
    }

    .dropdown {
      position: relative;
      cursor: pointer;
    }

    .dropdown-toggle {
      color: #555;
      font-weight: 500;
      text-decoration: none;
      padding: 5px;
    }

    .dropdown-menu {
      position: absolute;
      top: 30px;
      right: 0;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 150px;
      display: none;
      z-index: 1000;
    }

    .dropdown-menu a {
      display: block;
      padding: 10px 15px;
      text-decoration: none;
      color: #333;
      transition: background 0.2s;
    }

    .dropdown-menu a:hover {
      background-color: #f0f0f0;
    }

    .dropdown:hover .dropdown-menu {
      display: block;
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
      }

      nav {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        width: 100%;
        display: none;
        margin-top: 15px;
      }

      nav.active {
        display: flex;
      }

      .dropdown-menu {
        left: 0;
        right: auto;
      }
    }
  </style>
</head>
<body>

  <header class="hed">
    <div class="logo">SmartLearn</div>
    <div class="menu-toggle" id="menu-toggle">☰</div>
    <nav id="navbar">
      <a href="./index.html">Home</a>
      <a href="#">Courses</a>
      <a href="#">About</a>
      <a href="#">Contact</a>
      <div class="dropdown">
        <span class="dropdown-toggle">Login ▾</span>
        <div class="dropdown-menu">
          <a href="./login.php">Student</a>
          <a href="./lecture_log.php">Lecturer</a>
          <a href="./staff_log.php">Admin</a>
        </div>
      </div>
    </nav>
  </header>

  <script>
    // Toggle navigation on mobile
    const menuToggle = document.getElementById('menu-toggle');
    const navbar = document.getElementById('navbar');

    menuToggle.addEventListener('click', () => {
      navbar.classList.toggle('active');
    });
  </script>
    <div class="form-container">
        <h1>Login</h1>

        <form id="loginForm" action="" method="POST">
            <div class="input-group">
                <label for="student_id">Student ID:</label>
                <input type="text" id="student_id" name="student_id" placeholder="Enter your student ID" required>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-actions">
                <button type="submit">Login</button>
            </div>
            <div class="" float="right">
                <a href="./reset_pass.php">Forgot password</a>
            </div>

            <?php if ($error_message != ""): ?>
                <div id="error-message" class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </form>
    </div>

    <footer style="position: fixed; bottom: 0; left: 0; width: 100%; text-align: center; background-color: #f1f1f1; padding: 10px;">
  © 2025 SmartLearn. All rights reserved.
</footer>
    <script src="js/login.js"></script>

</body>
</html>
