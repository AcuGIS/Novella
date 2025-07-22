<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* viewer.twig */
class __TwigTemplate_3969bd6f2034a457dd513bd06a3de2e8 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'extra_css' => [$this, 'block_extra_css'],
            'content' => [$this, 'block_content'],
            'extra_js' => [$this, 'block_extra_js'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("base.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "GIS Data Viewer";
        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_css(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 6
        yield from $this->yieldParentBlock("extra_css", $context, $blocks);
        yield "
<!-- OpenLayers CSS -->
<link rel=\"stylesheet\" href=\"https://unpkg.com/ol@7.4.0/ol.css\">
<!-- Layer Switcher CSS -->
<link rel=\"stylesheet\" href=\"https://unpkg.com/ol-layerswitcher@4.1.1/dist/ol-layerswitcher.css\">
<!-- Font Awesome -->
<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
<!-- html2canvas -->
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js\"></script>
<!-- jsPDF -->
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js\"></script>
<style>
    /* Reset and base styles */
    .content-wrapper {
        position: relative;
        height: calc(100vh - 0px);
        margin-top: 0px;
        width: 100%;
    }

    /* Main content area */
    .content {
        display: flex;
        height: 100%;
        width: 100%;
        overflow: hidden;
    }

    /* Sidebar styles */
    .sidebar {
        width: 300px;
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding: 1rem;
        background: #e9ecef;
        border-bottom: 1px solid #dee2e6;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    /* Map container styles */
    .map-container {
        flex: 1;
        position: relative;
        height: 100%;
        background: #f0f0f0;
        overflow: hidden;
        min-width: 0; /* Allow container to shrink below its content size */
    }

    #map {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    /* Test map container */
    #test-map {
        width: 200px;
        height: 150px;
        border: 1px solid #ccc;
        margin: 10px;
        background: #fff;
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 1000;
        display: none; /* Hide the test map since we don't need it anymore */
    }

    /* Dataset list styles */
    .dataset-list {
        list-style: none;
    }

    .dataset-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: move;
        transition: all 0.2s;
        user-select: none;
    }

    .dataset-item:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .dataset-item.dragging {
        opacity: 0.5;
        background: #e9ecef;
    }

    /* Search box styles */
    .search-box {
        position: relative;
        margin-bottom: 1rem;
    }

    .search-box input {
        width: 100%;
        padding: 0.5rem;
        padding-right: 2rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .search-box button {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
    }

    /* Debug panel styles */
    .debug-panel {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 0.5rem;
        font-family: monospace;
        font-size: 0.8rem;
        z-index: 1000;
    }

    /* Export button styles */
    .export-button {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        padding: 8px 16px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        cursor: pointer;
        font-size: 14px;
        color: #333;
        transition: all 0.2s ease;
    }

    .export-button:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .export-button:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Popup styles */
    .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 280px;
        max-width: 400px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
    }

    .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: \" \";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
    }

    .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
    }

    .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
        z-index: 1001;
    }

    .ol-popup-closer:after {
        content: \"✖\";
    }

    .feature-info {
        margin: 0;
        padding: 0;
    }
    .feature-info dt {
        font-weight: bold;
        margin-top: 8px;
    }
    .feature-info dd {
        margin-left: 0;
        margin-bottom: 4px;
    }
</style>
";
        yield from [];
    }

    // line 252
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 253
        yield "<style>
    /* Override base template main element */
    main.container-fluid {
        max-width: 1600px !important;
        margin: 0 auto !important;
    }
    .content-wrapper {
        margin: 0 -1rem;  /* Negative margin to allow content to extend full width */
        width: calc(100% + 2rem);  /* Compensate for the negative margin */
    }
</style>
<div class=\"content-wrapper\">
    <div class=\"content\">
        <div class=\"sidebar\">
            <div class=\"sidebar-header\">
                <div class=\"search-box\">
                    <input type=\"text\" id=\"dataset-search\" placeholder=\"Search datasets...\">
                    <button id=\"search-reset\" title=\"Clear search\">×</button>
                </div>
            </div>
            <div class=\"sidebar-content\">
                <ul class=\"dataset-list\" id=\"dataset-list\">
                    ";
        // line 275
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["datasets"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["dataset"]) {
            // line 276
            yield "                    <li class=\"dataset-item\" 
                        draggable=\"true\"
                        data-dataset-id=\"";
            // line 278
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "id", [], "any", false, false, false, 278), "html", null, true);
            yield "\"
                        data-title=\"";
            // line 279
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "title", [], "any", false, false, false, 279), "html", null, true);
            yield "\"
                        data-wms-url=\"";
            // line 280
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "wmsUrl", [], "any", false, false, false, 280), "html", null, true);
            yield "\"
                        data-wms-layer=\"";
            // line 281
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "wmsLayer", [], "any", false, false, false, 281), "html", null, true);
            yield "\">
                        ";
            // line 282
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "title", [], "any", false, false, false, 282), "html", null, true);
            yield "
                    </li>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['dataset'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 285
        yield "                </ul>
                <div class=\"pagination-controls\" style=\"margin-top: 1rem; text-align: center;\">
                    <button id=\"prev-page\" class=\"btn btn-sm btn-outline-secondary\" style=\"margin-right: 0.5rem;\">Previous</button>
                    <span id=\"page-info\" style=\"margin: 0 0.5rem;\">Page 1</span>
                    <button id=\"next-page\" class=\"btn btn-sm btn-outline-secondary\" style=\"margin-left: 0.5rem;\">Next</button>
                </div>
            </div>
        </div>
        <div class=\"map-container\">
            <div id=\"map\"></div>
            <button id=\"export-pdf\" class=\"export-button\" title=\"Export current map view to PDF\">
                <i class=\"fas fa-file-pdf\"></i> Export PDF
            </button>
            <div id=\"popup\" class=\"ol-popup\">
                <a href=\"#\" id=\"popup-closer\" class=\"ol-popup-closer\"></a>
                <div id=\"popup-content\"></div>
            </div>
        </div>
    </div>
</div>
";
        yield from [];
    }

    // line 307
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 308
        yield from $this->yieldParentBlock("extra_js", $context, $blocks);
        yield "
<!-- OpenLayers -->
<script src=\"https://unpkg.com/ol@7.4.0/dist/ol.js\"></script>
<!-- Layer Switcher -->
<script src=\"https://unpkg.com/ol-layerswitcher@4.1.1/dist/ol-layerswitcher.js\"></script>

<script>
// Store all dataset items in memory
let allDatasets = [];
let filteredDatasets = [];
const ITEMS_PER_PAGE = 12;
let currentPage = 1;

