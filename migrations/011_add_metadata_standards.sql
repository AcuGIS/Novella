-- Add new metadata standards
INSERT INTO metadata_templates (name, description, metadata_standard, metadata_version, template_xml, is_default)
VALUES (
    'Default INSPIRE',
    'Default template for INSPIRE metadata',
    'INSPIRE',
    '1.3',
    '<?xml version="1.0" encoding="UTF-8"?>
<gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd"
                 xmlns:gco="http://www.isotc211.org/2005/gco"
                 xmlns:gml="http://www.opengis.net/gml/3.2"
                 xmlns:xlink="http://www.w3.org/1999/xlink"
                 xmlns:inspire="http://inspire.ec.europa.eu/schemas/inspire_dls/1.0">
    <gmd:fileIdentifier>
        <gco:CharacterString>{{fileIdentifier}}</gco:CharacterString>
    </gmd:fileIdentifier>
    
    <gmd:language>
        <gmd:LanguageCode codeList="http://www.loc.gov/standards/iso639-2/" codeListValue="{{language}}">{{language}}</gmd:LanguageCode>
    </gmd:language>
    
    <gmd:characterSet>
        <gmd:MD_CharacterSetCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_CharacterSetCode" codeListValue="utf8">utf8</gmd:MD_CharacterSetCode>
    </gmd:characterSet>
    
    <gmd:hierarchyLevel>
        <gmd:MD_ScopeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_ScopeCode" codeListValue="{{resourceType}}">{{resourceType}}</gmd:MD_ScopeCode>
    </gmd:hierarchyLevel>
    
    <gmd:contact>
        <gmd:CI_ResponsibleParty>
            <gmd:individualName>
                <gco:CharacterString>{{pointOfContactName}}</gco:CharacterString>
            </gmd:individualName>
            <gmd:organisationName>
                <gco:CharacterString>{{pointOfContactOrg}}</gco:CharacterString>
            </gmd:organisationName>
            <gmd:positionName>
                <gco:CharacterString>{{pointOfContactRole}}</gco:CharacterString>
            </gmd:positionName>
            <gmd:contactInfo>
                <gmd:CI_Contact>
                    <gmd:address>
                        <gmd:CI_Address>
                            <gmd:electronicMailAddress>
                                <gco:CharacterString>{{pointOfContactEmail}}</gco:CharacterString>
                            </gmd:electronicMailAddress>
                        </gmd:CI_Address>
                    </gmd:address>
                </gmd:CI_Contact>
            </gmd:contactInfo>
            <gmd:role>
                <gmd:CI_RoleCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_RoleCode" codeListValue="pointOfContact">pointOfContact</gmd:CI_RoleCode>
            </gmd:role>
        </gmd:CI_ResponsibleParty>
    </gmd:contact>
    
    <gmd:dateStamp>
        <gco:DateTime>{{dateStamp}}</gco:DateTime>
    </gmd:dateStamp>
    
    <gmd:metadataStandardName>
        <gco:CharacterString>ISO 19115</gco:CharacterString>
    </gmd:metadataStandardName>
    
    <gmd:metadataStandardVersion>
        <gco:CharacterString>2018</gco:CharacterString>
    </gmd:metadataStandardVersion>
    
    <gmd:identificationInfo>
        <gmd:MD_DataIdentification>
            <gmd:citation>
                <gmd:CI_Citation>
                    <gmd:title>
                        <gco:CharacterString>{{title}}</gco:CharacterString>
                    </gmd:title>
                    <gmd:date>
                        <gmd:CI_Date>
                            <gmd:date>
                                <gco:Date>{{publicationDate}}</gco:Date>
                            </gmd:date>
                            <gmd:dateType>
                                <gmd:CI_DateTypeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_DateTypeCode" codeListValue="publication">publication</gmd:CI_DateTypeCode>
                            </gmd:dateType>
                        </gmd:CI_Date>
                    </gmd:date>
                </gmd:CI_Citation>
            </gmd:citation>
            
            <gmd:abstract>
                <gco:CharacterString>{{abstract}}</gco:CharacterString>
            </gmd:abstract>
            
            <gmd:purpose>
                <gco:CharacterString>{{purpose}}</gco:CharacterString>
            </gmd:purpose>
            
            <gmd:descriptiveKeywords>
                <gmd:MD_Keywords>
                    {% for keyword in keywords %}
                    <gmd:keyword>
                        <gco:CharacterString>{{keyword}}</gco:CharacterString>
                    </gmd:keyword>
                    {% endfor %}
                </gmd:MD_Keywords>
            </gmd:descriptiveKeywords>
            
            <gmd:resourceConstraints>
                <gmd:MD_LegalConstraints>
                    <gmd:useConstraints>
                        <gmd:MD_RestrictionCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_RestrictionCode" codeListValue="otherRestrictions">otherRestrictions</gmd:MD_RestrictionCode>
                    </gmd:useConstraints>
                    <gmd:otherConstraints>
                        <gco:CharacterString>{{useConstraints}}</gco:CharacterString>
                    </gmd:otherConstraints>
                </gmd:MD_LegalConstraints>
            </gmd:resourceConstraints>
            
            <gmd:extent>
                <gmd:EX_Extent>
                    <gmd:geographicElement>
                        <gmd:EX_GeographicBoundingBox>
                            <gmd:westBoundLongitude>
                                <gco:Decimal>{{westBoundLongitude}}</gco:Decimal>
                            </gmd:westBoundLongitude>
                            <gmd:eastBoundLongitude>
                                <gco:Decimal>{{eastBoundLongitude}}</gco:Decimal>
                            </gmd:eastBoundLongitude>
                            <gmd:southBoundLatitude>
                                <gco:Decimal>{{southBoundLatitude}}</gco:Decimal>
                            </gmd:southBoundLatitude>
                            <gmd:northBoundLatitude>
                                <gco:Decimal>{{northBoundLatitude}}</gco:Decimal>
                            </gmd:northBoundLatitude>
                        </gmd:EX_GeographicBoundingBox>
                    </gmd:geographicElement>
                </gmd:EX_Extent>
            </gmd:extent>
        </gmd:MD_DataIdentification>
    </gmd:identificationInfo>
    
    <inspire:resourceLocator>
        <inspire:URL>{{resourceUrl}}</inspire:URL>
    </inspire:resourceLocator>
    
    <inspire:resourceType>
        <inspire:ResourceType>{{inspireResourceType}}</inspire:ResourceType>
    </inspire:resourceType>
    
    <inspire:conformity>
        <inspire:Conformity>
            <inspire:specification>
                <inspire:Specification>
                    <inspire:title>{{inspireSpecificationTitle}}</inspire:title>
                    <inspire:date>
                        <inspire:Date>
                            <inspire:date>{{inspireSpecificationDate}}</inspire:date>
                            <inspire:dateType>{{inspireSpecificationDateType}}</inspire:dateType>
                        </inspire:Date>
                    </inspire:date>
                </inspire:Specification>
            </inspire:specification>
            <inspire:degree>{{inspireConformityDegree}}</inspire:degree>
        </inspire:Conformity>
    </inspire:conformity>
</gmd:MD_Metadata>',
    true
);

