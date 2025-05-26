-- Create metadata_templates table
CREATE TABLE metadata_templates (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    metadata_standard VARCHAR(50) NOT NULL,
    metadata_version VARCHAR(50) NOT NULL,
    template_xml TEXT NOT NULL,
    is_default BOOLEAN DEFAULT false,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create metadata_template_fields table for storing field definitions
CREATE TABLE metadata_template_fields (
    id SERIAL PRIMARY KEY,
    template_id INTEGER REFERENCES metadata_templates(id) ON DELETE CASCADE,
    field_name VARCHAR(255) NOT NULL,
    field_path VARCHAR(255) NOT NULL,
    field_type VARCHAR(50) NOT NULL,
    is_required BOOLEAN DEFAULT false,
    default_value TEXT,
    description TEXT,
    validation_rules TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create index on template_id
CREATE INDEX idx_metadata_template_fields_template_id ON metadata_template_fields(template_id);

-- Insert default ISO 19115 template
INSERT INTO metadata_templates (name, description, metadata_standard, metadata_version, template_xml, is_default)
VALUES (
    'Default ISO 19115',
    'Default template for ISO 19115 metadata',
    'ISO 19115',
    '2018',
    '<?xml version="1.0" encoding="UTF-8"?>
<gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd"
                 xmlns:gco="http://www.isotc211.org/2005/gco"
                 xmlns:gml="http://www.opengis.net/gml/3.2"
                 xmlns:xlink="http://www.w3.org/1999/xlink">
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
                    <gmd:citedResponsibleParty>
                        <gmd:CI_ResponsibleParty>
                            <gmd:individualName>
                                <gco:CharacterString>{{authorName}}</gco:CharacterString>
                            </gmd:individualName>
                            <gmd:organisationName>
                                <gco:CharacterString>{{authorOrg}}</gco:CharacterString>
                            </gmd:organisationName>
                            <gmd:role>
                                <gmd:CI_RoleCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_RoleCode" codeListValue="author">author</gmd:CI_RoleCode>
                            </gmd:role>
                        </gmd:CI_ResponsibleParty>
                    </gmd:citedResponsibleParty>
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
                <gmd:MD_SecurityConstraints>
                    <gmd:classification>
                        <gmd:MD_ClassificationCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_ClassificationCode" codeListValue="unclassified">unclassified</gmd:MD_ClassificationCode>
                    </gmd:classification>
                    <gmd:userNote>
                        <gco:CharacterString>{{accessConstraints}}</gco:CharacterString>
                    </gmd:userNote>
                </gmd:MD_SecurityConstraints>
                <gmd:MD_LegalConstraints>
                    <gmd:otherConstraints>
                        <gco:CharacterString>{{license}}</gco:CharacterString>
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
                    <gmd:temporalElement>
                        <gmd:EX_TemporalExtent>
                            <gmd:extent>
                                <gml:TimePeriod gml:id="tp1">
                                    <gml:beginPosition>{{temporalExtentBegin}}</gml:beginPosition>
                                    <gml:endPosition>{{temporalExtentEnd}}</gml:endPosition>
                                </gml:TimePeriod>
                            </gmd:extent>
                        </gmd:EX_TemporalExtent>
                    </gmd:temporalElement>
                </gmd:EX_Extent>
            </gmd:extent>
        </gmd:MD_DataIdentification>
    </gmd:identificationInfo>
    
    <gmd:referenceSystemInfo>
        <gmd:MD_ReferenceSystem>
            <gmd:referenceSystemIdentifier>
                <gmd:RS_Identifier>
                    <gmd:code>
                        <gco:CharacterString>{{crs}}</gco:CharacterString>
                    </gmd:code>
                    <gmd:codeSpace>
                        <gco:CharacterString>EPSG</gco:CharacterString>
                    </gmd:codeSpace>
                </gmd:RS_Identifier>
            </gmd:referenceSystemIdentifier>
            <gmd:referenceSystemType>
                <gmd:MD_ReferenceSystemTypeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_ReferenceSystemTypeCode" codeListValue="{{crsType}}">{{crsType}}</gmd:MD_ReferenceSystemTypeCode>
            </gmd:referenceSystemType>
            <gmd:description>
                <gco:CharacterString>{{crsDetails}}</gco:CharacterString>
            </gmd:description>
        </gmd:MD_ReferenceSystem>
    </gmd:referenceSystemInfo>
    
    <gmd:dataQualityInfo>
        <gmd:DQ_DataQuality>
            <gmd:scope>
                <gmd:DQ_Scope>
                    <gmd:level>
                        <gmd:MD_ScopeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_ScopeCode" codeListValue="{{scope}}">{{scope}}</gmd:MD_ScopeCode>
                    </gmd:level>
                    <gmd:levelDescription>
                        <gmd:MD_ScopeDescription>
                            <gmd:level>
                                <gmd:MD_ScopeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_ScopeCode" codeListValue="{{qualityLevel}}">{{qualityLevel}}</gmd:MD_ScopeCode>
                            </gmd:level>
                        </gmd:MD_ScopeDescription>
                    </gmd:levelDescription>
                </gmd:DQ_Scope>
            </gmd:scope>
            <gmd:lineage>
                <gmd:LI_Lineage>
                    <gmd:statement>
                        <gco:CharacterString>{{lineage}}</gco:CharacterString>
                    </gmd:statement>
                </gmd:LI_Lineage>
            </gmd:lineage>
            <gmd:report>
                <gmd:DQ_DomainConsistency>
                    <gmd:result>
                        <gmd:DQ_ConformanceResult>
                            <gmd:pass>
                                <gco:Boolean>{{conformity}}</gco:Boolean>
                            </gmd:pass>
                        </gmd:DQ_ConformanceResult>
                    </gmd:result>
                </gmd:DQ_DomainConsistency>
                <gmd:DQ_Completeness>
                    <gmd:result>
                        <gmd:DQ_QuantitativeResult>
                            <gmd:value>
                                <gco:Record>{{completeness}}</gco:Record>
                            </gmd:value>
                        </gmd:DQ_QuantitativeResult>
                    </gmd:result>
                </gmd:DQ_Completeness>
                <gmd:DQ_LogicalConsistency>
                    <gmd:result>
                        <gmd:DQ_QuantitativeResult>
                            <gmd:value>
                                <gco:Record>{{logicalConsistency}}</gco:Record>
                            </gmd:value>
                        </gmd:DQ_QuantitativeResult>
                    </gmd:result>
                </gmd:DQ_LogicalConsistency>
                <gmd:DQ_PositionalAccuracy>
                    <gmd:result>
                        <gmd:DQ_QuantitativeResult>
                            <gmd:value>
                                <gco:Record>{{positionalAccuracy}}</gco:Record>
                            </gmd:value>
                        </gmd:DQ_QuantitativeResult>
                    </gmd:result>
                </gmd:DQ_PositionalAccuracy>
                <gmd:DQ_TemporalAccuracy>
                    <gmd:result>
                        <gmd:DQ_QuantitativeResult>
                            <gmd:value>
                                <gco:Record>{{temporalAccuracy}}</gco:Record>
                            </gmd:value>
                        </gmd:DQ_QuantitativeResult>
                    </gmd:result>
                </gmd:DQ_TemporalAccuracy>
                <gmd:DQ_ThematicAccuracy>
                    <gmd:result>
                        <gmd:DQ_QuantitativeResult>
                            <gmd:value>
                                <gco:Record>{{thematicAccuracy}}</gco:Record>
                            </gmd:value>
                        </gmd:DQ_QuantitativeResult>
                    </gmd:result>
                </gmd:DQ_ThematicAccuracy>
            </gmd:report>
        </gmd:DQ_DataQuality>
    </gmd:dataQualityInfo>
    
    <gmd:distributionInfo>
        <gmd:MD_Distribution>
            <gmd:distributor>
                <gmd:MD_Distributor>
                    <gmd:distributorContact>
                        <gmd:CI_ResponsibleParty>
                            <gmd:organisationName>
                                <gco:CharacterString>{{distributorName}}</gco:CharacterString>
                            </gmd:organisationName>
                            <gmd:contactInfo>
                                <gmd:CI_Contact>
                                    <gmd:address>
                                        <gmd:CI_Address>
                                            <gmd:electronicMailAddress>
                                                <gco:CharacterString>{{distributorEmail}}</gco:CharacterString>
                                            </gmd:electronicMailAddress>
                                        </gmd:CI_Address>
                                    </gmd:address>
                                </gmd:CI_Contact>
                            </gmd:contactInfo>
                            <gmd:role>
                                <gmd:CI_RoleCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_RoleCode" codeListValue="distributor">distributor</gmd:CI_RoleCode>
                            </gmd:role>
                        </gmd:CI_ResponsibleParty>
                    </gmd:distributorContact>
                    <gmd:distributorFormat>
                        <gmd:MD_Format>
                            <gmd:name>
                                <gco:CharacterString>{{distributionFormat}}</gco:CharacterString>
                            </gmd:name>
                            <gmd:version>
                                <gco:CharacterString>1.0</gco:CharacterString>
                            </gmd:version>
                        </gmd:MD_Format>
                    </gmd:distributorFormat>
                    <gmd:distributorTransferOptions>
                        <gmd:MD_DigitalTransferOptions>
                            <gmd:onLine>
                                <gmd:CI_OnlineResource>
                                    <gmd:linkage>
                                        <gmd:URL>{{distributionUrl}}</gmd:URL>
                                    </gmd:linkage>
                                    <gmd:protocol>
                                        <gco:CharacterString>{{distributionProtocol}}</gco:CharacterString>
                                    </gmd:protocol>
                                </gmd:CI_OnlineResource>
                            </gmd:onLine>
                        </gmd:MD_DigitalTransferOptions>
                    </gmd:distributorTransferOptions>
                </gmd:MD_Distributor>
            </gmd:distributor>
        </gmd:MD_Distribution>
    </gmd:distributionInfo>
    
    <gmd:metadataMaintenance>
        <gmd:MD_MaintenanceInformation>
            <gmd:maintenanceAndUpdateFrequency>
                <gmd:MD_MaintenanceFrequencyCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_MaintenanceFrequencyCode" codeListValue="{{updateFrequency}}">{{updateFrequency}}</gmd:MD_MaintenanceFrequencyCode>
            </gmd:maintenanceAndUpdateFrequency>
            <gmd:maintenanceNote>
                <gco:CharacterString>{{maintenanceNotes}}</gco:CharacterString>
            </gmd:maintenanceNote>
        </gmd:MD_MaintenanceInformation>
    </gmd:metadataMaintenance>
</gmd:MD_Metadata>',
    true
);

-- Insert default INSPIRE template
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

-- Insert default FGDC template
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

-- Insert default OGC CSW template
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

-- Insert template fields for ISO 19115
INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (1, 'Lineage', 'lineage', 'textarea', true, 'Statement about the lineage of the dataset'),
    (1, 'Scope', 'scope', 'select', true, 'Scope of the data quality information',
     options: ['dataset', 'series', 'featureType', 'feature', 'attributeType', 'attribute', 'tile', 'model', 'other']),
    (1, 'Conformity', 'conformity', 'select', true, 'Degree of conformity to the specification',
     options: ['conformant', 'notConformant', 'notEvaluated']),
    (1, 'Completeness', 'completeness', 'textarea', false, 'Completeness of the dataset'),
    (1, 'Logical Consistency', 'logicalConsistency', 'textarea', false, 'Logical consistency of the dataset'),
    (1, 'Positional Accuracy', 'positionalAccuracy', 'textarea', false, 'Positional accuracy of the dataset'),
    (1, 'Temporal Accuracy', 'temporalAccuracy', 'textarea', false, 'Temporal accuracy of the dataset'),
    (1, 'Thematic Accuracy', 'thematicAccuracy', 'textarea', false, 'Thematic accuracy of the dataset'),
    (1, 'Quality Level', 'qualityLevel', 'select', false, 'Quality level of the dataset',
     options: ['raw', 'processed', 'derived', 'interpreted']);

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

-- Insert template fields for OGC CSW
INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (4, 'Identifier', 'identifier', 'string', true, 'Unique identifier for the record'),
    (4, 'Title', 'title', 'string', true, 'Name of the dataset or service'),
    (4, 'Type', 'type', 'string', true, 'Type of resource'),
    (4, 'Subject', 'subject', 'string', true, 'Subject of the resource'),
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