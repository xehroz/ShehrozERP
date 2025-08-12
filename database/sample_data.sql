-- Procurement ERP Sample Data
-- This file adds sample data for testing purposes

-- Add suppliers
INSERT INTO suppliers (supplier_code, supplier_name, contact_person, phone, email, address, city, country, ntn, strn, payment_terms, status) VALUES 
('SUP-001', 'Khan Electronics Ltd.', 'Ahmed Khan', '+92-321-1234567', 'ahmed@khanelec.com', '123 Korangi Industrial Area', 'Karachi', 'Pakistan', '1234567-8', 'STR-987654', 'Net 30 days', 'active'),
('SUP-002', 'Lahore Computer Supplies', 'Fatima Ali', '+92-300-9876543', 'fatima@lcs.com.pk', '45-B Gulberg III', 'Lahore', 'Pakistan', '8765432-1', 'STR-123456', 'Net 15 days', 'active'),
('SUP-003', 'Islamabad Office Solutions', 'Usman Malik', '+92-333-5556677', 'usman@iosltd.com', 'Plot 78, I-9 Markaz', 'Islamabad', 'Pakistan', '5432198-7', 'STR-456789', 'Net 45 days', 'active'),
('SUP-004', 'Peshawar Hardware Traders', 'Nadia Khan', '+92-345-1112233', 'nadia@pht.pk', '22 Saddar Road', 'Peshawar', 'Pakistan', '9988776-5', 'STR-654321', 'Cash on delivery', 'active'),
('SUP-005', 'Global Tech Imports', 'Sajid Mahmood', '+92-321-7778889', 'sajid@globaltech.com.pk', '56-C PECHS Block 6', 'Karachi', 'Pakistan', '1122334-4', 'STR-778899', 'Letter of credit', 'active');

-- Add inventory items
INSERT INTO inventory_items (item_code, item_name, description, category, unit, current_price, tax_rate, reorder_level, current_stock, status) VALUES 
('IT-001', 'Dell Latitude 5420', '14-inch Business Laptop, Intel i5, 8GB RAM, 256GB SSD', 'Laptops', 'Piece', 120000.00, 18.00, 5, 10, 'active'),
('IT-002', 'HP LaserJet Pro M404dn', 'Monochrome Laser Printer, 40ppm, Duplex', 'Printers', 'Piece', 45000.00, 18.00, 3, 5, 'active'),
('IT-003', 'Logitech MK270 Combo', 'Wireless Keyboard and Mouse Combo', 'Peripherals', 'Set', 3500.00, 18.00, 10, 25, 'active'),
('IT-004', 'WD Elements 2TB', 'External Hard Drive, USB 3.0', 'Storage', 'Piece', 12000.00, 18.00, 8, 15, 'active'),
('IT-005', 'Samsung T55 24"', '24-inch Curved Monitor, Full HD', 'Monitors', 'Piece', 35000.00, 18.00, 5, 8, 'active'),
('OFF-001', 'HP A4 Paper', '80gsm, 500 sheets per ream', 'Office Supplies', 'Ream', 850.00, 18.00, 50, 100, 'active'),
('OFF-002', 'Pilot G-2 Pens', 'Blue, 0.7mm, Box of 12', 'Office Supplies', 'Box', 950.00, 18.00, 20, 35, 'active'),
('OFF-003', 'Staedtler Pencils', 'HB, Box of 10', 'Office Supplies', 'Box', 350.00, 18.00, 15, 25, 'active'),
('OFF-004', 'Post-it Notes', '3x3 inches, Yellow, Pack of 12', 'Office Supplies', 'Pack', 1200.00, 18.00, 10, 20, 'active'),
('FUR-001', 'Executive Office Chair', 'Ergonomic, Leather, Black', 'Furniture', 'Piece', 25000.00, 18.00, 3, 5, 'active'),
('FUR-002', 'Meeting Table', '8-person Conference Table, Oak Finish', 'Furniture', 'Piece', 45000.00, 18.00, 2, 3, 'active');

-- Add users
INSERT INTO users (name, email, password, role, status) VALUES 
-- Default password for all users is 'password': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
('Kamran Ahmed', 'kamran@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'active'),
('Zainab Khan', 'zainab@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finance', 'active'),
('Hassan Ali', 'hassan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'director', 'active'),
('Amina Malik', 'amina@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'purchaser', 'active'),
('Bilal Shah', 'bilal@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active');

-- Add purchase orders
INSERT INTO purchase_orders (po_number, supplier_id, po_date, delivery_date, subtotal, tax_amount, total_amount, status, notes, created_by) VALUES 
('PO-202508-0001', 1, '2025-08-01', '2025-08-15', 123500.00, 22230.00, 145730.00, 'approved', 'Office equipment upgrade for finance department', 4),
('PO-202508-0002', 2, '2025-08-03', '2025-08-20', 9350.00, 1683.00, 11033.00, 'pending_approval', 'Monthly office supplies restock', 4),
('PO-202508-0003', 3, '2025-08-05', '2025-08-25', 84000.00, 15120.00, 99120.00, 'draft', 'IT department equipment', 4),
('PO-202508-0004', 5, '2025-08-07', '2025-08-18', 240000.00, 43200.00, 283200.00, 'approved_by_manager', 'New laptops for sales team', 4),
('PO-202508-0005', 4, '2025-08-10', '2025-08-30', 70000.00, 12600.00, 82600.00, 'approved_by_finance', 'Office furniture for new branch', 4);

-- Add purchase order items
INSERT INTO purchase_order_items (po_id, item_id, description, quantity, unit_price, tax_rate, tax_amount, total) VALUES 
-- PO-202508-0001 items
(1, 2, 'HP LaserJet Pro M404dn Printer', 2, 45000.00, 18.00, 16200.00, 106200.00),
(1, 5, 'Samsung T55 24" Monitor', 1, 35000.00, 18.00, 6300.00, 41300.00),

-- PO-202508-0002 items
(2, 6, 'HP A4 Paper (80gsm)', 10, 850.00, 18.00, 1530.00, 10030.00),
(2, 7, 'Pilot G-2 Pens (Blue)', 1, 950.00, 18.00, 171.00, 1121.00),

-- PO-202508-0003 items
(3, 3, 'Logitech MK270 Keyboard/Mouse Combo', 8, 3500.00, 18.00, 5040.00, 33040.00),
(3, 4, 'WD Elements 2TB External HDD', 5, 12000.00, 18.00, 10800.00, 70800.00),

-- PO-202508-0004 items
(4, 1, 'Dell Latitude 5420 Laptop', 2, 120000.00, 18.00, 43200.00, 283200.00),

-- PO-202508-0005 items
(5, 10, 'Executive Office Chair', 2, 25000.00, 18.00, 9000.00, 59000.00),
(5, 11, 'Meeting Table (8-person)', 1, 45000.00, 18.00, 8100.00, 53100.00);

-- Add purchase order approvals
INSERT INTO purchase_order_approvals (po_id, user_id, action, comments, created_at) VALUES 
(1, 2, 'approve', 'Approved as per department budget allocation', '2025-08-02 14:35:22'),
(1, 3, 'approve', 'Final approval granted', '2025-08-02 16:45:10'),
(4, 2, 'approve', 'Approved within budget limits', '2025-08-08 09:22:45'),
(5, 2, 'approve', 'Approved for new branch setup', '2025-08-11 11:15:33');