-- Insert template fields for INSPIRE
INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (2, 'File Identifier', 'fileIdentifier', 'string', true, 'Unique identifier for the metadata record'),
    (2, 'Title', 'title', 'string', true, 'Name of the dataset or service'),
    (2, 'Abstract', 'abstract', 'text', true, 'Brief description of the dataset'),
    (2, 'Purpose', 'purpose', 'text', true, 'Reason why the data was created'),
    (2, 'Resource Type', 'resourceType', 'string', true, 'Type of resource (dataset, series, service)'),
    (2, 'Language', 'language', 'string', true, 'Language of the metadata content'),
    (2, 'Keywords', 'keywords', 'array', false, 'Tags or themes describing the dataset'),
    (2, 'Resource URL', 'resourceUrl', 'string', true, 'URL to access the resource'),
    (2, 'INSPIRE Resource Type', 'inspireResourceType', 'string', true, 'INSPIRE resource type'),
    (2, 'INSPIRE Specification Title', 'inspireSpecificationTitle', 'string', true, 'Title of the INSPIRE specification'),
    (2, 'INSPIRE Specification Date', 'inspireSpecificationDate', 'date', true, 'Date of the INSPIRE specification'),
    (2, 'INSPIRE Specification Date Type', 'inspireSpecificationDateType', 'string', true, 'Type of the INSPIRE specification date'),
    (2, 'INSPIRE Conformity Degree', 'inspireConformityDegree', 'string', true, 'Degree of conformity to INSPIRE');

