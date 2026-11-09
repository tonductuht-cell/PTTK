<?php
require_once __DIR__ . '/DAO.php';
require_once __DIR__ . '/../model/Invoice8.php';
require_once __DIR__ . '/../model/User8.php';

class InvoiceDAO8 extends DAO {
    public function getListInvoice(
        string $customerId,
        ?string $status = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $where = ['si.customer_id = ?'];
        $params = [$customerId];

        if (!empty($status)) {
            $where[] = 'si.status = ?';
            $params[] = $status;
        }
        if (!empty($startDate)) {
            $where[] = 'si.sale_date >= ?';
            $params[] = $startDate;
        }
        if (!empty($endDate)) {
            $where[] = 'si.sale_date <= ?';
            $params[] = $endDate;
        }

        $sql = "
            SELECT si.id, si.sale_date, si.delivery_address, si.status,
                   u.id AS customer_id, u.name AS customer_name,
                   COALESCE(SUM(item.qty), 0) AS total_qty,
                   COALESCE(SUM(item.qty * item.price), 0) AS total_value
            FROM sale_invoices si
            JOIN users u ON u.id = si.customer_id
            JOIN sale_invoice_items item ON item.invoice_id = si.id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY si.id, si.sale_date, si.delivery_address, si.status, u.id, u.name
            ORDER BY si.sale_date DESC
        ";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $customer = new User8($row['customer_id'], null, null, 'customer', $row['customer_name']);
            $invoice = new Invoice8(
                $row['id'],
                $customer,
                $row['sale_date'],
                $row['delivery_address'],
                $row['status']
            );
            $invoice->totalQty = (int)$row['total_qty'];
            $invoice->totalValue = (float)$row['total_value'];
            $list[] = $invoice;
        }
        return $list;
    }
}
