<?php
require_once 'config/connection.php';
$page_title = 'Home - Home Ayurveda';

// Fetch all plants
$search = isset($_GET['search']) ? mysqli_real_escape_string($mysqli, $_GET['search']) : '';

if($search) {
    $query = "SELECT * FROM plants WHERE plant_name LIKE '%$search%' OR local_name LIKE '%$search%' OR diseases_helps LIKE '%$search%' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM plants ORDER BY id DESC";
}

$result = mysqli_query($mysqli, $query);

include 'includes/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-3">Discover Ayurvedic Plants</h1>
                <p class="lead mb-4 text-white">Explore natural remedies for various health conditions</p>

                
                <form action="index.php" method="GET" class="search-form">
                    <div class="input-group input-group-lg">
                        <input type="text" name="search" class="form-control" placeholder="Search by plant name or disease..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-success" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <?php if($search): ?>
    <h3 class="mb-4">Search Results for "<?php echo htmlspecialchars($search); ?>"</h3>
    <?php else: ?>
    <h3 class="mb-4">All Ayurvedic Plants</h3>
    <?php endif; ?>
    
    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($plant = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card plant-card h-100 shadow-sm">
                    <?php if($plant['image_path']): ?>
                    <img src="assets/uploads/plants/<?php echo htmlspecialchars($plant['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($plant['plant_name']); ?>">
                    <?php else: ?>
                    <img src="https://via.placeholder.com/400x300?text=No+Image" class="card-img-top" alt="No image">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title text-success"><?php echo htmlspecialchars($plant['plant_name']); ?></h5>
                        <?php if($plant['local_name']): ?>
                        <p class="text-muted small"><i><?php echo htmlspecialchars($plant['local_name']); ?></i></p>
                        <?php endif; ?>
                        <p class="card-text"><?php echo substr(htmlspecialchars($plant['short_description']), 0, 100); ?>...</p>
                        <a href="plant_detail.php?id=<?php echo $plant['id']; ?>" class="btn btn-success w-100">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No plants found. <?php echo $search ? 'Try a different search term.' : ''; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
