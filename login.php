<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $query = "SELECT * FROM Users WHERE username = '$username'";
        $result = mysqli_query($db, $query);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_name'] = $row['first_name'] . ' ' . $row['last_name'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with this username.";
        }
    }
}

include 'header.php';
?>
<!--------------- login-section --------------->
    <section class="login footer-padding">
        <div class="container">
            <div class="login-section">
                <div class="review-form">
                    <h5 class="comment-title">Log In</h5>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                    <div class="review-inner-form ">
                        <div class="review-form-name">
                            <label for="username" class="form-label">Username*</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="review-form-name">
                            <label for="password" class="form-label">Password*</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="password" required>
                        </div>
                        <div class="review-form-name checkbox">
                            <div class="checkbox-item">
                                <input type="checkbox">
                                <span class="address">
                                    Remember Me</span>
                            </div>
                            <div class="forget-pass">
                                <p>Forgot password?</p>
                            </div>
                        </div>
                    </div>
                    <div class="login-btn text-center">
                        <button type="submit" name="login" class="shop-btn">Log In</button>
                        <span class="shop-account">Don't have an account? <a href="create-account.php">Sign Up
                                Free</a></span>
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