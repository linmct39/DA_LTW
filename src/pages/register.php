<?php
session_start();
require_once("../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Validate cơ bản
    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra username hoặc email đã tồn tại chưa
        $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt_check = $db->prepare($check_query);
        $stmt_check->execute([$username, $email]);
        
        if ($stmt_check->rowCount() > 0) {
            $error = "Tên đăng nhập hoặc Email đã tồn tại!";
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Chèn vào Database
            $sql = "INSERT INTO users (username, email, password, fullname, phone, address, role, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 'customer', NOW())";
            $stmt = $db->prepare($sql);
            
            if ($stmt->execute([$username, $email, $hashed_password, $fullname, $phone, $address])) {
                $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    /* Sử dụng lại style của trang Login cho đồng bộ */
    .page-container { display: flex; flex-direction: column; min-height: 100vh; }
    .content { flex: 1; display: flex; justify-content: center; align-items: center; background: linear-gradient(180deg, #F9F9F5 0%, #FFFFFF 100%); padding: 40px 20px; }
    .auth-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
    .auth-title { text-align: center; color: #2E7D32; font-size: 28px; margin-bottom: 30px; font-weight: bold; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
    .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 15px; }
    .btn-auth { width: 100%; padding: 12px; background: #2E7D32; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px;}
    .btn-auth:hover { background: #1B5E20; }
    .auth-link { text-align: center; margin-top: 20px; font-size: 14px; }
    .auth-link a { color: #2E7D32; font-weight: bold; text-decoration: none; }
    .error-msg { background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
    .success-msg { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
</style>

<main class="content">
    <div class="auth-box">
        <h2 class="auth-title">Đăng Ký Tài Khoản</h2>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-msg">
                <?php echo $success; ?>
                <br><br>
                <a href="login.php" style="color: #2E7D32; font-weight: bold;">>> Bấm vào đây để Đăng nhập <<</a>
            </div>
        <?php else: ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="fullname" required placeholder="Nguyễn Văn A">
            </div>

            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" required placeholder="username123">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="email@example.com">
            </div>
            
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" required placeholder="0909xxxxxx">
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="address" required placeholder="Số nhà, đường...">
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required placeholder="******">
            </div>

            <div class="form-group">
                <label>Nhập lại mật khẩu</label>
                <input type="password" name="confirm_password" required placeholder="******">
            </div>

            <button type="submit" class="btn-auth">ĐĂNG KÝ</button>
        </form>
        <?php endif; ?>

        <div class="auth-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>