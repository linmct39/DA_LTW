<?php
session_start();
require_once("../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        header("Location: admin/index.php"); 
    } else {
        header("Location: ../index.php"); 
    }
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email && $password) {
        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role']; 

                if ($user['role'] == 'admin') {
                    header("Location: admin/index.php");
                } else {
                    if (!empty($_SESSION['cart'])) {
                        header("Location: checkout.php"); 
                    } else {
                        header("Location: ../index.php"); 
                    }
                }
                exit;
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Email này chưa được đăng ký!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    .page-container { display: flex; flex-direction: column; min-height: 100vh; }
    .content { flex: 1; display: flex; justify-content: center; align-items: center; background: linear-gradient(180deg, #F9F9F5 0%, #FFFFFF 100%); padding: 40px 20px; }
    .auth-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
    .auth-title { text-align: center; color: #2E7D32; font-size: 28px; margin-bottom: 30px; font-weight: bold; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
    .btn-auth { width: 100%; padding: 12px; background: #2E7D32; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-auth:hover { background: #1B5E20; }
    .auth-link { text-align: center; margin-top: 20px; font-size: 14px; }
    .auth-link a { color: #2E7D32; font-weight: bold; text-decoration: none; }
    .error-msg { background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 14px; }
</style>

<main class="content">
    <div class="auth-box">
        <h2 class="auth-title">Đăng Nhập</h2>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="admin@organic.com">
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-auth">ĐĂNG NHẬP</button>
        </form>
        <div class="auth-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>