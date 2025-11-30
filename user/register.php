<?php
require_once '../config/connection.php';

$name = $email = $username = $password = $confirm_password = "";
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if(empty(trim($_POST["name"]))) {
        $errors['name'] = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate email [web:35]
    if(empty(trim($_POST["email"]))) {
        $errors['email'] = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format.";
        }
    }
    
    // Validate username [web:35]
    if(empty(trim($_POST["username"]))) {
        $errors['username'] = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $errors['username'] = "Username can only contain letters, numbers, and underscores.";
    } else {
        $stmt = mysqli_prepare($mysqli, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($_POST["username"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) == 1) {
            $errors['username'] = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
        mysqli_stmt_close($stmt);
    }
    
    // Validate password [web:35]
    if(empty(trim($_POST["password"]))) {
        $errors['password'] = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6) {
        $errors['password'] = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $errors['confirm_password'] = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if($password != $confirm_password) {
            $errors['confirm_password'] = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($errors)) {
        $stmt = mysqli_prepare($mysqli, "INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)");
        
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_username, $param_password);
            
            $param_name = $name;
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if(mysqli_stmt_execute($stmt)) {
                header("location: login.php?registered=1");
                exit;
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$page_title = 'Register - Home Ayurveda';
$css_path = '../';
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center text-success mb-4">Create Account</h2>
                    <p class="text-center text-muted mb-4">Join Home Ayurveda to save your favorite plants</p>
                    
                    <?php if(isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <div class="invalid-feedback"><?php echo $errors['name'] ?? ''; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                        </div>
                        
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
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>">
                            <div class="invalid-feedback"><?php echo $errors['confirm_password'] ?? ''; ?></div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mb-3">Register</button>
                        
                        <p class="text-center mb-0">Already have an account? <a href="login.php">Login here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
