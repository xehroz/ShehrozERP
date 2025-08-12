<?php
/**
 * Procurement ERP - Error Handler Class
 * 
 * Handles application errors and exceptions
 */

namespace App\Helpers;

class ErrorHandler {
    /**
     * Handle PHP errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return false;
        }
        
        $message = "ERROR [{$errno}] {$errstr} in {$errfile} on line {$errline}";
        self::logError($message);
        
        if ($_ENV['APP_DEBUG'] === 'true') {
            echo "<h2>Error occurred:</h2>";
            echo "<p>{$message}</p>";
        } else {
            // In production, show a user-friendly error
            self::showErrorPage("An error occurred. Please try again later.");
        }
        
        // Don't execute PHP's internal error handler
        return true;
    }
    
    /**
     * Handle exceptions
     */
    public static function handleException($exception) {
        $message = "EXCEPTION: " . $exception->getMessage() . 
                 " in " . $exception->getFile() . 
                 " on line " . $exception->getLine() . 
                 "\nStack trace: " . $exception->getTraceAsString();
        
        self::logError($message);
        
        if ($_ENV['APP_DEBUG'] === 'true') {
            echo "<h2>Exception occurred:</h2>";
            echo "<p>" . nl2br(htmlspecialchars($message)) . "</p>";
        } else {
            // In production, show a user-friendly error
            self::showErrorPage("An unexpected error occurred. Please try again later.");
        }
    }
    
    /**
     * Log error to file
     */
    private static function logError($message) {
        $logDir = BASE_PATH . '/logs';
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/error_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        
        file_put_contents(
            $logFile,
            "[{$timestamp}] {$message}\n",
            FILE_APPEND
        );
    }
    
    /**
     * Show error page
     */
    private static function showErrorPage($message) {
        http_response_code(500);
        
        // Check if we have an error view
        $errorView = VIEW_PATH . '/error/500.php';
        
        if (file_exists($errorView)) {
            // Pass error message to the view
            $errorMessage = $message;
            require $errorView;
        } else {
            // Fallback if error view doesn't exist
            echo "<html><head><title>Error</title>";
            echo "<style>body{font-family:Arial,sans-serif;margin:40px;line-height:1.6;}";
            echo ".error-container{max-width:800px;margin:0 auto;padding:20px;border:1px solid #e74c3c;border-radius:5px;}";
            echo "h1{color:#e74c3c;}</style></head>";
            echo "<body><div class='error-container'>";
            echo "<h1>System Error</h1>";
            echo "<p>{$message}</p>";
            echo "<p>Please contact the system administrator for assistance.</p>";
            echo "</div></body></html>";
        }
    }
}