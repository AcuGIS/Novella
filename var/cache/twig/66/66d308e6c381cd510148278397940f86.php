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

/* about.twig */
class __TwigTemplate_b923240843e4152b050a0c7f1ef2ec13 extends Template
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
        yield "About Novella";
        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 6
        yield "<div class=\"max-w-7xl mx-auto\">
    <div class=\"bg-white rounded-lg shadow-md p-8 mb-8\">
<div class=\"flex items-center space-x-2\">
                    <img src=\"/storage/uploads/novella-logo-alt.png\" alt=\"Novella Logo\" class=\"h-8 w-auto\">
                    <a href=\"/\" class=\"text-3xl font-bold text-gray-900\">Novella</a>
                </div>
<br>
        
        <div class=\"grid grid-cols-1 lg:grid-cols-3 gap-8\">
            <!-- Main Content -->
            <div class=\"lg:col-span-2 space-y-8\">





                <!-- Overview Section -->
                <div class=\"bg-gray-50 rounded-lg p-6 border border-gray-200\">
                    <h2 class=\"text-xl font-semibold text-gray-800 mb-4 flex items-center\">
                        Overview
                    </h2>
                    <p class=\"text-gray-700 leading-relaxed\">Novella is a lightweight spatial data catalogue using ISO 19115 and INSPIRE standards. </p>
                </div>


<!-- Basic Usage Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            
                            Basic Usage
                        </h2>
                    </div>
                    <div class=\"p-6 space-y-6\">
                        <div>
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Getting Started</h3>
                            <ol class=\"space-y-2 text-gray-700\">
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">1</span>
                                    Log in to the system using your credentials
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">2</span>
                                    Navigate to \"Datasets\" to view existing metadata records
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">3</span>
                                    Use the \"New Dataset\" button to create a new metadata record
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">4</span>
                                    Fill in the required metadata fields following ISO 19115 standards
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">5</span>
                                    Save your changes and export to XML when ready
                                </li>
                            </ol>
                        </div>

                        <div>
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Key Features</h3>
                            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-3\">
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">Metadata entry form with validation</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">XML export functionality</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">Dataset management and organization</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">User management and access control</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">RESTful API access</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





 <!-- API Documentation Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-5 h-5 mr-2 text-purple-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4\"></path>
                            </svg>
                            API Documentation
                        </h2>
                    </div>
                    <div class=\"p-6 space-y-6\">
                        <div>
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Available Endpoints</h3>
                            
                            <div class=\"space-y-4\">
                                <div>
                                    <h4 class=\"text-md font-medium text-gray-700 mb-2\">Dataset Operations</h4>
                                    <div class=\"bg-gray-50 rounded-lg p-4 space-y-2\">
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets</code>
                                            <span class=\"text-gray-500 ml-2\">- List all datasets</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets/{id}</code>
                                            <span class=\"text-gray-500 ml-2\">- Get specific dataset</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-3\">POST</span>
                                            <code class=\"text-sm\">/api/datasets</code>
                                            <span class=\"text-gray-500 ml-2\">- Create new dataset</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium mr-3\">PUT</span>
                                            <code class=\"text-sm\">/api/datasets/{id}</code>
                                            <span class=\"text-gray-500 ml-2\">- Update dataset</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium mr-3\">DELETE</span>
                                            <code class=\"text-sm\">/api/datasets/{id}</code>
                                            <span class=\"text-gray-500 ml-2\">- Delete dataset</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class=\"text-md font-medium text-gray-700 mb-2\">Export Operations</h4>
                                    <div class=\"bg-gray-50 rounded-lg p-4 space-y-2\">
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets/{id}/xml</code>
                                            <span class=\"text-gray-500 ml-2\">- Export dataset to XML</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets/{id}/json</code>
                                            <span class=\"text-gray-500 ml-2\">- Export dataset to JSON</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class=\"text-md font-medium text-gray-700 mb-2\">Authentication</h4>
                                    <p class=\"text-gray-600 mb-2\">All API endpoints require authentication using Bearer token:</p>
                                    <pre class=\"bg-gray-800 text-green-400 p-3 rounded-lg text-sm\"><code>Authorization: Bearer your-token-here</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





                <!-- Adding New Fields Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-5 h-5 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                            </svg>
                            Adding New Fields for Datasets
                        </h2>
                    </div>
                    <div class=\"p-6 space-y-6\">
                        <p class=\"text-gray-700\">This application is designed to be extensible, allowing you to add new metadata fields as needed. Here's how to add new fields to datasets:</p>
                        
                        <div class=\"space-y-4\">
                            <div class=\"bg-blue-50 border-l-4 border-blue-400 p-4\">
                                <h3 class=\"text-lg font-medium text-blue-800 mb-2\">Step 1: Database Migration</h3>
                                <p class=\"text-blue-700 mb-3\">Create a new migration file in the <code class=\"bg-blue-100 px-2 py-1 rounded text-sm\">database/migrations/</code> directory:</p>
                                <pre class=\"bg-gray-800 text-green-400 p-4 rounded-lg text-sm overflow-x-auto\"><code>-- Example: add_new_field.sql