// Function to update the dataset list display
function updateDatasetList() {
    const datasetList = document.getElementById('dataset-list');
    const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
    const endIndex = startIndex + ITEMS_PER_PAGE;
    const totalPages = Math.ceil(filteredDatasets.length / ITEMS_PER_PAGE);
    
    // Clear the current list
    datasetList.innerHTML = '';
    
    // Add the items for the current page
    filteredDatasets.slice(startIndex, endIndex).forEach(item => {
        const li = document.createElement('li');
        li.className = 'dataset-item';
        li.draggable = true;
        li.dataset.datasetId = item.dataset.datasetId;
        li.dataset.title = item.dataset.title;
        li.dataset.wmsUrl = item.dataset.wmsUrl;
        li.dataset.wmsLayer = item.dataset.wmsLayer;
        li.textContent = item.dataset.title;
        
        // Add drag event listeners
        li.addEventListener('dragstart', handleDragStart);
        li.addEventListener('dragend', handleDragEnd);
        
        datasetList.appendChild(li);
    });
    
    // Update page info
    document.getElementById('page-info').textContent = `Page \${currentPage} of \${totalPages}`;
    
    // Update button states
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages || totalPages === 0;
}

// Initialize pagination and search
document.addEventListener('DOMContentLoaded', function() {
    // Store all dataset items
    allDatasets = Array.from(document.querySelectorAll('.dataset-item')).map(item => ({
        element: item,
        dataset: {
            datasetId: item.dataset.datasetId,
            title: item.dataset.title,
            wmsUrl: item.dataset.wmsUrl,
            wmsLayer: item.dataset.wmsLayer
        }
    }));
    filteredDatasets = [...allDatasets];
    
    // Set up pagination controls
    document.getElementById('prev-page').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateDatasetList();
        }
    });
    
    document.getElementById('next-page').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredDatasets.length / ITEMS_PER_PAGE);
        if (currentPage < totalPages) {
            currentPage++;
            updateDatasetList();
        }
    });
    
    // Set up search functionality
    const searchInput = document.getElementById('dataset-search');
    const searchReset = document.getElementById('search-reset');
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        filteredDatasets = allDatasets.filter(item => 
            item.dataset.title.toLowerCase().includes(searchTerm)
        );
        
        // Reset to first page when searching
        currentPage = 1;
        updateDatasetList();
    });
    
    if (searchReset) {
        searchReset.addEventListener('click', function() {
            searchInput.value = '';
            filteredDatasets = [...allDatasets];
            currentPage = 1;
            updateDatasetList();
        });
    }
    
    // Initial update
    updateDatasetList();
});

// Drag and drop handlers
function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', JSON.stringify({
        id: e.target.dataset.datasetId,
        title: e.target.dataset.title,
        url: e.target.dataset.wmsUrl,
        layer: e.target.dataset.wmsLayer
    }));
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

