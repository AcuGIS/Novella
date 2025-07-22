<?php

namespace Novella\Auth;

use Novella\Models\User;

class Auth {
    private $db;
    private static $instance = null;
    private $user = null;

    private function __construct($db) {
        $this->db = $db;
        $this->initSession();
    }

    public static function getInstance($db = null) {
        if (self::$instance === null) {
            if ($db === null) {
                throw new \Exception('Database connection required for first Auth instance');
            }
            self::$instance = new self($db);
        }
        return self::$instance;
    }

    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->user = User::findByUsername($this->db, $_SESSION['username']);
        }
    }

    public function login($username, $password) {
        error_log("Attempting login for username: " . $username);
        
        $user = User::findByUsername($this->db, $username);
        error_log("User found: " . ($user ? "Yes" : "No"));
        
        if ($user) {
            error_log("User details - ID: " . $user->getId() . ", Username: " . $user->getUsername() . ", Roles: " . implode(', ', $user->getRoles()));
            $authResult = $user->authenticate($password);
            error_log("Authentication result: " . ($authResult ? "Success" : "Failed"));
            
            if ($authResult) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $this->user = $user;
                error_log("Login successful - Session started");
                return true;
            }
        }
        
        error_log("Login failed for username: " . $username);
        return false;
    }

    public function logout() {
        session_destroy();
        $this->user = null;
    }

    public function isLoggedIn() {
        return $this->user !== null;
    }

    public function getCurrentUser() {
        return $this->user;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /login.php');
            exit;
        }
    }

    public function requireRole($role) {
        $this->requireLogin();
        
        if (!$this->user->hasRole($role)) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Access denied: Insufficient permissions';
            exit;
        }
    }

    public function requirePermission($permission) {
        $this->requireLogin();
        
        if (!$this->user->hasPermission($permission)) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Access denied: Insufficient permissions';
            exit;
        }
    }
} 