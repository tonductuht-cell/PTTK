<?php
require_once __DIR__ . '/DAO.php';
require_once __DIR__ . '/../model/Supplier8.php';

class SupplierDAO8 extends DAO {
    public function getListSupplier(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $supplierId = null
    ): array {
        $where = ['1=1'];
        $params = [];

        if (!empty($startDate)) {
            $where[] = 'ii.import_date >= ?';
            $params[] = $startDate;
        }
        if (!empty($endDate)) {
            $where[] = 'ii.import_date <= ?';
            $params[] = $endDate;
        }
        if (!empty($supplierId)) {
            $where[] = 'ii.supplier_id = ?';
            $params[] = $supplierId;
        }

        $sql = "
            SELECT s.id, s.name, s.phone, s.email, s.address,
                   COUNT(DISTINCT ii.id) AS import_count,
                   COALESCE(SUM(item.qty), 0) AS total_qty,
                   COALESCE(SUM(item.qty * item.price), 0) AS total_value
            FROM import_invoices ii
            JOIN suppliers s ON s.id = ii.supplier_id
            JOIN import_invoice_items item ON item.invoice_id = ii.id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY s.id, s.name, s.phone, s.email, s.address
            ORDER BY total_qty DESC
        ";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $supplier = new Supplier8(
                $row['id'],
                $row['name'],
                $row['phone'],
                $row['email'],
                $row['address']
            );
            $supplier->totalImportCount = (int)$row['import_count'];
            $supplier->totalImportQty = (int)$row['total_qty'];
            $supplier->totalImportValue = (float)$row['total_value'];
            $list[] = $supplier;
        }
        return $list;
    }

    public function getSupplierById(string $id): ?Supplier8 {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM suppliers WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row
            ? new Supplier8($row['id'], $row['name'], $row['phone'], $row['email'], $row['address'])
            : null;
    }

    public function getAllSuppliers(): array {
        $stmt = $this->getConnection()->query("SELECT * FROM suppliers ORDER BY name ASC");
        $list = [];
        foreach ($stmt->fetchAll() as $row) {
            $list[] = new Supplier8(
                $row['id'],
                $row['name'],
                $row['phone'],
                $row['email'],
                $row['address']
            );
        }
        return $list;
    }
}