// Wait for both DOM and OpenLayers to be ready
function initMap() {
    console.log('Initializing map...');

    // First try a simple map to test OpenLayers
    try {
        console.log('Creating test map...');
        const testMap = new ol.Map({
            target: 'test-map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            view: new ol.View({
                center: [0, 0],
                zoom: 2
            })
        });
        console.log('Test map created successfully');
    } catch (e) {
        console.error('Test map error:', e);
        return;
    }

    // If test map works, try the main map
    try {
        console.log('Creating main map...');
        
        // Initialize the map
        const map = new ol.Map({
            target: 'map',
            layers: [
                // Create a layer group for base layers
                new ol.layer.Group({
                    title: 'Base Maps',
                    layers: [
                        new ol.layer.Tile({
                            source: new ol.source.OSM({
                                crossOrigin: 'anonymous'
                            }),
                            title: 'OpenStreetMap',
                            type: 'base',
                            visible: true
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.XYZ({
                                url: 'https://{a-d}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
                                attributions: '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors © <a href=\"https://carto.com/attributions\">CARTO</a>',
                                crossOrigin: 'anonymous'
                            }),
                            title: 'Carto Light',
                            type: 'base',
                            visible: false
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.XYZ({
                                url: 'https://{a-d}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
                                attributions: '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors © <a href=\"https://carto.com/attributions\">CARTO</a>',
                                crossOrigin: 'anonymous'
                            }),
                            title: 'Carto Dark',
                            type: 'base',
                            visible: false
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.XYZ({
                                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                                attributions: '© <a href=\"https://www.esri.com/\">Esri</a>',
                                crossOrigin: 'anonymous'
                            }),
                            title: 'ESRI Satellite',
                            type: 'base',
                            visible: false
                        })
                    ]
                })
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat([0, 0]),
                zoom: 2
            }),
            controls: [
                // Default controls
                new ol.control.Zoom(),
                new ol.control.Attribution({
                    collapsible: false
                }),
                // Additional controls
                new ol.control.ScaleLine({
                    units: 'metric'
                }),
                new ol.control.FullScreen(),
                new ol.control.MousePosition({
                    coordinateFormat: ol.coordinate.createStringXY(4),
                    projection: 'EPSG:4326'
                }),
                // Add the layer switcher control
                new ol.control.LayerSwitcher({
                    tipLabel: 'Layers',
                    groupSelectStyle: 'group',
                    activationMode: 'click',
                    startActive: false
                })
            ]
        });

        // Create popup overlay
        const popup = new ol.Overlay({
            element: document.getElementById('popup'),
            positioning: 'bottom-center',
            stopEvent: false,
            offset: [0, -10]
        });
        map.addOverlay(popup);

        // Close popup when clicking the closer
        document.getElementById('popup-closer').onclick = function() {
            popup.setPosition(undefined);
            return false;
        };

        // Add click interaction
        map.on('click', function(evt) {
            // Clear any existing popup
            popup.setPosition(undefined);

            // Get all visible WMS layers and their z-indices
            const visibleLayers = Array.from(layers.values())
                .filter(layer => layer.getVisible() && layer.getSource() instanceof ol.source.TileWMS)
                .map(layer => ({
                    layer,
                    zIndex: layer.getZIndex() || 0
                }))
                .sort((a, b) => b.zIndex - a.zIndex); // Sort by z-index, highest first

            if (visibleLayers.length === 0) {
                console.log('No visible WMS layers found');
                return;
            }

            // Get the map's current view state
            const viewState = map.getView().getState();
            const resolution = viewState.resolution;
            const rotation = viewState.rotation;
            const center = viewState.center;

            // Find the topmost layer that has data at the clicked point
            let clickedLayer = null;
            const pixel = evt.pixel;
            const coordinate = evt.coordinate;

            // Try each layer in order of z-index (top to bottom)
            for (const {layer} of visibleLayers) {
                const source = layer.getSource();
                const params = source.getParams();
                const baseUrl = source.getUrls ? source.getUrls()[0] : source.url_;
                
                if (!baseUrl) continue;

                // Create a test request to check if the layer has data at this point
                const testParams = {
                    'REQUEST': 'GetFeatureInfo',
                    'SERVICE': 'WMS',
                    'VERSION': '1.3.0',
                    'LAYERS': params.LAYERS,
                    'QUERY_LAYERS': params.LAYERS,
                    'INFO_FORMAT': 'application/json',
                    'FEATURE_COUNT': 1,
                    'EXCEPTIONS': 'XML',
                    'STYLES': params.STYLES || '',
                    'FORMAT': 'image/png',
                    'TRANSPARENT': true,
                    'CRS': viewState.projection.getCode(),
                    'I': Math.round(pixel[0]),
                    'J': Math.round(pixel[1]),
                    'WIDTH': map.getSize()[0],
                    'HEIGHT': map.getSize()[1],
                    'BBOX': map.getView().calculateExtent(map.getSize()).join(','),
                    'BUFFER': 5
                };

                const queryString = Object.entries(testParams)
                    .map(([key, value]) => `\${encodeURIComponent(key)}=\${encodeURIComponent(value)}`)
                    .join('&');
                const url = `\${baseUrl}\${baseUrl.includes('?') ? '&' : '?'}\${queryString}`;

                try {
                    // Make a synchronous request to check for features
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', url, false); // false makes it synchronous
                    xhr.send();

                    if (xhr.status === 200) {
                        const contentType = xhr.getResponseHeader('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const data = JSON.parse(xhr.responseText);
                            if (data.features && data.features.length > 0) {
                                clickedLayer = layer;
                                break;
                            }
                        }
                    }
                } catch (error) {
                    console.log(`Error checking layer \${layer.get('title')}:`, error);
                    continue;
                }
            }

            if (!clickedLayer) {
                console.log('No features found at clicked position');
                return;
            }

            // Process the clicked layer
            const layer = clickedLayer;
            const source = layer.getSource();
            const params = source.getParams();
            
            // Get the WMS URL from the source configuration
            const baseUrl = source.getUrls ? source.getUrls()[0] : source.url_;
            if (!baseUrl) {
                console.log('No WMS URL found for layer:', layer.get('title'));
                return;
            }

            // Use consistent WMS version and format
            const version = '1.3.0';  // Always use WMS 1.3.0
            const format = 'application/json';  // Always use JSON format
            
            const mapSize = map.getSize();
            const extent = map.getView().calculateExtent(map.getSize());
            
            const wmsParams = {
                'REQUEST': 'GetFeatureInfo',
                'SERVICE': 'WMS',
                'VERSION': version,
                'LAYERS': params.LAYERS,
                'QUERY_LAYERS': params.LAYERS,
                'INFO_FORMAT': format,
                'FEATURE_COUNT': 10,
                'EXCEPTIONS': 'XML',
                'STYLES': params.STYLES || '',
                'FORMAT': 'image/png',
                'TRANSPARENT': true,
                'CRS': viewState.projection.getCode(),
                'I': Math.round(pixel[0]),
                'J': Math.round(pixel[1]),
                'WIDTH': mapSize[0],
                'HEIGHT': mapSize[1],
                'BBOX': extent.join(','),
                'BUFFER': 10
            };

            // Construct the URL
            const queryString = Object.entries(wmsParams)
                .map(([key, value]) => `\${encodeURIComponent(key)}=\${encodeURIComponent(value)}`)
                .join('&');
            const url = `\${baseUrl}\${baseUrl.includes('?') ? '&' : '?'}\${queryString}`;

            // Make a single request with consistent parameters
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: \${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Expected JSON response but got ' + contentType);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.exceptionCode) {
                        throw new Error(`WMS Exception: \${data.exceptionText || data.message || 'Unknown error'}`);
                    }
                    
                    if (!data.features || data.features.length === 0) {
                        console.log('No features found for layer:', layer.get('title'));
                        popup.setPosition(undefined);
                        return;
                    }

                    // Build popup content
                    let content = `<h3>\${layer.get('title')}</h3>`;
                    content += '<div class=\"feature-info\">';
                    
                    // Sort properties alphabetically for consistency
                    const feature = data.features[0];
                    const properties = feature.properties;
                    const sortedKeys = Object.keys(properties).sort();
                    
                    for (const key of sortedKeys) {
                        const value = properties[key];
                        if (value !== null && value !== undefined) {
                            let displayValue = value;
                            if (typeof value === 'number') {
                                displayValue = value.toLocaleString();
                            } else if (typeof value === 'string' && value.match(/^\\d{4}-\\d{2}-\\d{2}/)) {
                                displayValue = new Date(value).toLocaleDateString();
                            }
                            content += `<dt>\${key}</dt><dd>\${displayValue}</dd>`;
                        }
                    }
                    content += '</div>';
                    
                    document.getElementById('popup-content').innerHTML = content;
                    popup.setPosition(coordinate);
                })
                .catch(error => {
                    console.error('Error fetching feature info:', error);
                    popup.setPosition(undefined);
                });
        });

        console.log('Main map created successfully');
        
        // Layer management
        const layers = new Map();
        
        // Make dataset items draggable
        document.querySelectorAll('.dataset-item').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                console.log('Drag started:', this.dataset);
                this.classList.add('dragging');
                const data = {
                    id: this.dataset.datasetId,
                    title: this.dataset.title,
                    url: this.getAttribute('data-wms-url'),
                    layer: this.getAttribute('data-wms-layer')
                };
                e.dataTransfer.setData('text/plain', JSON.stringify(data));
                e.dataTransfer.effectAllowed = 'copy';
            });

            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
        });

        // Handle map drop
        const mapContainer = document.querySelector('.map-container');
        const mapElement = document.getElementById('map');

        mapContainer.addEventListener('dragenter', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        mapContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        mapContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });

        mapContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                console.log('Dropped data:', data);
                
                if (!layers.has(data.id)) {
                    console.log('Creating new layer for:', data.title);
                    
                    // Log the WMS parameters for debugging
                    console.log('WMS URL:', data.url);
                    console.log('WMS Layer:', data.layer);
                    
                    // Create WMS source with more parameters
                    const wmsSource = new ol.source.TileWMS({
                        url: data.url,
                        params: {
                            'LAYERS': data.layer,
                            'TILED': true,
                            'FORMAT': 'image/png',
                            'TRANSPARENT': true,
                            'VERSION': '1.1.1',
                            'SERVICE': 'WMS',
                            'REQUEST': 'GetMap',
                            'STYLES': '',
                            'SRS': 'EPSG:3857'  // Use Web Mercator projection
                        },
                        serverType: 'geoserver',
                        crossOrigin: 'anonymous'
                    });

                    // Add error handling for the WMS source
                    wmsSource.on('tileloaderror', function(error) {
                        console.error('WMS tile load error:', error);
                    });

                    // Create the layer with the WMS source
                    const layer = new ol.layer.Tile({
                        source: wmsSource,
                        title: data.title,
                        opacity: 1,
                        visible: true
                    });

                    // Add the layer to the map
                    map.addLayer(layer);
                    layers.set(data.id, layer);
                    
                    // Try to get the layer's extent from GetCapabilities
                    console.log('Fetching WMS capabilities...');
                    fetch('/wms/capabilities', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ url: data.url })
                    })
                    .then(response => {
                        console.log('WMS capabilities response status:', response.status);
                        console.log('WMS capabilities response headers:', Object.fromEntries(response.headers.entries()));
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('WMS capabilities error response:', text);
                                throw new Error(`HTTP error! status: \${response.status}, body: \${text}`);
                            });
                        }
                        return response.text().then(text => {
                            console.log('WMS capabilities response text:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse WMS capabilities response as JSON:', e);
                                throw new Error('Invalid JSON response from server');
                            }
                        });
                    })
                    .then(result => {
                        console.log('WMS capabilities result:', result);
                        if (result.status === 'success') {
                            // Find the layer in the capabilities response
                            const layerInfo = result.layers.find(l => l.name === data.layer);
                            if (layerInfo && layerInfo.bbox) {
                                console.log('Found layer extent:', layerInfo.bbox);
                                // Convert bbox to OpenLayers extent format [minx, miny, maxx, maxy]
                                const extent = [
                                    layerInfo.bbox[0], // west
                                    layerInfo.bbox[1], // south
                                    layerInfo.bbox[2], // east
                                    layerInfo.bbox[3]  // north
                                ];
                                // Transform extent from EPSG:4326 to EPSG:3857
                                const transformedExtent = ol.proj.transformExtent(extent, 'EPSG:4326', 'EPSG:3857');
                                console.log('Transformed extent:', transformedExtent);
                                // Fit the view to the extent with some padding
                                map.getView().fit(transformedExtent, {
                                    padding: [50, 50, 50, 50],
                                    maxZoom: 19,
                                    duration: 1000 // Animate the zoom over 1 second
                                });
                            } else {
                                console.log('No extent found for layer:', data.layer);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching layer extent:', error);
                    });

                    console.log('Layer added successfully');
                } else {
                    console.log('Layer already exists:', data.title);
                }
            } catch (error) {
                console.error('Error adding layer:', error);
            }
        });

        // Export to PDF functionality
        document.getElementById('export-pdf').addEventListener('click', async function() {
            try {
                // Show loading state
                const button = this;
                const originalText = button.textContent;
                button.textContent = 'Exporting...';
                button.disabled = true;

                // Get the map size
                const size = map.getSize();
                
                // Create a temporary canvas
                const mapCanvas = document.createElement('canvas');
                mapCanvas.width = size[0];
                mapCanvas.height = size[1];
                
                // Render the map to the canvas
                map.once('rendercomplete', function() {
                    const mapCanvas = map.getTargetElement().querySelector('canvas');
                    if (mapCanvas) {
                        // Create PDF with the same dimensions as the map
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF({
                            orientation: 'landscape',
                            unit: 'px',
                            format: [size[0], size[1]]
                        });

                        // Add the map canvas to the first page
                        pdf.addImage(
                            mapCanvas.toDataURL('image/jpeg', 1.0),
                            'JPEG',
                            0,
                            0,
                            size[0],
                            size[1]
                        );

                        // Add a second page for metadata
                        pdf.addPage();

                        // Set up metadata page styling
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const margin = 20;
                        const lineHeight = 12;
                        let yPos = margin;

                        // Add title
                        pdf.setFontSize(16);
                        pdf.text('Map Export Information', margin, yPos);
                        yPos += lineHeight * 2;

                        // Add export date
                        pdf.setFontSize(12);
                        const exportDate = new Date().toLocaleString();
                        pdf.text(`Export Date: \${exportDate}`, margin, yPos);
                        yPos += lineHeight * 1.5;

                        // Add map extent information
                        const view = map.getView();
                        const extent = view.calculateExtent();
                        const projExtent = ol.proj.transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
                        
                        pdf.setFontSize(14);
                        pdf.text('Map Extent:', margin, yPos);
                        yPos += lineHeight;
                        
                        pdf.setFontSize(12);
                        pdf.text(`North: \${projExtent[3].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight;
                        pdf.text(`South: \${projExtent[1].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight;
                        pdf.text(`East: \${projExtent[2].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight;
                        pdf.text(`West: \${projExtent[0].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight * 1.5;

                        // Add zoom level
                        pdf.text(`Zoom Level: \${view.getZoom().toFixed(2)}`, margin, yPos);
                        yPos += lineHeight * 1.5;

                        // Add visible layers information
                        pdf.setFontSize(14);
                        pdf.text('Visible Layers:', margin, yPos);
                        yPos += lineHeight;

                        // Function to process a single layer
                        function processLayer(layer, indent = 0) {
                            const title = layer.get('title') || 'Unnamed Layer';
                            const isGroup = layer instanceof ol.layer.Group;
                            
                            if (isGroup) {
                                // For layer groups, just add the group title
                                pdf.setFontSize(12);
                                pdf.text(`\${'  '.repeat(indent)}\${title} (Layer Group)`, margin, yPos);
                                yPos += lineHeight;
                                
                                // Process child layers
                                const childLayers = layer.getLayers().getArray();
                                childLayers.forEach(childLayer => {
                                    if (childLayer.getVisible()) {
                                        processLayer(childLayer, indent + 1);
                                    }
                                });
                            } else {
                                // For regular layers
                                const type = layer instanceof ol.layer.Tile ? 'Tile Layer' : 
                                           layer instanceof ol.layer.Vector ? 'Vector Layer' : 'Layer';
                                
                                // Check if it's a WMS layer
                                const source = layer.getSource();
                                const isWMS = source instanceof ol.source.TileWMS;
                                
                                pdf.setFontSize(12);
                                pdf.text(`\${'  '.repeat(indent)}\${title} (\${type}\${isWMS ? ' - WMS' : ''})`, margin, yPos);
                                yPos += lineHeight;

                                // If it's a WMS layer, add the service URL
                                if (isWMS && source.getUrls && source.getUrls().length > 0) {
                                    const url = source.getUrls()[0];
                                    pdf.setFontSize(10);
                                    pdf.text(`\${'  '.repeat(indent + 1)}Service URL: \${url}`, margin + 10, yPos);
                                    yPos += lineHeight;
                                    pdf.setFontSize(12);
                                }
                            }
                        }

                        // Get all top-level layers and process them
                        const layers = map.getLayers().getArray();
                        layers.forEach(layer => {
                            if (layer.getVisible()) {
                                processLayer(layer);
                            }
                        });

                        // Add metadata
                        pdf.setProperties({
                            title: 'GIS Map Export',
                            subject: 'Map export from GIS Viewer',
                            author: 'GIS Viewer',
                            keywords: 'map, gis, export',
                            creator: 'GIS Viewer',
                            creationDate: new Date()
                        });

                        // Save the PDF
                        pdf.save('map-export.pdf');
                        
                        // Restore button state
                        button.textContent = originalText;
                        button.disabled = false;
                    }
                });

                // Trigger a render
                map.renderSync();

            } catch (error) {
                console.error('Error exporting PDF:', error);
                alert('Error exporting PDF. Please try again.');
                
                // Restore button state
                const button = document.getElementById('export-pdf');
                button.textContent = 'Export to PDF';
                button.disabled = false;
            }
        });

    } catch (e) {
        console.error('Main map error:', e);
    }
}

// Try to initialize when DOM is ready
console.log('Setting up initialization...');
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMap);
} else {
    initMap();
}
</script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "viewer.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  418 => 308,  411 => 307,  386 => 285,  377 => 282,  373 => 281,  369 => 280,  365 => 279,  361 => 278,  357 => 276,  353 => 275,  329 => 253,  322 => 252,  72 => 6,  65 => 5,  54 => 3,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}GIS Data Viewer{% endblock %}

