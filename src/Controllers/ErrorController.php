<?php
/**
 * Procurement ERP - Error Controller
 * 
 * Handles error pages and error display
 */

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller {
    /**
     * Show 404 Not Found page
     */
    public function notFound($message = 'Page not found') {
        http_response_code(404);
        
        $this->render('error/404', [
            'pageTitle' => '404 Not Found',
            'message' => $message
        ]);
    }
    
    /**
     * Show 403 Forbidden page
     */
    public function forbidden() {
        http_response_code(403);
        
        $this->render('error/403', [
            'pageTitle' => '403 Forbidden',
            'message' => 'You do not have permission to access this resource.'
        ]);
    }
    
    /**
     * Show 500 Internal Server Error page
     */
    public function serverError($message = 'An internal server error occurred.') {
        http_response_code(500);
        
        $this->render('error/500', [
            'pageTitle' => '500 Internal Server Error',
            'message' => $message
        ]);
    }
}