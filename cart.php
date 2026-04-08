<?php
session_start();

// Handle Form Posts / Logic before headers
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
    
    if ($action == 'add' && $product_id > 0) {
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : (isset($_GET['qty']) ? (int)$_GET['qty'] : 1);
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
        header("Location: cart.php");
        exit;
    } elseif ($action == 'remove' && $product_id > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: cart.php");
        exit;
    }
}

// Update Cart Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $id = (int)$id;
            $qty = (int)$qty;
            if ($qty > 0) {
                $_SESSION['cart'][$id] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
    header("Location: cart.php");
    exit;
}

include 'header.php';

$cartData = [];
$totalCart = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $ids_str = implode(',', array_map('intval', $ids));
    $queryCart = "SELECT p.id, p.product_name, p.price, pi.filename 
                  FROM Products p 
                  LEFT JOIN Product_images pi ON p.id = pi.product_id 
                  WHERE p.id IN ($ids_str)
                  GROUP BY p.id";
    $resultCart = mysqli_query($db, $queryCart);
    if ($resultCart) {
        while ($row = mysqli_fetch_assoc($resultCart)) {
            $qty = $_SESSION['cart'][$row['id']];
            $row['qty'] = $qty;
            $row['total'] = $row['price'] * $qty;
            $totalCart += $row['total'];
            $cartData[] = $row;
        }
    }
}
?>

    <!--------------- blog-tittle-section---------------->
    <section class="blog about-blog">
        <div class="container">
            <div class="blog-bradcrum">
                <span><a href="index.php">Home</a></span>
                <span class="devider">/</span>
                <span><a href="#">Cart</a></span>
            </div>
            <div class="blog-heading about-heading">
                <h1 class="heading">Cart</h1>
            </div>
        </div>
    </section>
    <!--------------- blog-tittle-section-end---------------->

    <!--------------- cart-section---------------->
    <section class="product-cart product footer-padding">
        <div class="container">
            <form action="cart.php" method="POST">
                <input type="hidden" name="update_cart" value="1">
                <div class="cart-section">
                    <table>
                        <tbody>
                            <tr class="table-row table-top-row">
                                <td class="table-wrapper wrapper-product">
                                    <h5 class="table-heading">PRODUCT</h5>
                                </td>
                                <td class="table-wrapper">
                                    <div class="table-wrapper-center">
                                        <h5 class="table-heading">PRICE</h5>
                                    </div>
                                </td>
                                <td class="table-wrapper">
                                    <div class="table-wrapper-center">
                                        <h5 class="table-heading">QUANTITY</h5>
                                    </div>
                                </td>
                                <td class="table-wrapper wrapper-total">
                                    <div class="table-wrapper-center">
                                        <h5 class="table-heading">TOTAL</h5>
                                    </div>
                                </td>
                                <td class="table-wrapper">
                                    <div class="table-wrapper-center">
                                        <h5 class="table-heading">ACTION</h5>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php if (!empty($cartData)): ?>
                                <?php foreach ($cartData as $item): ?>
                                <tr class="table-row ticket-row">
                                    <td class="table-wrapper wrapper-product">
                                        <div class="wrapper">
                                            <div class="wrapper-img">
                                                <img src="<?php echo htmlspecialchars(getProductImage($item)); ?>"
                                                    alt="img">
                                            </div>
                                            <div class="wrapper-content">
                                                <a href="product-info.php?id=<?php echo $item['id']; ?>" class="heading text-decoration-none" style="color: inherit;"><?php echo htmlspecialchars($item['product_name']); ?></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-wrapper">
                                        <div class="table-wrapper-center">
                                            <h5 class="heading">$<?php echo number_format($item['price'], 2); ?></h5>
                                        </div>
                                    </td>
                                    <td class="table-wrapper">
                                        <div class="table-wrapper-center">
                                            <div class="quantity">
                                                <span class="minus" onclick="document.getElementById('cart_qty_<?php echo $item['id']; ?>').stepDown()">-</span>
                                                <input type="number" name="qty[<?php echo $item['id']; ?>]" id="cart_qty_<?php echo $item['id']; ?>" class="number" value="<?php echo $item['qty']; ?>" min="1" style="width: 40px; text-align: center; border: none; background: transparent; font-weight: 600; outline: none; -moz-appearance: textfield; padding:0;">
                                                <span class="plus" onclick="document.getElementById('cart_qty_<?php echo $item['id']; ?>').stepUp()">+</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-wrapper wrapper-total">
                                        <div class="table-wrapper-center">
                                            <h5 class="heading" style="color: #3b82f6;">$<?php echo number_format($item['total'], 2); ?></h5>
                                        </div>
                                    </td>
                                    <td class="table-wrapper">
                                        <div class="table-wrapper-center">
                                            <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>">
                                                <span>
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.7 0.3C9.3 -0.1 8.7 -0.1 8.3 0.3L5 3.6L1.7 0.3C1.3 -0.1 0.7 -0.1 0.3 0.3C-0.1 0.7 -0.1 1.3 0.3 1.7L3.6 5L0.3 8.3C-0.1 8.7 -0.1 9.3 0.3 9.7C0.7 10.1 1.3 10.1 1.7 9.7L5 6.4L8.3 9.7C8.7 10.1 9.3 10.1 9.7 9.7C10.1 9.3 10.1 8.7 9.7 8.3L6.4 5L9.7 1.7C10.1 1.3 10.1 0.7 9.7 0.3Z"
                                                            fill="#EB5757"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="p-4">
                                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" class="mb-3" xmlns="http://www.w3.org/2000/svg"><path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L20 21H4L5 9Z" stroke="#aaaaaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <h4 class="text-muted mb-4">Your cart is empty</h4>
                                            <a href="product-sidebar.php" class="shop-btn d-inline-block">Continue Shopping</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                        </tbody>
                    </table>
                </div>
                
                <?php if (!empty($cartData)): ?>
                <div class="cart-total-section mt-4 mb-4 text-end px-3">
                     <h4 class="d-inline-block me-4">Total:</h4>
                     <h3 class="d-inline-block" style="color: #3b82f6;">$<?php echo number_format($totalCart, 2); ?></h3>
                </div>
                
                <div class="wishlist-btn cart-btn">
                    <a href="empty-cart.php" class="clean-btn">Clear Cart</a>
                    <button type="submit" class="shop-btn update-btn" style="border:none;">Update Cart</button>
                    <a href="checkout.php" class="shop-btn">Proceed to Checkout</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </section>
    <!--------------- cart-section-end---------------->

<?php include 'footer.php'; ?>