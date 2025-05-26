<?php

declare(strict_types=1);

namespace GeoLibre\Model;

use Doctrine\DBAL\Connection;
use DOMDocument;
use DOMXPath;

class Metadata extends AbstractModel
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
        $this->table = 'metadata';
    }

    public function findByDatasetId(int $datasetId): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('*')
            ->from($this->table)
            ->where('dataset_id = :dataset_id')
            ->setParameter('dataset_id', $datasetId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    public function createMetadata(array $data): int
    {
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Ensure metadata_xml is properly formatted
        if (isset($data['metadata_xml'])) {
            if (is_array($data['metadata_xml'])) {
                $data['metadata_xml'] = $this->arrayToXml($data['metadata_xml']);
            }
        }

        return $this->create($data);
    }

    public function updateMetadata(int $datasetId, array $data): bool
    {
        error_log("=== Metadata Update Debug ===");
        error_log("Updating metadata for dataset ID: " . $datasetId);
        error_log("Update data: " . print_r($data, true));

        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Handle metadata_xml update if provided
        if (isset($data['metadata_xml'])) {
            if (is_array($data['metadata_xml'])) {
                $data['metadata_xml'] = $this->arrayToXml($data['metadata_xml']);
            }
        }

        // Get the metadata record for this dataset
        $qb = $this->createQueryBuilder();
        $result = $qb->select('id')
            ->from($this->table)
            ->where('dataset_id = :dataset_id')
            ->setParameter('dataset_id', $datasetId)
            ->executeQuery()
            ->fetchAssociative();

        error_log("Found metadata record: " . print_r($result, true));

        if (!$result) {
            error_log("No metadata record found for dataset_id: " . $datasetId);
            // Create new metadata record if it doesn't exist
            $data['dataset_id'] = $datasetId;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->create($data) > 0;
        }

        // Update existing record using the metadata record's ID
        $qb = $this->createQueryBuilder();
        $qb->update($this->table)
            ->set('metadata_xml', ':metadata_xml')
            ->set('quality_score', ':quality_score')
            ->set('updated_at', ':updated_at')
            ->set('metadata_standard', ':metadata_standard')
            ->set('metadata_version', ':metadata_version')
            ->where('id = :id')
            ->setParameters([
                'id' => $result['id'],
                'metadata_xml' => $data['metadata_xml'],
                'quality_score' => $data['quality_score'],
                'updated_at' => $data['updated_at'],
                'metadata_standard' => $data['metadata_standard'] ?? 'ISO 19115',
                'metadata_version' => $data['metadata_version'] ?? '2018'
            ]);

        error_log("Executing update query...");
        $result = $qb->executeQuery();
        $success = $result->rowCount() > 0;
        error_log("Update result: " . ($success ? "Success" : "Failed"));
        error_log("=== End Metadata Update Debug ===");

        return $success;
    }

    public function validateMetadata(string $xml): bool
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NONET | LIBXML_DTDLOAD | LIBXML_DTDVALID);
        
        // TODO: Add proper ISO 19115 schema validation
        // For now, we just check if it's well-formed XML
        return true;
    }

    public function searchByKeywords(array $keywords): array
    {
        $qb = $this->createQueryBuilder();
        $qb->select('m.*', 'd.title', 'd.description')
           ->from($this->table, 'm')
           ->join('m', 'datasets', 'd', 'm.dataset_id = d.id')
           ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
           ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
           ->where('h.id IS NULL OR (r.deleted = false AND h.id IS NOT NULL)');

        $conditions = [];
        foreach ($keywords as $index => $keyword) {
            $conditions[] = "m.metadata_xml::text ILIKE :keyword$index";
            $qb->setParameter("keyword$index", '%' . $keyword . '%');
        }

        if (!empty($conditions)) {
            $qb->andWhere(implode(' OR ', $conditions));
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function extractKeywords(string $xml): array
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $keywords = [];
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
        
        $keywordNodes = $xpath->query('//gmd:keyword');
        foreach ($keywordNodes as $node) {
            $keywords[] = trim($node->textContent);
        }
        
        return $keywords;
    }

    public function extractContacts(string $xml): array
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $contacts = [];
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
        
        $contactNodes = $xpath->query('//gmd:CI_ResponsibleParty');
        foreach ($contactNodes as $node) {
            $contact = [
                'role' => $xpath->evaluate('string(gmd:role/gmd:CI_RoleCode)', $node),
                'organization' => $xpath->evaluate('string(gmd:organisationName/gco:CharacterString)', $node),
                'individual_name' => $xpath->evaluate('string(gmd:individualName/gco:CharacterString)', $node),
                'position_name' => $xpath->evaluate('string(gmd:positionName/gco:CharacterString)', $node),
                'email' => $xpath->evaluate('string(gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString)', $node)
            ];
            $contacts[] = array_filter($contact);
        }
        
        return $contacts;
    }

    public function extractSpatialExtent(string $xml): ?array
    {
        try {
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $xpath->registerNamespace('gco', 'http://www.isotc211.org/2005/gco');
            
            $extent = $xpath->query('//gmd:EX_GeographicBoundingBox');
            if ($extent->length > 0) {
                $node = $extent->item(0);
                return [
                    'west' => (float)$xpath->evaluate('string(gmd:westBoundLongitude/gco:Decimal)', $node),
                    'east' => (float)$xpath->evaluate('string(gmd:eastBoundLongitude/gco:Decimal)', $node),
                    'south' => (float)$xpath->evaluate('string(gmd:southBoundLatitude/gco:Decimal)', $node),
                    'north' => (float)$xpath->evaluate('string(gmd:northBoundLatitude/gco:Decimal)', $node)
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Error extracting spatial extent: " . $e->getMessage());
            return null;
        }
    }

    public function extractTemporalExtent(string $xml): ?array
    {
        try {
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $xpath->registerNamespace('gco', 'http://www.isotc211.org/2005/gco');
            
            $temporalExtent = $xpath->query('//gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod');
            if ($temporalExtent->length > 0) {
                $node = $temporalExtent->item(0);
                return [
                    'start' => $xpath->evaluate('string(gml:beginPosition)', $node),
                    'end' => $xpath->evaluate('string(gml:endPosition)', $node)
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Error extracting temporal extent: " . $e->getMessage());
            return null;
        }
    }

    public function validateRequiredFields(string $xml): array
    {
        $requiredFields = [
            'title' => '//gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString',
            'abstract' => '//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString',
            'spatial_extent' => '//gmd:EX_GeographicBoundingBox',
            'temporal_extent' => '//gmd:temporalElement/gmd:EX_TemporalExtent',
            'keywords' => '//gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword',
            'contact' => '//gmd:contact/gmd:CI_ResponsibleParty'
        ];

        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
        $xpath->registerNamespace('gco', 'http://www.isotc211.org/2005/gco');
        
        $missingFields = [];
        foreach ($requiredFields as $field => $xpathQuery) {
            $result = $xpath->query($xpathQuery);
            if ($result->length === 0) {
                $missingFields[] = $field;
            }
        }
        
        return $missingFields;
    }

    public function calculateQualityScore(string $xml): float
    {
        error_log("=== Calculating Quality Score ===");
        
        // Validate XML input
        if (empty(trim($xml))) {
            error_log("Error: Empty XML string provided");
            return 0.0;
        }

        $score = 0.0;
        $maxScore = 100.0;
        $weights = [
            'title' => 10,
            'abstract' => 15,
            'spatial_extent' => 20,
            'temporal_extent' => 15,
            'keywords' => 10,
            'contact' => 15,
            'distribution_info' => 15
        ];

        error_log("Input XML: " . substr($xml, 0, 500) . "...");

        try {
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $xpath->registerNamespace('gco', 'http://www.isotc211.org/2005/gco');
            $xpath->registerNamespace('gml', 'http://www.opengis.net/gml/3.2');

            // Check title
            $title = $xpath->query('//gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString');
            if ($title->length > 0) {
                $titleText = trim($title->item(0)->textContent);
                if (!empty($titleText)) {
                    $score += $weights['title'];
                    error_log("Title found: " . $titleText . " (+" . $weights['title'] . " points)");
                } else {
                    error_log("Empty title found");
                }
            } else {
                error_log("No title found");
            }

            // Check abstract
            $abstract = $xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString');
            if ($abstract->length > 0) {
                $abstractText = trim($abstract->item(0)->textContent);
                if (!empty($abstractText)) {
                    $score += $weights['abstract'];
                    error_log("Abstract found: " . substr($abstractText, 0, 100) . "... (+" . $weights['abstract'] . " points)");
                } else {
                    error_log("Empty abstract found");
                }
            } else {
                error_log("No abstract found");
            }

            // Check spatial extent
            $spatialExtent = $xpath->query('//gmd:EX_GeographicBoundingBox');
            if ($spatialExtent->length > 0) {
                // Check if it's a default global extent
                $west = $xpath->evaluate('string(gmd:westBoundLongitude/gco:Decimal)', $spatialExtent->item(0));
                $east = $xpath->evaluate('string(gmd:eastBoundLongitude/gco:Decimal)', $spatialExtent->item(0));
                $south = $xpath->evaluate('string(gmd:southBoundLatitude/gco:Decimal)', $spatialExtent->item(0));
                $north = $xpath->evaluate('string(gmd:northBoundLatitude/gco:Decimal)', $spatialExtent->item(0));
                
                if ($west == '-180' && $east == '180' && $south == '-90' && $north == '90') {
                    // Default global extent - award partial points
                    $score += $weights['spatial_extent'] * 0.5;
                    error_log("Default global spatial extent found (+" . ($weights['spatial_extent'] * 0.5) . " points)");
                } else {
                    // Real spatial extent - award full points
                    $score += $weights['spatial_extent'];
                    error_log("Spatial extent found (+" . $weights['spatial_extent'] . " points)");
                }
            } else {
                error_log("No spatial extent found");
            }

            // Check temporal extent
            $temporalExtent = $xpath->query('//gmd:temporalElement/gmd:EX_TemporalExtent');
            if ($temporalExtent->length > 0) {
                // Check if it's a single-day temporal extent (likely default)
                $beginPosition = $xpath->evaluate('string(gmd:extent/gml:TimePeriod/gml:beginPosition)', $temporalExtent->item(0));
                $endPosition = $xpath->evaluate('string(gmd:extent/gml:TimePeriod/gml:endPosition)', $temporalExtent->item(0));
                
                if ($beginPosition === $endPosition) {
                    // Single-day temporal extent - award partial points
                    $score += $weights['temporal_extent'] * 0.5;
                    error_log("Default single-day temporal extent found (+" . ($weights['temporal_extent'] * 0.5) . " points)");
                } else {
                    // Real temporal extent - award full points
                    $score += $weights['temporal_extent'];
                    error_log("Temporal extent found (+" . $weights['temporal_extent'] . " points)");
                }
            } else {
                error_log("No temporal extent found");
            }

            // Check keywords
            $keywords = $xpath->query('//gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword/gco:CharacterString');
            if ($keywords->length > 0) {
                // Check if they are default WMS keywords
                $defaultKeywords = ['WMS', 'Web Map Service', 'OGC', 'GIS'];
                $isDefaultKeywords = true;
                foreach ($keywords as $keyword) {
                    if (!in_array(trim($keyword->textContent), $defaultKeywords)) {
                        $isDefaultKeywords = false;
                        break;
                    }
                }
                
                if ($isDefaultKeywords) {
                    // Default keywords - award partial points
                    $score += $weights['keywords'] * 0.5;
                    error_log("Default WMS keywords found (+" . ($weights['keywords'] * 0.5) . " points)");
                } else {
                    // Real keywords - award full points
                    $score += $weights['keywords'];
                    error_log("Keywords found: " . $keywords->length . " keywords (+" . $weights['keywords'] . " points)");
                }
            } else {
                error_log("No keywords found");
            }

            // Check contact
            $contact = $xpath->query('//gmd:contact/gmd:CI_ResponsibleParty');
            if ($contact->length > 0) {
                // Check if it's the default GeoLibre contact
                $orgName = $xpath->evaluate('string(gmd:organisationName/gco:CharacterString)', $contact->item(0));
                if ($orgName === 'GeoLibre') {
                    // Default contact - award partial points
                    $score += $weights['contact'] * 0.5;
                    error_log("Default GeoLibre contact found (+" . ($weights['contact'] * 0.5) . " points)");
                } else {
                    // Real contact - award full points
                    $score += $weights['contact'];
                    error_log("Contact found (+" . $weights['contact'] . " points)");
                }
            } else {
                error_log("No contact found");
            }

            // Check distribution info
            $distribution = $xpath->query('//gmd:distributionInfo/gmd:MD_Distribution');
            if ($distribution->length > 0) {
                // Check if it's a WMS distribution
                $protocol = $xpath->evaluate('string(gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:protocol/gco:CharacterString)', $distribution->item(0));
                if ($protocol === 'OGC:WMS') {
                    // WMS distribution - award full points
                    $score += $weights['distribution_info'];
                    error_log("WMS distribution info found (+" . $weights['distribution_info'] . " points)");
                } else {
                    // Other distribution - award partial points
                    $score += $weights['distribution_info'] * 0.5;
                    error_log("Non-WMS distribution info found (+" . ($weights['distribution_info'] * 0.5) . " points)");
                }
            } else {
                error_log("No distribution info found");
            }

            $finalScore = ($score / $maxScore) * 100;
            error_log("Final quality score: " . $finalScore . "% (Raw score: " . $score . "/" . $maxScore . ")");
            error_log("=== End Calculating Quality Score ===");
            return $finalScore;
        } catch (\Exception $e) {
            error_log("Error calculating quality score: " . $e->getMessage());
            return 0.0;
        }
    }

    private function arrayToXml(array $data): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><MD_Metadata></MD_Metadata>');
        $this->arrayToXmlRecursive($data, $xml);
        return $xml->asXML();
    }

    private function arrayToXmlRecursive(array $data, \SimpleXMLElement &$xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml->addChild($key);
                $this->arrayToXmlRecursive($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars((string) $value));
            }
        }
    }

    public function findAllPublic(int $page = 1, int $limit = 10, ?string $search = null, ?string $metadataStandard = null, ?string $harvestSource = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $offset = ($page - 1) * $limit;
        
        // First, get the distinct metadata IDs that match our criteria
        $subQb = $this->createQueryBuilder();
        $subQb->select('DISTINCT m.id')
            ->from('metadata', 'm')
            ->leftJoin('m', 'datasets', 'd', 'm.dataset_id = d.id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->where('d.is_public = true')
            ->andWhere('d.status = \'published\'');

        // Add search condition if provided
        if ($search) {
            $subQb->andWhere('m.metadata_xml ILIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Add metadata standard filter if provided
        if ($metadataStandard) {
            $subQb->andWhere('m.metadata_standard = :metadata_standard')
                ->setParameter('metadata_standard', $metadataStandard);
        }

        // Add harvest source filter if provided
        if ($harvestSource) {
            $subQb->andWhere('h.name = :harvest_source')
                ->setParameter('harvest_source', $harvestSource);
        }

        // Add date range filters if provided
        if ($dateFrom) {
            $subQb->andWhere('m.created_at >= :date_from')
                ->setParameter('date_from', $dateFrom);
        }
        if ($dateTo) {
            $subQb->andWhere('m.created_at <= :date_to')
                ->setParameter('date_to', $dateTo);
        }

        // Get total count
        $countQb = clone $subQb;
        $countQb->select('COUNT(DISTINCT m.id)');
        $total = (int) $countQb->executeQuery()->fetchOne();

        // Now get the full metadata details for the matching IDs
        $qb = $this->createQueryBuilder();
        $qb->select('m.*', 'd.title as dataset_title', 'd.description as dataset_description', 'h.name as harvest_source_name')
            ->from('metadata', 'm')
            ->leftJoin('m', 'datasets', 'd', 'm.dataset_id = d.id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->where('m.id IN (' . $subQb->getSQL() . ')')
            ->orderBy('m.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        // Copy parameters from subquery
        foreach ($subQb->getParameters() as $key => $value) {
            $qb->setParameter($key, $value);
        }

        // Get paginated results
        $result = $qb->executeQuery()->fetchAllAssociative();

        return [
            'items' => $result,
            'total' => $total
        ];
    }

    public function findPublicById(int $id): ?array
    {
        $qb = $this->createQueryBuilder();
        $result = $qb->select('m.*', 'd.title as dataset_title', 'd.description as dataset_description', 'h.name as harvest_source_name')
            ->from('metadata', 'm')
            ->leftJoin('m', 'datasets', 'd', 'm.dataset_id = d.id')
            ->leftJoin('d', 'oai_records', 'r', 'd.id = r.dataset_id')
            ->leftJoin('r', 'harvest_sources', 'h', 'r.harvest_source_id = h.id')
            ->where('m.id = :id')
            ->andWhere('d.is_public = true')
            ->andWhere('d.status = \'published\'')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }
} 