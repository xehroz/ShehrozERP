<?php
/**
 * Procurement ERP - Application Constants
 */

// Application paths
define('BASE_PATH', realpath(__DIR__ . '/../../'));
define('SRC_PATH', BASE_PATH . '/src');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEW_PATH', SRC_PATH . '/Views');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Default controller and action
define('DEFAULT_CONTROLLER', 'Dashboard');
define('DEFAULT_ACTION', 'index');

// Procurement system constants
define('PO_STATUS_DRAFT', 0);
define('PO_STATUS_PENDING_APPROVAL', 1);
define('PO_STATUS_APPROVED', 2);
define('PO_STATUS_REJECTED', 3);
define('PO_STATUS_SENT', 4);
define('PO_STATUS_PARTIALLY_RECEIVED', 5);
define('PO_STATUS_FULLY_RECEIVED', 6);
define('PO_STATUS_CLOSED', 7);
define('PO_STATUS_CANCELLED', 8);

// User roles
define('ROLE_ADMIN', 1);
define('ROLE_PROCUREMENT_MANAGER', 2);
define('ROLE_PROCUREMENT_OFFICER', 3);
define('ROLE_APPROVER', 4);
define('ROLE_FINANCE', 5);
define('ROLE_WAREHOUSE', 6);
define('ROLE_VIEWER', 7);