{% block extra_css %}
{{ parent() }}
<!-- OpenLayers CSS -->
<link rel=\"stylesheet\" href=\"https://unpkg.com/ol@7.4.0/ol.css\">
<!-- Layer Switcher CSS -->
<link rel=\"stylesheet\" href=\"https://unpkg.com/ol-layerswitcher@4.1.1/dist/ol-layerswitcher.css\">
<!-- Font Awesome -->
<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
<!-- html2canvas -->
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js\"></script>
<!-- jsPDF -->
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js\"></script>
<style>
    /* Reset and base styles */
    .content-wrapper {
        position: relative;
        height: calc(100vh - 0px);
        margin-top: 0px;
        width: 100%;
    }

    /* Main content area */
    .content {
        display: flex;
        height: 100%;
        width: 100%;
        overflow: hidden;
    }

    /* Sidebar styles */
    .sidebar {
        width: 300px;
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding: 1rem;
        background: #e9ecef;
        border-bottom: 1px solid #dee2e6;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    /* Map container styles */
    .map-container {
        flex: 1;
        position: relative;
        height: 100%;
        background: #f0f0f0;
        overflow: hidden;
        min-width: 0; /* Allow container to shrink below its content size */
    }

    #map {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    /* Test map container */
    #test-map {
        width: 200px;
        height: 150px;
        border: 1px solid #ccc;
        margin: 10px;
        background: #fff;
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 1000;
        display: none; /* Hide the test map since we don't need it anymore */
    }

    /* Dataset list styles */
    .dataset-list {
        list-style: none;
    }

    .dataset-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: move;
        transition: all 0.2s;
        user-select: none;
    }

    .dataset-item:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .dataset-item.dragging {
        opacity: 0.5;
        background: #e9ecef;
    }

    /* Search box styles */
    .search-box {
        position: relative;
        margin-bottom: 1rem;
    }

    .search-box input {
        width: 100%;
        padding: 0.5rem;
        padding-right: 2rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .search-box button {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
    }

    /* Debug panel styles */
    .debug-panel {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 0.5rem;
        font-family: monospace;
        font-size: 0.8rem;
        z-index: 1000;
    }

    /* Export button styles */
    .export-button {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        padding: 8px 16px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        cursor: pointer;
        font-size: 14px;
        color: #333;
        transition: all 0.2s ease;
    }

    .export-button:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .export-button:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Popup styles */
    .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 280px;
        max-width: 400px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
    }

    .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: \" \";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
    }

    .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
    }

    .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
        z-index: 1001;
    }

    .ol-popup-closer:after {
        content: \"✖\";
    }

    .feature-info {
        margin: 0;
        padding: 0;
    }
    .feature-info dt {
        font-weight: bold;
        margin-top: 8px;
    }
    .feature-info dd {
        margin-left: 0;
        margin-bottom: 4px;
    }