-- Add new field to metadata_records table
ALTER TABLE metadata_records
ADD COLUMN IF NOT EXISTS new_field_name VARCHAR(255);

-- Add comment to explain the column
COMMENT ON COLUMN metadata_records.new_field_name IS 'Description of what this field contains';</code></pre>
                            </div>
                            
                            <div class=\"bg-green-50 border-l-4 border-green-400 p-4\">
                                <h3 class=\"text-lg font-medium text-green-800 mb-2\">Step 2: Update the Form Template</h3>
                                <p class=\"text-green-700 mb-3\">Add the new field to the form in <code class=\"bg-green-100 px-2 py-1 rounded text-sm\">templates/form.twig</code>:</p>
                                <pre class=\"bg-gray-800 text-green-400 p-4 rounded-lg text-sm overflow-x-auto\"><code>&lt;div class=\"mb-4\"&gt;
    &lt;label for=\"new_field_name\" class=\"block text-sm font-medium text-gray-700 mb-1\"&gt;New Field Label&lt;/label&gt;
    &lt;input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
           id=\"new_field_name\" name=\"new_field_name\"
           value=\"";
        // line 217
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "new_field_name", [], "any", true, true, false, 217)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "new_field_name", [], "any", false, false, false, 217), "")) : ("")), "html", null, true);
        yield "\"&gt;
&lt;/div&gt;</code></pre>
                            </div>
                            
                            <div class=\"bg-purple-50 border-l-4 border-purple-400 p-4\">
                                <h3 class=\"text-lg font-medium text-purple-800 mb-2\">Step 3: Update the Controller</h3>
                                <p class=\"text-purple-700 mb-2\">Modify the relevant controller (usually <code class=\"bg-purple-100 px-2 py-1 rounded text-sm\">src/Controllers/GisController.php</code>) to handle the new field:</p>
                                <ul class=\"text-purple-700 list-disc list-inside space-y-1\">
                                    <li>Add the field to the data array when creating/updating records</li>
                                    <li>Include the field in validation if required</li>
                                    <li>Update any display logic to show the new field</li>
                                </ul>
                            </div>
                            
                            <div class=\"bg-yellow-50 border-l-4 border-yellow-400 p-4\">
                                <h3 class=\"text-lg font-medium text-yellow-800 mb-2\">Step 4: Update Display Templates</h3>
                                <p class=\"text-yellow-700 mb-3\">Add the new field to display templates like <code class=\"bg-yellow-100 px-2 py-1 rounded text-sm\">templates/dataset_detail.twig</code>:</p>
                                <pre class=\"bg-gray-800 text-green-400 p-4 rounded-lg text-sm overflow-x-auto\"><code>&lt;div class=\"mb-3\"&gt;
    &lt;strong&gt;New Field:&lt;/strong&gt; ";
        // line 235
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "new_field_name", [], "any", false, false, false, 235), "html", null, true);
        yield "
