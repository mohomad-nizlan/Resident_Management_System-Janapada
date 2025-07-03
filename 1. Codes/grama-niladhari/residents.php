<?php
session_start();
include 'connect.php';

// Check authorization
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search functionality
$results = [];
$search_query = "";
$search_type = "name";
$is_search = false;

if (isset($_GET['query'])) {
    $search_query = trim($_GET['query']);
    $search_type = $_GET['search_type'] ?? 'name';
    $is_search = true;
    
    $sql = "SELECT id, full_name, dob, nic, phone, address, email, occupation, gender, registered_date 
            FROM residents WHERE ";
    
    switch ($search_type) {
        case 'nic':
            $sql .= "nic = ?";
            break;
        case 'address':
            $sql .= "address LIKE ?";
            break;
        default:
            $sql .= "full_name LIKE ?";
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($search_type === 'nic') {
        $stmt->bind_param("sii", $search_query, $limit, $offset);
    } else {
        $like = "%$search_query%";
        $stmt->bind_param("sii", $like, $limit, $offset);
    }
    
    $stmt->execute();
    $results = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT id, full_name, dob, nic, phone, address, email, occupation, gender, registered_date 
                           FROM residents LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $results = $stmt->get_result();
}

// Count total records for pagination
if ($is_search) {
    $count_sql = "SELECT COUNT(*) as total FROM residents WHERE ";
    if ($search_type === 'nic') {
        $count_sql .= "nic = ?";
    } else {
        $count_sql .= ($search_type === 'address') ? "address LIKE ?" : "full_name LIKE ?";
    }
    
    $count_stmt = $conn->prepare($count_sql);
    $search_param = ($search_type === 'nic') ? $search_query : "%$search_query%";
    $count_stmt->bind_param("s", $search_param);
    $count_stmt->execute();
    $total_result = $count_stmt->get_result()->fetch_assoc();
    $total_records = $total_result['total'];
} else {
    $total_records = $conn->query("SELECT COUNT(*) as total FROM residents")->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-top: none;
        }
        
        .table td {
            vertical-align: middle;
            border-top: 1px solid #f0f0f0;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: #e63946;
            border-color: #e63946;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 40px;
            border-radius: 50px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6c757d;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .pagination .page-link {
            color: var(--primary-color);
        }
        
        .stats-card {
            padding: 1.5rem;
            border-radius: 10px;
            color: white;
            margin-bottom: 1.5rem;
        }
        
        .stats-card i {
            font-size: 2rem;
            opacity: 0.8;
        }
        
        .stats-card .count {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .stats-card .title {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        .stats-card.total {
            background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
        }
        
        .stats-card.male {
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
        }
        
        .stats-card.female {
            background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        }
        
        .stats-card.other {
            background: linear-gradient(135deg, #7209b7 0%, #560bad 100%);
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
                <a class="nav-link active" href="residents.php">
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
        <!-- Alerts moved here to appear only in content area -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="header">
            <div>
                <button class="btn btn-outline-primary sidebar-toggler d-none d-lg-none me-2">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Resident Management</h1>
            </div>
            
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'Admin') ?>&background=random" alt="User">
                <div>
                    <div class="fw-bold"><?= $_SESSION['username'] ?? 'Admin' ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card total">
                    <i class="fas fa-users"></i>
                    <div class="count"><?= $total_records ?></div>
                    <div class="title">Total Residents</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card male">
                    <i class="fas fa-male"></i>
                    <div class="count"><?= $conn->query("SELECT COUNT(*) FROM residents WHERE gender='Male'")->fetch_row()[0] ?></div>
                    <div class="title">Male Residents</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card female">
                    <i class="fas fa-female"></i>
                    <div class="count"><?= $conn->query("SELECT COUNT(*) FROM residents WHERE gender='Female'")->fetch_row()[0] ?></div>
                    <div class="title">Female Residents</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card other">
                    <i class="fas fa-user"></i>
                    <div class="count"><?= $conn->query("SELECT COUNT(*) FROM residents WHERE gender='Other'")->fetch_row()[0] ?></div>
                    <div class="title">Other</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Resident Records</h5>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Resident
                </a>
            </div>
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form method="get" class="row g-2">
                            <?php if ($is_search): ?>
                                <div class="col-12 mb-3">
                                    <a href="residents.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Clear Search
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <select name="search_type" class="form-select">
                                    <option value="name" <?= $search_type === 'name' ? 'selected' : '' ?>>Search by Name</option>
                                    <option value="address" <?= $search_type === 'address' ? 'selected' : '' ?>>Search by Address</option>
                                    <option value="nic" <?= $search_type === 'nic' ? 'selected' : '' ?>>Search by NIC</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="query" class="form-control" 
                                           value="<?= htmlspecialchars($search_query) ?>" 
                                           placeholder="Enter search term">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">DoB</th>
                                <th scope="col">NIC</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($results->num_rows > 0): ?>
                                <?php while($row = $results->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    <?php 
                                                        $avatarName = strtoupper(substr($row['full_name'], 0, 1));
                                                        // Generate a consistent color based on the first letter
                                                        $colors = [
                                                            'A' => '#4361ee', 'B' => '#3f37c9', 'C' => '#4895ef', 'D' => '#4cc9f0',
                                                            'E' => '#f72585', 'F' => '#b5179e', 'G' => '#7209b7', 'H' => '#560bad',
                                                            'I' => '#480ca8', 'J' => '#3a0ca3', 'K' => '#3f37c9', 'L' => '#4361ee',
                                                            'M' => '#4895ef', 'N' => '#4cc9f0', 'O' => '#f72585', 'P' => '#b5179e',
                                                            'Q' => '#7209b7', 'R' => '#560bad', 'S' => '#480ca8', 'T' => '#3a0ca3',
                                                            'U' => '#3f37c9', 'V' => '#4361ee', 'W' => '#4895ef', 'X' => '#4cc9f0',
                                                            'Y' => '#f72585', 'Z' => '#b5179e'
                                                        ];
                                                        $color = $colors[$avatarName] ?? '#6c757d'; // Default color if letter not in array
                                                    ?>
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 36px; height: 36px; background-color: <?= $color ?>; color: white;">
                                                        <?= $avatarName ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($row['full_name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= date('d M Y', strtotime($row['dob'])) ?></td>
                                        <td><?= htmlspecialchars($row['nic']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info action-btn" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="update.php?updateid=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary action-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?deleteid=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger action-btn" 
                                                   onclick="return confirm('Are you sure you want to delete this resident?');" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="py-4">
                                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                            <h5>No residents found</h5>
                                            <?php if($is_search): ?>
                                                <p>Try a different search term</p>
                                            <?php else: ?>
                                                <p>Add your first resident by clicking the "Add Resident" button</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?><?= $is_search ? '&query='.urlencode($search_query).'&search_type='.$search_type : '' ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" 
                                   style="<?= $i == $page ? 'background-color: #4361ee; color: white;' : '' ?>"
                                   href="?page=<?= $i ?><?= $is_search ? '&query='.urlencode($search_query).'&search_type='.$search_type : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?><?= $is_search ? '&query='.urlencode($search_query).'&search_type='.$search_type : '' ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
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