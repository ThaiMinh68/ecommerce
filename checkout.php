<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập trước khi mua hàng!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = intval($_SESSION['user_id']);
$user_query = mysqli_query($db, "SELECT * FROM Users WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

$user_fullname = $user_data['first_name'] . ' ' . $user_data['last_name'];
$user_email = $user_data['email'];
$user_phone = $user_data['phone'];
$address_parts = explode(', ', $user_data['address']);
$user_address = isset($address_parts[0]) ? $address_parts[0] : '';
$user_city = isset($address_parts[1]) ? $address_parts[1] : '';
$user_country = isset($address_parts[2]) ? $address_parts[2] : '';
$user_postcode = isset($address_parts[3]) ? $address_parts[3] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    if (empty($_SESSION['cart'])) {
        header("Location: cart.php");
        exit;
    }
    
    $subtotal = 0;
    $ids = array_keys($_SESSION['cart']);
    $ids_str = implode(',', array_map('intval', $ids));
    $query_cart = "SELECT id, price FROM Products WHERE id IN ($ids_str)";
    $result_cart = mysqli_query($db, $query_cart);
    while($row = mysqli_fetch_assoc($result_cart)) {
        $subtotal += $row['price'] * $_SESSION['cart'][$row['id']];
    }
    
    $fullname = mysqli_real_escape_string($db, $_POST['fullname']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $country = mysqli_real_escape_string($db, $_POST['country']);
    $address = mysqli_real_escape_string($db, $_POST['address']);
    $city = mysqli_real_escape_string($db, $_POST['city']);
    $postcode = mysqli_real_escape_string($db, $_POST['postcode']);
    $payment = mysqli_real_escape_string($db, $_POST['payment_method']);
    
    $full_address = "$address, $city, $postcode, $country";
    $code = 'ORD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    $total_qty = array_sum($_SESSION['cart']);
    
    $query_order = "INSERT INTO orders (code, user_id, guest_name, guest_email, address, phone, quantity, note, subtotal, shipping_fee, VAT, total, payment, status)
                    VALUES ('$code', $user_id, '$fullname', '$email', '$full_address', '$phone', $total_qty, '', $subtotal, 0, 0, $subtotal, '$payment', 'Pending')";
                    
    if (mysqli_query($db, $query_order)) {
        $order_id = mysqli_insert_id($db);
        
        foreach ($_SESSION['cart'] as $product_id => $qty) {
            $q_price = "SELECT price FROM Products WHERE id = " . intval($product_id);
            $r_price = mysqli_query($db, $q_price);
            $p_price = mysqli_fetch_assoc($r_price)['price'];
            
            $q_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $qty, $p_price)";
            mysqli_query($db, $q_detail);
        }
        
        unset($_SESSION['cart']);
        
        header("Location: checkout.php?success=1");
        exit;
    }
}
?>

