<?php
require_once 'config/connection.php';

$plant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch plant details
$stmt = mysqli_prepare($mysqli, "SELECT * FROM plants WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $plant_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$plant = mysqli_fetch_assoc($result);

if(!$plant) {
    header('Location: index.php');
    exit;
}

// Check if already in favorites
$is_favorite = false;
if(isset($_SESSION['user_id'])) {
    $check_stmt = mysqli_prepare($mysqli, "SELECT id FROM favorites WHERE user_id = ? AND plant_id = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $_SESSION['user_id'], $plant_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);  // Add this line
    $is_favorite = mysqli_stmt_num_rows($check_stmt) > 0;  // Fixed this line
}

// Handle add to favorites
if(isset($_POST['add_favorite']) && isset($_SESSION['user_id'])) {
    $fav_stmt = mysqli_prepare($mysqli, "INSERT INTO favorites (user_id, plant_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fav_stmt, "ii", $_SESSION['user_id'], $plant_id);
    if(mysqli_stmt_execute($fav_stmt)) {
        $success = "Added to favorites!";
        $is_favorite = true;
    }
}

// Handle remove from favorites
if(isset($_POST['remove_favorite']) && isset($_SESSION['user_id'])) {
    $rem_stmt = mysqli_prepare($mysqli, "DELETE FROM favorites WHERE user_id = ? AND plant_id = ?");
    mysqli_stmt_bind_param($rem_stmt, "ii", $_SESSION['user_id'], $plant_id);
    if(mysqli_stmt_execute($rem_stmt)) {
        $success = "Removed from favorites!";
        $is_favorite = false;
    }
}

$page_title = htmlspecialchars($plant['plant_name']) . ' - Home Ayurveda';
include 'includes/header.php';
?>

<div class="container my-5">
    <?php if(isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-5">
            <?php if($plant['image_path']): ?>
            <img src="assets/uploads/plants/<?php echo htmlspecialchars($plant['image_path']); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($plant['plant_name']); ?>">
            <?php else: ?>
            <img src="https://via.placeholder.com/500x400?text=No+Image" class="img-fluid rounded shadow" alt="No image">
            <?php endif; ?>
        </div>
        
        <div class="col-lg-7">
            <h1 class="text-success mb-2"><?php echo htmlspecialchars($plant['plant_name']); ?></h1>
            <?php if($plant['local_name']): ?>
            <p class="text-muted fs-5 mb-4"><i><?php echo htmlspecialchars($plant['local_name']); ?></i></p>
            <?php endif; ?>
            
            <div class="mb-4">
                <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['is_admin'])): ?>
                    <form method="POST" class="d-inline">
                        <?php if($is_favorite): ?>
                        <button type="submit" name="remove_favorite" class="btn btn-outline-danger">
                            <i class="fas fa-heart"></i> Remove from Favorites
                        </button>
                        <?php else: ?>
                        <button type="submit" name="add_favorite" class="btn btn-success">
                            <i class="far fa-heart"></i> Add to Favorites
                        </button>
                        <?php endif; ?>
                    </form>
                <?php elseif(!isset($_SESSION['user_id'])): ?>
                    <a href="user/login.php" class="btn btn-success">
                        <i class="far fa-heart"></i> Login to Add Favorites
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="detail-section">
                <h4 class="text-success"><i class="fas fa-info-circle"></i> Description</h4>
                <p><?php echo nl2br(htmlspecialchars($plant['full_description'])); ?></p>
            </div>
            
            <div class="detail-section">
                <h4 class="text-success"><i class="fas fa-heartbeat"></i> Helps With</h4>
                <p><?php echo nl2br(htmlspecialchars($plant['diseases_helps'])); ?></p>
            </div>
            
            <?php if($plant['part_used']): ?>
            <div class="detail-section">
                <h4 class="text-success"><i class="fas fa-seedling"></i> Part Used</h4>
                <p><?php echo htmlspecialchars($plant['part_used']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if($plant['how_to_use']): ?>
            <div class="detail-section">
                <h4 class="text-success"><i class="fas fa-prescription-bottle"></i> How to Use</h4>
                <p><?php echo nl2br(htmlspecialchars($plant['how_to_use'])); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if($plant['precautions']): ?>
            <div class="detail-section alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Precautions</h5>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($plant['precautions'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="index.php" class="btn btn-outline-success">
            <i class="fas fa-arrow-left"></i> Back to All Plants
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
