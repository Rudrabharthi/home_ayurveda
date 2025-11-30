<?php
require_once '../config/connection.php';

if(!isset($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

$plant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch existing plant data
$stmt = mysqli_prepare($mysqli, "SELECT * FROM plants WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $plant_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$plant = mysqli_fetch_assoc($result);

if(!$plant) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $plant_name = trim($_POST['plant_name']);
    $local_name = trim($_POST['local_name']);
    $short_description = trim($_POST['short_description']);
    $full_description = trim($_POST['full_description']);
    $diseases_helps = trim($_POST['diseases_helps']);
    $part_used = trim($_POST['part_used']);
    $how_to_use = trim($_POST['how_to_use']);
    $precautions = trim($_POST['precautions']);
    
    // Validate
    if(empty($plant_name)) {
        $errors['plant_name'] = "Plant name is required";
    }
    if(empty($short_description)) {
        $errors['short_description'] = "Short description is required";
    }
    
    // Handle image upload [web:54][web:59]
    $image_path = $plant['image_path']; // Keep existing image by default
    
    if(isset($_FILES['plant_image']) && $_FILES['plant_image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['plant_image']['name'];
        $file_size = $_FILES['plant_image']['size'];
        $file_tmp = $_FILES['plant_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if(!in_array($file_ext, $allowed_extensions)) {
            $errors['image'] = "Only JPG, JPEG, PNG & GIF files are allowed";
        }
        elseif($file_size > 15728640) {
            $errors['image'] = "File size must be less than 15MB";
        }
        elseif(!getimagesize($file_tmp)) {
            $errors['image'] = "File is not a valid image";
        }
        else {
            // Delete old image [web:62]
            if($plant['image_path']) {
                $old_file = "../assets/uploads/plants/" . $plant['image_path'];
                if(file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            // Upload new image
            $image_path = uniqid() . '_' . time() . '.' . $file_ext;
            $target_path = "../assets/uploads/plants/" . $image_path;
            
            if(!move_uploaded_file($file_tmp, $target_path)) {
                $errors['image'] = "Failed to upload image";
                $image_path = $plant['image_path']; // Revert to old path
            }
        }
    }
    
    // Update if no errors
    if(empty($errors)) {
        $update_stmt = mysqli_prepare($mysqli, "UPDATE plants SET plant_name=?, local_name=?, image_path=?, short_description=?, full_description=?, diseases_helps=?, part_used=?, how_to_use=?, precautions=? WHERE id=?");
        
        mysqli_stmt_bind_param($update_stmt, "sssssssssi", $plant_name, $local_name, $image_path, $short_description, $full_description, $diseases_helps, $part_used, $how_to_use, $precautions, $plant_id);
        
        if(mysqli_stmt_execute($update_stmt)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $errors['general'] = "Error updating plant. Please try again.";
        }
    }
}

$page_title = 'Edit Plant - Admin';
$css_path = '../';
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-success"><i class="fas fa-edit"></i> Edit Plant</h2>
                <a href="dashboard.php" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <div class="card shadow">
                <div class="card-body p-4">
                    <?php if(isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Plant Name (English) <span class="text-danger">*</span></label>
                                <input type="text" name="plant_name" class="form-control <?php echo isset($errors['plant_name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($plant['plant_name']); ?>">
                                <div class="invalid-feedback"><?php echo $errors['plant_name'] ?? ''; ?></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Local Name</label>
                                <input type="text" name="local_name" class="form-control" value="<?php echo htmlspecialchars($plant['local_name']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Plant Image</label>
                            <?php if($plant['image_path']): ?>
                            <div class="mb-2">
                                <img src="../assets/uploads/plants/<?php echo htmlspecialchars($plant['image_path']); ?>" alt="Current Image" style="max-width: 200px;" class="img-thumbnail">
                                <p class="text-muted small">Current image (upload new to replace)</p>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="plant_image" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" accept="image/*">
                            <div class="form-text">Allowed: JPG, JPEG, PNG, GIF (Max 5MB)</div>
                            <div class="invalid-feedback"><?php echo $errors['image'] ?? ''; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Short Description <span class="text-danger">*</span></label>
                            <textarea name="short_description" class="form-control <?php echo isset($errors['short_description']) ? 'is-invalid' : ''; ?>" rows="2"><?php echo htmlspecialchars($plant['short_description']); ?></textarea>
                            <div class="invalid-feedback"><?php echo $errors['short_description'] ?? ''; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Description</label>
                            <textarea name="full_description" class="form-control" rows="4"><?php echo htmlspecialchars($plant['full_description']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Diseases/Conditions it Helps With <span class="text-danger">*</span></label>
                            <textarea name="diseases_helps" class="form-control" rows="3"><?php echo htmlspecialchars($plant['diseases_helps']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Part Used</label>
                            <input type="text" name="part_used" class="form-control" value="<?php echo htmlspecialchars($plant['part_used']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">How to Use</label>
                            <textarea name="how_to_use" class="form-control" rows="4"><?php echo htmlspecialchars($plant['how_to_use']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Precautions</label>
                            <textarea name="precautions" class="form-control" rows="3"><?php echo htmlspecialchars($plant['precautions']); ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Update Plant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
