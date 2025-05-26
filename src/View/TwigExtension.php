<?php

declare(strict_types=1);

namespace GeoLibre\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use SimpleXMLElement;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('extract_xml_value', [$this, 'extractXmlValue']),
            new TwigFilter('extract_xml_values', [$this, 'extractXmlValues']),
            new TwigFilter('xml_parse', [$this, 'parseXml']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('user', function() {
                return $_SESSION['user'] ?? null;
            }),
            new TwigFunction('dump', function($var) {
                return '<pre>' . htmlspecialchars(print_r($var, true)) . '</pre>';
            }),
        ];
    }

    public function extractXmlValue(string $xml, string $xpath): string
    {
        try {
            $doc = new SimpleXMLElement($xml);
            $doc->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $doc->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
            $doc->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
            
            $result = $doc->xpath($xpath);
            return $result ? (string)$result[0] : '';
        } catch (\Exception $e) {
            error_log("Error extracting XML value: " . $e->getMessage());
            return '';
        }
    }

    public function extractXmlValues(string $xml, string $xpath): array
    {
        try {
            $doc = new SimpleXMLElement($xml);
            $doc->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $doc->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
            $doc->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
            
            $results = $doc->xpath($xpath);
            return array_map('strval', $results);
        } catch (\Exception $e) {
            error_log("Error extracting XML values: " . $e->getMessage());
            return [];
        }
    }

    public function parseXml(string $xml): ?\SimpleXMLElement
    {
        try {
            $doc = new \SimpleXMLElement($xml);
            $doc->registerXPathNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $doc->registerXPathNamespace('gco', 'http://www.isotc211.org/2005/gco');
            $doc->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
            return $doc;
        } catch (\Exception $e) {
            error_log("Error parsing XML: " . $e->getMessage());
            return null;
        }
    }
} 