<?php
/**
 * Procurement ERP - Application Bootstrap
 * 
 * This file initializes the application, sets up error handling,
 * database connections, and loads core components.
 */

// Set error reporting based on environment
if ($_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
}

// Start the session
session_start();

// Load configuration files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/constants.php';

// Set default timezone
date_default_timezone_set('UTC');

// Register error handler
set_error_handler('App\Helpers\ErrorHandler::handleError');
set_exception_handler('App\Helpers\ErrorHandler::handleException');

// Initialize the database connection
App\Core\Database::initialize();

// Load the white label configuration
App\Core\WhiteLabel::load();