&lt;/div&gt;</code></pre>
                            </div>
                            
                            <div class=\"bg-red-50 border-l-4 border-red-400 p-4\">
                                <h3 class=\"text-lg font-medium text-red-800 mb-2\">Step 5: Update XML Export</h3>
                                <p class=\"text-red-700\">If the field should be included in XML exports, update the XML generation logic in the controller.</p>
                            </div>
                        </div>
                        
                        <div class=\"bg-gray-50 rounded-lg p-4\">
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Common Field Types</h3>
                            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-3\">
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-blue-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Text fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">VARCHAR(255)</code> or <code class=\"bg-gray-200 px-1 rounded\">TEXT</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-green-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Date fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">DATE</code> or <code class=\"bg-gray-200 px-1 rounded\">TIMESTAMP</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-purple-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Numeric fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">INTEGER</code>, <code class=\"bg-gray-200 px-1 rounded\">DECIMAL</code>, or <code class=\"bg-gray-200 px-1 rounded\">REAL</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-yellow-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Boolean fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">BOOLEAN</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-red-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Array fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">TEXT[]</code> for multiple values</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class=\"bg-indigo-50 rounded-lg p-4\">
                            <h3 class=\"text-lg font-medium text-indigo-800 mb-3\">Best Practices</h3>
                            <ul class=\"text-indigo-700 space-y-2\">
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Always use <code class=\"bg-indigo-100 px-1 rounded text-sm\">IF NOT EXISTS</code> in migrations to prevent errors
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Add meaningful comments to database columns
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Follow the existing naming conventions (snake_case for database, camelCase for form fields)
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Test the new field thoroughly before deploying
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Consider whether the field should be required or optional
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Update documentation when adding new fields
                                </li>
                            </ul>
                        </div>
                        
                        <div class=\"bg-teal-50 rounded-lg p-4\">
                            <h3 class=\"text-lg font-medium text-teal-800 mb-3\">Example: Adding a \"Data Source\" Field</h3>
                            <p class=\"text-teal-700 mb-3\">Here's a complete example of adding a \"Data Source\" field:</p>
                            <ol class=\"text-teal-700 space-y-2\">
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">1</span>
                                    <strong>Migration:</strong> Create <code class=\"bg-teal-100 px-1 rounded text-sm\">add_data_source_field.sql</code>
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">2</span>
                                    <strong>Form:</strong> Add input field to the Identification Info section
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">3</span>
                                    <strong>Controller:</strong> Include in create/update methods
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">4</span>
                                    <strong>Display:</strong> Show in dataset detail view
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">5</span>
                                    <strong>Export:</strong> Include in XML generation
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                

               

                <!-- Standards Compliance Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-5 h-5 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\"></path>
                            </svg>
                            Standards Compliance
                        </h2>
                    </div>
                    <div class=\"p-6\">
                        <p class=\"text-gray-700 mb-4\">This application follows:</p>
                        <div class=\"space-y-2\">
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                <span class=\"text-gray-700\">ISO 19115:2014 - Geographic information - Metadata</span>
                            </div>
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                <span class=\"text-gray-700\">INSPIRE Metadata Implementing Rules</span>
                            </div>
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                <span class=\"text-gray-700\">ISO 19139:2007 - Geographic information - Metadata - XML schema implementation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class=\"space-y-6\">
                <!-- Quick Links -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-4 py-3 border-b border-gray-200\">
                        <h2 class=\"text-lg font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-4 h-4 mr-2 text-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1\"></path>
                            </svg>
                            Quick Links
                        </h2>
                    </div>
                    <div class=\"p-4 space-y-2\">
                        <a href=\"/\" class=\"flex items-center text-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path>
                            </svg>
                            View Datasets
                        </a>
                        <a href=\"/form\" class=\"flex items-center text-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                            </svg>
                            Create New Dataset
                        </a>
                        <a href=\"https://www.iso.org/standard/53798.html\" target=\"_blank\" class=\"flex items-center text-blue-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14\"></path>
                            </svg>
                            ISO 19115 Documentation
                        </a>
                        <a href=\"https://inspire.ec.europa.eu/\" target=\"_blank\" class=\"flex items-center text-blue-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14\"></path>
                            </svg>
                            INSPIRE Portal
                        </a>
                    </div>
                </div>

                <!-- Support -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-4 py-3 border-b border-gray-200\">
                        <h2 class=\"text-lg font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z\"></path>
                            </svg>
                            Support
                        </h2>
                    </div>
                    <div class=\"p-4\">
                        <p class=\"text-gray-700 mb-3\">For technical support or questions, please contact your system administrator.</p>
                        <div class=\"bg-blue-50 border border-blue-200 rounded-lg p-3\">
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-blue-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"></path>
                                </svg>
                                <span class=\"text-sm font-medium text-blue-800\">Version: 1.0.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "about.twig";
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
        return array (  304 => 235,  283 => 217,  70 => 6,  63 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}About Novella{% endblock %}

{% block content %}
<div class=\"max-w-7xl mx-auto\">
    <div class=\"bg-white rounded-lg shadow-md p-8 mb-8\">
<div class=\"flex items-center space-x-2\">
                    <img src=\"/storage/uploads/novella-logo-alt.png\" alt=\"Novella Logo\" class=\"h-8 w-auto\">
                    <a href=\"/\" class=\"text-3xl font-bold text-gray-900\">Novella</a>
                </div>
