<?php
require_once '../config/connection.php';

if(!isset($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

// Handle delete
if(isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    // Get image path first
    $img_stmt = mysqli_prepare($mysqli, "SELECT image_path FROM plants WHERE id = ?");
    mysqli_stmt_bind_param($img_stmt, "i", $delete_id);
    mysqli_stmt_execute($img_stmt);
    $img_result = mysqli_stmt_get_result($img_stmt);
    $img_data = mysqli_fetch_assoc($img_result);
    
    // Delete image file
    if($img_data && $img_data['image_path']) {
        $file_path = "../assets/uploads/plants/" . $img_data['image_path'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete from database
    $stmt = mysqli_prepare($mysqli, "DELETE FROM plants WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    
    $success = "Plant deleted successfully!";
}

// Fetch all plants
$result = mysqli_query($mysqli, "SELECT * FROM plants ORDER BY id DESC");

$page_title = 'Admin Dashboard - Home Ayurveda';
$css_path = '../';
include '../includes/header.php';
?>

<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-success"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <p class="text-muted">Manage all ayurvedic plants</p>
        </div>
    </div>
    
    <?php if(isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-leaf"></i> All Plants</h5>
            <a href="add_plant.php" class="btn btn-light">
                <i class="fas fa-plus"></i> Add New Plant
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Plant Name</th>
                            <th>Local Name</th>
                            <th>Diseases Helps</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($plant = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $plant['id']; ?></td>
                                <td>
                                    <?php if($plant['image_path']): ?>
                                    <img src="../assets/uploads/plants/<?php echo htmlspecialchars($plant['image_path']); ?>" alt="Plant" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                    <?php else: ?>
                                    <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($plant['plant_name']); ?></td>
                                <td><?php echo htmlspecialchars($plant['local_name']); ?></td>
                                <td><?php echo substr(htmlspecialchars($plant['diseases_helps']), 0, 50); ?>...</td>
                                <td><?php echo date('d M Y', strtotime($plant['created_at'])); ?></td>
                                <td>
                                    <a href="../plant_detail.php?id=<?php echo $plant['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_plant.php?id=<?php echo $plant['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $plant['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this plant?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No plants found. <a href="add_plant.php">Add your first plant</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
