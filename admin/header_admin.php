<?php
include '../config.php';
// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sneakers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-heptagon-half text-primary fs-3"></i>
            <h4>SneakerAdmin</h4>
        </div>
        <div class="sidebar-menu">
            <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="categories.php" class="nav-link <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                <i class="bi bi-tags"></i> Categories
            </a>
            <a href="products.php" class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i> Products
            </a>
            <a href="orders.php" class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i> Orders
            </a>
            <a href="users.php" class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Customers
            </a>
        </div>
    </nav>
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary" style="border-color: var(--border-color) !important;">
            <button class="btn btn-outline-light d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="m-0 text-white-50">Welcome back, Admin!</h5>
            <div class="user-profile d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold">Admin User</div>
                    <small class="text-white-50">Administrator</small>
                </div>
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
        </header>