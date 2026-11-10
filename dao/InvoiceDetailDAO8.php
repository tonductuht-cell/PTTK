<?php
require_once __DIR__ . '/DAO.php';
require_once __DIR__ . '/../model/Invoice8.php';
require_once __DIR__ . '/../model/InvoiceDetail8.php';
require_once __DIR__ . '/../model/Product8.php';
require_once __DIR__ . '/../model/User8.php';

class InvoiceDetailDAO8 extends DAO {
    public function getInvoiceDetail(string $invoiceId, ?string $customerId = null): ?Invoice8 {
        $where = ['si.id = ?'];
        $params = [$invoiceId];
        if ($customerId !== null) {
            $where[] = 'si.customer_id = ?';
            $params[] = $customerId;
        }

        $stmt = $this->getConnection()->prepare("
            SELECT si.id, si.sale_date, si.delivery_address, si.status,
                   u.id AS customer_id, u.name AS customer_name,
                   u.phone AS customer_phone, u.email AS customer_email
            FROM sale_invoices si
            JOIN users u ON u.id = si.customer_id
            WHERE " . implode(' AND ', $where) . "
            LIMIT 1
        ");
        $stmt->execute($params);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $customer = new User8(
            $row['customer_id'],
            null,
            null,
            'customer',
            $row['customer_name'],
            $row['customer_phone'],
            $row['customer_email']
        );
        $invoice = new Invoice8(
            $row['id'],
            $customer,
            $row['sale_date'],
            $row['delivery_address'],
            $row['status']
        );

        $stmtItem = $this->getConnection()->prepare("
            SELECT item.id, item.qty, item.price,
                   p.id AS product_id, p.name AS product_name,
                   p.type AS product_type, p.unit AS product_unit
            FROM sale_invoice_items item
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
            $detail = new InvoiceDetail8(
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

