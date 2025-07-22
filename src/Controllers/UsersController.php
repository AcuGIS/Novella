<?php

namespace Novella\Controllers;

use PDO;
use Exception;

class UsersController {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(): array {
        $stmt = $this->db->query("
            SELECT 
                u.*,
                array_agg(r.name) as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.username
        ");
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Clean up the roles array for each user
        foreach ($users as &$user) {
            $roles = str_replace(['{', '}', '"'], '', $user['roles']);
            $user['roles'] = array_filter(explode(',', $roles));
            unset($user['password_hash']); // Don't expose password hashes
        }
        
        return ['users' => $users];
    }

    public function getRoles(): array {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY name");
        return ['roles' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function create(array $data): int {
        // Validate required fields
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Username, email, and password are required');
        }

        // Check if username or email already exists
        $stmt = $this->db->prepare("
            SELECT id FROM users 
            WHERE username = ? OR email = ?
        ");
        $stmt->execute([$data['username'], $data['email']]);
        if ($stmt->fetch()) {
            throw new Exception('Username or email already exists');
        }

        // Start transaction
        $this->db->beginTransaction();

        try {
            // Create user
            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password_hash, is_active)
                VALUES (?, ?, ?, ?)
                RETURNING id
            ");
            
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            $isActive = isset($data['is_active']) ? (bool)$data['is_active'] : true;
            
            $stmt->execute([
                $data['username'],
                $data['email'],
                $passwordHash,
                $isActive
            ]);
            
            $userId = $stmt->fetchColumn();

            // Assign roles if provided
            if (!empty($data['roles']) && is_array($data['roles'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id)
                    SELECT ?, id FROM roles WHERE name = ?
                ");
                
                foreach ($data['roles'] as $role) {
                    $stmt->execute([$userId, $role]);
                }
            }

            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $userId, array $data): void {
        // Validate user exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            throw new Exception('User not found');
        }

        // Start transaction
        $this->db->beginTransaction();

        try {
            // Update user details
            $updates = [];
            $params = [];

            if (isset($data['email'])) {
                // Check if email is already used by another user
                $stmt = $this->db->prepare("
                    SELECT id FROM users 
                    WHERE email = ? AND id != ?
                ");
                $stmt->execute([$data['email'], $userId]);
                if ($stmt->fetch()) {
                    throw new Exception('Email already in use');
                }
                $updates[] = "email = ?";
                $params[] = $data['email'];
            }

            if (isset($data['password'])) {
                $updates[] = "password_hash = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (isset($data['is_active'])) {
                $updates[] = "is_active = ?";
                $params[] = (bool)$data['is_active'];
            }

            if (!empty($updates)) {
                $params[] = $userId;
                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET " . implode(", ", $updates) . "
                    WHERE id = ?
                ");
                $stmt->execute($params);
            }

            // Update roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                // Remove existing roles
                $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
                $stmt->execute([$userId]);

                // Add new roles
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id)
                    SELECT ?, id FROM roles WHERE name = ?
                ");
                
                foreach ($data['roles'] as $role) {
                    $stmt->execute([$userId, $role]);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $userId): void {
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            throw new Exception('User not found');
        }

        // Delete user (cascade will handle user_roles)
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public function getById(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                u.*,
                array_agg(r.name) as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        // Clean up the roles array
        $roles = str_replace(['{', '}', '"'], '', $user['roles']);
        $user['roles'] = array_filter(explode(',', $roles));
        unset($user['password_hash']); // Don't expose password hash
        
        return $user;
    }
} 