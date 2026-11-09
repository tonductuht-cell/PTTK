<?php
require_once __DIR__ . '/DAO.php';
require_once __DIR__ . '/../model/ImportInvoice8.php';
require_once __DIR__ . '/../model/Supplier8.php';
require_once __DIR__ . '/../model/User8.php';

class ImportInvoiceDAO8 extends DAO {
    public function getListImportInvoice(
        string $supplierId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $where = ['ii.supplier_id = ?'];
        $params = [$supplierId];

        if (!empty($startDate)) {
            $where[] = 'ii.import_date >= ?';
            $params[] = $startDate;
        }
        if (!empty($endDate)) {
            $where[] = 'ii.import_date <= ?';
            $params[] = $endDate;
        }

        $sql = "
            SELECT ii.id, ii.import_date,
                   s.id AS supplier_id, s.name AS supplier_name,
                   u.id AS staff_id, u.name AS staff_name,
                   COALESCE(SUM(item.qty), 0) AS total_qty,
                   COALESCE(SUM(item.qty * item.price), 0) AS total_value
            FROM import_invoices ii
            JOIN suppliers s ON s.id = ii.supplier_id
            JOIN users u ON u.id = ii.staff_id
            JOIN import_invoice_items item ON item.invoice_id = ii.id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY ii.id, ii.import_date, s.id, s.name, u.id, u.name
            ORDER BY ii.import_date DESC
        ";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $supplier = new Supplier8($row['supplier_id'], $row['supplier_name']);
            $staff = new User8($row['staff_id'], null, null, 'manager', $row['staff_name']);
            $invoice = new ImportInvoice8($row['id'], $supplier, $row['import_date'], $staff);
            $invoice->totalQty = (int)$row['total_qty'];
            $invoice->totalValue = (float)$row['total_value'];
            $list[] = $invoice;
        }
        return $list;
    }
}
