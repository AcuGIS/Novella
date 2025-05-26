<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use GeoLibre\Model\OaiPmh;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DateTime;
use SimpleXMLElement;

class OaiPmhController
{
    private OaiPmh $oaiPmh;
    private string $repositoryName;
    private string $baseUrl;
    private string $adminEmail;

    public function __construct(OaiPmh $oaiPmh, array $config)
    {
        $this->oaiPmh = $oaiPmh;
        $this->repositoryName = $config['repository_name'] ?? 'GeoLibre Repository';
        $this->baseUrl = $config['base_url'] ?? 'http://localhost/oai';
        $this->adminEmail = $config['admin_email'] ?? 'admin@localhost';
    }

    public function handle(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $verb = $params['verb'] ?? '';

        try {
            switch ($verb) {
                case 'Identify':
                    return $this->handleIdentify($response);
                case 'ListSets':
                    return $this->handleListSets($response);
                case 'ListMetadataFormats':
                    return $this->handleListMetadataFormats($response);
                case 'ListIdentifiers':
                    return $this->handleListIdentifiers($request, $response);
                case 'ListRecords':
                    return $this->handleListRecords($request, $response);
                case 'GetRecord':
                    return $this->handleGetRecord($request, $response);
                default:
                    return $this->errorResponse($response, 'badVerb', 'Illegal OAI verb');
            }
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'badArgument', $e->getMessage());
        }
    }

    private function handleIdentify(Response $response): Response
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><OAI-PMH></OAI-PMH>');
        $xml->addAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
        $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->addAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');

        $responseDate = $xml->addChild('responseDate', gmdate('Y-m-d\TH:i:s\Z'));
        $request = $xml->addChild('request', $this->baseUrl);
        $request->addAttribute('verb', 'Identify');

        $identify = $xml->addChild('Identify');
        $identify->addChild('repositoryName', $this->repositoryName);
        $identify->addChild('baseURL', $this->baseUrl);
        $identify->addChild('protocolVersion', '2.0');
        $identify->addChild('adminEmail', $this->adminEmail);
        $identify->addChild('earliestDatestamp', '2000-01-01T00:00:00Z');
        $identify->addChild('deletedRecord', 'transient');
        $identify->addChild('granularity', 'YYYY-MM-DDThh:mm:ssZ');

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function handleListSets(Response $response): Response
    {
        $xml = $this->createBaseXml('ListSets');
        $listSets = $xml->addChild('ListSets');

        $sets = $this->oaiPmh->getSets();
        foreach ($sets as $set) {
            $setElement = $listSets->addChild('set');
            $setElement->addChild('setSpec', $set['set_spec']);
            $setElement->addChild('setName', $set['set_name']);
            if (!empty($set['set_description'])) {
                $setElement->addChild('setDescription', $set['set_description']);
            }
        }

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function handleListMetadataFormats(Response $response): Response
    {
        $xml = $this->createBaseXml('ListMetadataFormats');
        $listMetadataFormats = $xml->addChild('ListMetadataFormats');

        $format = $listMetadataFormats->addChild('metadataFormat');
        $format->addChild('metadataPrefix', 'iso19115');
        $format->addChild('schema', 'http://www.isotc211.org/2005/gmd/gmd.xsd');
        $format->addChild('metadataNamespace', 'http://www.isotc211.org/2005/gmd');

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function handleListIdentifiers(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $from = $this->parseDate($params['from'] ?? null);
        $until = $this->parseDate($params['until'] ?? null);
        $set = $params['set'] ?? null;

        $records = $this->oaiPmh->getRecordsByDateRange($from, $until, $set);

        $xml = $this->createBaseXml('ListIdentifiers');
        $listIdentifiers = $xml->addChild('ListIdentifiers');

        foreach ($records as $record) {
            $header = $listIdentifiers->addChild('header');
            $header->addChild('identifier', $record['oai_identifier']);
            $header->addChild('datestamp', $record['datestamp']);
            if ($record['deleted']) {
                $header->addAttribute('status', 'deleted');
            }
        }

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function handleListRecords(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $from = $this->parseDate($params['from'] ?? null);
        $until = $this->parseDate($params['until'] ?? null);
        $set = $params['set'] ?? null;

        $records = $this->oaiPmh->getRecordsByDateRange($from, $until, $set);

        $xml = $this->createBaseXml('ListRecords');
        $listRecords = $xml->addChild('ListRecords');

        foreach ($records as $record) {
            $recordElement = $listRecords->addChild('record');
            
            $header = $recordElement->addChild('header');
            $header->addChild('identifier', $record['oai_identifier']);
            $header->addChild('datestamp', $record['datestamp']);
            if ($record['deleted']) {
                $header->addAttribute('status', 'deleted');
            }

            if (!$record['deleted'] && !empty($record['metadata_xml'])) {
                $metadata = $recordElement->addChild('metadata');
                $metadataXml = new SimpleXMLElement($record['metadata_xml']);
                $this->appendXml($metadata, $metadataXml);
            }
        }

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function handleGetRecord(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $identifier = $params['identifier'] ?? '';

        if (empty($identifier)) {
            return $this->errorResponse($response, 'badArgument', 'Missing required argument: identifier');
        }

        $record = $this->oaiPmh->getRecordByIdentifier($identifier);
        if (!$record) {
            return $this->errorResponse($response, 'idDoesNotExist', 'The value of the identifier argument is unknown or illegal in this repository');
        }

        $xml = $this->createBaseXml('GetRecord');
        $getRecord = $xml->addChild('GetRecord');
        
        $recordElement = $getRecord->addChild('record');
        $header = $recordElement->addChild('header');
        $header->addChild('identifier', $record['oai_identifier']);
        $header->addChild('datestamp', $record['datestamp']);
        if ($record['deleted']) {
            $header->addAttribute('status', 'deleted');
        }

        if (!$record['deleted'] && !empty($record['metadata_xml'])) {
            $metadata = $recordElement->addChild('metadata');
            $metadataXml = new SimpleXMLElement($record['metadata_xml']);
            $this->appendXml($metadata, $metadataXml);
        }

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function createBaseXml(string $verb): SimpleXMLElement
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><OAI-PMH></OAI-PMH>');
        $xml->addAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
        $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->addAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');

        $responseDate = $xml->addChild('responseDate', gmdate('Y-m-d\TH:i:s\Z'));
        $request = $xml->addChild('request', $this->baseUrl);
        $request->addAttribute('verb', $verb);

        return $xml;
    }

    private function errorResponse(Response $response, string $code, string $message): Response
    {
        $xml = $this->createBaseXml('');
        $error = $xml->addChild('error', $message);
        $error->addAttribute('code', $code);

        $response->getBody()->write($xml->asXML());
        return $response->withHeader('Content-Type', 'application/xml');
    }

    private function parseDate(?string $date): DateTime
    {
        if (empty($date)) {
            return new DateTime('2000-01-01');
        }
        return new DateTime($date);
    }

    private function appendXml(SimpleXMLElement $to, SimpleXMLElement $from): void
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
} 