</style>
{% endblock %}

{% block content %}
<style>
    /* Override base template main element */
    main.container-fluid {
        max-width: 1600px !important;
        margin: 0 auto !important;
    }
    .content-wrapper {
        margin: 0 -1rem;  /* Negative margin to allow content to extend full width */
        width: calc(100% + 2rem);  /* Compensate for the negative margin */
    }
</style>
<div class=\"content-wrapper\">
    <div class=\"content\">
        <div class=\"sidebar\">
            <div class=\"sidebar-header\">
                <div class=\"search-box\">
                    <input type=\"text\" id=\"dataset-search\" placeholder=\"Search datasets...\">
                    <button id=\"search-reset\" title=\"Clear search\">×</button>
                </div>
            </div>
            <div class=\"sidebar-content\">
                <ul class=\"dataset-list\" id=\"dataset-list\">
                    {% for dataset in datasets %}
                    <li class=\"dataset-item\" 
                        draggable=\"true\"
                        data-dataset-id=\"{{ dataset.id }}\"
                        data-title=\"{{ dataset.title }}\"
                        data-wms-url=\"{{ dataset.wmsUrl }}\"
                        data-wms-layer=\"{{ dataset.wmsLayer }}\">
                        {{ dataset.title }}
                    </li>
                    {% endfor %}
                </ul>
                <div class=\"pagination-controls\" style=\"margin-top: 1rem; text-align: center;\">
                    <button id=\"prev-page\" class=\"btn btn-sm btn-outline-secondary\" style=\"margin-right: 0.5rem;\">Previous</button>
                    <span id=\"page-info\" style=\"margin: 0 0.5rem;\">Page 1</span>
                    <button id=\"next-page\" class=\"btn btn-sm btn-outline-secondary\" style=\"margin-left: 0.5rem;\">Next</button>
                </div>
            </div>
        </div>
        <div class=\"map-container\">
            <div id=\"map\"></div>
            <button id=\"export-pdf\" class=\"export-button\" title=\"Export current map view to PDF\">
                <i class=\"fas fa-file-pdf\"></i> Export PDF
            </button>
            <div id=\"popup\" class=\"ol-popup\">
                <a href=\"#\" id=\"popup-closer\" class=\"ol-popup-closer\"></a>
                <div id=\"popup-content\"></div>
            </div>
        </div>
    </div>
</div>
{% endblock %}

{% block extra_js %}
{{ parent() }}
<!-- OpenLayers -->
<script src=\"https://unpkg.com/ol@7.4.0/dist/ol.js\"></script>
<!-- Layer Switcher -->
<script src=\"https://unpkg.com/ol-layerswitcher@4.1.1/dist/ol-layerswitcher.js\"></script>

<script>
// Store all dataset items in memory
let allDatasets = [];
let filteredDatasets = [];
const ITEMS_PER_PAGE = 12;
let currentPage = 1;

// Function to update the dataset list display
function updateDatasetList() {
    const datasetList = document.getElementById('dataset-list');
    const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
    const endIndex = startIndex + ITEMS_PER_PAGE;
    const totalPages = Math.ceil(filteredDatasets.length / ITEMS_PER_PAGE);
    
    // Clear the current list
    datasetList.innerHTML = '';
    
    // Add the items for the current page
    filteredDatasets.slice(startIndex, endIndex).forEach(item => {
        const li = document.createElement('li');
        li.className = 'dataset-item';
        li.draggable = true;
        li.dataset.datasetId = item.dataset.datasetId;
        li.dataset.title = item.dataset.title;
        li.dataset.wmsUrl = item.dataset.wmsUrl;
        li.dataset.wmsLayer = item.dataset.wmsLayer;
        li.textContent = item.dataset.title;
        
        // Add drag event listeners
        li.addEventListener('dragstart', handleDragStart);
        li.addEventListener('dragend', handleDragEnd);
        
        datasetList.appendChild(li);
    });
    
    // Update page info
    document.getElementById('page-info').textContent = `Page \${currentPage} of \${totalPages}`;
    
    // Update button states
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages || totalPages === 0;
}