-- Add FGDC template
INSERT INTO metadata_templates (name, description, metadata_standard, metadata_version, template_xml, is_default)
VALUES (
    'Default FGDC',
    'Default template for FGDC metadata',
    'FGDC',
    '1998',
    '<?xml version="1.0" encoding="UTF-8"?>
<metadata>
    <idinfo>
        <citation>
            <citeinfo>
                <origin>{{origin}}</origin>
                <pubdate>{{pubdate}}</pubdate>
                <title>{{title}}</title>
                <geoform>{{geoform}}</geoform>
                <pubinfo>
                    <pubplace>{{pubplace}}</pubplace>
                    <publish>{{publish}}</publish>
                </pubinfo>
            </citeinfo>
        </citation>
        <descript>
            <abstract>{{abstract}}</abstract>
            <purpose>{{purpose}}</purpose>
            <supplinf>{{supplinf}}</supplinf>
        </descript>
        <timeperd>
            <timeinfo>
                <rngdates>
                    <begdate>{{begdate}}</begdate>
                    <enddate>{{enddate}}</enddate>
                </rngdates>
            </timeinfo>
            <current>{{current}}</current>
        </timeperd>
        <status>
            <progress>{{progress}}</progress>
            <update>{{update}}</update>
        </status>
        <spdom>
            <bounding>
                <westbc>{{westbc}}</westbc>
                <eastbc>{{eastbc}}</eastbc>
                <northbc>{{northbc}}</northbc>
                <southbc>{{southbc}}</southbc>
            </bounding>
        </spdom>
        <keywords>
            <theme>
                <themekt>{{themekt}}</themekt>
                <themekey>{{themekey}}</themekey>
            </theme>
            <place>
                <placekt>{{placekt}}</placekt>
                <placekey>{{placekey}}</placekey>
            </place>
        </keywords>
        <accconst>{{accconst}}</accconst>
        <useconst>{{useconst}}</useconst>
        <ptcontac>
            <cntinfo>
                <cntperp>
                    <cntper>{{cntper}}</cntper>
                    <cntorg>{{cntorg}}</cntorg>
                </cntperp>
                <cntaddr>
                    <addrtype>{{addrtype}}</addrtype>
                    <address>{{address}}</address>
                    <city>{{city}}</city>
                    <state>{{state}}</state>
                    <postal>{{postal}}</postal>
                    <country>{{country}}</country>
                </cntaddr>
                <cntvoice>{{cntvoice}}</cntvoice>
                <cntemail>{{cntemail}}</cntemail>
            </cntinfo>
        </ptcontac>
    </idinfo>
    <dataqual>
        <attracc>
            <attraccr>{{attraccr}}</attraccr>
        </attracc>
        <logic>{{logic}}</logic>
        <complete>{{complete}}</complete>
        <posacc>
            <horizpa>
                <horizpar>{{horizpar}}</horizpar>
                <qhorizpa>
                    <horizpav>{{horizpav}}</horizpav>
                    <horizpae>{{horizpae}}</horizpae>
                </qhorizpa>
            </horizpa>
        </posacc>
        <lineage>
            <procstep>
                <procdesc>{{procdesc}}</procdesc>
                <procdate>{{procdate}}</procdate>
            </procstep>
        </lineage>
    </dataqual>
    <spdoinfo>
        <indspref>{{indspref}}</indspref>
        <direct>{{direct}}</direct>
        <ptvctinf>
            <sdtsterm>
                <sdtstype>{{sdtstype}}</sdtstype>
                <ptvctcnt>{{ptvctcnt}}</ptvctcnt>
            </sdtsterm>
        </ptvctinf>
    </spdoinfo>
    <spref>
        <horizsys>
            <geograph>
                <latres>{{latres}}</latres>
                <longres>{{longres}}</longres>
                <geogunit>{{geogunit}}</geogunit>
            </geograph>
            <geodetic>
                <horizdn>{{horizdn}}</horizdn>
                <ellips>{{ellips}}</ellips>
                <semiaxis>{{semiaxis}}</semiaxis>
                <denflat>{{denflat}}</denflat>
            </geodetic>
        </horizsys>
    </spref>
    <distinfo>
        <distrib>
            <cntinfo>
                <cntperp>
                    <cntper>{{distcntper}}</cntper>
                    <cntorg>{{distcntorg}}</cntorg>
                </cntperp>
                <cntaddr>
                    <addrtype>{{distaddrtype}}</addrtype>
                    <address>{{distaddress}}</address>
                    <city>{{distcity}}</city>
                    <state>{{diststate}}</state>
                    <postal>{{distpostal}}</postal>
                    <country>{{distcountry}}</country>
                </cntaddr>
                <cntvoice>{{distcntvoice}}</cntvoice>
                <cntemail>{{distcntemail}}</cntemail>
            </cntinfo>
        </distrib>
        <resdesc>{{resdesc}}</resdesc>
        <distliab>{{distliab}}</distliab>
        <stdorder>
            <digform>
                <digtinfo>
                    <formname>{{formname}}</formname>
                    <formvern>{{formvern}}</formvern>
                    <filedec>{{filedec}}</filedec>
                    <transize>{{transize}}</transize>
                </digtinfo>
                <digtopt>
                    <onlinopt>
                        <computer>
                            <networka>
                                <networkr>{{networkr}}</networkr>
                            </networka>
                        </computer>
                    </onlinopt>
                </digtopt>
            </digform>
        </stdorder>
    </distinfo>
</metadata>',
    true
);

