<?php
/**
 * Procurement ERP - Authentication Controller
 * 
 * Handles user authentication functionality
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class AuthController extends Controller {
    /**
     * Show login form
     */
    public function login() {
        // If user is already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        // Render login view
        $this->render('auth/login', [
            'pageTitle' => 'Login',
            'includeLayout' => false
        ], 'auth');
    }
    
    /**
     * Process login form submission
     */
    public function authenticate() {
        // If not a POST request, redirect to login form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/login');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate form
        if (empty($username) || empty($password)) {
            $_SESSION['flash_message'] = 'Please enter both username and password.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/auth/login');
        }
        
        // In a real application, we would verify against database
        // For demonstration, use hardcoded credentials (admin/admin)
        if ($username === 'admin' && $password === 'admin') {
            // Set session variables
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Administrator';
            $_SESSION['user_role'] = ROLE_ADMIN;
            
            // Redirect to intended page or dashboard
            $redirectUrl = $_SESSION['redirect_after_login'] ?? '/dashboard';
            unset($_SESSION['redirect_after_login']);
            
            $this->redirect($redirectUrl);
        } else {
            // Invalid credentials
            $_SESSION['flash_message'] = 'Invalid username or password.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/auth/login');
        }
    }
    
    /**
     * Process user logout
     */
    public function logout() {
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        $this->redirect('/auth/login');
    }
    
    /**
     * Show forgot password form
     */
    public function forgotPassword() {
        $this->render('auth/forgot-password', [
            'pageTitle' => 'Forgot Password',
            'includeLayout' => false
        ], 'auth');
    }
    
    /**
     * Process forgot password form
     */
    public function resetPassword() {
        // In a real application, this would send a password reset link
        
        $_SESSION['flash_message'] = 'If the email exists in our system, you will receive a password reset link shortly.';
        $_SESSION['flash_type'] = 'info';
        $this->redirect('/auth/login');
    }
}