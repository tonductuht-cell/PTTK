<?php
session_start();
require_once __DIR__ . '/../dao/UserDAO8.php';

if (!empty($_SESSION['user'])) {
    $destination = $_SESSION['user']['role'] === 'manager'
        ? 'Manager8/ManagerMainMenu8.php'
        : 'Customer8/CustomerMainMenu8.php';
    header('Location: ' . $destination);
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
    } else {
        try {
            $user = (new UserDAO8())->checkLogin($username, $password);
            if ($user !== null) {
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                ];
                $destination = $user->role === 'manager'
                    ? 'Manager8/ManagerMainMenu8.php'
                    : 'Customer8/CustomerMainMenu8.php';
                header('Location: ' . $destination);
                exit;
            }
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
        } catch (PDOException $exception) {
            error_log('Không thể xác thực tài khoản ShopMan: ' . $exception->getMessage());
            $error = 'Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại sau.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShopMan – Đăng nhập</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="page active">
  <div class="login-wrapper">
    <main class="login-card">
      <div class="login-logo">
        <div class="login-eyebrow">HỆ THỐNG QUẢN LÝ BÁCH HÓA</div>
        <h1>ShopMan</h1>
        <p>Đăng nhập để tiếp tục sử dụng hệ thống</p>
      </div>
      <?php if ($error !== ''): ?>
        <div class="error-msg" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      <form method="post" action="LoginView8.php">
        <div class="form-group">
          <label for="username">Tên đăng nhập</label>
          <input id="username" type="text" name="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="Nhập tên đăng nhập" autocomplete="username" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input id="password" type="password" name="password" placeholder="Nhập mật khẩu"
                 autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn-primary btn-full">Đăng nhập</button>
      </form>
      <div class="login-hint">
        <p><strong>Tài khoản thử nghiệm:</strong></p>
        <p>Quản lý: <code>admin / admin123</code></p>
        <p>Khách hàng: <code>khach1 / 123456</code></p>
      </div>
    </main>
  </div>
</div>
</body>
</html>