<br>
        
        <div class=\"grid grid-cols-1 lg:grid-cols-3 gap-8\">
            <!-- Main Content -->
            <div class=\"lg:col-span-2 space-y-8\">





                <!-- Overview Section -->
                <div class=\"bg-gray-50 rounded-lg p-6 border border-gray-200\">
                    <h2 class=\"text-xl font-semibold text-gray-800 mb-4 flex items-center\">
                        Overview
                    </h2>
                    <p class=\"text-gray-700 leading-relaxed\">Novella is a lightweight spatial data catalogue using ISO 19115 and INSPIRE standards. </p>
                </div>


<!-- Basic Usage Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            
                            Basic Usage
                        </h2>
                    </div>
                    <div class=\"p-6 space-y-6\">
                        <div>
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Getting Started</h3>
                            <ol class=\"space-y-2 text-gray-700\">
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">1</span>
                                    Log in to the system using your credentials
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">2</span>
                                    Navigate to \"Datasets\" to view existing metadata records
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">3</span>
                                    Use the \"New Dataset\" button to create a new metadata record
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">4</span>
                                    Fill in the required metadata fields following ISO 19115 standards
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-gray-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 mt-0.5\">5</span>
                                    Save your changes and export to XML when ready
                                </li>
                            </ol>
                        </div>

                        <div>
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Key Features</h3>
                            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-3\">
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">Metadata entry form with validation</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">XML export functionality</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">Dataset management and organization</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">User management and access control</span>
                                </div>
                                <div class=\"flex items-center\">
                                    <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    <span class=\"text-gray-700\">RESTful API access</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





 <!-- API Documentation Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-5 h-5 mr-2 text-purple-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4\"></path>
                            </svg>
                            API Documentation
                        </h2>
                    </div>
                    <div class=\"p-6 space-y-6\">
                        <div>
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Available Endpoints</h3>
                            
                            <div class=\"space-y-4\">
                                <div>
                                    <h4 class=\"text-md font-medium text-gray-700 mb-2\">Dataset Operations</h4>
                                    <div class=\"bg-gray-50 rounded-lg p-4 space-y-2\">
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets</code>
                                            <span class=\"text-gray-500 ml-2\">- List all datasets</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets/{id}</code>
                                            <span class=\"text-gray-500 ml-2\">- Get specific dataset</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-3\">POST</span>
                                            <code class=\"text-sm\">/api/datasets</code>
                                            <span class=\"text-gray-500 ml-2\">- Create new dataset</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium mr-3\">PUT</span>
                                            <code class=\"text-sm\">/api/datasets/{id}</code>
                                            <span class=\"text-gray-500 ml-2\">- Update dataset</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium mr-3\">DELETE</span>
                                            <code class=\"text-sm\">/api/datasets/{id}</code>
                                            <span class=\"text-gray-500 ml-2\">- Delete dataset</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class=\"text-md font-medium text-gray-700 mb-2\">Export Operations</h4>
                                    <div class=\"bg-gray-50 rounded-lg p-4 space-y-2\">
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets/{id}/xml</code>
                                            <span class=\"text-gray-500 ml-2\">- Export dataset to XML</span>
                                        </div>
                                        <div class=\"flex items-center\">
                                            <span class=\"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3\">GET</span>
                                            <code class=\"text-sm\">/api/datasets/{id}/json</code>
                                            <span class=\"text-gray-500 ml-2\">- Export dataset to JSON</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class=\"text-md font-medium text-gray-700 mb-2\">Authentication</h4>
                                    <p class=\"text-gray-600 mb-2\">All API endpoints require authentication using Bearer token:</p>
                                    <pre class=\"bg-gray-800 text-green-400 p-3 rounded-lg text-sm\"><code>Authorization: Bearer your-token-here</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





                <!-- Adding New Fields Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-5 h-5 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                            </svg>
                            Adding New Fields for Datasets
                        </h2>
                    </div>
                    <div class=\"p-6 space-y-6\">
                        <p class=\"text-gray-700\">This application is designed to be extensible, allowing you to add new metadata fields as needed. Here's how to add new fields to datasets:</p>
                        
                        <div class=\"space-y-4\">
                            <div class=\"bg-blue-50 border-l-4 border-blue-400 p-4\">
                                <h3 class=\"text-lg font-medium text-blue-800 mb-2\">Step 1: Database Migration</h3>
                                <p class=\"text-blue-700 mb-3\">Create a new migration file in the <code class=\"bg-blue-100 px-2 py-1 rounded text-sm\">database/migrations/</code> directory:</p>
                                <pre class=\"bg-gray-800 text-green-400 p-4 rounded-lg text-sm overflow-x-auto\"><code>-- Example: add_new_field.sql
