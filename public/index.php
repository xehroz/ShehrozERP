<?php
/**
 * Shehroz ERP - Main Entry Point
 * 
 * A lightweight PHP-based ERP system focused on procurement with white-labeling support
 */

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Initialize application
require_once __DIR__ . '/../src/Config/bootstrap.php';

// Handle the request
$router = new App\Core\Router();
$router->dispatch();