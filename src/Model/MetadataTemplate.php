<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;
use DateTime;

class MetadataTemplate
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
           ->from('metadata_templates')
           ->orderBy('name', 'ASC');
        
        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function getById(int $id): ?array
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
           ->from('metadata_templates')
           ->where('id = :id')
           ->setParameter('id', $id);
        
        $result = $qb->executeQuery()->fetchAssociative();
        return $result ?: null;
    }

    public function getFields(int $templateId): array
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
           ->from('metadata_template_fields')
           ->where('template_id = :template_id')
           ->setParameter('template_id', $templateId)
           ->orderBy('id', 'ASC');
        
        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function create(array $data): int
    {
        $qb = $this->db->createQueryBuilder();
        $qb->insert('metadata_templates')
           ->values([
               'name' => ':name',
               'description' => ':description',
               'metadata_standard' => ':metadata_standard',
               'metadata_version' => ':metadata_version',
               'template_xml' => ':template_xml',
               'is_default' => ':is_default',
               'created_at' => ':created_at',
               'updated_at' => ':updated_at'
           ])
           ->setParameters([
               'name' => $data['name'],
               'description' => $data['description'] ?? null,
               'metadata_standard' => $data['metadata_standard'],
               'metadata_version' => $data['metadata_version'],
               'template_xml' => $data['template_xml'],
               'is_default' => $data['is_default'] ?? false,
               'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
               'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
           ]);
        
        $qb->executeQuery();
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        // Debug logging
        error_log('MetadataTemplate::update - Input data: ' . print_r($data, true));
        
        // Ensure boolean values are properly converted and bound
        $isDefault = (bool)($data['is_default'] ?? false);  // Use consistent boolean conversion with controller
        error_log('MetadataTemplate::update - is_default value: ' . ($isDefault ? 'true' : 'false'));
        error_log('MetadataTemplate::update - is_default type: ' . gettype($isDefault));
        
        $qb = $this->db->createQueryBuilder();
        $qb->update('metadata_templates')
           ->set('name', ':name')
           ->set('description', ':description')
           ->set('metadata_standard', ':metadata_standard')
           ->set('metadata_version', ':metadata_version')
           ->set('template_xml', ':template_xml')
           ->set('is_default', ':is_default')
           ->set('updated_at', ':updated_at')
           ->where('id = :id')
           ->setParameters([
               'id' => $id,
               'name' => $data['name'],
               'description' => $data['description'] ?? null,
               'metadata_standard' => $data['metadata_standard'],
               'metadata_version' => $data['metadata_version'],
               'template_xml' => $data['template_xml'],
               'is_default' => $isDefault,
               'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
           ], [
               'is_default' => \PDO::PARAM_BOOL  // Explicitly bind as boolean
           ]);
        
        try {
            $result = $qb->executeQuery()->rowCount() > 0;
            error_log('MetadataTemplate::update - Update successful: ' . ($result ? 'true' : 'false'));
            return $result;
        } catch (\Exception $e) {
            error_log('MetadataTemplate::update - Error: ' . $e->getMessage());
            error_log('MetadataTemplate::update - SQL: ' . $qb->getSQL());
            error_log('MetadataTemplate::update - Parameters: ' . print_r($qb->getParameters(), true));
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $qb = $this->db->createQueryBuilder();
        $qb->delete('metadata_templates')
           ->where('id = :id')
           ->setParameter('id', $id);
        
        return $qb->executeQuery()->rowCount() > 0;
    }

    public function addField(int $templateId, array $fieldData): int
    {
        $qb = $this->db->createQueryBuilder();
        $qb->insert('metadata_template_fields')
           ->values([
               'template_id' => ':template_id',
               'field_name' => ':field_name',
               'field_path' => ':field_path',
               'field_type' => ':field_type',
               'is_required' => ':is_required',
               'default_value' => ':default_value',
               'description' => ':description',
               'validation_rules' => ':validation_rules',
               'created_at' => ':created_at',
               'updated_at' => ':updated_at'
           ])
           ->setParameters([
               'template_id' => $templateId,
               'field_name' => $fieldData['field_name'],
               'field_path' => $fieldData['field_path'],
               'field_type' => $fieldData['field_type'],
               'is_required' => $fieldData['is_required'] ?? false,
               'default_value' => $fieldData['default_value'] ?? null,
               'description' => $fieldData['description'] ?? null,
               'validation_rules' => $fieldData['validation_rules'] ?? null,
               'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
               'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
           ]);
        
        $qb->executeQuery();
        return (int) $this->db->lastInsertId();
    }

    public function updateField(int $fieldId, array $fieldData): bool
    {
        $qb = $this->db->createQueryBuilder();
        $qb->update('metadata_template_fields')
           ->set('field_name', ':field_name')
           ->set('field_path', ':field_path')
           ->set('field_type', ':field_type')
           ->set('is_required', ':is_required')
           ->set('default_value', ':default_value')
           ->set('description', ':description')
           ->set('validation_rules', ':validation_rules')
           ->set('updated_at', ':updated_at')
           ->where('id = :id')
           ->setParameters([
               'id' => $fieldId,
               'field_name' => $fieldData['field_name'],
               'field_path' => $fieldData['field_path'],
               'field_type' => $fieldData['field_type'],
               'is_required' => $fieldData['is_required'] ?? false,
               'default_value' => $fieldData['default_value'] ?? null,
               'description' => $fieldData['description'] ?? null,
               'validation_rules' => $fieldData['validation_rules'] ?? null,
               'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
           ]);
        
        return $qb->executeQuery()->rowCount() > 0;
    }

    public function deleteField(int $fieldId): bool
    {
        $qb = $this->db->createQueryBuilder();
        $qb->delete('metadata_template_fields')
           ->where('id = :id')
           ->setParameter('id', $fieldId);
        
        return $qb->executeQuery()->rowCount() > 0;
    }

    public function getDefaultTemplate(): ?array
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
           ->from('metadata_templates')
           ->where('is_default = true')
           ->setMaxResults(1);
        
        $result = $qb->executeQuery()->fetchAssociative();
        return $result ?: null;
    }

    public function setDefaultTemplate(int $id): bool
    {
        // First, unset any existing default
        $qb = $this->db->createQueryBuilder();
        $qb->update('metadata_templates')
           ->set('is_default', ':false')
           ->where('is_default = :true')
           ->setParameter('false', false)
           ->setParameter('true', true);
        $qb->executeQuery();

        // Then set the new default
        $qb = $this->db->createQueryBuilder();
        $qb->update('metadata_templates')
           ->set('is_default', ':true')
           ->where('id = :id')
           ->setParameter('true', true)
           ->setParameter('id', $id);
        
        return $qb->executeQuery()->rowCount() > 0;
    }

    public function createField(array $data): int
    {
        $qb = $this->db->createQueryBuilder();
        $qb->insert('metadata_template_fields')
           ->values([
               'template_id' => ':template_id',
               'field_name' => ':field_name',
               'field_path' => ':field_path',
               'field_type' => ':field_type',
               'is_required' => ':is_required',
               'description' => ':description',
               'created_at' => ':created_at',
               'updated_at' => ':updated_at'
           ])
           ->setParameters([
               'template_id' => $data['template_id'],
               'field_name' => $data['field_name'],
               'field_path' => $data['field_path'],
               'field_type' => $data['field_type'],
               'is_required' => $data['is_required'] ?? false,
               'description' => $data['description'] ?? null,
               'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
               'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
           ]);
        
        $qb->executeQuery();
        return (int) $this->db->lastInsertId();
    }
} 