-- Add new field to metadata_records table
ALTER TABLE metadata_records
ADD COLUMN IF NOT EXISTS new_field_name VARCHAR(255);

-- Add comment to explain the column
COMMENT ON COLUMN metadata_records.new_field_name IS 'Description of what this field contains';</code></pre>
                            </div>
                            
                            <div class=\"bg-green-50 border-l-4 border-green-400 p-4\">
                                <h3 class=\"text-lg font-medium text-green-800 mb-2\">Step 2: Update the Form Template</h3>
                                <p class=\"text-green-700 mb-3\">Add the new field to the form in <code class=\"bg-green-100 px-2 py-1 rounded text-sm\">templates/form.twig</code>:</p>
                                <pre class=\"bg-gray-800 text-green-400 p-4 rounded-lg text-sm overflow-x-auto\"><code>&lt;div class=\"mb-4\"&gt;
    &lt;label for=\"new_field_name\" class=\"block text-sm font-medium text-gray-700 mb-1\"&gt;New Field Label&lt;/label&gt;
    &lt;input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
           id=\"new_field_name\" name=\"new_field_name\"
           value=\"{{ dataset.new_field_name|default('') }}\"&gt;
&lt;/div&gt;</code></pre>
                            </div>
                            
                            <div class=\"bg-purple-50 border-l-4 border-purple-400 p-4\">
                                <h3 class=\"text-lg font-medium text-purple-800 mb-2\">Step 3: Update the Controller</h3>
                                <p class=\"text-purple-700 mb-2\">Modify the relevant controller (usually <code class=\"bg-purple-100 px-2 py-1 rounded text-sm\">src/Controllers/GisController.php</code>) to handle the new field:</p>
                                <ul class=\"text-purple-700 list-disc list-inside space-y-1\">
                                    <li>Add the field to the data array when creating/updating records</li>
                                    <li>Include the field in validation if required</li>
                                    <li>Update any display logic to show the new field</li>
                                </ul>
                            </div>
                            
                            <div class=\"bg-yellow-50 border-l-4 border-yellow-400 p-4\">
                                <h3 class=\"text-lg font-medium text-yellow-800 mb-2\">Step 4: Update Display Templates</h3>
                                <p class=\"text-yellow-700 mb-3\">Add the new field to display templates like <code class=\"bg-yellow-100 px-2 py-1 rounded text-sm\">templates/dataset_detail.twig</code>:</p>
                                <pre class=\"bg-gray-800 text-green-400 p-4 rounded-lg text-sm overflow-x-auto\"><code>&lt;div class=\"mb-3\"&gt;
    &lt;strong&gt;New Field:&lt;/strong&gt; {{ dataset.new_field_name }}
