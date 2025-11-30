<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Home Ayurveda'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo isset($css_path) ? $css_path : ''; ?>assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo isset($css_path) ? $css_path : ''; ?>index.php">
                <i class="fas fa-leaf"></i> Home Ayurveda
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>index.php">Home</a>
                    </li>
                    <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['is_admin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>user/favorites.php">My Favorites</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>user/login.php?logout=1">Logout</a>
                    </li>
                    <?php elseif(isset($_SESSION['is_admin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>admin/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>admin/login.php?logout=1">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>user/login.php">User Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($css_path) ? $css_path : ''; ?>admin/login.php">Admin Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