<?php
include "header.php";
?>
    <!--------------- blog-tittle-section---------------->
    <section class="blog about-blog">
        <div class="container">
            <div class="blog-bradcrum">
                <span><a href="index.html">Home</a></span>
                <span class="devider">/</span>
                <span><a href="#">Checkout</a></span>
            </div>
            <div class="blog-heading about-heading">
                <h1 class="heading">Checkout</h1>
            </div>
        </div>
    </section>
    <!--------------- blog-tittle-section-end---------------->

    <!--------------- checkout-section---------------->
    <section class="checkout product footer-padding">
        <div class="container">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success text-center">
                    <h4 class="alert-heading">Order Placed Successfully!</h4>
                    <p>Thank you for your purchase. We have received your order and will process it shortly.</p>
                    <a href="index.php" class="shop-btn mt-3 d-inline-block">Continue Shopping</a>
                </div>
            <?php else: ?>
            <div class="checkout-section">
                <form action="checkout.php" method="POST">
                <div class="row gy-5">
                    <div class="col-lg-6">
                        <div class="checkout-wrapper">
                            <div class="account-section billing-section">
                                <h5 class="wrapper-heading">Billing Details</h5>
                                <div class="review-form">
                                    <div class="review-form-name mb-3">
                                        <label for="fullname" class="form-label">Full Name*</label>
                                        <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Enter your full name" value="<?php echo htmlspecialchars($user_fullname); ?>" required>
                                    </div>
                                    <div class=" account-inner-form">
                                        <div class="review-form-name">
                                            <label for="email" class="form-label">Email*</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="user@gmail.com" value="<?php echo htmlspecialchars($user_email); ?>" required>
                                        </div>
                                        <div class="review-form-name">
                                            <label for="phone" class="form-label">Phone*</label>
                                            <input type="tel" id="phone" name="phone" class="form-control"
                                                placeholder="+880388**0899" value="<?php echo htmlspecialchars($user_phone); ?>" required>
                                        </div>
                                    </div>
                                    <div class="review-form-name">
                                        <label for="country" class="form-label">Country*</label>
                                        <input type="text" id="country" name="country" class="form-control" placeholder="Enter your Country" value="<?php echo htmlspecialchars($user_country); ?>" required>
                                    </div>
                                    <div class="review-form-name address-form">
                                        <label for="address" class="form-label">Address*</label>
                                        <input type="text" id="address" name="address" class="form-control"
                                            placeholder="Enter your Address" value="<?php echo htmlspecialchars($user_address); ?>" required>
                                    </div>
                                    <div class=" account-inner-form city-inner-form">
                                        <div class="review-form-name">
                                            <label for="city" class="form-label">Town / City*</label>
                                            <input type="text" id="city" name="city" class="form-control" placeholder="Enter your City" value="<?php echo htmlspecialchars($user_city); ?>" required>
                                        </div>
                                        <div class="review-form-name">
                                            <label for="number" class="form-label">Postcode / ZIP*</label>
                                            <input type="text" id="number" name="postcode" class="form-control" placeholder="Enter ZIP or Postcode" value="<?php echo htmlspecialchars($user_postcode); ?>" required>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="checkout-wrapper">
                            <a href="#" class="shop-btn">Enter Coupon Code</a>
                            <div class="account-section billing-section">
                                <h5 class="wrapper-heading">Order Summary</h5>
                                <div class="order-summery">
                                    <div class="subtotal product-total">
                                        <h5 class="wrapper-heading">PRODUCT</h5>
                                        <h5 class="wrapper-heading">TOTAL</h5>
                                    </div>
                                    <hr>
                                    <div class="subtotal product-total">
                                        <ul class="product-list">
                                            <?php foreach ($cart_items as $item): 
                                                $qty = $_SESSION['cart'][$item['id']];
                                                $item_total = $item['price'] * $qty;
                                            ?>
                                            <li>
                                                <div class="product-info">
                                                    <h5 class="wrapper-heading"><?php echo htmlspecialchars($item['product_name']); ?> x<?php echo $qty; ?></h5>
                                                </div>
                                                <div class="price">
                                                    <h5 class="wrapper-heading">$<?php echo number_format($item_total, 2); ?></h5>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="subtotal product-total">
                                        <h5 class="wrapper-heading">SUBTOTAL</h5>
                                        <h5 class="wrapper-heading">$<?php echo number_format($subtotal, 2); ?></h5>
                                    </div>
                                    <div class="subtotal product-total">
                                        <ul class="product-list">
                                            <li>
                                                <div class="product-info">
                                                    <p class="paragraph">SHIPPING</p>
                                                    <h5 class="wrapper-heading">Free Shipping</h5>

                                                </div>
                                                <div class="price">
                                                    <h5 class="wrapper-heading">+$0</h5>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="subtotal total">
                                        <h5 class="wrapper-heading">TOTAL</h5>
                                        <h5 class="wrapper-heading price">$<?php echo number_format($subtotal, 2); ?></h5>
                                    </div>
                                    <div class="subtotal payment-type">
                                        <div class="checkbox-item">
                                            <input type="radio" id="bank" name="payment_method" value="bank" required>
                                            <div class="bank">
                                                <h5 class="wrapper-heading">Direct Bank Transfer</h5>
                                                <p class="paragraph">Make your payment directly into our bank account.
                                                    Please use
                                                    <span class="inner-text">
                                                        your Order ID as the payment reference.
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="radio" id="cash" name="payment_method" value="cod" required>
                                            <div class="cash">
                                                <h5 class="wrapper-heading">Cash on Delivery</h5>
                                            </div>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="radio" id="credit" name="payment_method" value="credit" required>
                                            <div class="credit">
                                                <h5 class="wrapper-heading">Credit/Debit Cards or Paypal</h5>

                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="place_order" class="shop-btn w-100" style="border:none;">Place Order Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <!--------------- checkout-section-end---------------->

<?php
include "footer.php";
?>