<?php
/**
 * Procurement ERP - Base Controller Class
 * 
 * Provides common controller functionality
 */

namespace App\Core;

class Controller {
    /**
     * Constructor
     */
    public function __construct() {
        // Ensure user is authenticated for all controllers except Auth
        $this->checkAuthentication();
    }
    
    /**
     * Check if user is authenticated
     */
    protected function checkAuthentication() {
        // Skip auth check for AuthController
        $currentController = get_class($this);
        if ($currentController === 'App\Controllers\AuthController') {
            return;
        }
        
        // Skip auth check for error pages
        if ($currentController === 'App\Controllers\ErrorController') {
            return;
        }
        
        // Redirect to login if not authenticated
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Render a view with data
     * 
     * @param string $view View name (folder/file without extension)
     * @param array $data Data to pass to the view
     */
    protected function render($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: $viewPath");
        }
        
        require $viewPath;
        
        // Get the content of the buffer
        $viewContent = ob_get_clean();
        
        // Include the layout if not a partial view
        if (isset($data['isPartial']) && $data['isPartial']) {
            echo $viewContent;
        } else {
            $layout = isset($data['layout']) ? $data['layout'] : 'default';
            $layoutPath = dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
            
            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout file not found: $layoutPath");
            }
            
            require $layoutPath;
        }
    }
    
    /**
     * Redirect to a URL
     * 
     * @param string $url URL to redirect to
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Return JSON response
     * 
     * @param array $data Data to return
     * @param int $status HTTP status code
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Show 404 not found page
     * 
     * @param string $message Custom message
     */
    protected function notFound($message = 'Page not found') {
        $this->render('error/404', [
            'pageTitle' => '404 Not Found',
            'message' => $message
        ]);
        exit;
    }
    
    /**
     * Show 403 forbidden page
     * 
     * @param string $message Custom message
     */
    protected function forbidden($message = 'Access denied') {
        $this->render('error/403', [
            'pageTitle' => '403 Forbidden',
            'message' => $message
        ]);
        exit;
    }
    
    /**
     * Show 500 error page
     * 
     * @param string $message Custom message
     */
    protected function serverError($message = 'An error occurred') {
        $this->render('error/500', [
            'pageTitle' => '500 Server Error',
            'message' => $message
        ]);
        exit;
    }
    
    /**
     * Get status badge CSS class
     * 
     * @param string $status Status code
     * @return string CSS class
     */
    protected function getStatusBadgeClass($status) {
        $statusClasses = [
            'draft' => 'secondary',
            'pending_approval' => 'warning',
            'approved_by_manager' => 'info',
            'approved_by_finance' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'canceled' => 'danger',
            'completed' => 'primary',
            'paid' => 'success',
            'partial' => 'info',
        ];
        
        return $statusClasses[$status] ?? 'secondary';
    }
    
    /**
     * Get human-readable status label
     * 
     * @param string $status Status code
     * @return string Human-readable status
     */
    protected function getStatusLabel($status) {
        $statusLabels = [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'approved_by_manager' => 'Manager Approved',
            'approved_by_finance' => 'Finance Approved',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'canceled' => 'Canceled',
            'completed' => 'Completed',
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
        ];
        
        return $statusLabels[$status] ?? ucfirst($status);
    }
    
    /**
     * Calculate time elapsed since a given date
     * 
     * @param string $datetime Date and time string
     * @return string Human-readable time elapsed
     */
    protected function timeElapsed($datetime) {
        $now = new \DateTime();
        $past = new \DateTime($datetime);
        $diff = $now->diff($past);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->d > 0) {
            if ($diff->d >= 7) {
                $weeks = floor($diff->d / 7);
                return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
            }
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
        
        return 'Just now';
    }
}