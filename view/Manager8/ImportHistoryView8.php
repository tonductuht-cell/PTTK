<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header('Location: ../LoginView8.php');
    exit;
}
$user = $_SESSION['user'];
require_once __DIR__ . '/../../dao/SupplierDAO8.php';
require_once __DIR__ . '/../../dao/ImportInvoiceDAO8.php';

$supplierId = isset($_GET['supplierId']) && is_string($_GET['supplierId']) ? $_GET['supplierId'] : '';
$startDate = isset($_GET['startDate']) && is_string($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) && is_string($_GET['endDate']) ? $_GET['endDate'] : '';
if ($supplierId === '') {
    header('Location: SupplierImportStatisticView8.php');
    exit;
}

$supplierDAO = new SupplierDAO8();
$supplier = $supplierDAO->getSupplierById($supplierId);
$error = '';
try {
    $listInvoices = (new ImportInvoiceDAO8())->getListImportInvoice($supplierId, $startDate, $endDate);
} catch (PDOException $exception) {
    error_log('Không thể tải lịch sử nhập hàng: ' . $exception->getMessage());
    $listInvoices = [];
    $error = 'Không thể tải lịch sử nhập hàng. Vui lòng kiểm tra kết nối cơ sở dữ liệu.';
}
$totalValue = array_sum(array_map(static fn($invoice) => $invoice->totalValue, $listInvoices));
$formatDate = static function (string $value): string {
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value ? $date->format('d/m/Y') : $value;
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShopMan – Lịch sử nhập hàng</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="standalone-screen">
<header class="app-navbar">
  <a href="ManagerMainMenu8.php" class="navbar-brand">ShopMan</a>
  <div class="navbar-user-box"><div class="navbar-user-info"><span class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span></div><a href="../Logout8.php" class="btn-nav-logout">Đăng xuất</a></div>
</header>
<main class="app-container">
  <div class="screen-header-box pttk-style">
    <div class="screen-header-left">
      <h2>LỊCH SỬ NHẬP HÀNG CỦA NHÀ CUNG CẤP</h2>
      <p>Đối tác: <strong style="color:#000;"><?= htmlspecialchars($supplier ? $supplier->name : $supplierId, ENT_QUOTES, 'UTF-8') ?></strong> (Mã NCC: <?= htmlspecialchars($supplierId, ENT_QUOTES, 'UTF-8') ?>)
        <?php if ($startDate !== '' || $endDate !== ''): ?>
          &nbsp;|&nbsp; Lọc từ ngày: <strong><?= htmlspecialchars($formatDate($startDate), ENT_QUOTES, 'UTF-8') ?></strong> đến: <strong><?= htmlspecialchars($formatDate($endDate), ENT_QUOTES, 'UTF-8') ?></strong>
        <?php endif; ?>
      </p>
    </div>
  </div>
  <?php if ($error !== ''): ?><p role="alert" class="error-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <section class="data-card">
    <div class="card-header-row"><h3>Các hoá đơn nhập hàng phát sinh</h3><span class="badge"><?= count($listInvoices) ?> hoá đơn</span></div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Mã HD nhập</th><th>Ngày nhập kho</th><th>Nhân viên phụ trách</th><th style="text-align:right;">Tổng sản lượng</th><th style="text-align:right;">Tổng giá trị (VNĐ)</th><th>Thao tác</th></tr></thead>
        <tbody>
          <?php if (empty($listInvoices)): ?>
            <tr><td colspan="6" class="empty-state"><p>Không có hoá đơn nhập nào của nhà cung cấp này trong khoảng thời gian đã chọn.</p></td></tr>
          <?php else: foreach ($listInvoices as $invoice): ?>
            <?php $detailUrl = 'ImportInvoiceDetailView8.php?invoiceId=' . urlencode($invoice->id) . '&supplierId=' . urlencode($supplierId) . '&startDate=' . urlencode($startDate) . '&endDate=' . urlencode($endDate); ?>
            <tr>
              <td><?= htmlspecialchars($invoice->id, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= date('d/m/Y', strtotime($invoice->importDate)) ?></td>
              <td><?= htmlspecialchars($invoice->staff ? $invoice->staff->name : 'Quản lý', ENT_QUOTES, 'UTF-8') ?></td>
              <td style="text-align:right;font-weight:700;color:#2b6cb0;"><?= number_format($invoice->totalQty, 0, ',', '.') ?></td>
              <td style="text-align:right;font-weight:700;color:#276749;"><?= number_format($invoice->totalValue, 0, ',', '.') ?> ₫</td>
              <td><a href="<?= $detailUrl ?>" class="btn-primary" style="text-decoration:none;font-size:.82rem;padding:.45rem .9rem;display:inline-block;">Click để xem</a></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
        <?php if (!empty($listInvoices)): ?>
        <tfoot><tr style="background:#f7fafc;font-weight:800;border-top:2px solid #cbd5e0;">
          <td colspan="3"></td><td style="text-align:right;padding:1rem;font-size:1.05rem;">Tổng:</td><td style="text-align:right;color:#276749;font-size:1.15rem;padding:1rem;"><?= number_format($totalValue, 0, ',', '.') ?> ₫</td><td></td>
        </tr></tfoot>
        <?php endif; ?>
      </table>
    </div>
  </section>
  <div style="margin-top:1rem;"><a href="SupplierImportStatisticView8.php?startDate=<?= urlencode($startDate) ?>&amp;endDate=<?= urlencode($endDate) ?>" class="btn-primary" style="text-decoration:none;background:linear-gradient(135deg,#2b6cb0,#1a365d);font-weight:700;border-radius:10px;padding:.65rem 1.2rem;">Quay lại</a></div>
</main>
</body>
</html>
