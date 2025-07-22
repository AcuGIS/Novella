<?php

namespace Novella\Controllers;

use PDO;

class TopicsController {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index() {
        $stmt = $this->db->query('SELECT * FROM topics ORDER BY created_at DESC');
        $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['topics' => $topics];
    }

    public function add($data) {
        $stmt = $this->db->prepare('
            INSERT INTO topics (topic, description)
            VALUES (:topic, :description)
            RETURNING id
        ');

        $stmt->execute([
            'topic' => $data['topic'],
            'description' => $data['description'] ?? null
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function edit($data) {
        $stmt = $this->db->prepare('
            UPDATE topics 
            SET topic = :topic, 
                description = :description
            WHERE id = :id
        ');

        return $stmt->execute([
            'id' => $data['id'],
            'topic' => $data['topic'],
            'description' => $data['description'] ?? null
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM topics WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
} 