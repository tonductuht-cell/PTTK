<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../LoginView8.php');
    exit;
}
$user = $_SESSION['user'];
require_once __DIR__ . '/../../dao/InvoiceDetailDAO8.php';

$invoiceId = isset($_GET['invoiceId']) && is_string($_GET['invoiceId']) ? $_GET['invoiceId'] : '';
if ($invoiceId === '') {
    header('Location: InvoiceTrackingMenu8.php');
    exit;
}

try {
    $invoice = (new InvoiceDetailDAO8())->getInvoiceDetail($invoiceId, $user['id']);
} catch (PDOException $exception) {
    error_log('Không thể tải chi tiết hoá đơn: ' . $exception->getMessage());
    $invoice = null;
}
if (!$invoice) {
    http_response_code(404);
    $error = 'Không tìm thấy hoá đơn hoặc bạn không có quyền xem hoá đơn này.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShopMan – Chi tiết hoá đơn đặt mua</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="standalone-screen">
<header class="app-navbar customer-nav">
  <a href="CustomerMainMenu8.php" class="navbar-brand">ShopMan</a>
  <div class="navbar-user-box"><div class="navbar-user-info"><span class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span></div><a href="../Logout8.php" class="btn-nav-logout">Đăng xuất</a></div>
</header>
<main class="app-container">
  <?php if (!$invoice): ?>
    <section class="data-card">
      <p class="error-msg" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <a href="InvoiceTrackingMenu8.php" class="btn-primary" style="text-decoration:none;">Quay lại danh sách hoá đơn</a>
    </section>
  <?php else: ?>
  <div class="screen-header-box"><div class="screen-header-left"><h2>Chi tiết hoá đơn đặt mua</h2></div></div>
  <section class="data-card" style="margin-bottom:1.5rem;border-left:4px solid #3182ce;">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.2rem;">
      <div><div style="color:#718096;font-size:.8rem;font-weight:600;">TRẠNG THÁI HIỆN TẠI</div><div style="margin-top:.2rem;"><?= htmlspecialchars($invoice->status, ENT_QUOTES, 'UTF-8') ?></div></div>
      <div><div style="color:#718096;font-size:.8rem;font-weight:600;">THỜI GIAN ĐẶT HÀNG</div><div style="font-size:1.05rem;font-weight:700;color:#2d3748;"><?= date('d/m/Y', strtotime($invoice->saleDate)) ?></div></div>
      <div><div style="color:#718096;font-size:.8rem;font-weight:600;">ĐỊA CHỈ GIAO HÀNG</div><div style="font-size:.95rem;font-weight:600;color:#2d3748;"><?= htmlspecialchars($invoice->deliveryAddress, ENT_QUOTES, 'UTF-8') ?></div></div>
    </div>
  </section>
  <section class="data-card">
    <div class="card-header-row"><h3>Danh sách mặt hàng đã đặt</h3><span class="badge"><?= count($invoice->listDetail) ?> mặt hàng</span></div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Mã hàng</th><th>Tên hàng hoá</th><th>Loại mặt hàng</th><th>Đơn vị tính</th><th style="text-align:center;">Số lượng đặt</th><th style="text-align:right;">Đơn giá</th><th style="text-align:right;">Thành tiền</th></tr></thead>
        <tbody>
          <?php foreach ($invoice->listDetail as $detail): ?>
            <tr>
              <td><strong><?= htmlspecialchars($detail->product?->id ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td><strong><?= htmlspecialchars($detail->product?->name ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td><?= htmlspecialchars($detail->product?->type ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($detail->product?->unit ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td style="text-align:center;font-weight:700;color:#2b6cb0;"><span style="display:inline-block;min-width:5ch;text-align:right;font-variant-numeric:tabular-nums;"><?= number_format($detail->qty, 0, ',', '.') ?></span></td>
              <td style="text-align:right;"><?= number_format($detail->price, 0, ',', '.') ?> ₫</td>
              <td style="text-align:right;font-weight:700;color:#276749;"><?= number_format($detail->getSubtotal(), 0, ',', '.') ?> ₫</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot><tr style="background:#f7fafc;font-weight:800;border-top:2px solid #cbd5e0;">
          <td colspan="6" style="text-align:left;padding:1rem;">TỔNG TIỀN THANH TOÁN:</td>
          <td style="text-align:right;color:#2b6cb0;font-size:1.2rem;padding:1rem;"><?= number_format($invoice->totalValue, 0, ',', '.') ?> ₫</td>
        </tr></tfoot>
      </table>
    </div>
  </section>
  <div style="display:flex;justify-content:space-between;gap:.6rem;flex-wrap:wrap;margin-top:1rem;">
    <a href="InvoiceTrackingMenu8.php" class="btn-primary" style="text-decoration:none;background:linear-gradient(135deg,#2b6cb0,#1a365d);">Quay lại</a>
    <a href="CustomerMainMenu8.php" class="btn-primary" style="text-decoration:none;background:linear-gradient(135deg,#2b6cb0,#1a365d);">Menu chính</a>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
