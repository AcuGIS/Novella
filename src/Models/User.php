<?php

namespace Novella\Models;

class User {
    private $db;
    private $id;
    private $username;
    private $email;
    private $roles = [];
    private $isActive;
    private $lastLogin;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function create($db, $username, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?)
            RETURNING id
        ");
        
        $stmt->execute([$username, $email, $passwordHash]);
        return $stmt->fetchColumn();
    }

    public static function findByUsername($db, $username) {
        $stmt = $db->prepare("
            SELECT u.*, array_agg(r.name) as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.username = ?
            GROUP BY u.id
        ");
        
        $stmt->execute([$username]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$userData) {
            return null;
        }

        $user = new self($db);
        $user->id = $userData['id'];
        $user->username = $userData['username'];
        $user->email = $userData['email'];
        $user->isActive = $userData['is_active'];
        $user->lastLogin = $userData['last_login'];
        
        // Clean up the roles array - remove curly braces and quotes
        $roles = str_replace(['{', '}', '"'], '', $userData['roles']);
        $user->roles = array_filter(explode(',', $roles));
        
        error_log("User roles after cleanup: " . implode(', ', $user->roles));
        
        return $user;
    }

    public function authenticate($password) {
        error_log("Authenticating user ID: " . $this->id);
        
        $stmt = $this->db->prepare("
            SELECT password_hash, is_active
            FROM users
            WHERE id = ?
        ");
        
        $stmt->execute([$this->id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            error_log("No user found with ID: " . $this->id);
            return false;
        }
        
        error_log("User active status: " . ($result['is_active'] ? "Yes" : "No"));
        
        if (!$result['is_active']) {
            error_log("User account is not active");
            return false;
        }
        
        $storedHash = $result['password_hash'];
        error_log("Stored hash exists: " . (!empty($storedHash) ? "Yes" : "No"));
        
        $verifyResult = password_verify($password, $storedHash);
        error_log("Password verification result: " . ($verifyResult ? "Success" : "Failed"));
        
        if ($verifyResult) {
            $this->updateLastLogin();
            error_log("Last login updated");
            return true;
        }
        
        error_log("Authentication failed for user ID: " . $this->id);
        return false;
    }

    private function updateLastLogin() {
        $stmt = $this->db->prepare("
            UPDATE users
            SET last_login = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $stmt->execute([$this->id]);
    }

    public function hasRole($role) {
        return in_array($role, $this->roles);
    }

    public function hasPermission($permission) {
        // Admin has all permissions
        if ($this->hasRole('admin')) {
            return true;  // Admin has all permissions including manage_harvest
        }

        // Publisher permissions
        if ($this->hasRole('publisher')) {
            return in_array($permission, [
                'publish_dataset',
                'edit_dataset',
                'manage_topics',
                'manage_keywords',
                'manage_harvest'
            ]);
        }

        // Editor permissions
        if ($this->hasRole('editor')) {
            return in_array($permission, [
                'edit_dataset',
                'manage_topics',
                'manage_keywords'
                // Editors don't have manage_harvest permission
            ]);
        }

        return false;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRoles() { return $this->roles; }
    public function isActive() { return $this->isActive; }
    public function getLastLogin() { return $this->lastLogin; }
} 