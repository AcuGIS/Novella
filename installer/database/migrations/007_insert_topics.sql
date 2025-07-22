-- Insert common GIS topics into the topics table
-- These topics represent common categories for GIS datasets

INSERT INTO public.topics
    (id, topic, description, created_at, updated_at)
VALUES
    ('c4dc2560-473f-4e70-a30d-39a2512baf6d', 'Inland Waters', 'Inland water features, drainage systems and their characteristics.', '2025-06-06 12:09:27.749376+00', '2025-06-06 12:09:45.428284+00'),
    ('63dbe907-97df-4684-a2c9-2510788cb222', 'Environment', 'Environmental resources, protection and conservation.', '2025-06-06 12:41:30.159051+00', '2025-06-06 12:41:30.159051+00'),
    ('7990225c-d8b1-4038-8325-23e0fdce1001', 'Health', 'Health, health services, human ecology, and safety.', '2025-06-06 12:41:49.30235+00', '2025-06-06 12:41:49.30235+00'),
    ('9c249929-4547-4938-8151-ba8c57756717', 'Administrative Boundaries', 'Political and administrative boundaries including countries, states, provinces, counties, municipalities, and electoral districts', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('80a9a885-e5f5-4391-8cb9-5129135b16a0', 'Agriculture and Farming', 'Agricultural land use, crop types, farming practices, agricultural facilities, and rural development', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('9673bf90-9b78-41c1-9f14-4963f38c3d5a', 'Biodiversity and Ecosystems', 'Protected areas, habitats, species distribution, ecological corridors, and environmental conservation', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('a86b87f6-f95c-450b-8fc6-77896e9991e7', 'Buildings and Infrastructure', 'Buildings, roads, bridges, utilities, transportation networks, and urban infrastructure', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('91f10055-5f15-4842-bffc-0fa7a583e57f', 'Business and Economy', 'Commercial areas, industrial zones, economic activities, business locations, and trade data', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('1197e380-c0b2-4688-8e0f-4bba450be34b', 'Climate and Weather', 'Climate data, weather patterns, temperature, precipitation, wind, and atmospheric conditions', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('747579be-c2cc-4d28-a788-49c6679f3397', 'Demographics and Population', 'Population statistics, demographics, census data, migration patterns, and social indicators', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('58791389-04e3-4c7a-bd2d-def8a46f3063', 'Education and Research', 'Educational institutions, research facilities, libraries, and academic resources', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('7c14a619-da39-4fe3-a3b1-c9c0562c5b02', 'Emergency and Safety', 'Emergency services, disaster management, safety facilities, and risk assessment data', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('044ed5e2-5df6-417a-8d49-e826e951523b', 'Energy and Utilities', 'Power plants, energy infrastructure, utility networks, renewable energy sources, and energy consumption', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('9c99fc27-1d18-49c2-917d-3bdbeccdf168', 'Environment and Pollution', 'Environmental monitoring, pollution levels, air quality, water quality, and environmental impact', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('584c18d6-915b-4ca8-8da9-c608aba198f8', 'Forestry and Vegetation', 'Forest cover, vegetation types, land cover, forestry management, and natural resources', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('b02e4bd4-2680-431a-8959-8bbaf7aaef2a', 'Geology and Soils', 'Geological formations, soil types, mineral resources, geological hazards, and earth sciences', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('fd4d6c95-5b07-420b-a2b6-4c6833b3363f', 'Health and Healthcare', 'Healthcare facilities, medical services, public health data, and health-related infrastructure', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('3bb5fa9c-7edf-4bcc-a39c-a032930c11bb', 'Historical and Cultural', 'Historical sites, cultural heritage, archaeological sites, monuments, and cultural resources', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('0a7413bf-1c4e-496a-9e7a-2123038a834f', 'Hydrology and Water', 'Water bodies, rivers, lakes, watersheds, groundwater, water quality, and water management', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('3325169d-a421-4fa0-8def-96da7b49d4ee', 'Land Use and Planning', 'Land use classification, urban planning, zoning, development plans, and land management', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('34ed9616-d712-4c0a-a512-1dec7bdd43c8', 'Mining and Minerals', 'Mining operations, mineral deposits, quarries, extraction sites, and mining infrastructure', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('fcdacc33-c4e7-435c-8def-d4da3aa63ad6', 'Recreation and Tourism', 'Recreational areas, tourist attractions, parks, trails, and leisure facilities', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('140a0789-2027-4203-9ab0-6245432d993f', 'Transportation and Mobility', 'Transportation networks, roads, railways, airports, ports, and mobility data', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('bc96e1e5-ff9f-4c40-bddf-5d5a609fa6b5', 'Urban Development', 'Urban areas, city planning, urban growth, housing, and urban infrastructure', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00'),
    ('badc5e05-3d2d-48a6-9a1b-a80ceb2f04fe', 'Waste Management', 'Waste disposal sites, recycling facilities, waste collection, and environmental waste data', '2025-06-28 20:32:56.146458+00', '2025-06-28 20:32:56.146458+00');
