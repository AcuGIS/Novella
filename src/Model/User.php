<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;

class User
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public array $roles = [];
    public ?int $group_id = null;
    public ?string $group_name = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $last_login_at = null;
    public ?string $name = null;
    public ?string $last_login = null;

    public function __construct(
        private Connection $db
    ) {}

    private function decodeRoles(array $user): array
    {
        if (isset($user['roles'])) {
            if (is_string($user['roles'])) {
                $user['roles'] = json_decode($user['roles'], true) ?? ['ROLE_USER'];
            }
        } else {
            $user['roles'] = ['ROLE_USER'];
        }
        return $user;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('
            SELECT u.*, g.name as group_name 
            FROM users u 
            LEFT JOIN user_groups g ON u.group_id = g.id 
            WHERE u.username = :username
        ');
        $stmt->bindValue('username', $username);
        $result = $stmt->executeQuery();
        
        $user = $result->fetchAssociative();
        return $user ? $this->decodeRoles($user) : null;
    }

    public function findById(int $id): ?self
    {
        $stmt = $this->db->prepare('
            SELECT u.*, g.name as group_name 
            FROM users u 
            LEFT JOIN user_groups g ON u.group_id = g.id 
            WHERE u.id = :id
        ');
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery();
        $userData = $result->fetchAssociative();
        if (!$userData) {
            return null;
        }
        $userData = $this->decodeRoles($userData);
        $user = new self($this->db);
        foreach ($userData as $key => $value) {
            $user->$key = $value;
        }
        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare('
            SELECT u.*, g.name as group_name 
            FROM users u 
            LEFT JOIN user_groups g ON u.group_id = g.id 
            ORDER BY u.username
        ');
        $result = $stmt->executeQuery();
        
        $users = $result->fetchAllAssociative();
        return array_map([$this, 'decodeRoles'], $users);
    }

    public function create(array $data): int
    {
        // Get group_id from group name if provided
        if (isset($data['group_name'])) {
            $stmt = $this->db->prepare('SELECT id FROM user_groups WHERE name = :name');
            $stmt->bindValue('name', $data['group_name']);
            $result = $stmt->executeQuery();
            $group = $result->fetchAssociative();
            if ($group) {
                $data['group_id'] = $group['id'];
            }
        }

        $this->db->insert('users', [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'roles' => json_encode($data['roles'] ?? ['ROLE_USER']),
            'name' => $data['name'] ?? $data['username'],
            'group_id' => $data['group_id'] ?? $this->getDefaultGroupId()
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $updateData = [];
        
        if (isset($data['username'])) {
            $updateData['username'] = $data['username'];
        }
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        if (!empty($data['password'])) {  // Only update password if a new one is provided
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['roles'])) {
            $updateData['roles'] = json_encode($data['roles']);
        }
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        if (isset($data['group_name'])) {
            $stmt = $this->db->prepare('SELECT id FROM user_groups WHERE name = :name');
            $stmt->bindValue('name', $data['group_name']);
            $result = $stmt->executeQuery();
            $group = $result->fetchAssociative();
            if ($group) {
                $updateData['group_id'] = $group['id'];
            }
        }

        if (!empty($updateData)) {
            $this->db->update('users', $updateData, ['id' => $id]);
        }
    }

    private function getDefaultGroupId(): int
    {
        $stmt = $this->db->prepare('SELECT id FROM user_groups WHERE name = :name');
        $stmt->bindValue('name', 'Editor');
        $result = $stmt->executeQuery();
        $group = $result->fetchAssociative();
        return $group ? (int)$group['id'] : 1; // Default to first group if Editor not found
    }

    public function getGroupName(): ?string
    {
        if (!$this->group_id) {
            return null;
        }
        
        $stmt = $this->db->prepare('SELECT name FROM user_groups WHERE id = :id');
        $stmt->bindValue('id', $this->group_id);
        $result = $stmt->executeQuery();
        $group = $result->fetchAssociative();
        return $group ? $group['name'] : null;
    }

    public function isAdmin(): bool
    {
        return $this->group_name === 'Admin';
    }

    public function isPublisher(): bool
    {
        return $this->group_name === 'Publisher';
    }

    public function isEditor(): bool
    {
        return $this->group_name === 'Editor';
    }

    public function delete(int $id): void
    {
        $this->db->delete('users', ['id' => $id]);
    }
} 