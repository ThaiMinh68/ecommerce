<?php include 'header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Manage Orders</h2>
</div>

<?php
// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = mysqli_real_escape_string($db, $_POST['status']);
    mysqli_query($db, "UPDATE Orders SET status='$status', updated_at=CURRENT_TIMESTAMP() WHERE id=$order_id");
}

// Fetch orders with optional user filter
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$where_clause = $user_filter > 0 ? "WHERE o.user_id = $user_filter" : "";

$query = "SELECT o.*, u.first_name, u.last_name, u.email FROM Orders o LEFT JOIN Users u ON o.user_id = u.id $where_clause ORDER BY o.created_at DESC";
$result = mysqli_query($db, $query);
$orders = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="fw-bold text-white">#<?php echo $order['code'] ? $order['code'] : $order['id']; ?></td>
                            <td>
                                <div class="fw-medium text-white"><?php echo htmlspecialchars($order['first_name'] ? ($order['first_name'] . ' ' . $order['last_name']) : ($order['guest_name'] ?: 'Guest')); ?></div>
                                <small class="text-secondary"><?php echo htmlspecialchars($order['email'] ? $order['email'] : ($order['guest_email'] ?: 'N/A')); ?></small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td class="text-success fw-bold">$<?php echo number_format($order['total'], 2); ?></td>
                            <td>
                                <?php 
                                $status = $order['status'] ?: 'Pending';
                                $badgeClass = 'bg-secondary';
                                if ($status == 'Pending') $badgeClass = 'bg-warning text-dark';
                                if ($status == 'Processing') $badgeClass = 'bg-info text-dark';
                                if ($status == 'Shipped') $badgeClass = 'bg-primary';
                                if ($status == 'Delivered') $badgeClass = 'bg-success';
                                if ($status == 'Cancelled') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                            </td>
                            <td class="text-nowrap">
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-info me-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <button class="btn btn-sm btn-outline-primary" onclick="updateStatus(<?php echo $order['id']; ?>, '<?php echo $status; ?>')">
                                    Update
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-card border-secondary">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-white">Update Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="status_order_id">
                    <select class="form-select bg-dark text-white border-secondary" name="status" id="current_status">
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary border-0" style="background:#475569;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(id, currentStatus) {
    document.getElementById('status_order_id').value = id;
    document.getElementById('current_status').value = currentStatus;
    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}
</script>

<?php include 'footer_admin.php'; ?>
