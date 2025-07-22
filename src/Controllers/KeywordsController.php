<?php

namespace Novella\Controllers;

use PDO;

class KeywordsController {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index() {
        $stmt = $this->db->query('SELECT * FROM keywords ORDER BY created_at DESC');
        $keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['keywords' => $keywords];
    }

    public function add($data) {
        $stmt = $this->db->prepare('
            INSERT INTO keywords (keyword, description)
            VALUES (:keyword, :description)
            RETURNING id
        ');

        $stmt->execute([
            'keyword' => $data['keyword'],
            'description' => $data['description'] ?? null
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function edit($data) {
        $stmt = $this->db->prepare('
            UPDATE keywords 
            SET keyword = :keyword, 
                description = :description
            WHERE id = :id
        ');

        return $stmt->execute([
            'id' => $data['id'],
            'keyword' => $data['keyword'],
            'description' => $data['description'] ?? null
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM keywords WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
} 