// Initialize pagination and search
document.addEventListener('DOMContentLoaded', function() {
    // Store all dataset items
    allDatasets = Array.from(document.querySelectorAll('.dataset-item')).map(item => ({
        element: item,
        dataset: {
            datasetId: item.dataset.datasetId,
            title: item.dataset.title,
            wmsUrl: item.dataset.wmsUrl,
            wmsLayer: item.dataset.wmsLayer
        }
    }));
    filteredDatasets = [...allDatasets];
    
    // Set up pagination controls
    document.getElementById('prev-page').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateDatasetList();
        }
    });
    
    document.getElementById('next-page').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredDatasets.length / ITEMS_PER_PAGE);
        if (currentPage < totalPages) {
            currentPage++;
            updateDatasetList();
        }
    });
    
    // Set up search functionality
    const searchInput = document.getElementById('dataset-search');
    const searchReset = document.getElementById('search-reset');
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        filteredDatasets = allDatasets.filter(item => 
            item.dataset.title.toLowerCase().includes(searchTerm)
        );
        
        // Reset to first page when searching
        currentPage = 1;
        updateDatasetList();
    });
    
    if (searchReset) {
        searchReset.addEventListener('click', function() {
            searchInput.value = '';
            filteredDatasets = [...allDatasets];
            currentPage = 1;
            updateDatasetList();
        });
    }
    
    // Initial update
    updateDatasetList();
});

// Drag and drop handlers
function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', JSON.stringify({
        id: e.target.dataset.datasetId,
        title: e.target.dataset.title,
        url: e.target.dataset.wmsUrl,
        layer: e.target.dataset.wmsLayer
    }));
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

