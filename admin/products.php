<?php include 'header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Manage Products</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> Add Product
    </button>
</div>

<?php
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $product_name = mysqli_real_escape_string($db, $_POST['product_name']);
        $price = mysqli_real_escape_string($db, $_POST['price']);
        $short_description = mysqli_real_escape_string($db, $_POST['short_description']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $stock = mysqli_real_escape_string($db, $_POST['stock']);
        $category_id = mysqli_real_escape_string($db, $_POST['category_id']);
        
        $query = "INSERT INTO Products (product_name, price, short_description, description, stock, category_id) 
                  VALUES ('$product_name', '$price', '$short_description', '$description', '$stock', '$category_id')";
        if (mysqli_query($db, $query)) {
            $product_id = mysqli_insert_id($db);
            
            // Handle image upload
            if (!empty($_FILES['product_image']['name'])) {
                $upload_dir = '../assets/images/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_ext, $allowed_ext)) {
                    $filename = 'product_' . $product_id . '_' . time() . '.' . $file_ext;
                    $upload_path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                        $img_query = "INSERT INTO Product_images (product_id, filename) VALUES ($product_id, '$filename')";
                        mysqli_query($db, $img_query);
                    }
                }
            }
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $product_name = mysqli_real_escape_string($db, $_POST['product_name']);
        $price = mysqli_real_escape_string($db, $_POST['price']);
        $short_description = mysqli_real_escape_string($db, $_POST['short_description']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $stock = mysqli_real_escape_string($db, $_POST['stock']);
        $category_id = mysqli_real_escape_string($db, $_POST['category_id']);
        
        $query = "UPDATE Products SET product_name='$product_name', price='$price', 
                  short_description='$short_description', description='$description', 
                  stock='$stock', category_id='$category_id' WHERE id=$id";
        mysqli_query($db, $query);
        
        // Handle image replacement
        if (!empty($_FILES['product_image']['name'])) {
            $upload_dir = '../assets/images/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Delete old image
            $img_query = "SELECT filename FROM Product_images WHERE product_id=$id";
            $img_result = mysqli_query($db, $img_query);
            if ($img_result && mysqli_num_rows($img_result) > 0) {
                $old_img = mysqli_fetch_assoc($img_result);
                $old_path = $upload_dir . $old_img['filename'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            
            $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $filename = 'product_' . $id . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                    $img_query = "UPDATE Product_images SET filename='$filename' WHERE product_id=$id";
                    mysqli_query($db, $img_query);
                }
            }
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        
        // Delete image files
        $img_query = "SELECT filename FROM Product_images WHERE product_id=$id";
        $img_result = mysqli_query($db, $img_query);
        if ($img_result) {
            while ($img = mysqli_fetch_assoc($img_result)) {
                $path = '../assets/images/products/' . $img['filename'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
        
        // Delete product images records
        $del_img = "DELETE FROM Product_images WHERE product_id=$id";
        mysqli_query($db, $del_img);
        
        // Delete product
        $query = "DELETE FROM Products WHERE id=$id";
        mysqli_query($db, $query);
    }
}

// Fetch products with categories and images
$query = "SELECT p.*, c.category_name, GROUP_CONCAT(pi.filename) as images 
          FROM Products p 
          LEFT JOIN Categories c ON p.category_id = c.id 
          LEFT JOIN Product_images pi ON p.id = pi.product_id 
          GROUP BY p.id 
          ORDER BY p.id DESC";
$result = mysqli_query($db, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch categories for dropdown
$cat_query = "SELECT * FROM Categories ORDER BY category_name";
$cat_result = mysqli_query($db, $cat_query);
$categories = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);
?>

<!-- Add Product Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-card border-secondary">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-white">Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <div class="col-md-4">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <input type="text" class="form-control" id="short_description" name="short_description" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary border-0" style="background:#475569;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary px-4">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Products List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="10%">Image</th>
                        <th width="25%">Product Name</th>
                        <th width="15%">Category</th>
                        <th width="15%">Price</th>
                        <th width="10%">Stock</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?php echo $product['id']; ?></td>
                            <td>
                                <?php if (!empty($product['images'])): 
                                    $images = explode(',', $product['images']);
                                    if (!empty($images[0])): ?>
                                        <img src="../assets/images/products/<?php echo htmlspecialchars($images[0]); ?>" 
                                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                             class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                    <?php endif; 
                                else: ?>
                                    <div class="bg-dark rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-image text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><div class="fw-medium text-white"><?php echo htmlspecialchars($product['product_name']); ?></div></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span></td>
                            <td class="text-success fw-bold">$<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <?php if ($product['stock'] > 10): ?>
                                    <span class="text-white"><?php echo $product['stock']; ?></span>
                                <?php elseif ($product['stock'] > 0): ?>
                                    <span class="text-warning"><?php echo $product['stock']; ?></span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning" onclick="editProduct(
                                        <?php echo $product['id']; ?>,
                                        '<?php echo htmlspecialchars(addslashes($product['product_name'])); ?>',
                                        <?php echo $product['price']; ?>,
                                        '<?php echo htmlspecialchars(addslashes($product['short_description'])); ?>',
                                        '<?php echo htmlspecialchars(str_replace(["\r\n", "\r", "\n"], "\\n", addslashes($product['description']))); ?>',
                                        <?php echo $product['stock']; ?>,
                                        <?php echo $product['category_id'] ? $product['category_id'] : 'null'; ?>
                                    )">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
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
                            <td colspan="7" class="text-center py-4 text-muted">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-card border-secondary">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title text-white">Edit Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_category_id" class="form-label">Category</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="edit_stock" name="stock" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_product_image" class="form-label">Product Image (optional)</label>
                            <input type="file" class="form-control" id="edit_product_image" name="product_image" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_short_description" class="form-label">Short Description</label>
                        <input type="text" class="form-control" id="edit_short_description" name="short_description" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary border-0" style="background:#475569;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit" class="btn btn-primary px-4">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(id, name, price, short_desc, description, stock, category_id) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_short_description').value = short_desc;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_stock').value = stock;
    document.getElementById('edit_category_id').value = category_id || "";
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?php include 'footer_admin.php'; ?>