&lt;/div&gt;</code></pre>
                            </div>
                            
                            <div class=\"bg-red-50 border-l-4 border-red-400 p-4\">
                                <h3 class=\"text-lg font-medium text-red-800 mb-2\">Step 5: Update XML Export</h3>
                                <p class=\"text-red-700\">If the field should be included in XML exports, update the XML generation logic in the controller.</p>
                            </div>
                        </div>
                        
                        <div class=\"bg-gray-50 rounded-lg p-4\">
                            <h3 class=\"text-lg font-medium text-gray-800 mb-3\">Common Field Types</h3>
                            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-3\">
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-blue-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Text fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">VARCHAR(255)</code> or <code class=\"bg-gray-200 px-1 rounded\">TEXT</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-green-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Date fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">DATE</code> or <code class=\"bg-gray-200 px-1 rounded\">TIMESTAMP</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-purple-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Numeric fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">INTEGER</code>, <code class=\"bg-gray-200 px-1 rounded\">DECIMAL</code>, or <code class=\"bg-gray-200 px-1 rounded\">REAL</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-yellow-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Boolean fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">BOOLEAN</code></span>
                                </div>
                                <div class=\"flex items-center\">
                                    <span class=\"w-3 h-3 bg-red-500 rounded-full mr-2\"></span>
                                    <span class=\"text-sm text-gray-700\"><strong>Array fields:</strong> <code class=\"bg-gray-200 px-1 rounded\">TEXT[]</code> for multiple values</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class=\"bg-indigo-50 rounded-lg p-4\">
                            <h3 class=\"text-lg font-medium text-indigo-800 mb-3\">Best Practices</h3>
                            <ul class=\"text-indigo-700 space-y-2\">
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Always use <code class=\"bg-indigo-100 px-1 rounded text-sm\">IF NOT EXISTS</code> in migrations to prevent errors
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Add meaningful comments to database columns
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Follow the existing naming conventions (snake_case for database, camelCase for form fields)
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Test the new field thoroughly before deploying
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Consider whether the field should be required or optional
                                </li>
                                <li class=\"flex items-start\">
                                    <svg class=\"w-4 h-4 mr-2 mt-0.5 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                    </svg>
                                    Update documentation when adding new fields
                                </li>
                            </ul>
                        </div>
                        
                        <div class=\"bg-teal-50 rounded-lg p-4\">
                            <h3 class=\"text-lg font-medium text-teal-800 mb-3\">Example: Adding a \"Data Source\" Field</h3>
                            <p class=\"text-teal-700 mb-3\">Here's a complete example of adding a \"Data Source\" field:</p>
                            <ol class=\"text-teal-700 space-y-2\">
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">1</span>
                                    <strong>Migration:</strong> Create <code class=\"bg-teal-100 px-1 rounded text-sm\">add_data_source_field.sql</code>
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">2</span>
                                    <strong>Form:</strong> Add input field to the Identification Info section
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">3</span>
                                    <strong>Controller:</strong> Include in create/update methods
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">4</span>
                                    <strong>Display:</strong> Show in dataset detail view
                                </li>
                                <li class=\"flex items-start\">
                                    <span class=\"bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-2 mt-0.5\">5</span>
                                    <strong>Export:</strong> Include in XML generation
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                

               

                <!-- Standards Compliance Section -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-6 py-4 border-b border-gray-200\">
                        <h2 class=\"text-xl font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-5 h-5 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\"></path>
                            </svg>
                            Standards Compliance
                        </h2>
                    </div>
                    <div class=\"p-6\">
                        <p class=\"text-gray-700 mb-4\">This application follows:</p>
                        <div class=\"space-y-2\">
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                <span class=\"text-gray-700\">ISO 19115:2014 - Geographic information - Metadata</span>
                            </div>
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                <span class=\"text-gray-700\">INSPIRE Metadata Implementing Rules</span>
                            </div>
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                <span class=\"text-gray-700\">ISO 19139:2007 - Geographic information - Metadata - XML schema implementation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class=\"space-y-6\">
                <!-- Quick Links -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-4 py-3 border-b border-gray-200\">
                        <h2 class=\"text-lg font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-4 h-4 mr-2 text-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1\"></path>
                            </svg>
                            Quick Links
                        </h2>
                    </div>
                    <div class=\"p-4 space-y-2\">
                        <a href=\"/\" class=\"flex items-center text-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path>
                            </svg>
                            View Datasets
                        </a>
                        <a href=\"/form\" class=\"flex items-center text-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                            </svg>
                            Create New Dataset
                        </a>
                        <a href=\"https://www.iso.org/standard/53798.html\" target=\"_blank\" class=\"flex items-center text-blue-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14\"></path>
                            </svg>
                            ISO 19115 Documentation
                        </a>
                        <a href=\"https://inspire.ec.europa.eu/\" target=\"_blank\" class=\"flex items-center text-blue-600 hover:text-blue-800 transition-colors\">
                            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14\"></path>
                            </svg>
                            INSPIRE Portal
                        </a>
                    </div>
                </div>

                <!-- Support -->
                <div class=\"bg-white border border-gray-200 rounded-lg overflow-hidden\">
                    <div class=\"bg-gray-50 px-4 py-3 border-b border-gray-200\">
                        <h2 class=\"text-lg font-semibold text-gray-800 flex items-center\">
                            <svg class=\"w-4 h-4 mr-2 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z\"></path>
                            </svg>
                            Support
                        </h2>
                    </div>
                    <div class=\"p-4\">
                        <p class=\"text-gray-700 mb-3\">For technical support or questions, please contact your system administrator.</p>
                        <div class=\"bg-blue-50 border border-blue-200 rounded-lg p-3\">
                            <div class=\"flex items-center\">
                                <svg class=\"w-4 h-4 mr-2 text-blue-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"></path>
                                </svg>
                                <span class=\"text-sm font-medium text-blue-800\">Version: 1.0.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %} ", "about.twig", "/var/www/novella/templates/about.twig");
    }
}
