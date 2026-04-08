<?php
include 'header_admin.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid Order ID.</div>";
    include 'footer_admin.php';
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT o.*, u.first_name, u.last_name, u.email FROM Orders o LEFT JOIN Users u ON o.user_id = u.id WHERE o.id = $id";
$result = mysqli_query($db, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<div class='alert alert-danger'>Order not found.</div>";
    include 'footer_admin.php';
    exit;
}

// Fetch order items
$item_query = "SELECT od.*, p.product_name, pi.filename FROM Order_details od LEFT JOIN Products p ON od.product_id = p.id LEFT JOIN Product_images pi ON p.id = pi.product_id WHERE od.order_id = $id GROUP BY od.id";
$items_result = mysqli_query($db, $item_query);
$items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Order Details: #<?php echo $order['code'] ?: $order['id']; ?></h2>
    <a href="orders.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Orders</a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-card border-secondary h-100">
            <div class="card-body">
                <h5 class="card-title text-primary"><i class="bi bi-person-lines-fill"></i> Customer Info</h5>
                <hr class="border-secondary">
                <p class="text-white mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($order['first_name'] ? ($order['first_name'] . ' ' . $order['last_name']) : ($order['guest_name'] ?: 'Guest')); ?></p>
                <p class="text-white mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ? $order['email'] : ($order['guest_email'] ?: 'N/A')); ?></p>
                <p class="text-white mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-card border-secondary h-100">
            <div class="card-body">
                <h5 class="card-title text-primary"><i class="bi bi-truck"></i> Delivery Info</h5>
                <hr class="border-secondary">
                <p class="text-white mb-2"><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p class="text-white mb-2"><strong>Payment:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($order['payment']); ?></span></p>
                <p class="text-white mb-2"><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-card border-secondary h-100">
            <div class="card-body">
                <h5 class="card-title text-primary"><i class="bi bi-info-circle"></i> Order Status</h5>
                <hr class="border-secondary">
                <?php 
                $status = $order['status'] ?: 'Pending';
                $badgeClass = 'bg-secondary';
                if ($status == 'Pending') $badgeClass = 'bg-warning text-dark';
                if ($status == 'Processing') $badgeClass = 'bg-info text-dark';
                if ($status == 'Shipped') $badgeClass = 'bg-primary';
                if ($status == 'Delivered') $badgeClass = 'bg-success';
                if ($status == 'Cancelled') $badgeClass = 'bg-danger';
                ?>
                <h3 class="mt-3"><span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span></h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-card text-white border-bottom border-secondary pt-3 pb-3">
        <h5 class="mb-0"><i class="bi bi-cart"></i> Ordered Items</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="100">Image</th>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php 
                            $img = $item['filename'] ? '../assets/images/products/'.$item['filename'] : '../assets/images/homepage-one/product-img/product-img-1.webp';
                            ?>
                            <img src="<?php echo $img; ?>" alt="Product" class="img-thumbnail bg-dark border-secondary" style="width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-medium text-white"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <small class="text-secondary">PID: #<?php echo $item['product_id']; ?></small>
                        </td>
                        <td class="text-center align-middle"><?php echo $item['quantity']; ?></td>
                        <td class="text-end align-middle">$<?php echo number_format($item['price'], 2); ?></td>
                        <td class="text-end align-middle fw-bold text-primary">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-top border-secondary">
                    <tr>
                        <td colspan="4" class="text-end text-white fw-bold py-3">Subtotal:</td>
                        <td class="text-end text-white fw-bold py-3">$<?php echo number_format($order['subtotal'], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-white fw-bold">Shipping Fee:</td>
                        <td class="text-end text-white fw-bold">+$<?php echo number_format($order['shipping_fee'], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-white fw-bold fs-5 pt-3 mb-2">Final Total:</td>
                        <td class="text-end text-success fw-bold fs-5 pt-3 mb-2">$<?php echo number_format($order['total'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
