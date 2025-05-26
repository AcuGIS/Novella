<?php

namespace GeoLibre\Model;

class Topic extends AbstractModel
{
    protected string $table = 'topics';
    
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by'
    ];

    public int $id;
    public string $name;
    public string $description;
    public int $created_by;
    public int $updated_by;
    public string $created_at;
    public string $updated_at;

    public function with($relation)
    {
        return $this;
    }

    public function get()
    {
        return $this->findAll();
    }

    public function all()
    {
        return $this->findAll();
    }

    public function findOrFail($id)
    {
        $result = $this->find($id);
        if (!$result) {
            throw new \Exception("Topic not found");
        }
        return $result;
    }

    public function datasets()
    {
        return $this->belongsToMany(Dataset::class, 'dataset_topics', 'topic_id', 'dataset_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function find(int $id): ?array
    {
        return parent::find($id);
    }

    public function getDb()
    {
        return $this->db;
    }

    public static function createWithDb($db, $id)
    {
        $topic = new self($db);
        $topic->id = $id;
        return $topic;
    }

    protected function belongsToMany($related, $table, $foreignKey, $relatedKey)
    {
        return new class($this->db, $table, $foreignKey, $relatedKey, $related, $this->id) {
            private $db;
            private $table;
            private $foreignKey;
            private $relatedKey;
            private $related;
            private $id;

            public function __construct($db, $table, $foreignKey, $relatedKey, $related, $id)
            {
                $this->db = $db;
                $this->table = $table;
                $this->foreignKey = $foreignKey;
                $this->relatedKey = $relatedKey;
                $this->related = $related;
                $this->id = $id;
            }

            public function sync($ids)
            {
                // First, delete all existing relationships
                $this->db->delete($this->table, [$this->foreignKey => $this->id]);

                // Then, insert the new relationships
                foreach ($ids as $id) {
                    $this->db->insert($this->table, [
                        $this->foreignKey => $this->id,
                        $this->relatedKey => $id
                    ]);
                }
            }

            public function detach()
            {
                $this->db->delete($this->table, [$this->foreignKey => $this->id]);
            }

            public function getIds()
            {
                $qb = $this->db->createQueryBuilder();
                $result = $qb->select($this->relatedKey)
                    ->from($this->table)
                    ->where($this->foreignKey . ' = :id')
                    ->setParameter('id', $this->id)
                    ->executeQuery()
                    ->fetchAllAssociative();
                
                return array_column($result, $this->relatedKey);
            }
        };
    }

    protected function belongsTo($related, $foreignKey)
    {
        return new class($this->db, $related, $foreignKey) {
            private $db;
            private $related;
            private $foreignKey;

            public function __construct($db, $related, $foreignKey)
            {
                $this->db = $db;
                $this->related = $related;
                $this->foreignKey = $foreignKey;
            }
        };
    }

    public function update(int $id, array $data): bool
    {
        if ($id === $this->id) {
            // Update the instance properties
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
        return parent::update($id, $data);
    }
} 