-- Insert template fields for FGDC
INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (3, 'Origin', 'origin', 'string', true, 'Originator of the dataset'),
    (3, 'Publication Date', 'pubdate', 'date', true, 'Date of publication'),
    (3, 'Title', 'title', 'string', true, 'Name of the dataset'),
    (3, 'Geographic Form', 'geoform', 'string', true, 'Form of the geographic data'),
    (3, 'Publication Place', 'pubplace', 'string', true, 'Place of publication'),
    (3, 'Publisher', 'publish', 'string', true, 'Publisher of the dataset'),
    (3, 'Abstract', 'abstract', 'text', true, 'Brief description of the dataset'),
    (3, 'Purpose', 'purpose', 'text', true, 'Reason why the data was created'),
    (3, 'Supplemental Information', 'supplinf', 'text', false, 'Additional information about the dataset'),
    (3, 'Begin Date', 'begdate', 'date', true, 'Start date of the dataset'),
    (3, 'End Date', 'enddate', 'date', true, 'End date of the dataset'),
    (3, 'Currentness Reference', 'current', 'string', true, 'Reference to currentness of the data'),
    (3, 'Progress', 'progress', 'string', true, 'Status of the dataset'),
    (3, 'Update Frequency', 'update', 'string', true, 'Frequency of updates'),
    (3, 'West Bounding Coordinate', 'westbc', 'decimal', true, 'Westernmost coordinate'),
    (3, 'East Bounding Coordinate', 'eastbc', 'decimal', true, 'Easternmost coordinate'),
    (3, 'North Bounding Coordinate', 'northbc', 'decimal', true, 'Northernmost coordinate'),
    (3, 'South Bounding Coordinate', 'southbc', 'decimal', true, 'Southernmost coordinate');

