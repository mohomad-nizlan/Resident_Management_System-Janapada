
<?php
session_start();
include 'connect.php';

// 1. Authorization Check - Only allow logged-in admins to delete
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// 2. Validate and Sanitize Input
$id = isset($_GET['deleteid']) ? intval($_GET['deleteid']) : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid resident ID";
    header("Location: residents.php");
    exit();
}

// 3. Verify Record Exists Before Deletion
$check_stmt = $conn->prepare("SELECT id, full_name FROM residents WHERE id = ?");
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = "Resident record not found";
    header("Location: residents.php");
    exit();
}

$resident = $check_result->fetch_assoc();
$check_stmt->close();

// 4. Check if this is a confirmation request
if (!isset($_GET['confirm'])) {
    // Show confirmation page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Deletion - Grama Niladhari</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="icon" type="image/png" href="assets/resident.png">
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
            
            .confirmation-card {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                padding: 2rem;
                max-width: 600px;
                margin: 0 auto;
            }
            
            .confirmation-icon {
                font-size: 4rem;
                color: #e63946;
                margin-bottom: 1.5rem;
            }
            
            .btn-danger {
                background-color: #e63946;
                border-color: #e63946;
            }
            
            .btn-danger:hover {
                background-color: #c1121f;
                border-color: #c1121f;
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
                    <h1><i class="fas fa-trash-alt me-2"></i>Confirm Deletion</h1>
                </div>
                
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'Admin') ?>&background=random" alt="User">
                    <div>
                        <div class="fw-bold"><?= $_SESSION['username'] ?? 'Admin' ?></div>
                        <small class="text-muted">Administrator</small>
                    </div>
                </div>
            </div>

            <div class="confirmation-card text-center">
                <div class="confirmation-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="mb-3">Confirm Deletion</h3>
                <p>Are you sure you want to permanently delete the resident record for:</p>
                <h4 class="mb-4"><?= htmlspecialchars($resident['full_name']) ?> (ID: <?= $id ?>)</h4>
                <p class="text-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i>This action cannot be undone!</p>
                
                <div class="d-flex justify-content-center">
                    <a href="residents.php" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <a href="delete.php?deleteid=<?= $id ?>&confirm=1" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Confirm Delete
                    </a>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Sidebar toggle for mobile
            document.querySelector('.sidebar-toggler').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('show');
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

// 5. Proceed with deletion after confirmation
$conn->begin_transaction();
try {
    // First, archive the record (optional - for audit trail)
    //$archive_stmt = $conn->prepare("INSERT INTO deleted_residents 
                                   //SELECT *, NOW(), ? FROM residents WHERE id = ?");
    //$archive_stmt->bind_param("si", $_SESSION['user_id'], $id);
    //$archive_stmt->execute();
    //$archive_stmt->close();

    // Then delete from main table
    $delete_stmt = $conn->prepare("DELETE FROM residents WHERE id = ?");
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $conn->commit();
        
        // Log the deletion
        $log_message = sprintf(
            "Resident ID %d (%s) deleted by User ID %d on %s",
            $id,
            $resident['full_name'],
            $_SESSION['user_id'],
            date('Y-m-d H:i:s')
        );
        file_put_contents('logs/deletions.log', $log_message . PHP_EOL, FILE_APPEND);
        
        $_SESSION['success'] = "Resident deleted successfully!";
    } else {
        throw new Exception("Database error during deletion");
    }
    
    $delete_stmt->close();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error deleting resident: " . $e->getMessage();
}

$conn->close();

// 6. Redirect back to residents list
header("Location: residents.php");
exit();
?>