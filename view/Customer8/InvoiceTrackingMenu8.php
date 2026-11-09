<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../LoginView8.php');
    exit;
}
$user = $_SESSION['user'];
require_once __DIR__ . '/../../dao/InvoiceDAO8.php';

$error = '';
try {
    $listInvoices = (new InvoiceDAO8())->getListInvoice($user['id']);
} catch (PDOException $exception) {
    error_log('Không thể tải danh sách hoá đơn: ' . $exception->getMessage());
    $listInvoices = [];
    $error = 'Không thể tải danh sách hoá đơn. Vui lòng kiểm tra kết nối cơ sở dữ liệu.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShopMan – Danh sách hoá đơn đặt mua</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="standalone-screen">
<header class="app-navbar customer-nav">
  <a href="CustomerMainMenu8.php" class="navbar-brand">ShopMan</a>
  <div class="navbar-user-box"><div class="navbar-user-info"><span class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span></div><a href="../Logout8.php" class="btn-nav-logout">Đăng xuất</a></div>
</header>
<main class="app-container">
  <div class="screen-header-box"><div class="screen-header-left"><h2>Danh sách hoá đơn đặt mua</h2></div></div>
  <?php if ($error !== ''): ?><p role="alert" class="error-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <section class="data-card">
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Mã hoá đơn</th><th>Ngày đặt mua</th><th>Địa chỉ nhận hàng</th><th>Trạng thái</th><th style="text-align:center;">Số lượng mặt hàng</th><th style="text-align:right;">Tổng tiền</th><th>Thao tác</th></tr></thead>
        <tbody>
          <?php if (empty($listInvoices)): ?>
            <tr><td colspan="7" class="empty-state">Không tìm thấy hoá đơn đặt mua nào.</td></tr>
          <?php else: foreach ($listInvoices as $invoice): ?>
            <tr>
              <td><?= htmlspecialchars($invoice->id, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= date('d/m/Y', strtotime($invoice->saleDate)) ?></td>
              <td><?= htmlspecialchars($invoice->deliveryAddress, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($invoice->status, ENT_QUOTES, 'UTF-8') ?></td>
              <td style="text-align:center;"><span style="display:inline-block;min-width:5ch;text-align:right;font-variant-numeric:tabular-nums;"><?= number_format($invoice->totalQty, 0, ',', '.') ?></span></td>
              <td style="text-align:right;"><?= number_format($invoice->totalValue, 0, ',', '.') ?> ₫</td>
              <td><a href="InvoiceDetailView8.php?invoiceId=<?= urlencode($invoice->id) ?>" class="btn-primary" style="text-decoration:none;font-size:.82rem;padding:.45rem .9rem;display:inline-block;">Click để xem</a></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
  <div style="margin-top:1rem;"><a href="CustomerMainMenu8.php" class="btn-primary" style="text-decoration:none;background:linear-gradient(135deg,#2b6cb0,#1a365d);">Quay lại</a></div>
</main>
</body>
</html>
