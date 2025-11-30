<?php
require_once '../config/connection.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle remove from favorites
if(isset($_POST['remove_favorite'])) {
    $plant_id = intval($_POST['plant_id']);
    $stmt = mysqli_prepare($mysqli, "DELETE FROM favorites WHERE user_id = ? AND plant_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $plant_id);
    mysqli_stmt_execute($stmt);
    $success = "Removed from favorites!";
}

// Fetch favorite plants
$stmt = mysqli_prepare($mysqli, "SELECT p.* FROM plants p INNER JOIN favorites f ON p.id = f.plant_id WHERE f.user_id = ? ORDER BY f.created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$page_title = 'My Favorites - Home Ayurveda';
$css_path = '../';
include '../includes/header.php';
?>

<div class="container my-5">
    <h2 class="text-success mb-4"><i class="fas fa-heart"></i> My Favorite Plants</h2>
    
    <?php if(isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($plant = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card plant-card h-100 shadow-sm">
                    <?php if($plant['image_path']): ?>
                    <img src="../assets/uploads/plants/<?php echo htmlspecialchars($plant['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($plant['plant_name']); ?>">
                    <?php else: ?>
                    <img src="https://via.placeholder.com/400x300?text=No+Image" class="card-img-top" alt="No image">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title text-success"><?php echo htmlspecialchars($plant['plant_name']); ?></h5>
                        <?php if($plant['local_name']): ?>
                        <p class="text-muted small"><i><?php echo htmlspecialchars($plant['local_name']); ?></i></p>
                        <?php endif; ?>
                        <p class="card-text"><?php echo substr(htmlspecialchars($plant['short_description']), 0, 100); ?>...</p>
                        
                        <div class="d-grid gap-2">
                            <a href="../plant_detail.php?id=<?php echo $plant['id']; ?>" class="btn btn-success">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="plant_id" value="<?php echo $plant['id']; ?>">
                                <button type="submit" name="remove_favorite" class="btn btn-outline-danger w-100" onclick="return confirm('Remove from favorites?')">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> You haven't added any favorites yet. <a href="../index.php">Browse plants</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
