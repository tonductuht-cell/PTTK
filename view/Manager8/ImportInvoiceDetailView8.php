<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header('Location: ../LoginView8.php');
    exit;
}
$user = $_SESSION['user'];
require_once __DIR__ . '/../../dao/ImportInvoiceDetailDAO8.php';

$invoiceId = isset($_GET['invoiceId']) && is_string($_GET['invoiceId']) ? $_GET['invoiceId'] : '';
$supplierId = isset($_GET['supplierId']) && is_string($_GET['supplierId']) ? $_GET['supplierId'] : '';
$startDate = isset($_GET['startDate']) && is_string($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) && is_string($_GET['endDate']) ? $_GET['endDate'] : '';
if ($invoiceId === '') {
    header('Location: SupplierImportStatisticView8.php');
    exit;
}

try {
    $invoice = (new ImportInvoiceDetailDAO8())->getImportInvoice($invoiceId);
} catch (PDOException $exception) {
    error_log('Không thể tải chi tiết phiếu nhập: ' . $exception->getMessage());
    $invoice = null;
}
if (!$invoice) {
    http_response_code(404);
    $error = 'Không tìm thấy hoá đơn nhập #' . $invoiceId . ' hoặc không thể tải dữ liệu.';
}
$supplierId = $supplierId ?: ($invoice && $invoice->supplier ? $invoice->supplier->id : '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShopMan – Chi tiết hoá đơn nhập</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="standalone-screen">
<header class="app-navbar">
  <a href="ManagerMainMenu8.php" class="navbar-brand">ShopMan</a>
  <div class="navbar-user-box"><div class="navbar-user-info"><span class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span></div><a href="../Logout8.php" class="btn-nav-logout">Đăng xuất</a></div>
</header>
<main class="app-container">
  <?php if (!$invoice): ?>
    <section class="data-card"><p class="error-msg" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p></section>
  <?php else: ?>
  <div class="screen-header-box pttk-style"><div class="screen-header-left"><h2>CHI TIẾT HOÁ ĐƠN NHẬP KHO</h2><p>Mã chứng từ hoá đơn nhập: <strong style="color:#000;"><?= htmlspecialchars($invoice->id, ENT_QUOTES, 'UTF-8') ?></strong></p></div></div>
  <section class="data-card">
    <div class="card-header-row"><h3>Danh mục mặt hàng trong lần nhập này</h3><span class="badge"><?= count($invoice->listDetail) ?> mặt hàng</span></div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Mã hàng</th><th>Tên hàng hoá</th><th>Loại mặt hàng</th><th>Đơn vị tính</th><th style="text-align:right;">Số lượng nhập</th><th style="text-align:right;">Giá nhập (VNĐ)</th><th style="text-align:right;">Thành tiền (VNĐ)</th></tr></thead>
        <tbody>
          <?php foreach ($invoice->listDetail as $detail): ?>
            <tr>
              <td><strong><?= htmlspecialchars($detail->product?->id ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td><strong><?= htmlspecialchars($detail->product?->name ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td><?= htmlspecialchars($detail->product?->type ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($detail->product?->unit ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td style="text-align:right;font-weight:700;color:#2b6cb0;"><?= number_format($detail->qty, 0, ',', '.') ?></td>
              <td style="text-align:right;"><?= number_format($detail->price, 0, ',', '.') ?> ₫</td>
              <td style="text-align:right;font-weight:700;color:#276749;"><?= number_format($detail->getSubtotal(), 0, ',', '.') ?> ₫</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot><tr style="background:#f7fafc;font-weight:800;border-top:2px solid #cbd5e0;">
          <td colspan="5"></td><td style="text-align:right;padding:1rem;">Tổng:</td><td style="text-align:right;color:#667eea;font-size:1.2rem;padding:1rem;"><?= number_format($invoice->totalValue, 0, ',', '.') ?> ₫</td>
        </tr></tfoot>
      </table>
    </div>
  </section>
  <div style="display:flex;justify-content:space-between;gap:.6rem;flex-wrap:wrap;margin-top:1rem;">
    <a href="ImportHistoryView8.php?supplierId=<?= urlencode($supplierId) ?>&amp;startDate=<?= urlencode($startDate) ?>&amp;endDate=<?= urlencode($endDate) ?>" class="btn-primary" style="text-decoration:none;background:linear-gradient(135deg,#2b6cb0,#1a365d);font-weight:700;border-radius:10px;padding:.65rem 1.2rem;">Quay lại</a>
    <a href="ManagerMainMenu8.php" class="btn-primary" style="text-decoration:none;font-weight:700;border-radius:10px;padding:.65rem 1.2rem;">Menu chính</a>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
