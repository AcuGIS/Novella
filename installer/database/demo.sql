--
-- Data for Name: metadata_records; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.metadata_records
    (id, title, abstract, purpose, keywords, created_at, updated_at, wms_url, wms_layer, contact_org, conformity, service_url, metadata_date, metadata_language, metadata_point_of_contact, spatial_resolution, resource_type, lineage, data_format, distribution_url, coupled_resource, metadata_poc_organization, metadata_poc_email, metadata_poc_role, resource_identifier, maintenance_frequency, character_set, topic_id, inspire_theme_id, is_public)
VALUES
    ('83d2ec55-67c2-4752-b201-d83316abddf5', 'Paris1550', 'A geo-rectified overlay of Paris in 1550', 'Show overlay of mapwarper map', '{paris,mapwarper}', '2025-06-12 12:48:33.462581+00', '2025-06-29 16:58:22.616525+00', '', '', NULL, NULL, NULL, NULL, 'eng', NULL, '3 meters', 'dataset', 'Geotiff is from mapwarper and was published here as a demo.', '{geotiff}', 'https://mapwarper.com', 'None', 'Cited Inc Meta POC', 'hello@citedcorp.com', 'Author', '83d2ec55-67c2-4752-b201-d83316abddf5', 'notPlanned', 'UTF-8', '3bb5fa9c-7edf-4bcc-a39c-a032930c11bb', 'c3cbf989-b16d-4174-b930-f3872357c4be', true),
    ('181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'Chicago QGIS Project', 'QGIS Project using ESRI Shapefiles', 'Demo using Chicago Data Portal Shapefiles', '{"chicago parks waterways"}', '2025-07-06 21:35:12.431676+00', '2025-07-07 07:22:46.775697+00', 'https://qgis-server/qgis/181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'chicagomap', NULL, NULL, NULL, NULL, 'eng', NULL, '3 meters', 'dataset', 'Downloaded from Chicago Data Portal', '{qgs}', 'https://data.chicago.gov', 'None', 'Cited Inc.', 'hello@citedcorp.com', 'Author', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'annually', 'UTF-8', 'bc96e1e5-ff9f-4c40-bddf-5d5a609fa6b5', 'd895b630-1881-4873-a27e-859aa7f9e5b9', true),
    ('00ee1f11-9808-4895-a2d2-9612cd15df49', 'Citybees', 'QField Bee Farm Project.  The Bee Farming project showcases form-based data entry and spatial data collection', 'QGIS Demo', '{infoMapAccessService}', '2025-07-07 06:34:36.171194+00', '2025-07-07 07:22:54.41529+00', 'https://qgis-server/qgis/00ee1f11-9808-4895-a2d2-9612cd15df49', 'Apiary', NULL, NULL, NULL, NULL, 'eng', NULL, 'Unkown', 'dataset', 'Downloaded from https://docs.qfield.org/get-started/sample-projects/', '{qgs}', 'https://docs.qfield.org/get-started/sample-projects/', 'None', 'QFieldCloud', 'qfield@cloud.com', 'Author', '26d46074-ce22-4808-9b18-2411b64cf777', 'notPlanned', 'UTF-8', '80a9a885-e5f5-4391-8cb9-5129135b16a0', '3511088f-0bd6-4900-ab03-f0df543c81db', true),
    ('727b6dd7-b4bf-4234-b506-d320d4522d79', 'Chicago Neighborhooods', 'Created from zipped shape file', 'Demo for shape file uploads', '{chicago,neighborhoods}', '2025-06-11 08:01:39.269335+00', '2025-07-07 07:22:07.356375+00', '', '', NULL, NULL, NULL, NULL, 'eng', NULL, '5 Meters', 'dataset', 'Downloaded from Chicago Data Portal on 4 June, 2025', '{shapefile}', 'https://data.chicago.gov', 'None', 'Your Metadata Organization', 'yourmeta@email.com', 'Publisher', '727b6dd7-b4bf-4234-b506-d320d4522d79', 'asNeeded', 'UTF-8', 'bc96e1e5-ff9f-4c40-bddf-5d5a609fa6b5', 'd895b630-1881-4873-a27e-859aa7f9e5b9', true),
    ('e10d4e6a-3ffd-4c86-a192-339ea06eaf00', 'Bestaande open bodemenergiesystemen', 'Open bodemenergiesystemen maken gebruik van zandpakketten in de ondergrond tot een maximale diepte van circa 250 meter beneden maaiveld (m-mv). De landelijke bodemgeschiktheidskaarten voor open systemen (opgesteld in opdracht van RVO ten behoeve van de Warmteatlas) is gebaseerd op het toepassen van bodemenergie tot deze diepte. In de Warmteatlas wordt de energie die uit de bodem ontrokken kan worden weergegeven. In debodemgeschiktheidskaart voor Overijssel wordt de energie die vanuit de bodem geleverd kan worden met een behulp van een warmtepomp (met een COP van 4) weergegeven. De bodemgeschiktheid inclusief toepassing van een warmtepomp geeft een indicatie van de hoeveelheid energie die benut kan worden door toepassing van een open systeem in combinate met het gebruik van een warmtepomp. De bodemgeschiktheid is bepaald op basis van de totale beschikbare dikte aan zandpakketten tot 250 m-mv. De bodem boven de gemiddelde grondwaterstand (REGIS) is hierin niet meegenomen. Aangenomen is dat filters over 80% van de totale dikte van het pakket geplaatst kunnen worden en dat er in de systemen uitgegaan wordt van een energiebalans. Regionaal kan de diepte tot waar een open systeem geplaatst kan worden, beperkt zijn door wet- en regelgeving. Voor meer informatie met betrekking tot de berekening van het potentieel wordt verwezen naar de Warmteatlas (http://rvo.b3p.nl/viewer/app/Warmteatlas/v2).', '', '{bodemenergie,systemen,energie}', '2025-07-07 06:55:41.924352+00', '2025-07-07 07:13:49.534732+00', 'https://services.geodataoverijssel.nl/geoserver/LIP/wms', 'LIP_Bestaande_open_bodemenergiesystemen', NULL, NULL, NULL, NULL, 'eng', NULL, 'Unkown', 'service', 'Data harvested from WMS service: https://services.geodataoverijssel.nl/geoserver/LIP/wms', '{wms}', 'https://services.geodataoverijssel.nl/geoserver/LIP/wms', 'None', 'Provincie Overijssel', 'kennishub@overijssel.nl', 'Author', 'e10d4e6a-3ffd-4c86-a192-339ea06eaf00', 'quarterly', '', 'b02e4bd4-2680-431a-8959-8bbaf7aaef2a', '322e1410-ceb7-46eb-a316-24cc0c0a2e0d', true),
    ('c750585a-f021-4874-8171-711ab8e67c74', 'Begaanbaarheid hoofdwegen RIVUS', 'Bij een hevige regenbui kan het water op straat zo hoog komen dat de weg onbegaanbaar wordt. De begaanbaarheidskaart laat zien welke wegen begaanbaar zijn bij dezelfde bui als waar de wateroverlastkaart op gebaseerd is: 60 millimeter in een uur. Wegen zijn geclassificeerd als ''begaanbaar'' als er een maximale waterdiepte is van 10 centimeter (groen). Bij waterdieptes tussen de 10 en 30 centimeter zijn de wegen geclassificeerd als ''begaanbaar voor calamiteitenverkeer'' (geel). Wegen met waterdieptes van 30 centimeter en meer zijn ''onbegaanbaar'' (rood). De belangrijkste ontsluitingsroutes zijn dikker weergegeven.', '', '{begaanbaar,rivus,verkeer,water}', '2025-07-07 06:55:41.934369+00', '2025-07-07 07:22:33.475424+00', 'https://services.geodataoverijssel.nl/geoserver/LIP/wms', 'LIP_Kwetsbaarheid_hoofdwegen_Rivus', NULL, NULL, NULL, NULL, 'eng', NULL, '', 'service', 'Data harvested from WMS service: https://services.geodataoverijssel.nl/geoserver/LIP/wms', '{wms}', 'https://services.geodataoverijssel.nl/geoserver/LIP/wms', 'None', 'Provincie Overijssel', 'kennishub@overijssel.nl', 'Author', 'c750585a-f021-4874-8171-711ab8e67c74', 'annually', '', '3bb5fa9c-7edf-4bcc-a39c-a032930c11bb', 'e0ed5e9f-5a3b-4864-b7bc-1fa05d8513ee', true);

--
-- Data for Name: citations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.citations
    (id, metadata_id, citation_date, responsible_org, responsible_person, role, created_at)
VALUES
    ('ac922494-6fda-4796-abde-0a11f1305579', '83d2ec55-67c2-4752-b201-d83316abddf5', '2025-06-12', 'Cited Inc', 'Cited Inc GIS', 'publisher', '2025-06-12 12:48:33.462581+00'),
    ('bb589a0d-2200-42bc-aa8e-89fffd027ec3', '00ee1f11-9808-4895-a2d2-9612cd15df49', '2025-07-07', 'OPENGIS.ch', 'OpenGIS', 'author', '2025-07-07 06:34:36.171194+00'),
    ('4b92ab86-6683-49f5-85fc-d1d9c14a06f1', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', '2025-07-06', 'Novella', 'Cited Inc', 'pointOfContact', '2025-07-06 21:35:12.431676+00'),
    ('6c99b4e8-69cd-47a6-9469-39931c0202e7', 'e10d4e6a-3ffd-4c86-a192-339ea06eaf00', '2025-07-07', 'Novella GIS', 'System', 'publisher', '2025-07-07 06:55:41.924352+00'),
    ('72d5b284-e1b9-4044-9084-a301672f05a1', 'c750585a-f021-4874-8171-711ab8e67c74', '2025-07-07', 'Novella GIS', 'System', '', '2025-07-07 06:55:41.934369+00'),
    ('97557e85-188c-4450-aba8-2b12e438b0d9', '727b6dd7-b4bf-4234-b506-d320d4522d79', '2025-06-11', 'Cited Inc.', 'Cited Inc.', 'publisher', '2025-06-11 08:01:39.269335+00');

--
-- Data for Name: constraints; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.constraints
    (id, metadata_id, use_constraints, created_at, access_constraints, use_limitation)
VALUES
    ('9cd09aa8-8024-4da0-b439-e89fac3080c5', '83d2ec55-67c2-4752-b201-d83316abddf5', 'See MapWarper for usage constraints', '2025-06-12 12:48:33.462581+00', 'See MapWarper for access constraints', 'See MapWarper for use limits'),
    ('66abe0d8-2e58-4b71-8f53-50e315f3f538', '00ee1f11-9808-4895-a2d2-9612cd15df49', 'See https://docs.qfield.org/get-started/sample-projects/', '2025-07-07 06:34:36.171194+00', 'See https://docs.qfield.org/get-started/sample-projects/', 'See https://docs.qfield.org/get-started/sample-projects/'),
    ('024589e2-8388-420b-b80b-d17562c789e9', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'See Chicago Data Portal', '2025-07-06 21:36:00.316964+00', 'See Chicago Data Portal', 'See Chicago Data Portal'),
    ('9fee0efa-4f1f-4a39-96f1-9d4b058637d7', 'e10d4e6a-3ffd-4c86-a192-339ea06eaf00', 'See https://www.geoportaaloverijssel.nl/', '2025-07-07 07:13:49.534732+00', 'See https://www.geoportaaloverijssel.nl/', 'See https://www.geoportaaloverijssel.nl/'),
    ('ce101bca-bcff-4818-bc75-311be04cd412', 'c750585a-f021-4874-8171-711ab8e67c74', 'See https://www.overijssel.nl', '2025-07-07 07:19:56.844733+00', 'See https://www.overijssel.nl', 'See https://www.overijssel.nl'),
    ('feb0ae7e-8757-4ba7-bc1a-574bf5a99604', '727b6dd7-b4bf-4234-b506-d320d4522d79', 'See Chicago Data Portal for use constraints', '2025-06-11 08:01:39.269335+00', 'See Chicago Data Portal for use constraints', 'See Chicago Data Portal for limitations');

--
-- Data for Name: geographic_extents; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.geographic_extents
    (id, metadata_id, west_longitude, east_longitude, south_latitude, north_latitude, created_at)
VALUES
    ('763555e8-6ada-4b6d-b9e8-078f832634a3', '83d2ec55-67c2-4752-b201-d83316abddf5', 2.315340, 2.399038, 48.827492, 48.884698, '2025-06-12 12:48:33.462581+00'),
    ('28505c11-4f28-420d-b2b3-f42635970169', '00ee1f11-9808-4895-a2d2-9612cd15df49', 9.249581, 9.263862, 46.806322, 46.815363, '2025-07-07 06:34:36.171194+00'),
    ('e27201fe-86f4-4d8f-a860-e92dc2c4536e', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', -88.062610, -87.401641, 41.635080, 42.032501, '2025-07-06 21:35:12.431676+00'),
    ('3f6256ae-0faa-41a5-b0cd-3d34a41d9d5a', 'e10d4e6a-3ffd-4c86-a192-339ea06eaf00', 5.869481, 6.912716, 52.198071, 52.818449, '2025-07-07 06:55:41.924352+00'),
    ('1499e215-8ed8-48fa-9fe2-e6988bdd5927', 'c750585a-f021-4874-8171-711ab8e67c74', 5.856562, 6.378535, 52.236223, 52.683892, '2025-07-07 06:55:41.934369+00'),
    ('4012c69d-46b5-4326-a965-1ff764e33170', '727b6dd7-b4bf-4234-b506-d320d4522d79', -87.940114, -87.524137, 41.644543, 42.023039, '2025-06-11 08:01:39.269335+00');


--
-- Data for Name: gis_files; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.gis_files
    (id, metadata_id, file_name, file_type, file_size, file_path, mime_type, created_at, thumbnail_path)
VALUES
    ('debfb885-fad1-476e-a2b2-b225d1951a1e', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'ChicagoNeighborhoods.zip', 'zip', 1965192, '/var/www/novella/storage/uploads//686aec106b750_ChicagoNeighborhoods.zip', 'application/zip', '2025-07-06 21:35:12.466534+00', NULL),
    ('cc659c73-fae6-4325-80b1-b0fa200780be', '00ee1f11-9808-4895-a2d2-9612cd15df49', 'CityBees.zip', 'zip', 1765364, '/var/www/novella/storage/uploads//686b6a7c2c17d_CityBees.zip', 'application/zip', '2025-07-07 06:34:36.193357+00', NULL),
    ('04010538-d7e8-42aa-863e-5993045df25b', '83d2ec55-67c2-4752-b201-d83316abddf5', 'paris.tif', 'tiff', 3813004, '/var/www/novella/storage/uploads/6841522424b0f_paris.tif', 'image/tiff', '2025-07-06 21:35:12.439593+00', ''),
    ('2eed415f-e23d-4001-8d52-c46093fd7f92', '727b6dd7-b4bf-4234-b506-d320d4522d79', 'neighborhoods.zip', 'zip', 604969, '/var/www/novella/storage/uploads/68493169bcaaa_neighborhoods.zip', 'application/zip', '2025-07-06 21:35:12.439593+00', '');


--
-- Data for Name: inspire_metadata; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.inspire_metadata
    (id, metadata_id, point_of_contact_org, conformity_result, spatial_data_service_url, created_at)
VALUES
    ('47433d99-bcdc-4600-9df7-50e2601c78f8', '83d2ec55-67c2-4752-b201-d83316abddf5', 'Cited Inc Met Org', 'conformant', 'https://mapwarper.com', '2025-06-12 12:48:33.462581+00'),
    ('dedd159b-9deb-4138-ac12-f67caf4f4cd7', '00ee1f11-9808-4895-a2d2-9612cd15df49', 'QFieldCloud', 'conformant', 'https://qgis-server/qgis/00ee1f11-9808-4895-a2d2-9612cd15df49?SERVICE=WMS&REQUEST=GetCapabilities&VERSION=1.3.0', '2025-07-07 06:34:36.171194+00'),
    ('6a5c27e3-aa6e-4f94-9f81-72b7c7fe2620', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'Cited Inc.', 'conformant', 'https://qgis-server/qgis/181dd2c6-6d56-4c91-b550-0b62ea6d4a68?SERVICE=WMS&REQUEST=GetCapabilities&VERSION=1.3.0', '2025-07-06 21:35:12.431676+00'),
    ('93028d01-1380-43e9-b1b6-d7902f4d370e', 'e10d4e6a-3ffd-4c86-a192-339ea06eaf00', 'Provincie Overijssel', 'conformant', 'https://services.geodataoverijssel.nl/geoserver/LIP/wms', '2025-07-07 07:13:49.534732+00'),
    ('834716cb-a588-4977-8127-3aff2a9716fb', 'c750585a-f021-4874-8171-711ab8e67c74', 'Provincie Overijssel', 'conformant', 'https://services.geodataoverijssel.nl/geoserver/LIP/wms', '2025-07-07 07:19:56.844733+00'),
    ('2a638ebd-e121-4e38-852a-46b2b76b021f', '727b6dd7-b4bf-4234-b506-d320d4522d79', 'Cited Meta', 'conformant', 'https://data.chicago.gov', '2025-06-11 08:01:39.269335+00');

--
-- Data for Name: spatial_representations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.spatial_representations
    (id, metadata_id, coordinate_system, created_at)
VALUES
    ('abd79cf9-cde5-44ec-8a6f-2d8983073f76', '83d2ec55-67c2-4752-b201-d83316abddf5', 'EPSG:4326', '2025-06-12 12:48:33.462581+00'),
    ('99f38136-9260-45ad-b442-c6b46908075d', '00ee1f11-9808-4895-a2d2-9612cd15df49', 'EPSG:7030', '2025-07-07 06:34:36.171194+00'),
    ('2d9ef72d-6635-42a7-bcc2-daba266b1bec', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', 'EPSG:4326', '2025-07-06 21:35:12.431676+00'),
    ('280237f9-99bf-49b3-bb04-567fc32ff462', '727b6dd7-b4bf-4234-b506-d320d4522d79', 'EPSG:4326', '2025-06-11 08:01:39.269335+00');


--
-- Data for Name: temporal_extents; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.temporal_extents
    (id, metadata_id, start_date, end_date, created_at)
VALUES
    ('87ceada6-a9d8-4eca-8354-5a0ffcfe6514', '83d2ec55-67c2-4752-b201-d83316abddf5', '2025-06-12', '2025-06-12', '2025-06-12 12:48:33.462581+00'),
    ('4e86ebd2-429f-4a2f-a1d7-26858d307a11', '00ee1f11-9808-4895-a2d2-9612cd15df49', '2025-07-07', '2025-07-07', '2025-07-07 06:34:36.171194+00'),
    ('d7f0398e-be2a-47ec-be00-e4276a766edc', '181dd2c6-6d56-4c91-b550-0b62ea6d4a68', '2025-07-06', '2025-07-06', '2025-07-06 21:35:12.431676+00'),
    ('dc58b4e7-f930-42a8-9c08-e81aa2860b11', 'e10d4e6a-3ffd-4c86-a192-339ea06eaf00', '2025-07-07', '2025-07-07', '2025-07-07 07:13:49.534732+00'),
    ('c740b3b1-f939-4581-820c-2c86d1c13f45', 'c750585a-f021-4874-8171-711ab8e67c74', '2025-07-07', '2025-07-07', '2025-07-07 07:19:56.844733+00'),
    ('8280ae2c-36ae-48b8-8156-ec9382b20cee', '727b6dd7-b4bf-4234-b506-d320d4522d79', '2025-06-11', '2025-06-11', '2025-06-11 08:01:39.269335+00');
