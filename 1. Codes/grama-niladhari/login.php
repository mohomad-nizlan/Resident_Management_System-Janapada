<?php
session_start();
include 'connect.php';

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user_id'] = true;
        $_SESSION['username'] = $username;
        header("Location: residents.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Resident Management</title>
    <link rel="icon" type="image/png" href="assets/resident.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
:root {
    --primary: #3f37c9;
    --accent: #4895ef;
    --light: #ffffff;
    --gray-light: #f5f6fa;
    --shadow: rgba(0, 0, 0, 0.1);

    --gradient-total: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
    --gradient-male: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
    --gradient-female: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
    --gradient-other: linear-gradient(135deg, #7209b7 0%, #560bad 100%);
}

body {
    background: linear-gradient(135deg, #4361ee, #4895ef, #f72585, #7209b7);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
}

.login-container {
    max-width: 420px;
    width: 100%;
    background-color: var(--light);
    padding: 2.5rem;
    border-radius: 16px;
    box-shadow: 0 12px 30px var(--shadow);
    animation: fadeInUp 0.6s;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.brand-logo {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--gray-light);
    border-radius: 50%;
    padding: 15px;
}

.brand-logo img {
    max-width: 100%;
    max-height: 100%;
}

.login-header h2 {
    color: var(--primary);
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.login-header p {
    color: #6c757d;
    margin-bottom: 0;
}

.form-control:focus {
    border-color: #f72585;
    box-shadow: 0 0 0 0.2rem rgba(247, 37, 133, 0.25);
}

.btn-login {
    background: var(--gradient-female);
    color: white;
    font-weight: 600;
    padding: 12px;
    border: none;
    border-radius: 10px;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-login:hover {
    background: var(--gradient-other);
    transform: translateY(-2px);
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

    </style>
</head>
<body>
    <div class="login-container animate__animated animate__fadeIn">
        <div class="login-header">
            <div class="brand-logo">
                <img src="assets/resident.png" alt="Resident Logo">
            </div>
            <h2>Resident Management</h2>
            <p>Grama Niladhari Division</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" autocomplete="off" required placeholder="Username">
                <label for="username"><i class="fas fa-user me-2"></i>Username</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" required placeholder="Password">
                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
            </div>

            <button type="submit" name="login" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>