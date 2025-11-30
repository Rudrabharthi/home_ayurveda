<?php
require_once '../config/connection.php';

// Logout
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$username = $password = "";
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["username"]))) {
        $errors['username'] = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))) {
        $errors['password'] = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if(empty($errors)) {
        $stmt = mysqli_prepare($mysqli, "SELECT id, username, password, is_admin FROM users WHERE username = ?");
        
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    // Initialize variables to avoid null type warnings
                    $id = 0;
                    $username = "";
                    $hashed_password = "";
                    $is_admin = 0;
                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $is_admin);
                    if(mysqli_stmt_fetch($stmt)) {
                        if(password_verify($password, $hashed_password)) {
                            if($is_admin == 0) {
                                $errors['general'] = "You are not authorized as admin.";
                            } else {
                                $_SESSION["user_id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["is_admin"] = true;
                                header("location: dashboard.php");
                                exit;
                            }
                        } else {
                            $errors['general'] = "Invalid username or password.";
                        }
                    }
                } else {
                    $errors['general'] = "Invalid username or password.";
                }
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$page_title = 'Admin Login - Home Ayurveda';
$css_path = '../';
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center text-success mb-4"><i class="fas fa-shield-alt"></i> Admin Login</h2>
                    
                    <?php if(isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <div class="invalid-feedback"><?php echo $errors['username'] ?? ''; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>">
                            <div class="invalid-feedback"><?php echo $errors['password'] ?? ''; ?></div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mb-3">Login as Admin</button>
                        
                        <!-- <p class="text-center text-muted small mb-0"></p> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