// Wait for both DOM and OpenLayers to be ready
function initMap() {
    console.log('Initializing map...');

    // First try a simple map to test OpenLayers
    try {
        console.log('Creating test map...');
        const testMap = new ol.Map({
            target: 'test-map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            view: new ol.View({
                center: [0, 0],
                zoom: 2
            })
        });
        console.log('Test map created successfully');
    } catch (e) {
        console.error('Test map error:', e);
        return;
    }

    // If test map works, try the main map
    try {
        console.log('Creating main map...');
        
        // Initialize the map
        const map = new ol.Map({
            target: 'map',
            layers: [
                // Create a layer group for base layers
                new ol.layer.Group({
                    title: 'Base Maps',
                    layers: [
                        new ol.layer.Tile({
                            source: new ol.source.OSM({
                                crossOrigin: 'anonymous'
                            }),
                            title: 'OpenStreetMap',
                            type: 'base',
                            visible: true
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.XYZ({
                                url: 'https://{a-d}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
                                attributions: '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors © <a href=\"https://carto.com/attributions\">CARTO</a>',
                                crossOrigin: 'anonymous'
                            }),
                            title: 'Carto Light',
                            type: 'base',
                            visible: false
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.XYZ({
                                url: 'https://{a-d}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
                                attributions: '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors © <a href=\"https://carto.com/attributions\">CARTO</a>',
                                crossOrigin: 'anonymous'
                            }),
                            title: 'Carto Dark',
                            type: 'base',
                            visible: false
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.XYZ({
                                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                                attributions: '© <a href=\"https://www.esri.com/\">Esri</a>',
                                crossOrigin: 'anonymous'
                            }),
                            title: 'ESRI Satellite',
                            type: 'base',
                            visible: false
                        })
                    ]
                })
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat([0, 0]),
                zoom: 2
            }),
            controls: [
                // Default controls
                new ol.control.Zoom(),
                new ol.control.Attribution({
                    collapsible: false
                }),
                // Additional controls
                new ol.control.ScaleLine({
                    units: 'metric'
                }),
                new ol.control.FullScreen(),
                new ol.control.MousePosition({
                    coordinateFormat: ol.coordinate.createStringXY(4),
                    projection: 'EPSG:4326'
                }),
                // Add the layer switcher control
                new ol.control.LayerSwitcher({
                    tipLabel: 'Layers',
                    groupSelectStyle: 'group',
                    activationMode: 'click',
                    startActive: false
                })
            ]
        });

        // Create popup overlay
        const popup = new ol.Overlay({
            element: document.getElementById('popup'),
            positioning: 'bottom-center',
            stopEvent: false,
            offset: [0, -10]
        });
        map.addOverlay(popup);

        // Close popup when clicking the closer
        document.getElementById('popup-closer').onclick = function() {
            popup.setPosition(undefined);
            return false;
        };

        // Add click interaction
        map.on('click', function(evt) {
            // Clear any existing popup
            popup.setPosition(undefined);

            // Get all visible WMS layers and their z-indices
            const visibleLayers = Array.from(layers.values())
                .filter(layer => layer.getVisible() && layer.getSource() instanceof ol.source.TileWMS)
                .map(layer => ({
                    layer,
                    zIndex: layer.getZIndex() || 0
                }))
                .sort((a, b) => b.zIndex - a.zIndex); // Sort by z-index, highest first

            if (visibleLayers.length === 0) {
                console.log('No visible WMS layers found');
                return;
            }

            // Get the map's current view state
            const viewState = map.getView().getState();
            const resolution = viewState.resolution;
            const rotation = viewState.rotation;
            const center = viewState.center;

            // Find the topmost layer that has data at the clicked point
            let clickedLayer = null;
            const pixel = evt.pixel;
            const coordinate = evt.coordinate;

            // Try each layer in order of z-index (top to bottom)
            for (const {layer} of visibleLayers) {
                const source = layer.getSource();
                const params = source.getParams();
                const baseUrl = source.getUrls ? source.getUrls()[0] : source.url_;
                
                if (!baseUrl) continue;

                // Create a test request to check if the layer has data at this point
                const testParams = {
                    'REQUEST': 'GetFeatureInfo',
                    'SERVICE': 'WMS',
                    'VERSION': '1.3.0',
                    'LAYERS': params.LAYERS,
                    'QUERY_LAYERS': params.LAYERS,
                    'INFO_FORMAT': 'application/json',
                    'FEATURE_COUNT': 1,
                    'EXCEPTIONS': 'XML',
                    'STYLES': params.STYLES || '',
                    'FORMAT': 'image/png',
                    'TRANSPARENT': true,
                    'CRS': viewState.projection.getCode(),
                    'I': Math.round(pixel[0]),
                    'J': Math.round(pixel[1]),
                    'WIDTH': map.getSize()[0],
                    'HEIGHT': map.getSize()[1],
                    'BBOX': map.getView().calculateExtent(map.getSize()).join(','),
                    'BUFFER': 5
                };

                const queryString = Object.entries(testParams)
                    .map(([key, value]) => `\${encodeURIComponent(key)}=\${encodeURIComponent(value)}`)
                    .join('&');
                const url = `\${baseUrl}\${baseUrl.includes('?') ? '&' : '?'}\${queryString}`;

                try {
                    // Make a synchronous request to check for features
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', url, false); // false makes it synchronous
                    xhr.send();

                    if (xhr.status === 200) {
                        const contentType = xhr.getResponseHeader('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const data = JSON.parse(xhr.responseText);
                            if (data.features && data.features.length > 0) {
                                clickedLayer = layer;
                                break;
                            }
                        }
                    }
                } catch (error) {
                    console.log(`Error checking layer \${layer.get('title')}:`, error);
                    continue;
                }
            }

            if (!clickedLayer) {
                console.log('No features found at clicked position');
                return;
            }

            // Process the clicked layer
            const layer = clickedLayer;
            const source = layer.getSource();
            const params = source.getParams();
            
            // Get the WMS URL from the source configuration
            const baseUrl = source.getUrls ? source.getUrls()[0] : source.url_;
            if (!baseUrl) {
                console.log('No WMS URL found for layer:', layer.get('title'));
                return;
            }

            // Use consistent WMS version and format
            const version = '1.3.0';  // Always use WMS 1.3.0
            const format = 'application/json';  // Always use JSON format
            
            const mapSize = map.getSize();
            const extent = map.getView().calculateExtent(map.getSize());
            
            const wmsParams = {
                'REQUEST': 'GetFeatureInfo',
                'SERVICE': 'WMS',
                'VERSION': version,
                'LAYERS': params.LAYERS,
                'QUERY_LAYERS': params.LAYERS,
                'INFO_FORMAT': format,
                'FEATURE_COUNT': 10,
                'EXCEPTIONS': 'XML',
                'STYLES': params.STYLES || '',
                'FORMAT': 'image/png',
                'TRANSPARENT': true,
                'CRS': viewState.projection.getCode(),
                'I': Math.round(pixel[0]),
                'J': Math.round(pixel[1]),
                'WIDTH': mapSize[0],
                'HEIGHT': mapSize[1],
                'BBOX': extent.join(','),
                'BUFFER': 10
            };

            // Construct the URL
            const queryString = Object.entries(wmsParams)
                .map(([key, value]) => `\${encodeURIComponent(key)}=\${encodeURIComponent(value)}`)
                .join('&');
            const url = `\${baseUrl}\${baseUrl.includes('?') ? '&' : '?'}\${queryString}`;

            // Make a single request with consistent parameters
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: \${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Expected JSON response but got ' + contentType);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.exceptionCode) {
                        throw new Error(`WMS Exception: \${data.exceptionText || data.message || 'Unknown error'}`);
                    }
                    
                    if (!data.features || data.features.length === 0) {
                        console.log('No features found for layer:', layer.get('title'));
                        popup.setPosition(undefined);
                        return;
                    }

                    // Build popup content
                    let content = `<h3>\${layer.get('title')}</h3>`;
                    content += '<div class=\"feature-info\">';
                    
                    // Sort properties alphabetically for consistency
                    const feature = data.features[0];
                    const properties = feature.properties;
                    const sortedKeys = Object.keys(properties).sort();
                    
                    for (const key of sortedKeys) {
                        const value = properties[key];
                        if (value !== null && value !== undefined) {
                            let displayValue = value;
                            if (typeof value === 'number') {
                                displayValue = value.toLocaleString();
                            } else if (typeof value === 'string' && value.match(/^\\d{4}-\\d{2}-\\d{2}/)) {
                                displayValue = new Date(value).toLocaleDateString();
                            }
                            content += `<dt>\${key}</dt><dd>\${displayValue}</dd>`;
                        }
                    }
                    content += '</div>';
                    
                    document.getElementById('popup-content').innerHTML = content;
                    popup.setPosition(coordinate);
                })
                .catch(error => {
                    console.error('Error fetching feature info:', error);
                    popup.setPosition(undefined);
                });
        });

        console.log('Main map created successfully');
        
        // Layer management
        const layers = new Map();
        
        // Make dataset items draggable
        document.querySelectorAll('.dataset-item').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                console.log('Drag started:', this.dataset);
                this.classList.add('dragging');
                const data = {
                    id: this.dataset.datasetId,
                    title: this.dataset.title,
                    url: this.getAttribute('data-wms-url'),
                    layer: this.getAttribute('data-wms-layer')
                };
                e.dataTransfer.setData('text/plain', JSON.stringify(data));
                e.dataTransfer.effectAllowed = 'copy';
            });

            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
        });

        // Handle map drop
        const mapContainer = document.querySelector('.map-container');
        const mapElement = document.getElementById('map');

        mapContainer.addEventListener('dragenter', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        mapContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        mapContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });

        mapContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                console.log('Dropped data:', data);
                
                if (!layers.has(data.id)) {
                    console.log('Creating new layer for:', data.title);
                    
                    // Log the WMS parameters for debugging
                    console.log('WMS URL:', data.url);
                    console.log('WMS Layer:', data.layer);
                    
                    // Create WMS source with more parameters
                    const wmsSource = new ol.source.TileWMS({
                        url: data.url,
                        params: {
                            'LAYERS': data.layer,
                            'TILED': true,
                            'FORMAT': 'image/png',
                            'TRANSPARENT': true,
                            'VERSION': '1.1.1',
                            'SERVICE': 'WMS',
                            'REQUEST': 'GetMap',
                            'STYLES': '',
                            'SRS': 'EPSG:3857'  // Use Web Mercator projection
                        },
                        serverType: 'geoserver',
                        crossOrigin: 'anonymous'
                    });

                    // Add error handling for the WMS source
                    wmsSource.on('tileloaderror', function(error) {
                        console.error('WMS tile load error:', error);
                    });

                    // Create the layer with the WMS source
                    const layer = new ol.layer.Tile({
                        source: wmsSource,
                        title: data.title,
                        opacity: 1,
                        visible: true
                    });

                    // Add the layer to the map
                    map.addLayer(layer);
                    layers.set(data.id, layer);
                    
                    // Try to get the layer's extent from GetCapabilities
                    console.log('Fetching WMS capabilities...');
                    fetch('/wms/capabilities', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ url: data.url })
                    })
                    .then(response => {
                        console.log('WMS capabilities response status:', response.status);
                        console.log('WMS capabilities response headers:', Object.fromEntries(response.headers.entries()));
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('WMS capabilities error response:', text);
                                throw new Error(`HTTP error! status: \${response.status}, body: \${text}`);
                            });
                        }
                        return response.text().then(text => {
                            console.log('WMS capabilities response text:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse WMS capabilities response as JSON:', e);
                                throw new Error('Invalid JSON response from server');
                            }
                        });
                    })
                    .then(result => {
                        console.log('WMS capabilities result:', result);
                        if (result.status === 'success') {
                            // Find the layer in the capabilities response
                            const layerInfo = result.layers.find(l => l.name === data.layer);
                            if (layerInfo && layerInfo.bbox) {
                                console.log('Found layer extent:', layerInfo.bbox);
                                // Convert bbox to OpenLayers extent format [minx, miny, maxx, maxy]
                                const extent = [
                                    layerInfo.bbox[0], // west
                                    layerInfo.bbox[1], // south
                                    layerInfo.bbox[2], // east
                                    layerInfo.bbox[3]  // north
                                ];
                                // Transform extent from EPSG:4326 to EPSG:3857
                                const transformedExtent = ol.proj.transformExtent(extent, 'EPSG:4326', 'EPSG:3857');
                                console.log('Transformed extent:', transformedExtent);
                                // Fit the view to the extent with some padding
                                map.getView().fit(transformedExtent, {
                                    padding: [50, 50, 50, 50],
                                    maxZoom: 19,
                                    duration: 1000 // Animate the zoom over 1 second
                                });
                            } else {
                                console.log('No extent found for layer:', data.layer);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching layer extent:', error);
                    });

                    console.log('Layer added successfully');
                } else {
                    console.log('Layer already exists:', data.title);
                }
            } catch (error) {
                console.error('Error adding layer:', error);
            }
        });

        // Export to PDF functionality
        document.getElementById('export-pdf').addEventListener('click', async function() {
            try {
                // Show loading state
                const button = this;
                const originalText = button.textContent;
                button.textContent = 'Exporting...';
                button.disabled = true;

                // Get the map size
                const size = map.getSize();
                
                // Create a temporary canvas
                const mapCanvas = document.createElement('canvas');
                mapCanvas.width = size[0];
                mapCanvas.height = size[1];
                
                // Render the map to the canvas
                map.once('rendercomplete', function() {
                    const mapCanvas = map.getTargetElement().querySelector('canvas');
                    if (mapCanvas) {
                        // Create PDF with the same dimensions as the map
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF({
                            orientation: 'landscape',
                            unit: 'px',
                            format: [size[0], size[1]]
                        });

                        // Add the map canvas to the first page
                        pdf.addImage(
                            mapCanvas.toDataURL('image/jpeg', 1.0),
                            'JPEG',
                            0,
                            0,
                            size[0],
                            size[1]
                        );

                        // Add a second page for metadata
                        pdf.addPage();

                        // Set up metadata page styling
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const margin = 20;
                        const lineHeight = 12;
                        let yPos = margin;

                        // Add title
                        pdf.setFontSize(16);
                        pdf.text('Map Export Information', margin, yPos);
                        yPos += lineHeight * 2;

                        // Add export date
                        pdf.setFontSize(12);
                        const exportDate = new Date().toLocaleString();
                        pdf.text(`Export Date: \${exportDate}`, margin, yPos);
                        yPos += lineHeight * 1.5;

                        // Add map extent information
                        const view = map.getView();
                        const extent = view.calculateExtent();
                        const projExtent = ol.proj.transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
                        
                        pdf.setFontSize(14);
                        pdf.text('Map Extent:', margin, yPos);
                        yPos += lineHeight;
                        
                        pdf.setFontSize(12);
                        pdf.text(`North: \${projExtent[3].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight;
                        pdf.text(`South: \${projExtent[1].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight;
                        pdf.text(`East: \${projExtent[2].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight;
                        pdf.text(`West: \${projExtent[0].toFixed(6)}°`, margin, yPos);
                        yPos += lineHeight * 1.5;

                        // Add zoom level
                        pdf.text(`Zoom Level: \${view.getZoom().toFixed(2)}`, margin, yPos);
                        yPos += lineHeight * 1.5;

                        // Add visible layers information
                        pdf.setFontSize(14);
                        pdf.text('Visible Layers:', margin, yPos);
                        yPos += lineHeight;

                        // Function to process a single layer
                        function processLayer(layer, indent = 0) {
                            const title = layer.get('title') || 'Unnamed Layer';
                            const isGroup = layer instanceof ol.layer.Group;
                            
                            if (isGroup) {
                                // For layer groups, just add the group title
                                pdf.setFontSize(12);
                                pdf.text(`\${'  '.repeat(indent)}\${title} (Layer Group)`, margin, yPos);
                                yPos += lineHeight;
                                
                                // Process child layers
                                const childLayers = layer.getLayers().getArray();
                                childLayers.forEach(childLayer => {
                                    if (childLayer.getVisible()) {
                                        processLayer(childLayer, indent + 1);
                                    }
                                });
                            } else {
                                // For regular layers
                                const type = layer instanceof ol.layer.Tile ? 'Tile Layer' : 
                                           layer instanceof ol.layer.Vector ? 'Vector Layer' : 'Layer';
                                
                                // Check if it's a WMS layer
                                const source = layer.getSource();
                                const isWMS = source instanceof ol.source.TileWMS;
                                
                                pdf.setFontSize(12);
                                pdf.text(`\${'  '.repeat(indent)}\${title} (\${type}\${isWMS ? ' - WMS' : ''})`, margin, yPos);
                                yPos += lineHeight;

                                // If it's a WMS layer, add the service URL
                                if (isWMS && source.getUrls && source.getUrls().length > 0) {
                                    const url = source.getUrls()[0];
                                    pdf.setFontSize(10);
                                    pdf.text(`\${'  '.repeat(indent + 1)}Service URL: \${url}`, margin + 10, yPos);
                                    yPos += lineHeight;
                                    pdf.setFontSize(12);
                                }
                            }
                        }

                        // Get all top-level layers and process them
                        const layers = map.getLayers().getArray();
                        layers.forEach(layer => {
                            if (layer.getVisible()) {
                                processLayer(layer);
                            }
                        });

                        // Add metadata
                        pdf.setProperties({
                            title: 'GIS Map Export',
                            subject: 'Map export from GIS Viewer',
                            author: 'GIS Viewer',
                            keywords: 'map, gis, export',
                            creator: 'GIS Viewer',
                            creationDate: new Date()
                        });

                        // Save the PDF
                        pdf.save('map-export.pdf');
                        
                        // Restore button state
                        button.textContent = originalText;
                        button.disabled = false;
                    }
                });

                // Trigger a render
                map.renderSync();

            } catch (error) {
                console.error('Error exporting PDF:', error);
                alert('Error exporting PDF. Please try again.');
                
                // Restore button state
                const button = document.getElementById('export-pdf');
                button.textContent = 'Export to PDF';
                button.disabled = false;
            }
        });

    } catch (e) {
        console.error('Main map error:', e);
    }
}

// Try to initialize when DOM is ready
console.log('Setting up initialization...');
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMap);
} else {
    initMap();
}
</script>
{% endblock %} ", "viewer.twig", "/var/www/novella/templates/viewer.twig");
    }
}
