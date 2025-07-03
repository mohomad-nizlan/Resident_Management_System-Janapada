[file name]: view.php
<?php
require_once 'auth_check.php';
include 'connect.php';

// Verify login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: residents.php");
    exit();
}

// Get resident data
$stmt = $conn->prepare("SELECT * FROM residents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resident = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$resident) {
    header("Location: residents.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Resident - <?= htmlspecialchars($resident['full_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand h3 {
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .sidebar-brand p {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0;
            border-radius: 0;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header h1 {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .profile-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 2rem;
            font-size: 3rem;
            color: white;
        }
        
        .profile-info h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .info-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }
        
        .gender-badge {
            background-color: <?= $resident['gender'] === 'Male' ? '#4361ee' : ($resident['gender'] === 'Female' ? '#f72585' : '#7209b7') ?>;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggler {
                display: block !important;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3 style="padding-left: 15px;"><i class="fas fa-users me-2"></i>ජnapada</h3>
            <p>Grama Niladhari Division Sri Lanka</p>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="residents.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="residents.php">
                    <i class="fas fa-users"></i> Residents
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add.php">
                    <i class="fas fa-user-plus"></i> Add Resident
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div>
                <button class="btn btn-outline-primary sidebar-toggler d-none d-lg-none me-2">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><i class="fas fa-user me-2"></i>Resident Details</h1>
            </div>
            
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'Admin') ?>&background=random" alt="User">
                <div>
                    <div class="fw-bold"><?= $_SESSION['username'] ?? 'Admin' ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar gender-badge">
                    <?= strtoupper(substr($resident['full_name'], 0, 1)) ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($resident['full_name']) ?></h2>
                    <p><span class="badge gender-badge"><?= htmlspecialchars($resident['gender']) ?></span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-id-card me-2"></i>NIC Number</div>
                        <div class="info-value"><?= htmlspecialchars($resident['nic']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-birthday-cake me-2"></i>Date of Birth</div>
                        <div class="info-value"><?= date('d M Y', strtotime($resident['dob'])) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone me-2"></i>Phone Number</div>
                        <div class="info-value"><?= htmlspecialchars($resident['phone']) ?></div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-envelope me-2"></i>Email Address</div>
                        <div class="info-value"><?= htmlspecialchars($resident['email']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-briefcase me-2"></i>Occupation</div>
                        <div class="info-value"><?= htmlspecialchars($resident['occupation'] ?? 'N/A') ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-calendar-check me-2"></i>Registered Date</div>
                        <div class="info-value"><?= date('d M Y H:i', strtotime($resident['registered_date'])) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="info-item mt-4">
                <div class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Address</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($resident['address'])) ?></div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="residents.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
                <div>
                    <a href="update.php?updateid=<?= $id ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.querySelector('.sidebar-toggler').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>