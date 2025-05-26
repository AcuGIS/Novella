<?php

namespace GeoLibre\Service;

use Doctrine\DBAL\Connection;

class OaiPmhService
{
    private Connection $db;
    private string $repositoryName;
    private string $baseUrl;
    private string $adminEmail;
    private array $metadataFormats = [
        'oai_dc' => [
            'prefix' => 'oai_dc',
            'schema' => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
            'namespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/'
        ],
        'iso19115' => [
            'prefix' => 'iso19115',
            'schema' => 'http://www.isotc211.org/2005/gmd/gmd.xsd',
            'namespace' => 'http://www.isotc211.org/2005/gmd'
        ]
    ];

    public function __construct(
        Connection $db,
        string $repositoryName,
        string $baseUrl,
        string $adminEmail
    ) {
        $this->db = $db;
        $this->repositoryName = $repositoryName;
        $this->baseUrl = $baseUrl;
        $this->adminEmail = $adminEmail;
    }

    public function identify(): array
    {
        return [
            'repositoryName' => $this->repositoryName,
            'baseURL' => $this->baseUrl,
            'protocolVersion' => '2.0',
            'adminEmail' => $this->adminEmail,
            'earliestDatestamp' => $this->getEarliestDatestamp(),
            'deletedRecord' => 'no',
            'granularity' => 'YYYY-MM-DDThh:mm:ssZ',
            'compression' => ['gzip', 'deflate']
        ];
    }

    public function listMetadataFormats(?string $identifier = null): array
    {
        if ($identifier !== null) {
            // Check if identifier exists
            $sql = "SELECT id FROM metadata WHERE identifier = ?";
            $result = $this->db->executeQuery($sql, [$identifier])->fetchAssociative();
            if (!$result) {
                throw new \Exception('idDoesNotExist');
            }
        }

        return [
            'metadataFormat' => array_values($this->metadataFormats)
        ];
    }

    public function listSets(?string $resumptionToken = null): array
    {
        // For now, return empty sets as we haven't implemented set functionality
        return [
            'set' => []
        ];
    }

    public function listIdentifiers(
        ?string $metadataPrefix,
        ?string $from = null,
        ?string $until = null,
        ?string $set = null,
        ?string $resumptionToken = null
    ): array {
        if (!isset($metadataPrefix) || !isset($this->metadataFormats[$metadataPrefix])) {
            throw new \Exception('cannotDisseminateFormat');
        }

        $sql = "SELECT identifier, datestamp FROM metadata WHERE 1=1";
        $params = [];

        if ($from) {
            $sql .= " AND datestamp >= ?";
            $params[] = $from;
        }
        if ($until) {
            $sql .= " AND datestamp <= ?";
            $params[] = $until;
        }

        $sql .= " ORDER BY datestamp ASC LIMIT 100";

        $records = $this->db->executeQuery($sql, $params)->fetchAllAssociative();

        return [
            'header' => array_map(function($record) {
                return [
                    'identifier' => $record['identifier'],
                    'datestamp' => $record['datestamp']
                ];
            }, $records)
        ];
    }

    public function listRecords(
        ?string $metadataPrefix,
        ?string $from = null,
        ?string $until = null,
        ?string $set = null,
        ?string $resumptionToken = null
    ): array {
        if (!isset($metadataPrefix) || !isset($this->metadataFormats[$metadataPrefix])) {
            throw new \Exception('cannotDisseminateFormat');
        }

        $sql = "SELECT m.*, md.content as metadata 
                FROM metadata m 
                LEFT JOIN metadata_content md ON m.id = md.metadata_id 
                WHERE 1=1";
        $params = [];

        if ($from) {
            $sql .= " AND m.datestamp >= ?";
            $params[] = $from;
        }
        if ($until) {
            $sql .= " AND m.datestamp <= ?";
            $params[] = $until;
        }

        $sql .= " ORDER BY m.datestamp ASC LIMIT 100";

        $records = $this->db->executeQuery($sql, $params)->fetchAllAssociative();

        return [
            'record' => array_map(function($record) use ($metadataPrefix) {
                return [
                    'header' => [
                        'identifier' => $record['identifier'],
                        'datestamp' => $record['datestamp']
                    ],
                    'metadata' => [
                        $metadataPrefix => $record['metadata']
                    ]
                ];
            }, $records)
        ];
    }

    public function getRecord(string $identifier, string $metadataPrefix): array
    {
        if (!isset($this->metadataFormats[$metadataPrefix])) {
            throw new \Exception('cannotDisseminateFormat');
        }

        $sql = "SELECT m.*, md.content as metadata 
                FROM metadata m 
                LEFT JOIN metadata_content md ON m.id = md.metadata_id 
                WHERE m.identifier = ?";
        
        $record = $this->db->executeQuery($sql, [$identifier])->fetchAssociative();
        
        if (!$record) {
            throw new \Exception('idDoesNotExist');
        }

        return [
            'record' => [
                'header' => [
                    'identifier' => $record['identifier'],
                    'datestamp' => $record['datestamp']
                ],
                'metadata' => [
                    $metadataPrefix => $record['metadata']
                ]
            ]
        ];
    }

    private function getEarliestDatestamp(): string
    {
        $sql = "SELECT MIN(created_at) as earliest FROM metadata";
        $result = $this->db->executeQuery($sql)->fetchAssociative();
        
        return $result['earliest'] ?? date('Y-m-d\TH:i:s\Z');
    }
} 