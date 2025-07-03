[file name]: add.php
<?php
include 'connect.php';

// Check authorization
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if(isset($_POST['submit'])) {
    // Sanitize inputs
    $name = sanitizeInput($_POST['InputFullName']);
    $dob = $_POST['InputDOB'];
    $nic = strtoupper(sanitizeInput($_POST['InputNIC']));
    $num = preg_replace('/[^0-9]/', '', $_POST['InputNumber']);
    $address = sanitizeInput($_POST['InputAddress']);
    $email = filter_var(sanitizeInput($_POST['InputEmail']), FILTER_SANITIZE_EMAIL);
    $occupation = sanitizeInput($_POST['InputOccupation']);
    $gender = $_POST['InputGender'];

    // Validation
    $errors = [];

    // Required fields validation
    $requiredFields = [
        'Full Name' => $name,
        'Date of Birth' => $dob,
        'NIC' => $nic,
        'Phone Number' => $num,
        'Address' => $address,
        'Email' => $email,
        'Gender' => $gender
    ];

    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            $errors[] = "$field is required";
        }
    }

    // NIC validation
    if (!preg_match("/^(?:\d{9}[vV]|\d{12})$/", $nic)) {
        $errors[] = "Invalid NIC Format (Use 123456789V or 123456789012 format)";
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address format";
    }

    // Phone validation
    if (!preg_match("/^[0-9]{10}$/", $num)) {
        $errors[] = "Phone number must be 10 digits";
    }

    // Gender validation
    if (!in_array($gender, ['Male','Female','Other'])) {
        $errors[] = "Invalid gender selection";
    }

    // Check for duplicate NIC
    $check_nic = $conn->prepare("SELECT nic FROM residents WHERE nic = ?");
    $check_nic->bind_param("s", $nic);
    $check_nic->execute();
    if ($check_nic->fetch()) {
        $errors[] = "NIC already exists in the database!";
    }
    $check_nic->close();

    if(empty($errors)) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO residents (full_name, dob, nic, phone, address, email, occupation, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $name, $dob, $nic, $num, $address, $email, $occupation, $gender);

            if ($stmt->execute()) {
                $conn->commit();
                $_SESSION['success'] = "Resident registered successfully!";
                header("Location: residents.php");
                exit();
            } else {
                throw new Exception("Database error: ". $conn->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Resident - Grama Niladhari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .form-section h5 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
        
        label.required:after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-outline-secondary {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .invalid-feedback {
            font-size: 0.85rem;
        }
        
        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            overflow: hidden;
        }
        
        .avatar-preview span {
            font-size: 2.5rem;
            color: #6c757d;
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
    <link rel="icon" type="image/png" href="assets/resident.png">
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
                <a class="nav-link active" href="add.php">
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
                <h1><i class="fas fa-user-plus me-2"></i>Register New Resident</h1>
            </div>
            
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'Admin') ?>&background=random" alt="User">
                <div>
                    <div class="fw-bold"><?= $_SESSION['username'] ?? 'Admin' ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>

        <div class="form-container">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="avatar-preview">
                            <span id="avatar-text">?</span>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Avatar will be generated from name</small>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="form-section">
                            <h5><i class="fas fa-user-circle me-2"></i>Personal Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="InputFullName" class="form-label required">Full Name</label>
                                    <input type="text" class="form-control" name="InputFullName" 
                                           value="<?= isset($_POST['InputFullName']) ? htmlspecialchars($_POST['InputFullName']) : '' ?>" 
                                           placeholder="John Doe" required>
                                    <div class="invalid-feedback">Please enter full name</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="InputDOB" class="form-label required">Date of Birth</label>
                                    <input type="date" class="form-control" name="InputDOB" 
                                           value="<?= isset($_POST['InputDOB']) ? htmlspecialchars($_POST['InputDOB']) : '' ?>" 
                                           max="<?= date('Y-m-d') ?>" required>
                                    <div class="invalid-feedback">Please select date of birth</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="InputNIC" class="form-label required">NIC Number</label>
                                    <input type="text" class="form-control" name="InputNIC" 
                                           value="<?= isset($_POST['InputNIC']) ? htmlspecialchars($_POST['InputNIC']) : '' ?>" 
                                           placeholder="123456789V or 123456789012" required>
                                    <div class="invalid-feedback">Please enter valid NIC (123456789V or 123456789012 format)</div>
                                    <small class="text-muted">We'll never share your NIC with anyone else</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="InputGender" class="form-label required">Gender</label>
                                    <select class="form-select" name="InputGender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= (isset($_POST['InputGender']) && $_POST['InputGender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= (isset($_POST['InputGender']) && $_POST['InputGender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= (isset($_POST['InputGender']) && $_POST['InputGender'] === 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select gender</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fas fa-address-card me-2"></i>Contact Information</h5>
                    
                    <div class="mb-3">
                        <label for="InputAddress" class="form-label required">Address</label>
                        <textarea class="form-control" name="InputAddress" rows="3" required><?= isset($_POST['InputAddress']) ? htmlspecialchars($_POST['InputAddress']) : '' ?></textarea>
                        <div class="invalid-feedback">Please enter your address</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="InputNumber" class="form-label required">Phone Number</label>
                            <input type="tel" class="form-control" name="InputNumber" 
                                   value="<?= isset($_POST['InputNumber']) ? htmlspecialchars($_POST['InputNumber']) : '' ?>" 
                                   placeholder="0712345678" required>
                            <div class="invalid-feedback">Please enter 10 digit phone number</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="InputEmail" class="form-label required">Email Address</label>
                            <input type="email" class="form-control" name="InputEmail" 
                                   value="<?= isset($_POST['InputEmail']) ? htmlspecialchars($_POST['InputEmail']) : '' ?>" 
                                   placeholder="john@example.com" required>
                            <div class="invalid-feedback">Please enter valid email address</div>
                            <small class="text-muted">We'll never share your email with anyone else</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="InputOccupation" class="form-label">Occupation</label>
                        <input type="text" class="form-control" name="InputOccupation" 
                               value="<?= isset($_POST['InputOccupation']) ? htmlspecialchars($_POST['InputOccupation']) : '' ?>" 
                               placeholder="Software Engineer">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="residents.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Residents
                    </a>
                    <div>
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary" name="submit">
                            <i class="fas fa-save me-2"></i>Register Resident
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
        
        // Update avatar preview
        document.querySelector('[name="InputFullName"]').addEventListener('input', function() {
            const name = this.value.trim();
            const avatarText = document.getElementById('avatar-text');
            
            if (name.length > 0) {
                avatarText.textContent = name.charAt(0).toUpperCase();
            } else {
                avatarText.textContent = '?';
            }
        });
        
        // Sidebar toggle for mobile
        document.querySelector('.sidebar-toggler').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>