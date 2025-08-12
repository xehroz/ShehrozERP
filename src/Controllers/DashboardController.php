<?php
/**
 * Procurement ERP - Dashboard Controller
 * 
 * Handles dashboard functionality
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller {
    /**
     * Constructor - requires login for all dashboard actions
     */
    public function __construct() {
        $this->requireLogin();
    }
    
    /**
     * Dashboard index action
     */
    public function index() {
        // Get summary data for the dashboard
        $pendingPOs = $this->getPendingPurchaseOrders();
        $recentSuppliers = $this->getRecentSuppliers();
        $upcomingDeliveries = $this->getUpcomingDeliveries();
        $lowStockItems = $this->getLowStockItems();
        
        // Render the dashboard view
        $this->render('dashboard/index', [
            'pendingPOs' => $pendingPOs,
            'recentSuppliers' => $recentSuppliers,
            'upcomingDeliveries' => $upcomingDeliveries,
            'lowStockItems' => $lowStockItems,
            'pageTitle' => 'Procurement Dashboard',
            'pageDescription' => 'Overview of procurement activities'
        ]);
    }
    
    /**
     * Get pending purchase orders
     */
    private function getPendingPurchaseOrders() {
        // In a real application, this would query the database
        // For now, return sample data
        return [
            [
                'id' => 'PO-001',
                'supplier' => 'ABC Supplies',
                'status' => PO_STATUS_PENDING_APPROVAL,
                'amount' => 1250.00,
                'date' => date('Y-m-d', strtotime('-2 days'))
            ],
            [
                'id' => 'PO-002',
                'supplier' => 'XYZ Corporation',
                'status' => PO_STATUS_PENDING_APPROVAL,
                'amount' => 3750.00,
                'date' => date('Y-m-d', strtotime('-1 day'))
            ]
        ];
    }
    
    /**
     * Get recent suppliers
     */
    private function getRecentSuppliers() {
        // In a real application, this would query the database
        // For now, return sample data
        return [
            [
                'id' => 1,
                'name' => 'ABC Supplies',
                'contact' => 'John Smith',
                'email' => 'john@abcsupplies.com',
                'lastOrder' => date('Y-m-d', strtotime('-5 days'))
            ],
            [
                'id' => 2,
                'name' => 'XYZ Corporation',
                'contact' => 'Jane Doe',
                'email' => 'jane@xyzcorp.com',
                'lastOrder' => date('Y-m-d', strtotime('-7 days'))
            ],
            [
                'id' => 3,
                'name' => 'Office Depot',
                'contact' => 'Mike Johnson',
                'email' => 'mike@officedepot.com',
                'lastOrder' => date('Y-m-d', strtotime('-10 days'))
            ]
        ];
    }
    
    /**
     * Get upcoming deliveries
     */
    private function getUpcomingDeliveries() {
        // In a real application, this would query the database
        // For now, return sample data
        return [
            [
                'id' => 'PO-003',
                'supplier' => 'Office Depot',
                'expectedDate' => date('Y-m-d', strtotime('+3 days')),
                'items' => 5,
                'status' => PO_STATUS_SENT
            ],
            [
                'id' => 'PO-004',
                'supplier' => 'Tech Solutions',
                'expectedDate' => date('Y-m-d', strtotime('+5 days')),
                'items' => 2,
                'status' => PO_STATUS_SENT
            ]
        ];
    }
    
    /**
     * Get low stock items
     */
    private function getLowStockItems() {
        // In a real application, this would query the database
        // For now, return sample data
        return [
            [
                'id' => 'ITEM-001',
                'name' => 'Paper A4',
                'currentStock' => 50,
                'minStock' => 100,
                'supplier' => 'Office Depot'
            ],
            [
                'id' => 'ITEM-002',
                'name' => 'Ink Cartridge Black',
                'currentStock' => 5,
                'minStock' => 10,
                'supplier' => 'Tech Solutions'
            ],
            [
                'id' => 'ITEM-003',
                'name' => 'Stapler',
                'currentStock' => 3,
                'minStock' => 5,
                'supplier' => 'Office Depot'
            ]
        ];
    }
}