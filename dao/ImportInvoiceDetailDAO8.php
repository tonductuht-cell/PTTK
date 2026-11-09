<?php
require_once __DIR__ . '/DAO.php';
require_once __DIR__ . '/../model/ImportInvoice8.php';
require_once __DIR__ . '/../model/ImportInvoiceDetail8.php';
require_once __DIR__ . '/../model/Product8.php';
require_once __DIR__ . '/../model/Supplier8.php';
require_once __DIR__ . '/../model/User8.php';

class ImportInvoiceDetailDAO8 extends DAO {
    public function getImportInvoice(string $invoiceId): ?ImportInvoice8 {
        $stmt = $this->getConnection()->prepare("
            SELECT ii.id, ii.import_date,
                   s.id AS supplier_id, s.name AS supplier_name, s.phone AS supplier_phone,
                   u.id AS staff_id, u.name AS staff_name
            FROM import_invoices ii
            JOIN suppliers s ON s.id = ii.supplier_id
            JOIN users u ON u.id = ii.staff_id
            WHERE ii.id = ?
            LIMIT 1
        ");
        $stmt->execute([$invoiceId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $supplier = new Supplier8($row['supplier_id'], $row['supplier_name'], $row['supplier_phone']);
        $staff = new User8($row['staff_id'], null, null, 'manager', $row['staff_name']);
        $invoice = new ImportInvoice8($row['id'], $supplier, $row['import_date'], $staff);

        $stmtItem = $this->getConnection()->prepare("
            SELECT item.id, item.qty, item.price,
                   p.id AS product_id, p.name AS product_name,
                   p.type AS product_type, p.unit AS product_unit
            FROM import_invoice_items item
            JOIN products p ON p.id = item.product_id
            WHERE item.invoice_id = ?
            ORDER BY p.name ASC
        ");
        $stmtItem->execute([$invoiceId]);

        foreach ($stmtItem->fetchAll() as $itemRow) {
            $product = new Product8(
                $itemRow['product_id'],
                $itemRow['product_name'],
                $itemRow['product_type'],
                $itemRow['product_unit']
            );
            $detail = new ImportInvoiceDetail8(
                (int)$itemRow['id'],
                $invoiceId,
                $product,
                (int)$itemRow['qty'],
                (float)$itemRow['price']
            );
            $invoice->listDetail[] = $detail;
            $invoice->totalQty += (int)$itemRow['qty'];
            $invoice->totalValue += (float)$itemRow['qty'] * (float)$itemRow['price'];
        }
        return $invoice;
    }
}