-- Add OGC CSW template
INSERT INTO metadata_templates (name, description, metadata_standard, metadata_version, template_xml, is_default)
VALUES (
    'Default OGC CSW',
    'Default template for OGC CSW metadata',
    'OGC CSW',
    '2.0.2',
    '<?xml version="1.0" encoding="UTF-8"?>
<csw:Record xmlns:csw="http://www.opengis.net/cat/csw/2.0.2"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:dct="http://purl.org/dc/terms/"
            xmlns:ows="http://www.opengis.net/ows"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:identifier>{{identifier}}</dc:identifier>
    <dc:title>{{title}}</dc:title>
    <dc:type>{{type}}</dc:type>
    <dc:subject>{{subject}}</dc:subject>
    <dc:format>{{format}}</dc:format>
    <dc:date>{{date}}</dc:date>
    <dc:language>{{language}}</dc:language>
    <dc:rights>{{rights}}</dc:rights>
    <dc:source>{{source}}</dc:source>
    <dc:creator>{{creator}}</dc:creator>
    <dc:publisher>{{publisher}}</dc:publisher>
    <dc:contributor>{{contributor}}</dc:contributor>
    <dc:description>{{description}}</dc:description>
    <dct:abstract>{{abstract}}</dct:abstract>
    <dct:created>{{created}}</dct:created>
    <dct:modified>{{modified}}</dct:modified>
    <dct:issued>{{issued}}</dct:issued>
    <dct:spatial>
        <ows:BoundingBox>
            <ows:LowerCorner>{{lowerCorner}}</ows:LowerCorner>
            <ows:UpperCorner>{{upperCorner}}</ows:UpperCorner>
        </ows:BoundingBox>
    </dct:spatial>
    <dct:temporal>
        <dct:PeriodOfTime>
            <dct:start>{{temporalStart}}</dct:start>
            <dct:end>{{temporalEnd}}</dct:end>
        </dct:PeriodOfTime>
    </dct:temporal>
    <dct:references>
        <dct:URI>{{uri}}</dct:URI>
    </dct:references>
</csw:Record>',
    true
);

-- Insert template fields for OGC CSW
INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (4, 'Identifier', 'identifier', 'string', true, 'Unique identifier for the record'),
    (4, 'Title', 'title', 'string', true, 'Title of the resource'),
    (4, 'Type', 'type', 'string', true, 'Type of resource'),
    (4, 'Subject', 'subject', 'string', true, 'Subject keywords'),
    (4, 'Format', 'format', 'string', true, 'Format of the resource'),
    (4, 'Date', 'date', 'date', true, 'Date of the resource'),
    (4, 'Language', 'language', 'string', true, 'Language of the resource'),
    (4, 'Rights', 'rights', 'string', true, 'Rights information'),
    (4, 'Source', 'source', 'string', true, 'Source of the resource'),
    (4, 'Creator', 'creator', 'string', true, 'Creator of the resource'),
    (4, 'Publisher', 'publisher', 'string', true, 'Publisher of the resource'),
    (4, 'Contributor', 'contributor', 'string', false, 'Contributor to the resource'),
    (4, 'Description', 'description', 'text', true, 'Description of the resource'),
    (4, 'Abstract', 'abstract', 'text', true, 'Abstract of the resource'),
    (4, 'Created', 'created', 'date', true, 'Creation date'),
    (4, 'Modified', 'modified', 'date', true, 'Last modification date'),
    (4, 'Issued', 'issued', 'date', true, 'Issue date'),
    (4, 'Lower Corner', 'lowerCorner', 'string', true, 'Lower corner of bounding box'),
    (4, 'Upper Corner', 'upperCorner', 'string', true, 'Upper corner of bounding box'),
    (4, 'Temporal Start', 'temporalStart', 'date', true, 'Start of temporal extent'),
    (4, 'Temporal End', 'temporalEnd', 'date', true, 'End of temporal extent'),
    (4, 'URI', 'uri', 'string', true, 'URI of the resource');

DELETE FROM metadata_templates 
WHERE metadata_standard = 'ISO 19115' 
AND id NOT IN (
    SELECT MIN(id) 
    FROM metadata_templates 
    WHERE metadata_standard = 'ISO 19115' 
    GROUP BY metadata_standard
); 