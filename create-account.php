<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $fname = mysqli_real_escape_string($db, trim($_POST['fname']));
    $lname = mysqli_real_escape_string($db, trim($_POST['lname']));
    $email = mysqli_real_escape_string($db, trim($_POST['email']));
    $phone = mysqli_real_escape_string($db, trim($_POST['phone']));
    $password = $_POST['password'];
    
    // address parts
    $country = mysqli_real_escape_string($db, trim($_POST['country']));
    $address_line = mysqli_real_escape_string($db, trim($_POST['address']));
    $city = mysqli_real_escape_string($db, trim($_POST['city']));
    $postcode = mysqli_real_escape_string($db, trim($_POST['postcode']));
    
    $full_address = "$address_line, $city, $country, $postcode";

    if (empty($username) || empty($fname) || empty($lname) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        $check_user = mysqli_query($db, "SELECT email, username FROM Users WHERE email = '$email' OR username = '$username'");
        if (mysqli_num_rows($check_user) > 0) {
            $user_row = mysqli_fetch_assoc($check_user);
            if ($user_row['username'] == $username) {
                $error = "Username already taken! Please choose another one.";
            } else {
                $error = "Email already registered! Please log in.";
            }
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO Users (username, first_name, last_name, email, phone, address, password, role) 
                      VALUES ('$username', '$fname', '$lname', '$email', '$phone', '$full_address', '$hashed_password', 'customer')";
            if (mysqli_query($db, $query)) {
                $success = "Account created successfully! You can now <a href='login.php'>log in</a>.";
            } else {
                $error = "Error: " . mysqli_error($db);
            }
        }
    }
}

include 'header.php';
?>
 <!--------------- login-section--------------->
    <section class="login account footer-padding">
        <div class="container">
            <div class="login-section account-section">
                <div class="review-form">
                    <h5 class="comment-title">Create Account</h5>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" style="color: green; margin-bottom: 15px;"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="review-form-name address-form">
                            <label for="username" class="form-label">Username*</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Choose a Username" required>
                        </div>
                        <div class=" account-inner-form">
                            <div class="review-form-name">
                                <label for="fname" class="form-label">First Name*</label>
                                <input type="text" id="fname" name="fname" class="form-control" placeholder="First Name" required>
                            </div>
                        <div class="review-form-name">
                            <label for="lname" class="form-label">Last Name*</label>
                            <input type="text" id="lname" name="lname" class="form-control" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class=" account-inner-form">
                        <div class="review-form-name">
                            <label for="email" class="form-label">Email*</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                        </div>
                        <div class="review-form-name">
                            <label for="phone" class="form-label">Phone*</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="+880388**0899" required>
                        </div>
                    </div>
                    <div class="review-form-name address-form">
                        <label for="country" class="form-label">Country*</label>
                        <input type="text" id="country" name="country" class="form-control" placeholder="Enter your Country" required>
                    </div>
                    <div class="review-form-name address-form">
                        <label for="address" class="form-label">Address*</label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="Enter your Address" required>
                    </div>
                    <div class=" account-inner-form city-inner-form">
                        <div class="review-form-name">
                            <label for="city" class="form-label">Town / City*</label>
                            <input type="text" id="city" name="city" class="form-control" placeholder="Enter your City" required>
                        </div>
                        <div class="review-form-name">
                            <label for="number" class="form-label">Postcode / ZIP*</label>
                            <input type="text" id="number" name="postcode" class="form-control" placeholder="0000" required>
                        </div>
                    </div>
                    
                    <div class="review-form-name address-form">
                        <label for="password" class="form-label">Password*</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your Password" required>
                    </div>

                    <div class="review-form-name checkbox">
                        <div class="checkbox-item">
                            <input type="checkbox" required>
                            <p class="remember">
                                I agree all terms and condition in <span class="inner-text">ShopUs.</span></p>
                        </div>
                    </div>
                    <div class="login-btn text-center">
                        <button type="submit" name="register" class="shop-btn">Create an Account</button>
                        <span class="shop-account">Already have an account ?<a href="login.php">Log In</a></span>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!--------------- login-section-end --------------->
<?php
include 'footer.php';
?>