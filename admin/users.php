<?php include 'header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Customer Management</h2>
</div>

<?php
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        mysqli_query($db, "DELETE FROM Users WHERE id=$id");
    } elseif (isset($_POST['update_role'])) {
        $id = $_POST['id'];
        $role = mysqli_real_escape_string($db, $_POST['role']);
        mysqli_query($db, "UPDATE Users SET role='$role' WHERE id=$id");
    }
}

// Fetch users with order counts
$query = "SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) as order_count FROM Users u ORDER BY u.id DESC";
$result = mysqli_query($db, $query);
$users = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="25%">Name</th>
                        <th width="25%">Contact</th>
                        <th width="10%">Orders</th>
                        <th width="10%">Role</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#<?php echo $user['id']; ?></td>
                            <td>
                                <div class="fw-medium text-white"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                <?php if (!empty($user['address'])): ?>
                                    <small class="text-secondary"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($user['address']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="text-info text-decoration-none"><?php echo htmlspecialchars($user['email']); ?></a></div>
                                <?php if (!empty($user['phone'])): ?>
                                    <small class="text-secondary"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($user['phone']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="orders.php?user_id=<?php echo $user['id']; ?>" class="badge bg-info text-dark text-decoration-none">
                                    <?php echo $user['order_count']; ?> Orders
                                </a>
                            </td>
                            <td>
                                <?php 
                                $role = isset($user['role']) && $user['role'] ? $user['role'] : 'Customer';
                                ?>
                                <span class="badge <?php echo $role == 'Admin' ? 'bg-danger' : 'bg-primary'; ?>"><?php echo $role; ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning" title="Edit Role" onclick="editRole(<?php echo $user['id']; ?>, '<?php echo $role; ?>')">
                                        <i class="bi bi-shield-lock"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user? This may cause issues with their past orders.');">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-outline-danger" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-card border-secondary">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-white">Update User Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="role_user_id">
                    <select class="form-select bg-dark text-white border-secondary" name="role" id="current_role">
                        <option value="Customer">Customer</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary border-0" style="background:#475569;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_role" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRole(id, currentRole) {
    document.getElementById('role_user_id').value = id;
    document.getElementById('current_role').value = currentRole;
    var modal = new bootstrap.Modal(document.getElementById('roleModal'));
    modal.show();
}
</script>

<?php include 'footer_admin.php'; ?>
