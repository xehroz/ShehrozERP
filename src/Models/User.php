<?php
/**
 * Procurement ERP - User Model
 * 
 * Handles user data and authentication
 */

namespace App\Models;

use App\Core\Database;

class User {
    private Database $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get user by email
     * 
     * @param string $email User email
     * @return array|null User data or null if not found
     */
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Authenticate user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array|false User data or false if authentication failed
     */
    public function authenticate($email, $password) {
        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Remove password from user data before returning
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create password reset token
     * 
     * @param string $email User email
     * @return string|null Reset token or null if user not found
     */
    public function createPasswordResetToken($email) {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        $token = bin2hex(random_bytes(32));
        $expiryDate = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $query = "INSERT INTO password_resets (user_id, token, expiry_date) VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE token = ?, expiry_date = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('issss', $user['id'], $token, $expiryDate, $token, $expiryDate);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            return $token;
        }
        
        return null;
    }
    
    /**
     * Verify password reset token
     * 
     * @param string $token Reset token
     * @return int|false User ID or false if token invalid or expired
     */
    public function verifyPasswordResetToken($token) {
        $query = "SELECT user_id FROM password_resets WHERE token = ? AND expiry_date > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['user_id'];
        }
        
        return false;
    }
    
    /**
     * Reset user password
     * 
     * @param int $userId User ID
     * @param string $newPassword New password
     * @param string $token Reset token
     * @return bool Success status
     */
    public function resetPassword($userId, $newPassword, $token) {
        $this->db->beginTransaction();
        
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bind_param('si', $hashedPassword, $userId);
            $updateStmt->execute();
            
            if ($updateStmt->affected_rows <= 0) {
                $this->db->rollback();
                return false;
            }
            
            // Delete reset token
            $deleteQuery = "DELETE FROM password_resets WHERE user_id = ? AND token = ?";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bind_param('is', $userId, $token);
            $deleteStmt->execute();
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Get all active users
     * 
     * @param int $limit Limit results
     * @param int $offset Result offset
     * @return array List of users
     */
    public function getActiveUsers($limit = 10, $offset = 0) {
        $query = "SELECT id, name, email, role, created_at, last_login 
                  FROM users 
                  WHERE status = 'active' 
                  ORDER BY name ASC 
                  LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    /**
     * Count total active users
     * 
     * @return int Total count
     */
    public function countActiveUsers() {
        $query = "SELECT COUNT(*) AS total FROM users WHERE status = 'active'";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}