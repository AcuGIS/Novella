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

/* harvest.twig */
class __TwigTemplate_8f3d5f21eb1f9dc8e075c732bed667c4 extends Template
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
        yield "GIS Harvest Management";
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
        yield "<style>
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: white;
    }

    .layer-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .layer-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .layer-item:last-child {
        border-bottom: none;
    }

    .layer-item:hover {
        background-color: #f8f9fa;
    }

    .layer-checkbox {
        margin-right: 1rem;
    }

    .layer-info {
        flex-grow: 1;
    }

    .layer-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .layer-name {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .selected-layers {
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }

    .selected-layers h3 {
        margin-bottom: 0.5rem;
        font-size: 1rem;
        font-weight: 500;
    }

    .selected-layer-tag {
        display: inline-block;
        background-color: #e9ecef;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin: 0.25rem;
        font-size: 0.875rem;
    }

    .selected-layer-tag button {
        margin-left: 0.5rem;
        color: #dc3545;
        border: none;
        background: none;
        cursor: pointer;
        padding: 0;
    }

    .settings-list {
        margin-top: 2rem;
    }

    .settings-item {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .settings-name {
        font-weight: 500;
        font-size: 1.1rem;
    }

    .settings-actions {
        display: flex;
        gap: 0.5rem;
    }

    .settings-details {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .settings-status {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        position: relative;
        background-color: white;
        margin: 10% auto;
        padding: 2rem;
        width: 90%;
        max-width: 600px;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6c757d;
    }

    .modal-close:hover {
        color: #343a40;
    }
</style>
";
        yield from [];
    }

    // line 177
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 178
        yield "    <h1 class=\"text-3xl font-bold mb-8\">GIS Harvest Management</h1>
    
    <!-- Harvest Settings -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Harvest Settings</h2>
        </div>
        <div class=\"p-6\">
            <form id=\"harvestSettingsForm\" action=\"/harvest/settings\" method=\"POST\">
                <div class=\"mb-4\">
                    <label for=\"name\" class=\"block text-sm font-medium text-gray-700 mb-1\">Harvest Name</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"name\" name=\"name\" value=\"";
        // line 190
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "name", [], "any", true, true, false, 190)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "name", [], "any", false, false, false, 190), "")) : ("")), "html", null, true);
        yield "\" required>
                </div>
                <div class=\"mb-4\">
                    <label for=\"wms_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">WMS URL</label>
                    <div class=\"flex gap-2\">
                        <input type=\"url\" class=\"flex-grow px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                               id=\"wms_url\" name=\"wms_url\" value=\"";
        // line 196
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "wms_url", [], "any", true, true, false, 196)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "wms_url", [], "any", false, false, false, 196), "")) : ("")), "html", null, true);
        yield "\" required>
                        <button type=\"button\" id=\"fetchLayersBtn\" class=\"px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                            Fetch Layers
                        </button>
                    </div>
                </div>
                <div class=\"mb-4\">
                    <label for=\"interval_minutes\" class=\"block text-sm font-medium text-gray-700 mb-1\">Harvest Interval (minutes)</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"interval_minutes\" name=\"interval_minutes\" value=\"";
        // line 205
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "interval_minutes", [], "any", true, true, false, 205)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "interval_minutes", [], "any", false, false, false, 205), "60")) : ("60")), "html", null, true);
        yield "\" min=\"1\" required>
                </div>
                <div class=\"mb-4\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-1\">Available Layers</label>
                    <div id=\"layerList\" class=\"layer-list\">
                        <div class=\"text-gray-500 italic\">Enter a WMS URL and click \"Fetch Layers\" to see available layers</div>
                    </div>
                    <div id=\"selectedLayers\" class=\"selected-layers mt-4\" style=\"display: none;\">
                        <h3>Selected Layers</h3>
                        <div id=\"selectedLayersList\"></div>
                    </div>
                    <input type=\"hidden\" id=\"layers\" name=\"layers\" value=\"";
        // line 216
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::default(json_encode(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "layers", [], "any", false, false, false, 216)), "[]"), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"harvest_username\" class=\"block text-sm font-medium text-gray-700 mb-1\">Username (if required)</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"harvest_username\" name=\"harvest_username\" value=\"";
        // line 221
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "harvest_username", [], "any", true, true, false, 221)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "harvest_username", [], "any", false, false, false, 221), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"harvest_password\" class=\"block text-sm font-medium text-gray-700 mb-1\">Password (if required)</label>
                    <input type=\"password\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"harvest_password\" name=\"harvest_password\" value=\"";
        // line 226
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "harvest_password", [], "any", true, true, false, 226)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["settings"] ?? null), "harvest_password", [], "any", false, false, false, 226), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <button type=\"submit\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Manual Harvest -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Manual Harvest</h2>
        </div>
        <div class=\"p-6\">
            <button id=\"startHarvestBtn\" class=\"bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2\">
                Start Harvest
            </button>
            <div id=\"harvestStatus\" class=\"mt-4 text-sm text-gray-600\"></div>
        </div>
    </div>

    <!-- Harvest History -->
    <div class=\"bg-white rounded-lg\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Harvest History</h2>
        </div>
        <div class=\"overflow-x-auto\">
            <table class=\"min-w-full divide-y divide-gray-200\">
                <thead class=\"bg-gray-50\">
                    <tr>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Name</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Start Time</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">End Time</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Status</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Records Processed</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Message</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-right text-sm font-medium\">Actions</th>
                    </tr>
                </thead>
                <tbody class=\"bg-white divide-y divide-gray-200\">
                    ";
        // line 267
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["harvests"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["harvest"]) {
            // line 268
            yield "                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">";
            // line 269
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "name", [], "any", false, false, false, 269), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">";
            // line 270
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "start_time", [], "any", false, false, false, 270), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">";
            // line 271
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "end_time", [], "any", true, true, false, 271)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "end_time", [], "any", false, false, false, 271), "-")) : ("-")), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm\">
                                <span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ";
            // line 274
            if ((CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "status", [], "any", false, false, false, 274) == "completed")) {
                // line 275
                yield "                                        bg-green-100 text-green-800
                                    ";
            } elseif ((CoreExtension::getAttribute($this->env, $this->source,             // line 276
$context["harvest"], "status", [], "any", false, false, false, 276) == "failed")) {
                // line 277
                yield "                                        bg-red-100 text-red-800
                                    ";
            } else {
                // line 279
                yield "                                        bg-yellow-100 text-yellow-800
                                    ";
            }
            // line 280
            yield "\">
                                    ";
            // line 281
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "status", [], "any", false, false, false, 281), "html", null, true);
            yield "
                                </span>
                            </td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">";
            // line 284
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "records_processed", [], "any", true, true, false, 284)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "records_processed", [], "any", false, false, false, 284), "0")) : ("0")), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 text-sm text-gray-500\">";
            // line 285
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "message", [], "any", true, true, false, 285)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "message", [], "any", false, false, false, 285), "-")) : ("-")), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                                <button onclick=\"editHarvest(";
            // line 287
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "id", [], "any", false, false, false, 287), "html", null, true);
            yield ")\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</button>
                                <button onclick=\"runHarvest(";
            // line 288
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "id", [], "any", false, false, false, 288), "html", null, true);
            yield ")\" class=\"text-green-600 hover:text-green-900 mr-3\">Run</button>
                                <button onclick=\"confirmDeleteHarvest(";
            // line 289
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["harvest"], "id", [], "any", false, false, false, 289), "html", null, true);
            yield ")\" class=\"text-red-600 hover:text-red-900\">Delete</button>
                            </td>
                        </tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['harvest'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 293
        yield "                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id=\"deleteHarvestModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
        <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
            <div class=\"mt-3 text-center\">
                <h3 class=\"text-lg leading-6 font-medium text-gray-900\">Delete Harvest</h3>
                <div class=\"mt-2 px-7 py-3\">
                    <p class=\"text-sm text-gray-500\">
                        Are you sure you want to delete this harvest? This will also delete all datasets associated with this harvest.
                    </p>
                </div>
                <div class=\"items-center px-4 py-3\">
                    <button id=\"confirmDeleteHarvestBtn\" class=\"px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 mr-2\">
                        Delete
                    </button>
                    <button onclick=\"closeDeleteHarvestModal()\" class=\"px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500\">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let availableLayers = {};
        let selectedLayers = new Set();
        let harvestToDelete = null;

        // Function to update the selected layers display
        function updateSelectedLayers() {
            const container = document.getElementById('selectedLayersList');
            const hiddenInput = document.getElementById('layers');
            container.innerHTML = '';
            
            if (selectedLayers.size === 0) {
                document.getElementById('selectedLayers').style.display = 'none';
                hiddenInput.value = '[]';
                return;
            }

            document.getElementById('selectedLayers').style.display = 'block';
            selectedLayers.forEach(layerName => {
                const tag = document.createElement('span');
                tag.className = 'selected-layer-tag';
                // Properly escape the layer name for HTML and JSON
                const escapedLayerName = layerName.replace(/\"/g, '&quot;');
                tag.innerHTML = `
                    \${escapedLayerName}
                    <button type=\"button\" onclick=\"toggleLayer('\${escapedLayerName}')\" class=\"text-red-600 hover:text-red-800\">
                        ×
                    </button>
                `;
                container.appendChild(tag);
            });

            // Store layers as a proper JSON array
            hiddenInput.value = JSON.stringify(Array.from(selectedLayers));
        }

        // Function to toggle layer selection
        function toggleLayer(layerName) {
            // Decode HTML entities when comparing layer names
            const decodedLayerName = layerName.replace(/&quot;/g, '\"');
            if (selectedLayers.has(decodedLayerName)) {
                selectedLayers.delete(decodedLayerName);
            } else {
                selectedLayers.add(decodedLayerName);
            }
            updateSelectedLayers();
        }

        // Function to fetch and display available layers
        async function fetchLayers() {
            const wmsUrl = document.getElementById('wms_url').value;
            if (!wmsUrl) {
                alert('Please enter a WMS URL first');
                return;
            }

            const layerList = document.getElementById('layerList');
            layerList.innerHTML = '<div class=\"text-gray-500\">Fetching layers...</div>';

            try {
                const response = await fetch('/harvest/layers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ wms_url: wmsUrl })
                });

                const result = await response.json();
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to fetch layers');
                }

                availableLayers = result.layers;
                layerList.innerHTML = '';

                if (Object.keys(availableLayers).length === 0) {
                    layerList.innerHTML = '<div class=\"text-gray-500 italic\">No layers found in this WMS service</div>';
                    return;
                }

                Object.entries(availableLayers).forEach(([layerName, layerInfo]) => {
                    const div = document.createElement('div');
                    div.className = 'layer-item';
                    // Properly escape the layer name for HTML
                    const escapedLayerName = layerName.replace(/\"/g, '&quot;');
                    div.innerHTML = `
                        <input type=\"checkbox\" 
                               class=\"layer-checkbox\" 
                               id=\"layer_\${escapedLayerName}\" 
                               \${selectedLayers.has(layerName) ? 'checked' : ''}
                               onchange=\"toggleLayer('\${escapedLayerName}')\">
                        <div class=\"layer-info\">
                            <div class=\"layer-title\">\${layerInfo.title || layerName}</div>
                            <div class=\"layer-name\">\${layerName}</div>
                        </div>
                    `;
                    layerList.appendChild(div);
                });

                // Restore previously selected layers
                const hiddenInput = document.getElementById('layers');
                try {
                    const savedLayers = JSON.parse(hiddenInput.value);
                    if (Array.isArray(savedLayers)) {
                        savedLayers.forEach(layer => {
                            if (availableLayers[layer]) {
                                selectedLayers.add(layer);
                            }
                        });
                        updateSelectedLayers();
                    }
                } catch (e) {
                    console.error('Error parsing saved layers:', e);
                }

            } catch (error) {
                layerList.innerHTML = `<div class=\"text-red-500\">Error: \${error.message}</div>`;
            }
        }

        // Add event listener for the fetch layers button
        document.getElementById('fetchLayersBtn').addEventListener('click', fetchLayers);

        // Function to edit harvest settings
        async function editHarvest(id) {
            try {
                const response = await fetch(`/harvest/settings/\${id}`);
                const result = await response.json();
                
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to load harvest settings');
                }

                const settings = result.data;
                
                // Add a hidden input for the harvest ID
                let idInput = document.getElementById('harvest_id');
                if (!idInput) {
                    idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.id = 'harvest_id';
                    idInput.name = 'id';
                    document.getElementById('harvestSettingsForm').appendChild(idInput);
                }
                idInput.value = id;
                
                // Populate form fields
                document.getElementById('name').value = settings.name;
                document.getElementById('wms_url').value = settings.wms_url;
                document.getElementById('interval_minutes').value = settings.interval_minutes;
                
                // Set layers
                selectedLayers = new Set(settings.layers);
                document.getElementById('layers').value = JSON.stringify(settings.layers);
                
                // Fetch and display available layers
                await fetchLayers();
                
                // Scroll to form
                document.getElementById('harvestSettingsForm').scrollIntoView({ behavior: 'smooth' });
            } catch (error) {
                alert('Error loading harvest settings: ' + error.message);
            }
        }

        // Modify the form submission to include the selected layers
        document.getElementById('harvestSettingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ensure we have selected layers
            if (selectedLayers.size === 0) {
                alert('Please select at least one layer');
                return;
            }
            
            try {
                const formData = {
                    name: document.getElementById('name').value,
                    wms_url: document.getElementById('wms_url').value,
                    interval_minutes: document.getElementById('interval_minutes').value,
                    harvest_username: document.getElementById('harvest_username').value,
                    layers: Array.from(selectedLayers)
                };

                // Include the harvest ID if it exists (for editing)
                const harvestId = document.getElementById('harvest_id')?.value;
                if (harvestId) {
                    formData.id = harvestId;
                }

                const response = await fetch('/harvest/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to save settings');
                }
            } catch (error) {
                alert('Error saving settings: ' + error.message);
            }
        });

        document.getElementById('startHarvestBtn').addEventListener('click', async function() {
            const statusDiv = document.getElementById('harvestStatus');
            statusDiv.textContent = 'Starting harvest...';
            this.disabled = true;
            
            try {
                const response = await fetch('/harvest/start', {
                    method: 'POST'
                });
                
                const result = await response.json();
                statusDiv.textContent = result.message;
                
                if (result.status === 'success') {
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                statusDiv.textContent = 'Error starting harvest: ' + error.message;
            } finally {
                this.disabled = false;
            }
        });

        // Function to run harvest
        async function runHarvest(id) {
            if (!confirm('Are you sure you want to run this harvest now?')) {
                return;
            }

            try {
                const response = await fetch(`/harvest/settings/\${id}/run`, {
                    method: 'POST'
                });
                
                const result = await response.json();
                alert(result.message || (result.status === 'success' ? 'Harvest started successfully' : 'Failed to start harvest'));
                
                if (result.status === 'success') {
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Error starting harvest: ' + error.message);
            }
        }

        function confirmDeleteHarvest(id) {
            harvestToDelete = id;
            document.getElementById('deleteHarvestModal').classList.remove('hidden');
        }

        function closeDeleteHarvestModal() {
            document.getElementById('deleteHarvestModal').classList.add('hidden');
            harvestToDelete = null;
        }

        async function deleteHarvest() {
            if (!harvestToDelete) return;

            try {
                const response = await fetch(`/harvest/settings/\${harvestToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to delete harvest');
                }
            } catch (error) {
                alert('Error deleting harvest: ' + error.message);
            } finally {
                closeDeleteHarvestModal();
            }
        }

        // Add event listener for the delete confirmation button
        document.getElementById('confirmDeleteHarvestBtn').addEventListener('click', deleteHarvest);
    </script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "harvest.twig";
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
        return array (  433 => 293,  423 => 289,  419 => 288,  415 => 287,  410 => 285,  406 => 284,  400 => 281,  397 => 280,  393 => 279,  389 => 277,  387 => 276,  384 => 275,  382 => 274,  376 => 271,  372 => 270,  368 => 269,  365 => 268,  361 => 267,  317 => 226,  309 => 221,  301 => 216,  287 => 205,  275 => 196,  266 => 190,  252 => 178,  245 => 177,  71 => 6,  64 => 5,  53 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}GIS Harvest Management{% endblock %}

{% block extra_css %}
<style>
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: white;
    }

    .layer-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .layer-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .layer-item:last-child {
        border-bottom: none;
    }

    .layer-item:hover {
        background-color: #f8f9fa;
    }

    .layer-checkbox {
        margin-right: 1rem;
    }

    .layer-info {
        flex-grow: 1;
    }

    .layer-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .layer-name {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .selected-layers {
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }

    .selected-layers h3 {
        margin-bottom: 0.5rem;
        font-size: 1rem;
        font-weight: 500;
    }

    .selected-layer-tag {
        display: inline-block;
        background-color: #e9ecef;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin: 0.25rem;
        font-size: 0.875rem;
    }

    .selected-layer-tag button {
        margin-left: 0.5rem;
        color: #dc3545;
        border: none;
        background: none;
        cursor: pointer;
        padding: 0;
    }

    .settings-list {
        margin-top: 2rem;
    }

    .settings-item {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .settings-name {
        font-weight: 500;
        font-size: 1.1rem;
    }

    .settings-actions {
        display: flex;
        gap: 0.5rem;
    }

    .settings-details {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .settings-status {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        position: relative;
        background-color: white;
        margin: 10% auto;
        padding: 2rem;
        width: 90%;
        max-width: 600px;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6c757d;
    }

    .modal-close:hover {
        color: #343a40;
    }
</style>
{% endblock %}

{% block content %}
    <h1 class=\"text-3xl font-bold mb-8\">GIS Harvest Management</h1>
    
    <!-- Harvest Settings -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Harvest Settings</h2>
        </div>
        <div class=\"p-6\">
            <form id=\"harvestSettingsForm\" action=\"/harvest/settings\" method=\"POST\">
                <div class=\"mb-4\">
                    <label for=\"name\" class=\"block text-sm font-medium text-gray-700 mb-1\">Harvest Name</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"name\" name=\"name\" value=\"{{ settings.name|default('') }}\" required>
                </div>
                <div class=\"mb-4\">
                    <label for=\"wms_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">WMS URL</label>
                    <div class=\"flex gap-2\">
                        <input type=\"url\" class=\"flex-grow px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                               id=\"wms_url\" name=\"wms_url\" value=\"{{ settings.wms_url|default('') }}\" required>
                        <button type=\"button\" id=\"fetchLayersBtn\" class=\"px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                            Fetch Layers
                        </button>
                    </div>
                </div>
                <div class=\"mb-4\">
                    <label for=\"interval_minutes\" class=\"block text-sm font-medium text-gray-700 mb-1\">Harvest Interval (minutes)</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"interval_minutes\" name=\"interval_minutes\" value=\"{{ settings.interval_minutes|default('60') }}\" min=\"1\" required>
                </div>
                <div class=\"mb-4\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-1\">Available Layers</label>
                    <div id=\"layerList\" class=\"layer-list\">
                        <div class=\"text-gray-500 italic\">Enter a WMS URL and click \"Fetch Layers\" to see available layers</div>
                    </div>
                    <div id=\"selectedLayers\" class=\"selected-layers mt-4\" style=\"display: none;\">
                        <h3>Selected Layers</h3>
                        <div id=\"selectedLayersList\"></div>
                    </div>
                    <input type=\"hidden\" id=\"layers\" name=\"layers\" value=\"{{ settings.layers|json_encode|default('[]') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"harvest_username\" class=\"block text-sm font-medium text-gray-700 mb-1\">Username (if required)</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"harvest_username\" name=\"harvest_username\" value=\"{{ settings.harvest_username|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"harvest_password\" class=\"block text-sm font-medium text-gray-700 mb-1\">Password (if required)</label>
                    <input type=\"password\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"harvest_password\" name=\"harvest_password\" value=\"{{ settings.harvest_password|default('') }}\">
                </div>
                <button type=\"submit\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Manual Harvest -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Manual Harvest</h2>
        </div>
        <div class=\"p-6\">
            <button id=\"startHarvestBtn\" class=\"bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2\">
                Start Harvest
            </button>
            <div id=\"harvestStatus\" class=\"mt-4 text-sm text-gray-600\"></div>
        </div>
    </div>

    <!-- Harvest History -->
    <div class=\"bg-white rounded-lg\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Harvest History</h2>
        </div>
        <div class=\"overflow-x-auto\">
            <table class=\"min-w-full divide-y divide-gray-200\">
                <thead class=\"bg-gray-50\">
                    <tr>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Name</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Start Time</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">End Time</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Status</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Records Processed</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Message</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-right text-sm font-medium\">Actions</th>
                    </tr>
                </thead>
                <tbody class=\"bg-white divide-y divide-gray-200\">
                    {% for harvest in harvests %}
                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">{{ harvest.name }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">{{ harvest.start_time }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">{{ harvest.end_time|default('-') }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm\">
                                <span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {% if harvest.status == 'completed' %}
                                        bg-green-100 text-green-800
                                    {% elseif harvest.status == 'failed' %}
                                        bg-red-100 text-red-800
                                    {% else %}
                                        bg-yellow-100 text-yellow-800
                                    {% endif %}\">
                                    {{ harvest.status }}
                                </span>
                            </td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">{{ harvest.records_processed|default('0') }}</td>
                            <td class=\"px-6 py-4 text-sm text-gray-500\">{{ harvest.message|default('-') }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                                <button onclick=\"editHarvest({{ harvest.id }})\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</button>
                                <button onclick=\"runHarvest({{ harvest.id }})\" class=\"text-green-600 hover:text-green-900 mr-3\">Run</button>
                                <button onclick=\"confirmDeleteHarvest({{ harvest.id }})\" class=\"text-red-600 hover:text-red-900\">Delete</button>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id=\"deleteHarvestModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
        <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
            <div class=\"mt-3 text-center\">
                <h3 class=\"text-lg leading-6 font-medium text-gray-900\">Delete Harvest</h3>
                <div class=\"mt-2 px-7 py-3\">
                    <p class=\"text-sm text-gray-500\">
                        Are you sure you want to delete this harvest? This will also delete all datasets associated with this harvest.
                    </p>
                </div>
                <div class=\"items-center px-4 py-3\">
                    <button id=\"confirmDeleteHarvestBtn\" class=\"px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 mr-2\">
                        Delete
                    </button>
                    <button onclick=\"closeDeleteHarvestModal()\" class=\"px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500\">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let availableLayers = {};
        let selectedLayers = new Set();
        let harvestToDelete = null;

        // Function to update the selected layers display
        function updateSelectedLayers() {
            const container = document.getElementById('selectedLayersList');
            const hiddenInput = document.getElementById('layers');
            container.innerHTML = '';
            
            if (selectedLayers.size === 0) {
                document.getElementById('selectedLayers').style.display = 'none';
                hiddenInput.value = '[]';
                return;
            }

            document.getElementById('selectedLayers').style.display = 'block';
            selectedLayers.forEach(layerName => {
                const tag = document.createElement('span');
                tag.className = 'selected-layer-tag';
                // Properly escape the layer name for HTML and JSON
                const escapedLayerName = layerName.replace(/\"/g, '&quot;');
                tag.innerHTML = `
                    \${escapedLayerName}
                    <button type=\"button\" onclick=\"toggleLayer('\${escapedLayerName}')\" class=\"text-red-600 hover:text-red-800\">
                        ×
                    </button>
                `;
                container.appendChild(tag);
            });

            // Store layers as a proper JSON array
            hiddenInput.value = JSON.stringify(Array.from(selectedLayers));
        }

        // Function to toggle layer selection
        function toggleLayer(layerName) {
            // Decode HTML entities when comparing layer names
            const decodedLayerName = layerName.replace(/&quot;/g, '\"');
            if (selectedLayers.has(decodedLayerName)) {
                selectedLayers.delete(decodedLayerName);
            } else {
                selectedLayers.add(decodedLayerName);
            }
            updateSelectedLayers();
        }

        // Function to fetch and display available layers
        async function fetchLayers() {
            const wmsUrl = document.getElementById('wms_url').value;
            if (!wmsUrl) {
                alert('Please enter a WMS URL first');
                return;
            }

            const layerList = document.getElementById('layerList');
            layerList.innerHTML = '<div class=\"text-gray-500\">Fetching layers...</div>';

            try {
                const response = await fetch('/harvest/layers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ wms_url: wmsUrl })
                });

                const result = await response.json();
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to fetch layers');
                }

                availableLayers = result.layers;
                layerList.innerHTML = '';

                if (Object.keys(availableLayers).length === 0) {
                    layerList.innerHTML = '<div class=\"text-gray-500 italic\">No layers found in this WMS service</div>';
                    return;
                }

                Object.entries(availableLayers).forEach(([layerName, layerInfo]) => {
                    const div = document.createElement('div');
                    div.className = 'layer-item';
                    // Properly escape the layer name for HTML
                    const escapedLayerName = layerName.replace(/\"/g, '&quot;');
                    div.innerHTML = `
                        <input type=\"checkbox\" 
                               class=\"layer-checkbox\" 
                               id=\"layer_\${escapedLayerName}\" 
                               \${selectedLayers.has(layerName) ? 'checked' : ''}
                               onchange=\"toggleLayer('\${escapedLayerName}')\">
                        <div class=\"layer-info\">
                            <div class=\"layer-title\">\${layerInfo.title || layerName}</div>
                            <div class=\"layer-name\">\${layerName}</div>
                        </div>
                    `;
                    layerList.appendChild(div);
                });

                // Restore previously selected layers
                const hiddenInput = document.getElementById('layers');
                try {
                    const savedLayers = JSON.parse(hiddenInput.value);
                    if (Array.isArray(savedLayers)) {
                        savedLayers.forEach(layer => {
                            if (availableLayers[layer]) {
                                selectedLayers.add(layer);
                            }
                        });
                        updateSelectedLayers();
                    }
                } catch (e) {
                    console.error('Error parsing saved layers:', e);
                }

            } catch (error) {
                layerList.innerHTML = `<div class=\"text-red-500\">Error: \${error.message}</div>`;
            }
        }

        // Add event listener for the fetch layers button
        document.getElementById('fetchLayersBtn').addEventListener('click', fetchLayers);

        // Function to edit harvest settings
        async function editHarvest(id) {
            try {
                const response = await fetch(`/harvest/settings/\${id}`);
                const result = await response.json();
                
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to load harvest settings');
                }

                const settings = result.data;
                
                // Add a hidden input for the harvest ID
                let idInput = document.getElementById('harvest_id');
                if (!idInput) {
                    idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.id = 'harvest_id';
                    idInput.name = 'id';
                    document.getElementById('harvestSettingsForm').appendChild(idInput);
                }
                idInput.value = id;
                
                // Populate form fields
                document.getElementById('name').value = settings.name;
                document.getElementById('wms_url').value = settings.wms_url;
                document.getElementById('interval_minutes').value = settings.interval_minutes;
                
                // Set layers
                selectedLayers = new Set(settings.layers);
                document.getElementById('layers').value = JSON.stringify(settings.layers);
                
                // Fetch and display available layers
                await fetchLayers();
                
                // Scroll to form
                document.getElementById('harvestSettingsForm').scrollIntoView({ behavior: 'smooth' });
            } catch (error) {
                alert('Error loading harvest settings: ' + error.message);
            }
        }

        // Modify the form submission to include the selected layers
        document.getElementById('harvestSettingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ensure we have selected layers
            if (selectedLayers.size === 0) {
                alert('Please select at least one layer');
                return;
            }
            
            try {
                const formData = {
                    name: document.getElementById('name').value,
                    wms_url: document.getElementById('wms_url').value,
                    interval_minutes: document.getElementById('interval_minutes').value,
                    harvest_username: document.getElementById('harvest_username').value,
                    layers: Array.from(selectedLayers)
                };

                // Include the harvest ID if it exists (for editing)
                const harvestId = document.getElementById('harvest_id')?.value;
                if (harvestId) {
                    formData.id = harvestId;
                }

                const response = await fetch('/harvest/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to save settings');
                }
            } catch (error) {
                alert('Error saving settings: ' + error.message);
            }
        });

        document.getElementById('startHarvestBtn').addEventListener('click', async function() {
            const statusDiv = document.getElementById('harvestStatus');
            statusDiv.textContent = 'Starting harvest...';
            this.disabled = true;
            
            try {
                const response = await fetch('/harvest/start', {
                    method: 'POST'
                });
                
                const result = await response.json();
                statusDiv.textContent = result.message;
                
                if (result.status === 'success') {
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                statusDiv.textContent = 'Error starting harvest: ' + error.message;
            } finally {
                this.disabled = false;
            }
        });

        // Function to run harvest
        async function runHarvest(id) {
            if (!confirm('Are you sure you want to run this harvest now?')) {
                return;
            }

            try {
                const response = await fetch(`/harvest/settings/\${id}/run`, {
                    method: 'POST'
                });
                
                const result = await response.json();
                alert(result.message || (result.status === 'success' ? 'Harvest started successfully' : 'Failed to start harvest'));
                
                if (result.status === 'success') {
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Error starting harvest: ' + error.message);
            }
        }

        function confirmDeleteHarvest(id) {
            harvestToDelete = id;
            document.getElementById('deleteHarvestModal').classList.remove('hidden');
        }

        function closeDeleteHarvestModal() {
            document.getElementById('deleteHarvestModal').classList.add('hidden');
            harvestToDelete = null;
        }

        async function deleteHarvest() {
            if (!harvestToDelete) return;

            try {
                const response = await fetch(`/harvest/settings/\${harvestToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to delete harvest');
                }
            } catch (error) {
                alert('Error deleting harvest: ' + error.message);
            } finally {
                closeDeleteHarvestModal();
            }
        }

        // Add event listener for the delete confirmation button
        document.getElementById('confirmDeleteHarvestBtn').addEventListener('click', deleteHarvest);
    </script>
{% endblock %} ", "harvest.twig", "/var/www/novella/templates/harvest.twig");
    }
}
