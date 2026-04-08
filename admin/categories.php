<?php include 'header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Manage Categories</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> Add Category
    </button>
</div>

<?php
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $category_name = mysqli_real_escape_string($db, $_POST['category_name']);
        
        $query = "INSERT INTO Categories (category_name) VALUES ('$category_name')";
        mysqli_query($db, $query);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $category_name = mysqli_real_escape_string($db, $_POST['category_name']);
        
        $query = "UPDATE Categories SET category_name='$category_name' WHERE id=$id";
        mysqli_query($db, $query);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        
        // Note: For a real app, check for linked products before deleting.
        $query = "DELETE FROM Categories WHERE id=$id";
        mysqli_query($db, $query);
    }
}

// Fetch categories
$query = "SELECT * FROM Categories ORDER BY id DESC";
$result = mysqli_query($db, $query);
$categories = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<!-- Categories List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="70%">Category Name</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>#<?php echo $category['id']; ?></td>
                            <td><div class="fw-medium text-white"><?php echo htmlspecialchars($category['category_name']); ?></div></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars(addslashes($category['category_name'])); ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-card border-secondary">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-white">Add New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="category_name" name="category_name" placeholder="E.g., Running Shoes" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary border-0" style="background:#475569;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-card border-secondary">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-white">Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="edit_category_name" name="category_name" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary border-0" style="background:#475569;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit" class="btn btn-primary px-4">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_category_name').value = name;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?php include 'footer_admin.php'; ?>