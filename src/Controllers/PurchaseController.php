<?php
/**
 * Procurement ERP - Purchase Controller
 * 
 * Handles purchase order management functionality
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;
use App\Modules\Tax\TaxManager;
use App\Modules\Tax\FBRDigitalInvoicing;

class PurchaseController extends Controller {
    private Database $db;
    private TaxManager $taxManager;
    
    public function __construct() {
        parent::__construct();
        $this->db = new Database();
        $this->taxManager = new TaxManager();
    }
    
    /**
     * Display purchase order creation form
     */
    public function create() {
        // Get suppliers for dropdown
        $suppliers = $this->getSuppliers();
        $items = $this->getInventoryItems();
        $nextPoNumber = $this->generateNextPoNumber();
        
        $this->render('purchase/create', [
            'pageTitle' => 'Create Purchase Order',
            'suppliers' => $suppliers,
            'items' => $items,
            'nextPoNumber' => $nextPoNumber
        ]);
    }
    
    /**
     * Store new purchase order
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/purchase/create');
            return;
        }
        
        // Validate inputs
        $requiredFields = ['supplier_id', 'po_date', 'delivery_date', 'items'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['flash_message'] = 'All required fields must be filled out';
                $_SESSION['flash_type'] = 'danger';
                $this->redirect('/purchase/create');
                return;
            }
        }
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Insert purchase order header
            $po_number = $this->generateNextPoNumber();
            $sql = "INSERT INTO purchase_orders (
                po_number, supplier_id, po_date, delivery_date, 
                subtotal, tax_amount, total_amount, status, notes, 
                created_by, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $status = 'draft';
            $subtotal = 0;
            $tax_amount = 0;
            
            // Calculate totals from items
            $items = $_POST['items'];
            foreach ($items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineTotal;
                $tax = $this->taxManager->calculateSalesTax($lineTotal);
                $tax_amount += $tax;
            }
            
            $total_amount = $subtotal + $tax_amount;
            $createdBy = $_SESSION['user_id'];
            
            $stmt->bind_param(
                'sissdddssi',
                $po_number,
                $_POST['supplier_id'],
                $_POST['po_date'],
                $_POST['delivery_date'],
                $subtotal,
                $tax_amount,
                $total_amount,
                $status,
                $_POST['notes'] ?? '',
                $createdBy
            );
            
            $stmt->execute();
            $po_id = $this->db->lastInsertId();
            
            // Insert purchase order items
            $sql = "INSERT INTO purchase_order_items (
                po_id, item_id, description, quantity, unit_price, 
                tax_rate, tax_amount, total
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($items as $item) {
                $itemId = $item['item_id'];
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $lineTotal = $quantity * $unitPrice;
                $taxRate = $_POST['tax_rate'] ?? 18.0; // Default GST rate
                $taxAmount = $this->taxManager->calculateSalesTax($lineTotal);
                $total = $lineTotal + $taxAmount;
                
                $stmt->bind_param(
                    'iisddddd',
                    $po_id,
                    $itemId,
                    $item['description'],
                    $quantity,
                    $unitPrice,
                    $taxRate,
                    $taxAmount,
                    $total
                );
                
                $stmt->execute();
            }
            
            // Commit transaction
            $this->db->commit();
            
            $_SESSION['flash_message'] = 'Purchase Order ' . $po_number . ' created successfully';
            $_SESSION['flash_type'] = 'success';
            $this->redirect('/purchase/view/' . $po_id);
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            
            $_SESSION['flash_message'] = 'Error creating purchase order: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/purchase/create');
        }
    }
    
    /**
     * Display list of purchase orders
     */
    public function list() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get purchase orders with supplier info
        $sql = "SELECT po.*, s.supplier_name 
                FROM purchase_orders po
                JOIN suppliers s ON po.supplier_id = s.id
                ORDER BY po.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM purchase_orders";
        $countResult = $this->db->query($countSql);
        $totalRows = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRows / $limit);
        
        $this->render('purchase/list', [
            'pageTitle' => 'Purchase Orders',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    /**
     * View a single purchase order
     * 
     * @param int $id Purchase order ID
     */
    public function view($id) {
        // Get purchase order header
        $sql = "SELECT po.*, s.supplier_name, s.contact_person, s.phone, s.email, s.address
                FROM purchase_orders po
                JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $this->notFound('Purchase order not found');
            return;
        }
        
        $order = $result->fetch_assoc();
        
        // Get purchase order items
        $sql = "SELECT poi.*, i.item_name, i.item_code 
                FROM purchase_order_items poi
                LEFT JOIN inventory_items i ON poi.item_id = i.id
                WHERE poi.po_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $itemsResult = $stmt->get_result();
        
        $items = [];
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }
        
        // Get approval history
        $sql = "SELECT a.*, u.name as approver_name
                FROM purchase_order_approvals a
                JOIN users u ON a.user_id = u.id
                WHERE a.po_id = ?
                ORDER BY a.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $approvalsResult = $stmt->get_result();
        
        $approvals = [];
        while ($row = $approvalsResult->fetch_assoc()) {
            $approvals[] = $row;
        }
        
        $this->render('purchase/view', [
            'pageTitle' => 'Purchase Order: ' . $order['po_number'],
            'order' => $order,
            'items' => $items,
            'approvals' => $approvals,
            'canApprove' => $this->canUserApprove($_SESSION['user_id'], $order['status']),
        ]);
    }
    
    /**
     * Display purchase order approval list
     */
    public function approvals() {
        // Check if user has approval permission
        if (!$this->hasApprovalPermission()) {
            $this->forbidden('You do not have permission to access approvals');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        
        // Get purchase orders needing approval based on user role
        $sql = "SELECT po.*, s.supplier_name 
                FROM purchase_orders po
                JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.status = ?";
        
        $approvalStatus = 'pending_approval';
        if ($userRole === 'manager') {
            $approvalStatus = 'pending_approval';
        } else if ($userRole === 'finance') {
            $approvalStatus = 'approved_by_manager';
        } else if ($userRole === 'director') {
            $approvalStatus = 'approved_by_finance';
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $approvalStatus);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        $this->render('purchase/approvals', [
            'pageTitle' => 'Purchase Order Approvals',
            'orders' => $orders,
            'userRole' => $userRole
        ]);
    }
    
    /**
     * Approve or reject a purchase order
     * 
     * @param int $id Purchase order ID
     */
    public function processApproval($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/purchase/view/' . $id);
            return;
        }
        
        // Check if user has approval permission
        if (!$this->hasApprovalPermission()) {
            $this->forbidden('You do not have permission to approve purchase orders');
            return;
        }
        
        $action = $_POST['action'] ?? '';
        $comments = $_POST['comments'] ?? '';
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        
        // Get current PO status
        $sql = "SELECT status, total_amount FROM purchase_orders WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $this->notFound('Purchase order not found');
            return;
        }
        
        $po = $result->fetch_assoc();
        $currentStatus = $po['status'];
        $amount = $po['total_amount'];
        
        // Determine new status based on action, user role, and amount
        $newStatus = $currentStatus;
        $isRejected = ($action === 'reject');
        
        if ($isRejected) {
            $newStatus = 'rejected';
        } else {
            // Approval workflow based on role and amount
            if ($userRole === 'manager') {
                $newStatus = 'approved_by_manager';
                
                // Skip finance approval for small amounts
                if ($amount < 10000) {
                    $newStatus = 'approved';
                }
            } else if ($userRole === 'finance') {
                $newStatus = 'approved_by_finance';
                
                // Skip director approval for medium amounts
                if ($amount < 50000) {
                    $newStatus = 'approved';
                }
            } else if ($userRole === 'director') {
                $newStatus = 'approved';
            }
        }
        
        // Update purchase order status
        $this->db->beginTransaction();
        
        try {
            // Insert approval record
            $sql = "INSERT INTO purchase_order_approvals (
                po_id, user_id, action, comments, created_at
            ) VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(
                'iiss',
                $id,
                $userId,
                $action,
                $comments
            );
            $stmt->execute();
            
            // Update PO status
            $sql = "UPDATE purchase_orders SET status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('si', $newStatus, $id);
            $stmt->execute();
            
            $this->db->commit();
            
            $_SESSION['flash_message'] = 'Purchase order ' . ($isRejected ? 'rejected' : 'approved') . ' successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            $this->db->rollback();
            
            $_SESSION['flash_message'] = 'Error processing approval: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
        
        $this->redirect('/purchase/view/' . $id);
    }
    
    /**
     * Generate purchase order PDF
     * 
     * @param int $id Purchase order ID
     */
    public function generatePdf($id) {
        // In a real implementation, this would generate a PDF
        // For now, just show a message
        $_SESSION['flash_message'] = 'PDF generation would be implemented here';
        $_SESSION['flash_type'] = 'info';
        $this->redirect('/purchase/view/' . $id);
    }
    
    /**
     * Generate next PO number
     * 
     * @return string Next PO number
     */
    private function generateNextPoNumber() {
        // Get current year and month
        $year = date('Y');
        $month = date('m');
        
        // Get last PO number for this year/month
        $sql = "SELECT po_number FROM purchase_orders 
                WHERE po_number LIKE 'PO-{$year}{$month}%' 
                ORDER BY id DESC LIMIT 1";
        
        $result = $this->db->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastPoNumber = $row['po_number'];
            
            // Extract sequence number and increment
            $sequence = intval(substr($lastPoNumber, -4));
            $sequence++;
        } else {
            // First PO for this year/month
            $sequence = 1;
        }
        
        // Format as PO-YYYYMM-####
        return "PO-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get suppliers for dropdown
     * 
     * @return array List of suppliers
     */
    private function getSuppliers() {
        $sql = "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name";
        $result = $this->db->query($sql);
        
        $suppliers = [];
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }
        
        return $suppliers;
    }
    
    /**
     * Get inventory items for dropdown
     * 
     * @return array List of inventory items
     */
    private function getInventoryItems() {
        $sql = "SELECT id, item_code, item_name, unit, current_price 
                FROM inventory_items 
                ORDER BY item_name";
        $result = $this->db->query($sql);
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Check if user has approval permission
     * 
     * @return bool Has approval permission
     */
    private function hasApprovalPermission() {
        $allowedRoles = ['manager', 'finance', 'director', 'admin'];
        return in_array($_SESSION['user_role'] ?? '', $allowedRoles);
    }
    
    /**
     * Check if user can approve this purchase order
     * 
     * @param int $userId User ID
     * @param string $poStatus Current PO status
     * @return bool Can approve
     */
    private function canUserApprove($userId, $poStatus) {
        $userRole = $_SESSION['user_role'] ?? '';
        
        if (!$this->hasApprovalPermission()) {
            return false;
        }
        
        // Check role against status
        if (($userRole === 'manager' && $poStatus === 'pending_approval') ||
            ($userRole === 'finance' && $poStatus === 'approved_by_manager') ||
            ($userRole === 'director' && $poStatus === 'approved_by_finance') ||
            ($userRole === 'admin')) {
            return true;
        }
        
        return false;
    }
}