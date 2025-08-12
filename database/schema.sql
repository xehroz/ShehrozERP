-- Procurement ERP Database Schema

-- Users table for authentication and user management
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'finance', 'director', 'purchaser', 'user') NOT NULL DEFAULT 'user',
    status ENUM('active', 'inactive', 'locked') NOT NULL DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
    user_id INT NOT NULL,
    token VARCHAR(100) NOT NULL,
    expiry_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, token),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_code VARCHAR(20) NOT NULL UNIQUE,
    supplier_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50) DEFAULT 'Pakistan',
    ntn VARCHAR(20),
    strn VARCHAR(20),
    payment_terms VARCHAR(100),
    status ENUM('active', 'inactive', 'blacklisted') NOT NULL DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inventory items
CREATE TABLE IF NOT EXISTS inventory_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(20) NOT NULL UNIQUE,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    unit VARCHAR(20) NOT NULL,
    current_price DECIMAL(15,2) DEFAULT 0.00,
    tax_rate DECIMAL(5,2) DEFAULT 18.00,
    reorder_level INT DEFAULT 0,
    current_stock INT DEFAULT 0,
    status ENUM('active', 'inactive', 'discontinued') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Purchase orders
CREATE TABLE IF NOT EXISTS purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(20) NOT NULL UNIQUE,
    supplier_id INT NOT NULL,
    po_date DATE NOT NULL,
    delivery_date DATE NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft', 'pending_approval', 'approved_by_manager', 'approved_by_finance', 'approved', 'rejected', 'canceled', 'completed', 'received', 'partially_received') NOT NULL DEFAULT 'draft',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Purchase order items
CREATE TABLE IF NOT EXISTS purchase_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    item_id INT,
    description TEXT,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 18.00,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(15,2) NOT NULL,
    received_quantity DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id)
);

-- Purchase order approvals
CREATE TABLE IF NOT EXISTS purchase_order_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    user_id INT NOT NULL,
    action ENUM('approve', 'reject') NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Goods receipt notes
CREATE TABLE IF NOT EXISTS goods_receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grn_number VARCHAR(20) NOT NULL UNIQUE,
    po_id INT NOT NULL,
    receipt_date DATE NOT NULL,
    delivery_note_number VARCHAR(50),
    received_by INT,
    notes TEXT,
    status ENUM('draft', 'completed') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (received_by) REFERENCES users(id)
);

-- Goods receipt items
CREATE TABLE IF NOT EXISTS goods_receipt_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grn_id INT NOT NULL,
    po_item_id INT NOT NULL,
    quantity_received DECIMAL(10,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (grn_id) REFERENCES goods_receipts(id) ON DELETE CASCADE,
    FOREIGN KEY (po_item_id) REFERENCES purchase_order_items(id)
);

-- FBR Digital Invoices
CREATE TABLE IF NOT EXISTS fbr_digital_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL,
    irin VARCHAR(50) NOT NULL UNIQUE,
    invoice_date DATETIME NOT NULL,
    buyer_name VARCHAR(100) NOT NULL,
    buyer_ntn VARCHAR(20),
    total_amount DECIMAL(15,2) NOT NULL,
    total_tax DECIMAL(15,2) NOT NULL,
    request_data TEXT,
    response_data TEXT,
    verification_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- FBR API Logs
CREATE TABLE IF NOT EXISTS fbr_api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50),
    http_code INT,
    request_data TEXT,
    response_data TEXT,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Default password: password