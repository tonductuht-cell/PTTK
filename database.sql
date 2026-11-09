-- ============================================================
--  ShopMan – Database Schema + Sample Data
--  Import vào phpMyAdmin hoặc chạy: mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS shopman CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopman;

-- ==================== USERS ====================
CREATE TABLE IF NOT EXISTS users (
  id         VARCHAR(10)  PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('manager','customer') NOT NULL,
  name       VARCHAR(100) NOT NULL,
  phone      VARCHAR(20),
  email      VARCHAR(100),
  address    TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users VALUES
('U001','admin', 'admin123',  'manager',  'Tôn Đức Tú',     '0912345678', 'admin@shopman.vn',   NULL),
('U002','khach1','123456',    'customer', 'Tôn Đức Tú',      '0987654321', 'binh@gmail.com',     '12 Lê Lợi, Q.1, TP.HCM'),
('U003','disabled_U003','',   'customer', 'Lê Minh Châu',    '0901112233', 'chau@gmail.com',     '45 Đinh Tiên Hoàng, Q.3, TP.HCM');

-- ==================== SUPPLIERS ====================
CREATE TABLE IF NOT EXISTS suppliers (
  id      VARCHAR(10)  PRIMARY KEY,
  name    VARCHAR(150) NOT NULL,
  phone   VARCHAR(20),
  email   VARCHAR(100),
  address TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO suppliers VALUES
('NCC001','Công ty TNHH Thực Phẩm Sạch',  '02838888001','tpsach@gmail.com',  '100 Nguyễn Trãi, Q.5, TP.HCM'),
('NCC002','Công ty CP Điện Tử Việt Nam',   '02438888002','dtvn@company.vn',   '55 Láng Hạ, Đống Đa, Hà Nội'),
('NCC003','HTX Nông Sản Miền Tây',         '07028888003','nsmt@coop.vn',      '88 Hùng Vương, TP. Cần Thơ'),
('NCC004','Công ty TNHH Gia Dụng Phúc An', '02538888004','phuc-an@giadung.vn','33 Phan Đình Phùng, Đà Nẵng');

-- ==================== PRODUCTS ====================
CREATE TABLE IF NOT EXISTS products (
  id          VARCHAR(10)    PRIMARY KEY,
  name        VARCHAR(200)   NOT NULL,
  type        VARCHAR(50),
  unit        VARCHAR(20),
  buy_price   DECIMAL(15,2)  NOT NULL DEFAULT 0,
  sell_price  DECIMAL(15,2)  NOT NULL DEFAULT 0,
  stock       INT            NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products VALUES
('SP001','Gạo ST25 túi 5kg',       'Thực phẩm','Túi',   65000,   85000,  120),
('SP002','Dầu ăn Neptune 1L',       'Thực phẩm','Chai',  38000,   52000,   85),
('SP003','Nước mắm Phú Quốc 500ml', 'Gia vị',   'Chai',  25000,   38000,  150),
('SP004','Điện thoại Samsung A35',  'Điện tử',  'Cái',  7200000, 8500000,  20),
('SP005','Tai nghe Bluetooth Sony', 'Điện tử',  'Cái',   850000, 1200000,  35),
('SP006','Rau cải xanh tươi 500g',  'Rau củ',   'Gói',    8000,   15000,  200),
('SP007','Cà chua bi 1kg',          'Rau củ',   'Kg',    20000,   35000,   90),
('SP008','Nồi cơm điện Sunhouse',   'Gia dụng', 'Cái',   320000,  450000,  25);

-- ==================== IMPORT INVOICES ====================
CREATE TABLE IF NOT EXISTS import_invoices (
  id           VARCHAR(10) PRIMARY KEY,
  supplier_id  VARCHAR(10) NOT NULL,
  import_date  DATE        NOT NULL,
  staff_id     VARCHAR(10) NOT NULL,
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
  FOREIGN KEY (staff_id)    REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS import_invoice_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id  VARCHAR(10)   NOT NULL,
  product_id  VARCHAR(10)   NOT NULL,
  qty         INT           NOT NULL,
  price       DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (invoice_id)  REFERENCES import_invoices(id),
  FOREIGN KEY (product_id)  REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO import_invoices VALUES
('HDN001','NCC001','2026-01-15','U001'),
('HDN002','NCC002','2026-02-10','U001'),
('HDN003','NCC003','2026-03-05','U001'),
('HDN004','NCC001','2026-04-20','U001'),
('HDN005','NCC004','2026-05-18','U001'),
('HDN006','NCC002','2026-06-12','U001'),
('HDN007','NCC003','2026-07-08','U001'),
('HDN008','NCC001','2026-08-25','U001'),
('HDN009','NCC004','2026-09-14','U001'),
('HDN010','NCC002','2026-10-30','U001');

INSERT INTO import_invoice_items (invoice_id, product_id, qty, price) VALUES
('HDN001','SP001',100,65000), ('HDN001','SP002',60,38000),
('HDN002','SP004',10,7200000),('HDN002','SP005',20,850000),
('HDN003','SP006',300,8000),  ('HDN003','SP007',150,20000),
('HDN004','SP003',200,25000), ('HDN004','SP001',80,65000),
('HDN005','SP008',30,320000),
('HDN006','SP004',15,7200000),('HDN006','SP005',30,850000),
('HDN007','SP006',400,8000),  ('HDN007','SP007',200,20000),
('HDN008','SP001',150,65000), ('HDN008','SP002',90,38000),
('HDN009','SP008',20,320000),
('HDN010','SP005',25,850000);

-- ==================== SALE INVOICES ====================
CREATE TABLE IF NOT EXISTS sale_invoices (
  id               VARCHAR(10) PRIMARY KEY,
  customer_id      VARCHAR(10) NOT NULL,
  sale_date        DATE        NOT NULL,
  delivery_address TEXT,
  status           VARCHAR(50) NOT NULL DEFAULT 'Chờ duyệt',
  FOREIGN KEY (customer_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sale_invoice_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id  VARCHAR(10)   NOT NULL,
  product_id  VARCHAR(10)   NOT NULL,
  qty         INT           NOT NULL,
  price       DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (invoice_id) REFERENCES sale_invoices(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sale_invoice_history (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id  VARCHAR(10) NOT NULL,
  status      VARCHAR(50) NOT NULL,
  status_date DATETIME    NOT NULL,
  FOREIGN KEY (invoice_id) REFERENCES sale_invoices(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO sale_invoices VALUES
('HD001','U002','2026-10-01','12 Lê Lợi, Q.1, TP.HCM',         'Giao thành công'),
('HD002','U002','2026-10-15','12 Lê Lợi, Q.1, TP.HCM',         'Đang giao'),
('HD003','U002','2026-11-01','12 Lê Lợi, Q.1, TP.HCM',         'Chờ duyệt'),
('HD004','U003','2026-09-20','45 Đinh Tiên Hoàng, Q.3, TP.HCM','Giao thành công'),
('HD005','U003','2026-10-28','45 Đinh Tiên Hoàng, Q.3, TP.HCM','Đã huỷ'),
('HD006','U003','2026-11-05','45 Đinh Tiên Hoàng, Q.3, TP.HCM','Đang chuẩn bị');

INSERT INTO sale_invoice_items (invoice_id, product_id, qty, price) VALUES
('HD001','SP001',2,85000),('HD001','SP002',1,52000),
('HD002','SP004',1,8500000),
('HD003','SP003',3,38000),('HD003','SP006',2,15000),
('HD004','SP008',1,450000),('HD004','SP007',2,35000),
('HD005','SP005',1,1200000),
('HD006','SP001',3,85000),('HD006','SP003',2,38000);

INSERT INTO sale_invoice_history (invoice_id, status, status_date) VALUES
('HD001','Chờ duyệt',    '2026-10-01 09:00:00'),
('HD001','Đã duyệt',     '2026-10-01 10:30:00'),
('HD001','Đang chuẩn bị','2026-10-01 14:00:00'),
('HD001','Đang giao',    '2026-10-02 08:00:00'),
('HD001','Giao thành công','2026-10-02 15:00:00'),
('HD002','Chờ duyệt',    '2026-10-15 11:00:00'),
('HD002','Đã duyệt',     '2026-10-15 13:00:00'),
('HD002','Đang chuẩn bị','2026-10-16 09:00:00'),
('HD002','Đang giao',    '2026-10-17 08:00:00'),
('HD003','Chờ duyệt',    '2026-11-01 16:30:00'),
('HD004','Chờ duyệt',    '2026-09-20 10:00:00'),
('HD004','Đã duyệt',     '2026-09-20 11:30:00'),
('HD004','Đang chuẩn bị','2026-09-21 08:00:00'),
('HD004','Đang giao',    '2026-09-22 07:30:00'),
('HD004','Giao thành công','2026-09-22 14:00:00'),
('HD005','Chờ duyệt',    '2026-10-28 09:00:00'),
('HD005','Đã huỷ',       '2026-10-28 10:00:00'),
('HD006','Chờ duyệt',    '2026-11-05 08:30:00'),
('HD006','Đã duyệt',     '2026-11-05 10:00:00'),
('HD006','Đang chuẩn bị','2026-11-05 14:30:00');
