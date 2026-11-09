<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header('Location: ../LoginView8.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TRANG CHỦ – Hệ thống ShopMan</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{background:linear-gradient(135deg,#f0f4f9,#e2e8f0);font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:30px 20px;color:#1e293b}
    .user-status-bar,.main-card{width:100%;max-width:820px}.user-status-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
    .user-pill,.logout{background:#fff;padding:9px 16px;border-radius:28px;font-size:14px;font-weight:600}.logout{color:#b91c1c;background:#fff1f2;border:1px solid #fecaca;text-decoration:none}
    .main-card{background:#fff;border:2px solid #0f172a;border-radius:24px;overflow:hidden;box-shadow:0 20px 50px #0f172a18}
    .heading{background:linear-gradient(135deg,#fef08a,#facc15);border-bottom:2px solid #0f172a;text-align:center;padding:28px 20px}.heading h1{font-size:32px;letter-spacing:4px}.heading p{font-size:13px;margin-top:5px;color:#713f12}
    .actions{padding:40px 36px;display:grid;gap:18px}.action{width:100%;padding:20px 24px;min-height:68px;border:2px solid #0f172a;border-radius:16px;background:linear-gradient(135deg,#bbf7d0,#86efac);color:#0f172a;font-size:18px;font-weight:700;text-decoration:none;text-align:center;cursor:pointer;font-family:inherit;box-shadow:0 4px 0 #0f172a}.action:hover{background:linear-gradient(135deg,#a7f3d0,#6ee7b7)}
    @media(max-width:600px){.actions{padding:28px 18px}.heading h1{font-size:25px}.action{font-size:16px}}
  </style>
</head>
<body>
  <div class="user-status-bar">
    <div class="user-pill"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
    <a class="logout" href="../Logout8.php">Đăng xuất</a>
  </div>
  <main class="main-card">
    <header class="heading"><h1>TRANG CHỦ</h1><p>Hệ thống Quản lý Bách hoá Trực tuyến – ShopMan</p></header>
    <div class="actions">
      <a class="action" href="SupplierImportStatisticView8.php">Thống kê nhà cung cấp theo lượng hàng nhập</a>
      <button class="action" type="button" onclick="alert('Chức năng: Thống kê khách hàng đang được hoàn thiện!')">Thống kê khách hàng</button>
      <button class="action" type="button" onclick="alert('Chức năng: Thống kê lượng hàng nhập đang được tích hợp!')">Thống kê lượng hàng nhập</button>
      <button class="action" type="button" onclick="alert('Chức năng: Thống kê lượng hàng xuất đang được hoàn thiện!')">Thống kê lượng hàng xuất</button>
    </div>
  </main>
</body>
</html>
