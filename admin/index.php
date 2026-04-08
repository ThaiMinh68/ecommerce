<?php include 'header_admin.php'; ?>

<?php
// Get stats
$stats = [
    'users' => 0,
    'orders' => 0,
    'products' => 0,
    'revenue' => 0
];

// Product count
$res = mysqli_query($db, "SELECT COUNT(id) as count FROM Products");
if ($res) $stats['products'] = mysqli_fetch_assoc($res)['count'];

// User count
$res = mysqli_query($db, "SELECT COUNT(id) as count FROM Users");
if ($res) $stats['users'] = mysqli_fetch_assoc($res)['count'];

// Orders count
$res = mysqli_query($db, "SELECT COUNT(id) as count FROM Orders");
if ($res) $stats['orders'] = mysqli_fetch_assoc($res)['count'];

// Total revenue
$res = mysqli_query($db, "SELECT SUM(total) as revenue FROM Orders WHERE status != 'Cancelled'");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $stats['revenue'] = $row['revenue'] ?? 0;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Dashboard Overview</h2>
</div>

<div class="row g-4 mb-4">
    <!-- Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="stat-card p-4">
                <div class="stat-icon primary">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-details">
                    <p class="mb-1 text-secondary">Total Revenue</p>
                    <h3 class="mb-0 text-white">$<?php echo number_format($stats['revenue'], 2); ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="stat-card p-4">
                <div class="stat-icon success">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div class="stat-details">
                    <p class="mb-1 text-secondary">Total Orders</p>
                    <h3 class="mb-0 text-white"><?php echo number_format($stats['orders']); ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="stat-card p-4">
                <div class="stat-icon warning">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-details">
                    <p class="mb-1 text-secondary">Total Products</p>
                    <h3 class="mb-0 text-white"><?php echo number_format($stats['products']); ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="stat-card p-4">
                <div class="stat-icon danger">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <p class="mb-1 text-secondary">Total Customers</p>
                    <h3 class="mb-0 text-white"><?php echo number_format($stats['users']); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 text-center text-muted mt-5 pt-5">
        <div style="font-size: 3rem; opacity: 0.1;"><i class="bi bi-activity"></i></div>
        <h4 class="mt-3">System is active and monitoring</h4>
        <p>You can manage products, categories, orders and users from the sidebar menu.</p>
    </div>
</div>

<?php include 'footer_admin.php'; ?>