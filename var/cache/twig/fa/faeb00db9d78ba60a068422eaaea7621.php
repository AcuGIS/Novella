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

/* form.twig */
class __TwigTemplate_ec5479ffb7d2069abdaa754a32aa584e extends Template
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
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "Edit";
        } else {
            yield "New";
        }
        yield " ISO 19115 + INSPIRE Metadata";
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
        yield "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css\">
<style>
    .required::after {
        content: \" *\";
        color: red;
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: white;
    }
    
    #map {
        width: 100%;
        height: 400px;
        margin-top: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
</style>
";
        yield from [];
    }

    // line 31
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 32
        yield "    <h1 class=\"text-3xl font-bold mb-8\">";
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "Edit";
        } else {
            yield "New";
        }
        yield " ISO 19115 + INSPIRE Metadata</h1>
    
    <form id=\"metadataForm\" action=\"";
        // line 34
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "/metadata/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 34), "html", null, true);
            yield "/update";
        } else {
            yield "/metadata";
        }
        yield "\" 
          method=\"";
        // line 35
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "PUT";
        } else {
            yield "POST";
        }
        yield "\" 
          enctype=\"multipart/form-data\" 
          class=\"space-y-8\">
        
        ";
        // line 39
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 40
            yield "        <input type=\"hidden\" name=\"_method\" value=\"PUT\">
        ";
        }
        // line 42
        yield "
        <!-- Identification Info Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Identification Info</h2>
            <div class=\"mb-4\">
                <label for=\"title\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Title</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"title\" name=\"title\" required
                       value=\"";
        // line 50
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "title", [], "any", true, true, false, 50)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "title", [], "any", false, false, false, 50), "")) : ("")), "html", null, true);
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"abstract\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Abstract</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"abstract\" name=\"abstract\" rows=\"3\" required>";
        // line 55
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "abstract", [], "any", true, true, false, 55)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "abstract", [], "any", false, false, false, 55), "")) : ("")), "html", null, true);
        yield "</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"purpose\" class=\"block text-sm font-medium text-gray-700 mb-1\">Purpose</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"purpose\" name=\"purpose\" rows=\"2\">";
        // line 60
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "purpose", [], "any", true, true, false, 60)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "purpose", [], "any", false, false, false, 60), "")) : ("")), "html", null, true);
        yield "</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"keywords\" class=\"block text-sm font-medium text-gray-700 mb-1\">Keywords (comma separated)</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"keywords\" name=\"keywords\"
                       value=\"";
        // line 66
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "keywords", [], "any", false, false, false, 66)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::join(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "keywords", [], "any", false, false, false, 66), ", "), "html", null, true)) : (""));
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"topic\" class=\"block text-sm font-medium text-gray-700 mb-1\">Topic</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"topic\" name=\"topic\">
                    <option value=\"\">Select Topic</option>
                    ";
        // line 73
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["topics"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["topic"]) {
            // line 74
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "id", [], "any", false, false, false, 74), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "topic_id", [], "any", false, false, false, 74) == CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "id", [], "any", false, false, false, 74))) {
                yield "selected";
            }
            yield ">
                            ";
            // line 75
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "topic", [], "any", false, false, false, 75), "html", null, true);
            yield "
                        </option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['topic'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 78
        yield "                </select>
            </div>
            <div class=\"mb-4\">
                <label for=\"inspire_theme\" class=\"block text-sm font-medium text-gray-700 mb-1\">INSPIRE Theme</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"inspire_theme\" name=\"inspire_theme\">
                    <option value=\"\">Select INSPIRE Theme</option>
                    ";
        // line 85
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["keywords"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["keyword"]) {
            // line 86
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 86), "html", null, true);
            yield "\" ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "inspire_theme_id", [], "any", false, false, false, 86) == CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 86))) {
                yield "selected";
            }
            yield ">
                            ";
            // line 87
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "keyword", [], "any", false, false, false, 87), "html", null, true);
            yield "
                        </option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['keyword'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 90
        yield "                </select>
            </div>
            <div class=\"mb-4\">
                <label for=\"metadata_language\" class=\"block text-sm font-medium text-gray-700 mb-1\">Metadata Language</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"metadata_language\" name=\"metadata_language\" placeholder=\"e.g., en, fr, de\"
                       value=\"";
        // line 96
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_language", [], "any", true, true, false, 96)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_language", [], "any", false, false, false, 96), "")) : ("")), "html", null, true);
        yield "\">
            </div>
        </div>

        <!-- Citation Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Citation</h2>
            <div class=\"mb-4\">
                <label for=\"citation_date\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Citation Date</label>
                <input type=\"date\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"citation_date\" name=\"citation_date\" required
                       value=\"";
        // line 107
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "citation_date", [], "any", false, false, false, 107), "Y-m-d"), "html", null, true);
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"responsible_org\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Responsible Organization</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"responsible_org\" name=\"responsible_org\" required
                       value=\"";
        // line 113
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_org", [], "any", true, true, false, 113)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_org", [], "any", false, false, false, 113), "")) : ("")), "html", null, true);
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"responsible_person\" class=\"block text-sm font-medium text-gray-700 mb-1\">Responsible Person</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"responsible_person\" name=\"responsible_person\"
                       value=\"";
        // line 119
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_person", [], "any", true, true, false, 119)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_person", [], "any", false, false, false, 119), "")) : ("")), "html", null, true);
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"role\" class=\"block text-sm font-medium text-gray-700 mb-1\">Role</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"role\" name=\"role\">
                    <option value=\"\">Select Role</option>
                    <option value=\"pointOfContact\" ";
        // line 126
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 126) == "pointOfContact")) {
            yield "selected";
        }
        yield ">Point of Contact</option>
                    <option value=\"originator\" ";
        // line 127
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 127) == "originator")) {
            yield "selected";
        }
        yield ">Originator</option>
                    <option value=\"publisher\" ";
        // line 128
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 128) == "publisher")) {
            yield "selected";
        }
        yield ">Publisher</option>
                    <option value=\"author\" ";
        // line 129
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 129) == "author")) {
            yield "selected";
        }
        yield ">Author</option>
                    <option value=\"custodian\" ";
        // line 130
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 130) == "custodian")) {
            yield "selected";
        }
        yield ">Custodian</option>
                </select>
            </div>
        </div>

        <!-- WMS Layer Selector Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">WMS Layer Selection</h2>
            <div class=\"mb-4\">
                <label for=\"wms_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">WMS Service URL</label>
                <div class=\"flex space-x-2\">
                    <input type=\"url\" class=\"flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"wms_url\" name=\"wms_url\" placeholder=\"https://example.com/geoserver/wms\"
                           value=\"";
        // line 143
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_url", [], "any", false, false, false, 143), "html", null, true);
        yield "\">
                    <button type=\"button\" class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\" 
                            id=\"fetchLayersBtn\">Fetch Layers</button>
                </div>
            </div>
            <div class=\"mb-4 hidden\" id=\"layerSelectContainer\">
                <label for=\"wms_layer\" class=\"block text-sm font-medium text-gray-700 mb-1\">Select Layer</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"wms_layer\" name=\"wms_layer\">
                    <option value=\"\">Select a layer...</option>
                </select>
            </div>
            <div id=\"map\"></div>
        </div>

        <!-- GIS File Upload Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">GIS File Upload</h2>
            <div class=\"mb-4\">
                <label for=\"gis_files\" class=\"block text-sm font-medium text-gray-700 mb-1\">Upload GIS Files</label>
                <input type=\"file\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"gis_files\" name=\"gis_files[]\" multiple 
                       accept=\".zip,.shp,.gpkg,.tif,.tiff,.geotiff,.img,.ecw,.jp2,.sid,.asc,.grd,.nc\">
                <p class=\"mt-1 text-sm text-gray-500\">
                    Supported formats: Shapefile (.zip), GeoPackage (.gpkg), GeoTIFF (.tif, .tiff), 
                    ECW (.ecw), JPEG2000 (.jp2), MrSID (.sid), ASCII Grid (.asc), NetCDF (.nc)
                </p>
            </div>
            <div class=\"mb-4\">
                <label for=\"thumbnail\" class=\"block text-sm font-medium text-gray-700 mb-1\">Dataset Thumbnail</label>
                <input type=\"file\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"thumbnail\" name=\"thumbnail\" accept=\"image/jpeg,image/png,image/gif\">
                <p class=\"mt-1 text-sm text-gray-500\">
                    Upload a thumbnail image for the dataset (JPEG, PNG, or GIF format)
                </p>
            </div>
            <div id=\"uploadedFiles\" class=\"mt-4 space-y-4\">
                <!-- Uploaded files will be listed here -->
            </div>
        </div>

        <!-- Geographic Extent Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Geographic Extent</h2>
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">
                <div class=\"mb-4\">
                    <label for=\"west_longitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">West Longitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"west_longitude\" name=\"west_longitude\" step=\"0.000001\" required
                           value=\"";
        // line 192
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", true, true, false, 192)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 192), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"east_longitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">East Longitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"east_longitude\" name=\"east_longitude\" step=\"0.000001\" required
                           value=\"";
        // line 198
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", true, true, false, 198)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 198), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"south_latitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">South Latitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"south_latitude\" name=\"south_latitude\" step=\"0.000001\" required
                           value=\"";
        // line 204
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", true, true, false, 204)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 204), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"north_latitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">North Latitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"north_latitude\" name=\"north_latitude\" step=\"0.000001\" required
                           value=\"";
        // line 210
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", true, true, false, 210)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 210), "")) : ("")), "html", null, true);
        yield "\">
                </div>
            </div>
        </div>

        <!-- Temporal Extent Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Temporal Extent</h2>
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">
                <div class=\"mb-4\">
                    <label for=\"start_date\" class=\"block text-sm font-medium text-gray-700 mb-1\">Start Date</label>
                    <input type=\"date\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"start_date\" name=\"start_date\"
                           value=\"";
        // line 223
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "start_date", [], "any", false, false, false, 223), "Y-m-d"), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"end_date\" class=\"block text-sm font-medium text-gray-700 mb-1\">End Date</label>
                    <input type=\"date\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"end_date\" name=\"end_date\"
                           value=\"";
        // line 229
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "end_date", [], "any", false, false, false, 229), "Y-m-d"), "html", null, true);
        yield "\">
                </div>
            </div>
        </div>

        <!-- Spatial Representation Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Spatial Representation</h2>
            <div class=\"mb-4\">
                <label for=\"coordinate_system\" class=\"block text-sm font-medium text-gray-700 mb-1\">Coordinate System (EPSG Code)</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"coordinate_system\" name=\"coordinate_system\" placeholder=\"e.g., EPSG:4326\"
                       value=\"";
        // line 241
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coordinate_system", [], "any", true, true, false, 241)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coordinate_system", [], "any", false, false, false, 241), "")) : ("")), "html", null, true);
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"spatial_resolution\" class=\"block text-sm font-medium text-gray-700 mb-1\">Spatial Resolution</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"spatial_resolution\" name=\"spatial_resolution\" placeholder=\"e.g., 30m, 1:10000, 0.5 degrees\"
                       value=\"";
        // line 247
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_resolution", [], "any", true, true, false, 247)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_resolution", [], "any", false, false, false, 247), "")) : ("")), "html", null, true);
        yield "\">
                <p class=\"mt-1 text-sm text-gray-500\">Enter the scale or equivalent resolution of the data (e.g., 30m, 1:10000, 0.5 degrees)</p>
            </div>
        </div>

        <!-- Constraints Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Constraints</h2>
            <div class=\"mb-4\">
                <label for=\"access_constraints\" class=\"block text-sm font-medium text-gray-700 mb-1\">Access Constraints</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"access_constraints\" name=\"access_constraints\" rows=\"2\" 
                          placeholder=\"Restrictions and legal prerequisites for accessing the dataset\">";
        // line 259
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "access_constraints", [], "any", true, true, false, 259)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "access_constraints", [], "any", false, false, false, 259), "")) : ("")), "html", null, true);
        yield "</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"use_constraints\" class=\"block text-sm font-medium text-gray-700 mb-1\">Use Constraints</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"use_constraints\" name=\"use_constraints\" rows=\"2\" 
                          placeholder=\"Restrictions and legal prerequisites for using the dataset\">";
        // line 265
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_constraints", [], "any", true, true, false, 265)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_constraints", [], "any", false, false, false, 265), "")) : ("")), "html", null, true);
        yield "</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"use_limitation\" class=\"block text-sm font-medium text-gray-700 mb-1\">Use Limitation</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"use_limitation\" name=\"use_limitation\" rows=\"2\" 
                          placeholder=\"General restrictions and legal prerequisites for using the dataset\">";
        // line 271
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_limitation", [], "any", true, true, false, 271)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_limitation", [], "any", false, false, false, 271), "")) : ("")), "html", null, true);
        yield "</textarea>
            </div>
        </div>

        <!-- Data Quality Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Data Quality</h2>
            <div class=\"mb-4\">
                <label for=\"lineage\" class=\"block text-sm font-medium text-gray-700 mb-1\">Lineage</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"lineage\" name=\"lineage\" rows=\"2\" 
                          placeholder=\"Statement about data quality and origin\">";
        // line 282
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "lineage", [], "any", true, true, false, 282)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "lineage", [], "any", false, false, false, 282), "")) : ("")), "html", null, true);
        yield "</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"resource_type\" class=\"block text-sm font-medium text-gray-700 mb-1\">Scope</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"resource_type\" name=\"resource_type\">
                    <option value=\"\">Select Scope</option>
                    <option value=\"dataset\" ";
        // line 289
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 289) == "dataset")) {
            yield "selected";
        }
        yield ">Dataset</option>
                    <option value=\"series\" ";
        // line 290
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 290) == "series")) {
            yield "selected";
        }
        yield ">Series</option>
                    <option value=\"service\" ";
        // line 291
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 291) == "service")) {
            yield "selected";
        }
        yield ">Service</option>
                </select>
            </div>
        </div>

        <!-- INSPIRE Metadata Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">INSPIRE Metadata</h2>
            <div class=\"mb-4\">
                <label for=\"point_of_contact_org\" class=\"block text-sm font-medium text-gray-700 mb-1\">INSPIRE Point of Contact Organization</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"point_of_contact_org\" name=\"point_of_contact_org\"
                       value=\"";
        // line 303
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "point_of_contact_org", [], "any", true, true, false, 303)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "point_of_contact_org", [], "any", false, false, false, 303), "")) : ("")), "html", null, true);
        yield "\">
            </div>
            <div class=\"mb-4\">
                <label for=\"conformity_result\" class=\"block text-sm font-medium text-gray-700 mb-1\">Conformity Result</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"conformity_result\" name=\"conformity_result\">
                    <option value=\"\">Select Result</option>
                    <option value=\"conformant\" ";
        // line 310
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 310) == "conformant")) {
            yield "selected";
        }
        yield ">Conformant</option>
                    <option value=\"non-conformant\" ";
        // line 311
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 311) == "non-conformant")) {
            yield "selected";
        }
        yield ">Non-conformant</option>
                    <option value=\"unknown\" ";
        // line 312
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 312) == "unknown")) {
            yield "selected";
        }
        yield ">Unknown</option>
                </select>
            </div>
            <div class=\"mb-4\">
                <label for=\"spatial_data_service_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">Spatial Data Service URL</label>
                <input type=\"url\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"spatial_data_service_url\" name=\"spatial_data_service_url\"
                       value=\"";
        // line 319
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_data_service_url", [], "any", true, true, false, 319)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_data_service_url", [], "any", false, false, false, 319), "")) : ("")), "html", null, true);
        yield "\">
            </div>
        </div>

        <!-- Additional Metadata Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Additional Metadata</h2>
            <div class=\"mb-4\">
                <h3 class=\"text-lg font-medium text-gray-900 mb-3\">Metadata Point of Contact</h3>
                <div class=\"mb-4\">
                    <label for=\"metadata_poc_organization\" class=\"block text-sm font-medium text-gray-700 mb-1\">Organization</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"metadata_poc_organization\" name=\"metadata_poc_organization\" 
                           placeholder=\"Organization of the metadata point of contact\"
                           value=\"";
        // line 333
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_organization", [], "any", true, true, false, 333)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_organization", [], "any", false, false, false, 333), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"metadata_poc_email\" class=\"block text-sm font-medium text-gray-700 mb-1\">Email</label>
                    <input type=\"email\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"metadata_poc_email\" name=\"metadata_poc_email\" 
                           placeholder=\"Email address of the metadata point of contact\"
                           value=\"";
        // line 340
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_email", [], "any", true, true, false, 340)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_email", [], "any", false, false, false, 340), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"metadata_poc_role\" class=\"block text-sm font-medium text-gray-700 mb-1\">Role</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"metadata_poc_role\" name=\"metadata_poc_role\" 
                           placeholder=\"Role of the metadata point of contact\"
                           value=\"";
        // line 347
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", true, true, false, 347)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 347), "")) : ("")), "html", null, true);
        yield "\">
                </div>
            </div>

            <div class=\"mb-4\">
                <h3 class=\"text-lg font-medium text-gray-900 mb-3\">Resource Information</h3>
                <div class=\"mb-4\">
                    <label for=\"resource_type\" class=\"block text-sm font-medium text-gray-700 mb-1\">Resource Type</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"resource_type\" name=\"resource_type\">
                        <option value=\"\">Select Type</option>
                        <option value=\"dataset\" ";
        // line 358
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 358) == "dataset")) {
            yield "selected";
        }
        yield ">Dataset</option>
                        <option value=\"service\" ";
        // line 359
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 359) == "service")) {
            yield "selected";
        }
        yield ">Service</option>
                        <option value=\"series\" ";
        // line 360
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 360) == "series")) {
            yield "selected";
        }
        yield ">Series</option>
                        <option value=\"other\" ";
        // line 361
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 361) == "other")) {
            yield "selected";
        }
        yield ">Other</option>
                    </select>
                </div>
                <div class=\"mb-4\">
                    <label for=\"resource_identifier\" class=\"block text-sm font-medium text-gray-700 mb-1\">Resource Identifier</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"resource_identifier\" name=\"resource_identifier\" 
                           placeholder=\"Unique identifier for the resource\"
                           value=\"";
        // line 369
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_identifier", [], "any", true, true, false, 369)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_identifier", [], "any", false, false, false, 369), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"maintenance_frequency\" class=\"block text-sm font-medium text-gray-700 mb-1\">Maintenance Frequency</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"maintenance_frequency\" name=\"maintenance_frequency\">
                        <option value=\"\">Select Frequency</option>
                        <option value=\"continual\" ";
        // line 376
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 376) == "continual")) {
            yield "selected";
        }
        yield ">Continual</option>
                        <option value=\"daily\" ";
        // line 377
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 377) == "daily")) {
            yield "selected";
        }
        yield ">Daily</option>
                        <option value=\"weekly\" ";
        // line 378
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 378) == "weekly")) {
            yield "selected";
        }
        yield ">Weekly</option>
                        <option value=\"fortnightly\" ";
        // line 379
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 379) == "fortnightly")) {
            yield "selected";
        }
        yield ">Fortnightly</option>
                        <option value=\"monthly\" ";
        // line 380
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 380) == "monthly")) {
            yield "selected";
        }
        yield ">Monthly</option>
                        <option value=\"quarterly\" ";
        // line 381
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 381) == "quarterly")) {
            yield "selected";
        }
        yield ">Quarterly</option>
                        <option value=\"biannually\" ";
        // line 382
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 382) == "biannually")) {
            yield "selected";
        }
        yield ">Biannually</option>
                        <option value=\"annually\" ";
        // line 383
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 383) == "annually")) {
            yield "selected";
        }
        yield ">Annually</option>
                        <option value=\"asNeeded\" ";
        // line 384
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 384) == "asNeeded")) {
            yield "selected";
        }
        yield ">As Needed</option>
                        <option value=\"irregular\" ";
        // line 385
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 385) == "irregular")) {
            yield "selected";
        }
        yield ">Irregular</option>
                        <option value=\"notPlanned\" ";
        // line 386
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 386) == "notPlanned")) {
            yield "selected";
        }
        yield ">Not Planned</option>
                        <option value=\"unknown\" ";
        // line 387
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 387) == "unknown")) {
            yield "selected";
        }
        yield ">Unknown</option>
                    </select>
                </div>
                <div class=\"mb-4\">
                    <label for=\"character_set\" class=\"block text-sm font-medium text-gray-700 mb-1\">Character Set</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"character_set\" name=\"character_set\">
                        <option value=\"\">Select Character Set</option>
                        <option value=\"UTF-8\" ";
        // line 395
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 395) == "UTF-8")) {
            yield "selected";
        }
        yield ">UTF-8</option>
                        <option value=\"UTF-16\" ";
        // line 396
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 396) == "UTF-16")) {
            yield "selected";
        }
        yield ">UTF-16</option>
                        <option value=\"ISO-8859-1\" ";
        // line 397
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 397) == "ISO-8859-1")) {
            yield "selected";
        }
        yield ">ISO-8859-1</option>
                        <option value=\"ISO-8859-2\" ";
        // line 398
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 398) == "ISO-8859-2")) {
            yield "selected";
        }
        yield ">ISO-8859-2</option>
                        <option value=\"ISO-8859-15\" ";
        // line 399
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 399) == "ISO-8859-15")) {
            yield "selected";
        }
        yield ">ISO-8859-15</option>
                    </select>
                </div>
            </div>

            <div class=\"mb-4\">
                <h3 class=\"text-lg font-medium text-gray-900 mb-3\">Distribution Information</h3>
                <div class=\"mb-4\">
                    <label for=\"data_format\" class=\"block text-sm font-medium text-gray-700 mb-1\">Data Format</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"data_format\" name=\"data_format[]\" multiple>
                        <option value=\"shapefile\" ";
        // line 410
        if (CoreExtension::inFilter("shapefile", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 410))) {
            yield "selected";
        }
        yield ">Shapefile</option>
                        <option value=\"geopackage\" ";
        // line 411
        if (CoreExtension::inFilter("geopackage", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 411))) {
            yield "selected";
        }
        yield ">GeoPackage</option>
                        <option value=\"geotiff\" ";
        // line 412
        if (CoreExtension::inFilter("geotiff", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 412))) {
            yield "selected";
        }
        yield ">GeoTIFF</option>
                        <option value=\"ecw\" ";
        // line 413
        if (CoreExtension::inFilter("ecw", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 413))) {
            yield "selected";
        }
        yield ">ECW</option>
                        <option value=\"jp2\" ";
        // line 414
        if (CoreExtension::inFilter("jp2", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 414))) {
            yield "selected";
        }
        yield ">JPEG2000</option>
                        <option value=\"sid\" ";
        // line 415
        if (CoreExtension::inFilter("sid", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 415))) {
            yield "selected";
        }
        yield ">MrSID</option>
                        <option value=\"asc\" ";
        // line 416
        if (CoreExtension::inFilter("asc", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 416))) {
            yield "selected";
        }
        yield ">ASCII Grid</option>
                        <option value=\"nc\" ";
        // line 417
        if (CoreExtension::inFilter("nc", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 417))) {
            yield "selected";
        }
        yield ">NetCDF</option>
                        <option value=\"wms\" ";
        // line 418
        if (CoreExtension::inFilter("wms", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 418))) {
            yield "selected";
        }
        yield ">WMS</option>
                        <option value=\"wfs\" ";
        // line 419
        if (CoreExtension::inFilter("wfs", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 419))) {
            yield "selected";
        }
        yield ">WFS</option>
                        <option value=\"wcs\" ";
        // line 420
        if (CoreExtension::inFilter("wcs", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 420))) {
            yield "selected";
        }
        yield ">WCS</option>
                        <option value=\"other\" ";
        // line 421
        if (CoreExtension::inFilter("other", CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 421))) {
            yield "selected";
        }
        yield ">Other</option>
                    </select>
                    <p class=\"mt-1 text-sm text-gray-500\">Hold Ctrl/Cmd to select multiple formats</p>
                </div>
                <div class=\"mb-4\">
                    <label for=\"distribution_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">Distribution URL</label>
                    <input type=\"url\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"distribution_url\" name=\"distribution_url\" placeholder=\"Download or access URL\"
                           value=\"";
        // line 429
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "distribution_url", [], "any", true, true, false, 429)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "distribution_url", [], "any", false, false, false, 429), "")) : ("")), "html", null, true);
        yield "\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"coupled_resource\" class=\"block text-sm font-medium text-gray-700 mb-1\">Coupled Resource</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"coupled_resource\" name=\"coupled_resource\" placeholder=\"Associated dataset if metadata is for a service\"
                           value=\"";
        // line 435
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coupled_resource", [], "any", true, true, false, 435)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coupled_resource", [], "any", false, false, false, 435), "")) : ("")), "html", null, true);
        yield "\">
                </div>
            </div>
        </div>

        <div class=\"flex justify-end space-x-4\">
            <button type=\"submit\" class=\"bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                ";
        // line 442
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "Update";
        } else {
            yield "Save";
        }
        yield " Metadata
            </button>
            <button type=\"button\" onclick=\"exportToXML()\" 
                    class=\"bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                Export to ISO 19115 + INSPIRE XML
            </button>
        </div>
    </form>
";
        yield from [];
    }

    // line 452
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 453
        yield "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
<script src=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js\"></script>
<script>
    let wmsLayer = null;
    // Initialize OpenLayers map
    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([
                ";
        // line 467
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 467) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 467))) {
            // line 468
            yield "                    ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::default(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 468) + CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 468)) / 2), 0), "html", null, true);
            yield ",
                ";
        } else {
            // line 469
            yield "0,";
        }
        // line 470
        yield "                ";
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 470) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 470))) {
            // line 471
            yield "                    ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::default(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 471) + CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 471)) / 2), 0), "html", null, true);
            yield "
                ";
        } else {
            // line 472
            yield "0";
        }
        // line 473
        yield "            ]),
            zoom: 5
        })
    });

    // If in edit mode and we have WMS info, initialize the layer select
    ";
        // line 479
        if (((($context["is_edit"] ?? null) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_url", [], "any", false, false, false, 479)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_layer", [], "any", false, false, false, 479))) {
            // line 480
            yield "    document.addEventListener('DOMContentLoaded', function() {
        const wmsUrl = document.getElementById('wms_url');
        const layerSelect = document.getElementById('wms_layer');
        const layerSelectContainer = document.getElementById('layerSelectContainer');
        
        // Show the layer select container
        layerSelectContainer.classList.remove('hidden');
        
        // Fetch layers for the WMS service
        fetch('/wms/capabilities', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url: wmsUrl.value })
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                // Populate the layer select
                result.layers.forEach(layer => {
                    const option = document.createElement('option');
                    option.value = layer.name;
                    option.textContent = layer.title;
                    option.dataset.metadata = JSON.stringify(layer);
                    if (layer.name === '";
            // line 505
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_layer", [], "any", false, false, false, 505), "html", null, true);
            yield "') {
                        option.selected = true;
                    }
                    layerSelect.appendChild(option);
                });
                
                // Trigger the change event to update the map
                layerSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => {
            console.error('Error fetching WMS layers:', error);
        });
    });
    ";
        }
        // line 520
        yield "
    function exportToXML() {
        const metadataId = new URLSearchParams(window.location.search).get('id');
        if (metadataId) {
            window.location.href = `/metadata/\${metadataId}/xml`;
        } else {
            alert('Please save the metadata first before exporting to XML');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up form handlers');
        
        const form = document.getElementById('metadataForm');
        const gisFilesInput = document.getElementById('gis_files');
        const thumbnailInput = document.getElementById('thumbnail');
        
        if (!form) {
            console.error('Form element not found!');
            return;
        }
        
        // Handle GIS file selection and spatial extent extraction
        if (gisFilesInput) {
            gisFilesInput.addEventListener('change', async function(e) {
                e.preventDefault();
                console.log('GIS files selected:', this.files);
                
                if (this.files.length === 0) {
                    return;
                }

                // Validate file types
                const allowedTypes = ['.zip', '.shp', '.gpkg', '.tif', '.tiff', '.geotiff', '.img', '.ecw', '.jp2', '.sid', '.asc', '.grd', '.nc'];
                const invalidFiles = Array.from(this.files).filter(file => {
                    const ext = '.' + file.name.split('.').pop().toLowerCase();
                    return !allowedTypes.includes(ext);
                });

                if (invalidFiles.length > 0) {
                    alert('Invalid file type(s): ' + invalidFiles.map(f => f.name).join(', ') + 
                          '\\nSupported formats: Shapefile (.zip), GeoPackage (.gpkg), GeoTIFF (.tif, .tiff), ' +
                          'ECW (.ecw), JPEG2000 (.jp2), MrSID (.sid), ASCII Grid (.asc), NetCDF (.nc)');
                    this.value = ''; // Clear the input
                    return;
                }

                // Create FormData for spatial extent extraction only
                const formData = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    formData.append('gis_files[]', this.files[i]);
                    console.log('Added file to FormData for extent extraction:', this.files[i].name);
                }
                
                try {
                    // Send files to server for spatial extent extraction only
                    console.log('Sending files for spatial extent extraction');
                    const response = await fetch('/metadata/extract-spatial-extent', {
                        method: 'POST',
                        body: formData
                    });
                    
                    console.log('Response status:', response.status);
                    const responseText = await response.text();
                    console.log('Raw response text:', responseText);
                    
                    let result;
                    try {
                        result = JSON.parse(responseText);
                        console.log('Parsed response:', result);
                    } catch (error) {
                        console.error('Error parsing JSON response:', error);
                        console.error('Response text was:', responseText);
                        throw new Error('Invalid server response: ' + responseText);
                    }
                    
                    if (result.status === 'success' && result.spatial_extent) {
                        // Update the form fields with the extracted extents
                        const { west_longitude, east_longitude, south_latitude, north_latitude } = result.spatial_extent;
                        console.log('Extracted extents:', result.spatial_extent);
                        
                        document.getElementById('west_longitude').value = west_longitude;
                        document.getElementById('east_longitude').value = east_longitude;
                        document.getElementById('south_latitude').value = south_latitude;
                        document.getElementById('north_latitude').value = north_latitude;
                        
                        // Update the map view
                        const extent = [west_longitude, south_latitude, east_longitude, north_latitude];
                        console.log('Original extent:', extent);
                        
                        // Transform the extent from EPSG:4326 to EPSG:3857
                        const transformedExtent = ol.proj.transformExtent(extent, 'EPSG:4326', 'EPSG:3857');
                        console.log('Transformed extent:', transformedExtent);
                        
                        // Create a feature with the transformed extent
                        const extentFeature = new ol.Feature({
                            geometry: new ol.geom.Polygon.fromExtent(transformedExtent)
                        });
                        
                        // Remove any existing extent layer
                        map.getLayers().forEach(layer => {
                            if (layer.get('name') === 'extent') {
                                map.removeLayer(layer);
                            }
                        });
                        
                        // Add new extent layer
                        const extentLayer = new ol.layer.Vector({
                            name: 'extent',
                            source: new ol.source.Vector({
                                features: [extentFeature]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({
                                    color: 'rgba(255, 0, 0, 1.0)',
                                    width: 2
                                }),
                                fill: new ol.style.Fill({
                                    color: 'rgba(255, 0, 0, 0.1)'
                                })
                            })
                        });
                        map.addLayer(extentLayer);
                        
                        // Fit map to transformed extent
                        map.getView().fit(transformedExtent, {
                            padding: [50, 50, 50, 50],
                            maxZoom: 15,
                            duration: 1000
                        });
                        
                        // Show success message
                        const uploadedFilesDiv = document.getElementById('uploadedFiles');
                        uploadedFilesDiv.innerHTML = `
                            <div class=\"bg-green-50 p-4 rounded-md\">
                                <p class=\"text-sm text-green-700\">
                                    Spatial extents extracted successfully:<br>
                                    West: \${west_longitude}<br>
                                    East: \${east_longitude}<br>
                                    South: \${south_latitude}<br>
                                    North: \${north_latitude}
                                </p>
                            </div>`;
                            
                        // Also show the selected files
                        Array.from(this.files).forEach(file => {
                            const fileDiv = document.createElement('div');
                            fileDiv.className = 'bg-gray-50 p-4 rounded-md mt-2';
                            fileDiv.innerHTML = `
                                <div class=\"flex items-start justify-between\">
                                    <div>
                                        <h4 class=\"text-sm font-medium text-gray-900\">\${file.name}</h4>
                                        <p class=\"text-sm text-gray-500\">
                                            Type: \${file.name.split('.').pop().toLowerCase()}<br>
                                            Size: \${(file.size / 1024).toFixed(2)} KB
                                        </p>
                                    </div>
                                </div>`;
                            uploadedFilesDiv.appendChild(fileDiv);
                        });
                    } else {
                        throw new Error(result.message || 'Error extracting spatial extents');
                    }
                } catch (error) {
                    console.error('Error in spatial extent extraction:', error);
                    const uploadedFilesDiv = document.getElementById('uploadedFiles');
                    uploadedFilesDiv.innerHTML = `
                        <div class=\"bg-red-50 p-4 rounded-md\">
                            <p class=\"text-sm text-red-700\">
                                Error extracting spatial extents: \${error.message}
                            </p>
                        </div>`;
                }
            });
        }
        
        // Handle thumbnail selection
        if (thumbnailInput) {
            thumbnailInput.addEventListener('change', function(e) {
                e.preventDefault();
                console.log('Thumbnail selected:', this.files);
                
                if (this.files.length === 0) {
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const file = this.files[0];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
                    this.value = ''; // Clear the input
                    return;
                }

                // Update UI to show selected thumbnail
                const uploadedFilesDiv = document.getElementById('uploadedFiles');
                const thumbnailDiv = document.createElement('div');
                thumbnailDiv.className = 'bg-gray-50 p-4 rounded-md';
                thumbnailDiv.innerHTML = `
                    <div class=\"flex items-start justify-between\">
                        <div>
                            <h4 class=\"text-sm font-medium text-gray-900\">\${file.name}</h4>
                            <p class=\"text-sm text-gray-500\">
                                Type: Thumbnail (\${file.type})<br>
                                Size: \${(file.size / 1024).toFixed(2)} KB
                            </p>
                        </div>
                    </div>`;
                uploadedFilesDiv.appendChild(thumbnailDiv);
            });
        }
        
        // Handle form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submit event triggered');
            
            const formData = new FormData(this);
            const isEdit = ";
        // line 739
        if ((($tmp = ($context["is_edit"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield "true";
        } else {
            yield "false";
        }
        yield ";
            
            try {
                // Use the correct URL for metadata updates
                const url = isEdit ? `/metadata/";
        // line 743
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 743), "html", null, true);
        yield "/update` : '/metadata';
                const method = isEdit ? 'PUT' : 'POST';
                
                console.log('Submitting to URL:', url);
                console.log('Using method:', method);
                
                // For PUT requests, we need to handle both JSON data and file uploads
                if (isEdit) {
                    // Check if there are any files to upload
                    const hasFiles = formData.get('thumbnail')?.size > 0 || 
                                   Array.from(formData.getAll('gis_files[]')).some(file => file.size > 0);
                    
                    if (hasFiles) {
                        // If there are files, send as FormData with _method=PUT
                        formData.append('_method', 'PUT');
                        const response = await fetch(url, {
                            method: 'POST', // Use POST for file uploads
                            body: formData
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server response:', errorText);
                            throw new Error(errorText || 'Server error');
                        }
                        
                        const result = await response.json();
                        console.log('Update response:', result);
                        
                        if (result.status === 'success') {
                            window.location.href = `/datasets/\${result.id}`;
                        } else {
                            throw new Error(result.message || 'Error updating metadata');
                        }
                    } else {
                        // If no files, send as JSON
                        const jsonData = {};
                        formData.forEach((value, key) => {
                            // Handle arrays (like data_format)
                            if (key.endsWith('[]')) {
                                const baseKey = key.slice(0, -2);
                                if (!jsonData[baseKey]) {
                                    jsonData[baseKey] = [];
                                }
                                jsonData[baseKey].push(value);
                            } else {
                                jsonData[key] = value;
                            }
                        });
                        
                        console.log('Sending update data:', jsonData);
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(jsonData)
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server response:', errorText);
                            throw new Error(errorText || 'Server error');
                        }
                        
                        const result = await response.json();
                        console.log('Update response:', result);
                        
                        if (result.status === 'success') {
                            window.location.href = `/datasets/\${result.id}`;
                        } else {
                            throw new Error(result.message || 'Error updating metadata');
                        }
                    }
                } else {
                    // For POST requests (new records), send FormData as is
                    console.log('Sending POST request with FormData');
                    
                    // Show loading state
                    const submitButton = form.querySelector('button[type=\"submit\"]');
                    const originalButtonText = submitButton.textContent;
                    submitButton.disabled = true;
                    submitButton.textContent = 'Saving...';
                    
                    try {
                        const response = await fetch(url, {
                            method: method,
                            body: formData
                        });
                        
                        console.log('Response received:', response.status);
                        const responseText = await response.text();
                        console.log('Response text:', responseText);
                        
                        let result;
                        try {
                            result = JSON.parse(responseText);
                        } catch (error) {
                            console.error('Error parsing JSON response:', error);
                            throw new Error('Invalid server response: ' + responseText);
                        }

                        if (result.status === 'success') {
                            // If we have spatial extents from the uploaded files, update the map
                            if (result.files && result.files.some(f => f.west_longitude !== null)) {
                                const filesWithExtent = result.files.filter(f => f.west_longitude !== null);
                                if (filesWithExtent.length > 0) {
                                    // Use the first file's extent for now
                                    const { west_longitude, east_longitude, south_latitude, north_latitude } = filesWithExtent[0];
                                    
                                    // Update the map view
                                    const extent = [west_longitude, south_latitude, east_longitude, north_latitude];
                                    const transformedExtent = ol.proj.transformExtent(extent, 'EPSG:4326', 'EPSG:3857');
                                    
                                    // Create a feature with the transformed extent
                                    const extentFeature = new ol.Feature({
                                        geometry: new ol.geom.Polygon.fromExtent(transformedExtent)
                                    });
                                    
                                    // Remove any existing extent layer
                                    map.getLayers().forEach(layer => {
                                        if (layer.get('name') === 'extent') {
                                            map.removeLayer(layer);
                                        }
                                    });
                                    
                                    // Add new extent layer
                                    const extentLayer = new ol.layer.Vector({
                                        name: 'extent',
                                        source: new ol.source.Vector({
                                            features: [extentFeature]
                                        }),
                                        style: new ol.style.Style({
                                            stroke: new ol.style.Stroke({
                                                color: 'rgba(255, 0, 0, 1.0)',
                                                width: 2
                                            }),
                                            fill: new ol.style.Fill({
                                                color: 'rgba(255, 0, 0, 0.1)'
                                            })
                                        })
                                    });
                                    map.addLayer(extentLayer);
                                    
                                    // Fit map to transformed extent
                                    map.getView().fit(transformedExtent, {
                                        padding: [50, 50, 50, 50],
                                        maxZoom: 15,
                                        duration: 1000
                                    });
                                }
                            }
                            
                            // Update the uploaded files display
                            const uploadedFilesDiv = document.getElementById('uploadedFiles');
                            uploadedFilesDiv.innerHTML = ''; // Clear previous content
                            
                            result.files.forEach(file => {
                                const fileDiv = document.createElement('div');
                                fileDiv.className = 'bg-gray-50 p-4 rounded-md';
                                fileDiv.innerHTML = `
                                    <div class=\"flex items-start justify-between\">
                                        <div>
                                            <h4 class=\"text-sm font-medium text-gray-900\">\${file.file_name}</h4>
                                            <p class=\"text-sm text-gray-500\">
                                                Type: \${file.file_type}<br>
                                                Size: \${(file.file_size / 1024).toFixed(2)} KB
                                            </p>
                                            \${file.west_longitude !== null ? `
                                            <p class=\"text-sm text-gray-500 mt-2\">
                                                Extent:<br>
                                                West: \${file.west_longitude}<br>
                                                East: \${file.east_longitude}<br>
                                                South: \${file.south_latitude}<br>
                                                North: \${file.north_latitude}
                                            </p>
                                            ` : ''}
                                        </div>
                                    </div>`;
                                uploadedFilesDiv.appendChild(fileDiv);
                            });
                            
                            // Clear the file inputs
                            document.getElementById('gis_files').value = '';
                            document.getElementById('thumbnail').value = '';
                            
                            // Redirect to the dataset view
                            window.location.href = `/datasets/\${result.id}`;
                        } else {
                            throw new Error(result.message || 'Error saving metadata');
                        }
                    } finally {
                        // Restore button state
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    }
                }
            } catch (error) {
                console.error('Error in form submission:', error);
                alert(error.message || 'An error occurred while saving the metadata');
            }
        });
        
        console.log('Form handlers attached');
    });

    // WMS Layer Selection functionality
    document.getElementById('fetchLayersBtn').addEventListener('click', async function() {
        const wmsUrl = document.getElementById('wms_url').value;
        if (!wmsUrl) {
            alert('Please enter a WMS service URL');
            return;
        }

        try {
            const response = await fetch('/wms/capabilities', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ url: wmsUrl })
            });

            const result = await response.json();
            if (result.status === 'success') {
                const layerSelect = document.getElementById('wms_layer');
                layerSelect.innerHTML = '<option value=\"\">Select a layer...</option>';
                
                // Store service metadata for later use
                layerSelect.dataset.serviceMetadata = JSON.stringify(result.service);
                
                result.layers.forEach(layer => {
                    const option = document.createElement('option');
                    option.value = layer.name;
                    option.textContent = layer.title;
                    option.dataset.metadata = JSON.stringify(layer);
                    layerSelect.appendChild(option);
                });

                document.getElementById('layerSelectContainer').classList.remove('hidden');

                // Populate service-level metadata
                const serviceMetadata = result.service;
                if (serviceMetadata.title && !document.getElementById('title').value) {
                    document.getElementById('title').value = serviceMetadata.title;
                }
                if (serviceMetadata.abstract && !document.getElementById('abstract').value) {
                    document.getElementById('abstract').value = serviceMetadata.abstract;
                }
                if (serviceMetadata.keywords && serviceMetadata.keywords.length > 0 && !document.getElementById('keywords').value) {
                    document.getElementById('keywords').value = serviceMetadata.keywords.join(', ');
                }
                if (serviceMetadata.responsible_org && !document.getElementById('responsible_org').value) {
                    document.getElementById('responsible_org').value = serviceMetadata.responsible_org;
                }
                if (serviceMetadata.responsible_person && !document.getElementById('responsible_person').value) {
                    document.getElementById('responsible_person').value = serviceMetadata.responsible_person;
                }
                if (serviceMetadata.role && !document.getElementById('role').value) {
                    document.getElementById('role').value = serviceMetadata.role;
                }
                if (serviceMetadata.spatial_data_service_url && !document.getElementById('spatial_data_service_url').value) {
                    document.getElementById('spatial_data_service_url').value = serviceMetadata.spatial_data_service_url;
                }
            } else {
                alert('Error fetching layers: ' + result.message);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    document.getElementById('wms_layer').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const layerMetadata = JSON.parse(selectedOption.dataset.metadata);
            
            // Update spatial extent fields
            document.getElementById('west_longitude').value = layerMetadata.bbox[0];
            document.getElementById('south_latitude').value = layerMetadata.bbox[1];
            document.getElementById('east_longitude').value = layerMetadata.bbox[2];
            document.getElementById('north_latitude').value = layerMetadata.bbox[3];

            // Update other metadata fields if they're empty
            if (layerMetadata.title && !document.getElementById('title').value) {
                document.getElementById('title').value = layerMetadata.title;
            }
            if (layerMetadata.abstract && !document.getElementById('abstract').value) {
                document.getElementById('abstract').value = layerMetadata.abstract;
            }
            if (layerMetadata.keywords && layerMetadata.keywords.length > 0 && !document.getElementById('keywords').value) {
                document.getElementById('keywords').value = layerMetadata.keywords.join(', ');
            }
            if (layerMetadata.coordinate_system && !document.getElementById('coordinate_system').value) {
                document.getElementById('coordinate_system').value = layerMetadata.coordinate_system;
            }

            // Update map
            if (wmsLayer) {
                map.removeLayer(wmsLayer);
            }

            const wmsUrl = document.getElementById('wms_url').value;
            wmsLayer = new ol.layer.Tile({
                source: new ol.source.TileWMS({
                    url: wmsUrl,
                    params: {
                        'LAYERS': selectedOption.value,
                        'TILED': true
                    },
                    serverType: 'geoserver'
                })
            });

            map.addLayer(wmsLayer);

            // Fit map to layer extent
            const extent = ol.proj.transformExtent(
                layerMetadata.bbox,
                'EPSG:4326',
                'EPSG:3857'
            );
            map.getView().fit(extent, { padding: [50, 50, 50, 50] });
        }
    });
</script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "form.twig";
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
        return array (  1247 => 743,  1236 => 739,  1015 => 520,  997 => 505,  970 => 480,  968 => 479,  960 => 473,  957 => 472,  951 => 471,  948 => 470,  945 => 469,  939 => 468,  937 => 467,  921 => 453,  914 => 452,  896 => 442,  886 => 435,  877 => 429,  864 => 421,  858 => 420,  852 => 419,  846 => 418,  840 => 417,  834 => 416,  828 => 415,  822 => 414,  816 => 413,  810 => 412,  804 => 411,  798 => 410,  782 => 399,  776 => 398,  770 => 397,  764 => 396,  758 => 395,  745 => 387,  739 => 386,  733 => 385,  727 => 384,  721 => 383,  715 => 382,  709 => 381,  703 => 380,  697 => 379,  691 => 378,  685 => 377,  679 => 376,  669 => 369,  656 => 361,  650 => 360,  644 => 359,  638 => 358,  624 => 347,  614 => 340,  604 => 333,  587 => 319,  575 => 312,  569 => 311,  563 => 310,  553 => 303,  536 => 291,  530 => 290,  524 => 289,  514 => 282,  500 => 271,  491 => 265,  482 => 259,  467 => 247,  458 => 241,  443 => 229,  434 => 223,  418 => 210,  409 => 204,  400 => 198,  391 => 192,  339 => 143,  321 => 130,  315 => 129,  309 => 128,  303 => 127,  297 => 126,  287 => 119,  278 => 113,  269 => 107,  255 => 96,  247 => 90,  238 => 87,  229 => 86,  225 => 85,  216 => 78,  207 => 75,  198 => 74,  194 => 73,  184 => 66,  175 => 60,  167 => 55,  159 => 50,  149 => 42,  145 => 40,  143 => 39,  132 => 35,  122 => 34,  112 => 32,  105 => 31,  77 => 6,  70 => 5,  54 => 3,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}{% if is_edit %}Edit{% else %}New{% endif %} ISO 19115 + INSPIRE Metadata{% endblock %}

{% block extra_css %}
<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css\">
<style>
    .required::after {
        content: \" *\";
        color: red;
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: white;
    }
    
    #map {
        width: 100%;
        height: 400px;
        margin-top: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
</style>
{% endblock %}

{% block content %}
    <h1 class=\"text-3xl font-bold mb-8\">{% if is_edit %}Edit{% else %}New{% endif %} ISO 19115 + INSPIRE Metadata</h1>
    
    <form id=\"metadataForm\" action=\"{% if is_edit %}/metadata/{{ dataset.id }}/update{% else %}/metadata{% endif %}\" 
          method=\"{% if is_edit %}PUT{% else %}POST{% endif %}\" 
          enctype=\"multipart/form-data\" 
          class=\"space-y-8\">
        
        {% if is_edit %}
        <input type=\"hidden\" name=\"_method\" value=\"PUT\">
        {% endif %}

        <!-- Identification Info Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Identification Info</h2>
            <div class=\"mb-4\">
                <label for=\"title\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Title</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"title\" name=\"title\" required
                       value=\"{{ dataset.title|default('') }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"abstract\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Abstract</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"abstract\" name=\"abstract\" rows=\"3\" required>{{ dataset.abstract|default('') }}</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"purpose\" class=\"block text-sm font-medium text-gray-700 mb-1\">Purpose</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"purpose\" name=\"purpose\" rows=\"2\">{{ dataset.purpose|default('') }}</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"keywords\" class=\"block text-sm font-medium text-gray-700 mb-1\">Keywords (comma separated)</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"keywords\" name=\"keywords\"
                       value=\"{{ dataset.keywords ? dataset.keywords|join(', ') : '' }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"topic\" class=\"block text-sm font-medium text-gray-700 mb-1\">Topic</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"topic\" name=\"topic\">
                    <option value=\"\">Select Topic</option>
                    {% for topic in topics %}
                        <option value=\"{{ topic.id }}\" {% if dataset.topic_id == topic.id %}selected{% endif %}>
                            {{ topic.topic }}
                        </option>
                    {% endfor %}
                </select>
            </div>
            <div class=\"mb-4\">
                <label for=\"inspire_theme\" class=\"block text-sm font-medium text-gray-700 mb-1\">INSPIRE Theme</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"inspire_theme\" name=\"inspire_theme\">
                    <option value=\"\">Select INSPIRE Theme</option>
                    {% for keyword in keywords %}
                        <option value=\"{{ keyword.id }}\" {% if dataset.inspire_theme_id == keyword.id %}selected{% endif %}>
                            {{ keyword.keyword }}
                        </option>
                    {% endfor %}
                </select>
            </div>
            <div class=\"mb-4\">
                <label for=\"metadata_language\" class=\"block text-sm font-medium text-gray-700 mb-1\">Metadata Language</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"metadata_language\" name=\"metadata_language\" placeholder=\"e.g., en, fr, de\"
                       value=\"{{ dataset.metadata_language|default('') }}\">
            </div>
        </div>

        <!-- Citation Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Citation</h2>
            <div class=\"mb-4\">
                <label for=\"citation_date\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Citation Date</label>
                <input type=\"date\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"citation_date\" name=\"citation_date\" required
                       value=\"{{ dataset.citation_date|date('Y-m-d') }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"responsible_org\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">Responsible Organization</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"responsible_org\" name=\"responsible_org\" required
                       value=\"{{ dataset.responsible_org|default('') }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"responsible_person\" class=\"block text-sm font-medium text-gray-700 mb-1\">Responsible Person</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"responsible_person\" name=\"responsible_person\"
                       value=\"{{ dataset.responsible_person|default('') }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"role\" class=\"block text-sm font-medium text-gray-700 mb-1\">Role</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"role\" name=\"role\">
                    <option value=\"\">Select Role</option>
                    <option value=\"pointOfContact\" {% if dataset.role == 'pointOfContact' %}selected{% endif %}>Point of Contact</option>
                    <option value=\"originator\" {% if dataset.role == 'originator' %}selected{% endif %}>Originator</option>
                    <option value=\"publisher\" {% if dataset.role == 'publisher' %}selected{% endif %}>Publisher</option>
                    <option value=\"author\" {% if dataset.role == 'author' %}selected{% endif %}>Author</option>
                    <option value=\"custodian\" {% if dataset.role == 'custodian' %}selected{% endif %}>Custodian</option>
                </select>
            </div>
        </div>

        <!-- WMS Layer Selector Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">WMS Layer Selection</h2>
            <div class=\"mb-4\">
                <label for=\"wms_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">WMS Service URL</label>
                <div class=\"flex space-x-2\">
                    <input type=\"url\" class=\"flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"wms_url\" name=\"wms_url\" placeholder=\"https://example.com/geoserver/wms\"
                           value=\"{{ dataset.wms_url }}\">
                    <button type=\"button\" class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\" 
                            id=\"fetchLayersBtn\">Fetch Layers</button>
                </div>
            </div>
            <div class=\"mb-4 hidden\" id=\"layerSelectContainer\">
                <label for=\"wms_layer\" class=\"block text-sm font-medium text-gray-700 mb-1\">Select Layer</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"wms_layer\" name=\"wms_layer\">
                    <option value=\"\">Select a layer...</option>
                </select>
            </div>
            <div id=\"map\"></div>
        </div>

        <!-- GIS File Upload Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">GIS File Upload</h2>
            <div class=\"mb-4\">
                <label for=\"gis_files\" class=\"block text-sm font-medium text-gray-700 mb-1\">Upload GIS Files</label>
                <input type=\"file\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"gis_files\" name=\"gis_files[]\" multiple 
                       accept=\".zip,.shp,.gpkg,.tif,.tiff,.geotiff,.img,.ecw,.jp2,.sid,.asc,.grd,.nc\">
                <p class=\"mt-1 text-sm text-gray-500\">
                    Supported formats: Shapefile (.zip), GeoPackage (.gpkg), GeoTIFF (.tif, .tiff), 
                    ECW (.ecw), JPEG2000 (.jp2), MrSID (.sid), ASCII Grid (.asc), NetCDF (.nc)
                </p>
            </div>
            <div class=\"mb-4\">
                <label for=\"thumbnail\" class=\"block text-sm font-medium text-gray-700 mb-1\">Dataset Thumbnail</label>
                <input type=\"file\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"thumbnail\" name=\"thumbnail\" accept=\"image/jpeg,image/png,image/gif\">
                <p class=\"mt-1 text-sm text-gray-500\">
                    Upload a thumbnail image for the dataset (JPEG, PNG, or GIF format)
                </p>
            </div>
            <div id=\"uploadedFiles\" class=\"mt-4 space-y-4\">
                <!-- Uploaded files will be listed here -->
            </div>
        </div>

        <!-- Geographic Extent Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Geographic Extent</h2>
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">
                <div class=\"mb-4\">
                    <label for=\"west_longitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">West Longitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"west_longitude\" name=\"west_longitude\" step=\"0.000001\" required
                           value=\"{{ dataset.west_longitude|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"east_longitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">East Longitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"east_longitude\" name=\"east_longitude\" step=\"0.000001\" required
                           value=\"{{ dataset.east_longitude|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"south_latitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">South Latitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"south_latitude\" name=\"south_latitude\" step=\"0.000001\" required
                           value=\"{{ dataset.south_latitude|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"north_latitude\" class=\"block text-sm font-medium text-gray-700 mb-1 required\">North Latitude</label>
                    <input type=\"number\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"north_latitude\" name=\"north_latitude\" step=\"0.000001\" required
                           value=\"{{ dataset.north_latitude|default('') }}\">
                </div>
            </div>
        </div>

        <!-- Temporal Extent Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Temporal Extent</h2>
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">
                <div class=\"mb-4\">
                    <label for=\"start_date\" class=\"block text-sm font-medium text-gray-700 mb-1\">Start Date</label>
                    <input type=\"date\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"start_date\" name=\"start_date\"
                           value=\"{{ dataset.start_date|date('Y-m-d') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"end_date\" class=\"block text-sm font-medium text-gray-700 mb-1\">End Date</label>
                    <input type=\"date\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"end_date\" name=\"end_date\"
                           value=\"{{ dataset.end_date|date('Y-m-d') }}\">
                </div>
            </div>
        </div>

        <!-- Spatial Representation Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Spatial Representation</h2>
            <div class=\"mb-4\">
                <label for=\"coordinate_system\" class=\"block text-sm font-medium text-gray-700 mb-1\">Coordinate System (EPSG Code)</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"coordinate_system\" name=\"coordinate_system\" placeholder=\"e.g., EPSG:4326\"
                       value=\"{{ dataset.coordinate_system|default('') }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"spatial_resolution\" class=\"block text-sm font-medium text-gray-700 mb-1\">Spatial Resolution</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"spatial_resolution\" name=\"spatial_resolution\" placeholder=\"e.g., 30m, 1:10000, 0.5 degrees\"
                       value=\"{{ dataset.spatial_resolution|default('') }}\">
                <p class=\"mt-1 text-sm text-gray-500\">Enter the scale or equivalent resolution of the data (e.g., 30m, 1:10000, 0.5 degrees)</p>
            </div>
        </div>

        <!-- Constraints Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Constraints</h2>
            <div class=\"mb-4\">
                <label for=\"access_constraints\" class=\"block text-sm font-medium text-gray-700 mb-1\">Access Constraints</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"access_constraints\" name=\"access_constraints\" rows=\"2\" 
                          placeholder=\"Restrictions and legal prerequisites for accessing the dataset\">{{ dataset.access_constraints|default('') }}</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"use_constraints\" class=\"block text-sm font-medium text-gray-700 mb-1\">Use Constraints</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"use_constraints\" name=\"use_constraints\" rows=\"2\" 
                          placeholder=\"Restrictions and legal prerequisites for using the dataset\">{{ dataset.use_constraints|default('') }}</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"use_limitation\" class=\"block text-sm font-medium text-gray-700 mb-1\">Use Limitation</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"use_limitation\" name=\"use_limitation\" rows=\"2\" 
                          placeholder=\"General restrictions and legal prerequisites for using the dataset\">{{ dataset.use_limitation|default('') }}</textarea>
            </div>
        </div>

        <!-- Data Quality Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Data Quality</h2>
            <div class=\"mb-4\">
                <label for=\"lineage\" class=\"block text-sm font-medium text-gray-700 mb-1\">Lineage</label>
                <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                          id=\"lineage\" name=\"lineage\" rows=\"2\" 
                          placeholder=\"Statement about data quality and origin\">{{ dataset.lineage|default('') }}</textarea>
            </div>
            <div class=\"mb-4\">
                <label for=\"resource_type\" class=\"block text-sm font-medium text-gray-700 mb-1\">Scope</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"resource_type\" name=\"resource_type\">
                    <option value=\"\">Select Scope</option>
                    <option value=\"dataset\" {% if dataset.resource_type == 'dataset' %}selected{% endif %}>Dataset</option>
                    <option value=\"series\" {% if dataset.resource_type == 'series' %}selected{% endif %}>Series</option>
                    <option value=\"service\" {% if dataset.resource_type == 'service' %}selected{% endif %}>Service</option>
                </select>
            </div>
        </div>

        <!-- INSPIRE Metadata Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">INSPIRE Metadata</h2>
            <div class=\"mb-4\">
                <label for=\"point_of_contact_org\" class=\"block text-sm font-medium text-gray-700 mb-1\">INSPIRE Point of Contact Organization</label>
                <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"point_of_contact_org\" name=\"point_of_contact_org\"
                       value=\"{{ dataset.point_of_contact_org|default('') }}\">
            </div>
            <div class=\"mb-4\">
                <label for=\"conformity_result\" class=\"block text-sm font-medium text-gray-700 mb-1\">Conformity Result</label>
                <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                        id=\"conformity_result\" name=\"conformity_result\">
                    <option value=\"\">Select Result</option>
                    <option value=\"conformant\" {% if dataset.conformity_result == 'conformant' %}selected{% endif %}>Conformant</option>
                    <option value=\"non-conformant\" {% if dataset.conformity_result == 'non-conformant' %}selected{% endif %}>Non-conformant</option>
                    <option value=\"unknown\" {% if dataset.conformity_result == 'unknown' %}selected{% endif %}>Unknown</option>
                </select>
            </div>
            <div class=\"mb-4\">
                <label for=\"spatial_data_service_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">Spatial Data Service URL</label>
                <input type=\"url\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                       id=\"spatial_data_service_url\" name=\"spatial_data_service_url\"
                       value=\"{{ dataset.spatial_data_service_url|default('') }}\">
            </div>
        </div>

        <!-- Additional Metadata Section -->
        <div class=\"form-section\">
            <h2 class=\"text-xl font-semibold mb-4\">Additional Metadata</h2>
            <div class=\"mb-4\">
                <h3 class=\"text-lg font-medium text-gray-900 mb-3\">Metadata Point of Contact</h3>
                <div class=\"mb-4\">
                    <label for=\"metadata_poc_organization\" class=\"block text-sm font-medium text-gray-700 mb-1\">Organization</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"metadata_poc_organization\" name=\"metadata_poc_organization\" 
                           placeholder=\"Organization of the metadata point of contact\"
                           value=\"{{ dataset.metadata_poc_organization|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"metadata_poc_email\" class=\"block text-sm font-medium text-gray-700 mb-1\">Email</label>
                    <input type=\"email\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"metadata_poc_email\" name=\"metadata_poc_email\" 
                           placeholder=\"Email address of the metadata point of contact\"
                           value=\"{{ dataset.metadata_poc_email|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"metadata_poc_role\" class=\"block text-sm font-medium text-gray-700 mb-1\">Role</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"metadata_poc_role\" name=\"metadata_poc_role\" 
                           placeholder=\"Role of the metadata point of contact\"
                           value=\"{{ dataset.metadata_poc_role|default('') }}\">
                </div>
            </div>

            <div class=\"mb-4\">
                <h3 class=\"text-lg font-medium text-gray-900 mb-3\">Resource Information</h3>
                <div class=\"mb-4\">
                    <label for=\"resource_type\" class=\"block text-sm font-medium text-gray-700 mb-1\">Resource Type</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"resource_type\" name=\"resource_type\">
                        <option value=\"\">Select Type</option>
                        <option value=\"dataset\" {% if dataset.resource_type == 'dataset' %}selected{% endif %}>Dataset</option>
                        <option value=\"service\" {% if dataset.resource_type == 'service' %}selected{% endif %}>Service</option>
                        <option value=\"series\" {% if dataset.resource_type == 'series' %}selected{% endif %}>Series</option>
                        <option value=\"other\" {% if dataset.resource_type == 'other' %}selected{% endif %}>Other</option>
                    </select>
                </div>
                <div class=\"mb-4\">
                    <label for=\"resource_identifier\" class=\"block text-sm font-medium text-gray-700 mb-1\">Resource Identifier</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"resource_identifier\" name=\"resource_identifier\" 
                           placeholder=\"Unique identifier for the resource\"
                           value=\"{{ dataset.resource_identifier|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"maintenance_frequency\" class=\"block text-sm font-medium text-gray-700 mb-1\">Maintenance Frequency</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"maintenance_frequency\" name=\"maintenance_frequency\">
                        <option value=\"\">Select Frequency</option>
                        <option value=\"continual\" {% if dataset.maintenance_frequency == 'continual' %}selected{% endif %}>Continual</option>
                        <option value=\"daily\" {% if dataset.maintenance_frequency == 'daily' %}selected{% endif %}>Daily</option>
                        <option value=\"weekly\" {% if dataset.maintenance_frequency == 'weekly' %}selected{% endif %}>Weekly</option>
                        <option value=\"fortnightly\" {% if dataset.maintenance_frequency == 'fortnightly' %}selected{% endif %}>Fortnightly</option>
                        <option value=\"monthly\" {% if dataset.maintenance_frequency == 'monthly' %}selected{% endif %}>Monthly</option>
                        <option value=\"quarterly\" {% if dataset.maintenance_frequency == 'quarterly' %}selected{% endif %}>Quarterly</option>
                        <option value=\"biannually\" {% if dataset.maintenance_frequency == 'biannually' %}selected{% endif %}>Biannually</option>
                        <option value=\"annually\" {% if dataset.maintenance_frequency == 'annually' %}selected{% endif %}>Annually</option>
                        <option value=\"asNeeded\" {% if dataset.maintenance_frequency == 'asNeeded' %}selected{% endif %}>As Needed</option>
                        <option value=\"irregular\" {% if dataset.maintenance_frequency == 'irregular' %}selected{% endif %}>Irregular</option>
                        <option value=\"notPlanned\" {% if dataset.maintenance_frequency == 'notPlanned' %}selected{% endif %}>Not Planned</option>
                        <option value=\"unknown\" {% if dataset.maintenance_frequency == 'unknown' %}selected{% endif %}>Unknown</option>
                    </select>
                </div>
                <div class=\"mb-4\">
                    <label for=\"character_set\" class=\"block text-sm font-medium text-gray-700 mb-1\">Character Set</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"character_set\" name=\"character_set\">
                        <option value=\"\">Select Character Set</option>
                        <option value=\"UTF-8\" {% if dataset.character_set == 'UTF-8' %}selected{% endif %}>UTF-8</option>
                        <option value=\"UTF-16\" {% if dataset.character_set == 'UTF-16' %}selected{% endif %}>UTF-16</option>
                        <option value=\"ISO-8859-1\" {% if dataset.character_set == 'ISO-8859-1' %}selected{% endif %}>ISO-8859-1</option>
                        <option value=\"ISO-8859-2\" {% if dataset.character_set == 'ISO-8859-2' %}selected{% endif %}>ISO-8859-2</option>
                        <option value=\"ISO-8859-15\" {% if dataset.character_set == 'ISO-8859-15' %}selected{% endif %}>ISO-8859-15</option>
                    </select>
                </div>
            </div>

            <div class=\"mb-4\">
                <h3 class=\"text-lg font-medium text-gray-900 mb-3\">Distribution Information</h3>
                <div class=\"mb-4\">
                    <label for=\"data_format\" class=\"block text-sm font-medium text-gray-700 mb-1\">Data Format</label>
                    <select class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                            id=\"data_format\" name=\"data_format[]\" multiple>
                        <option value=\"shapefile\" {% if 'shapefile' in dataset.data_format %}selected{% endif %}>Shapefile</option>
                        <option value=\"geopackage\" {% if 'geopackage' in dataset.data_format %}selected{% endif %}>GeoPackage</option>
                        <option value=\"geotiff\" {% if 'geotiff' in dataset.data_format %}selected{% endif %}>GeoTIFF</option>
                        <option value=\"ecw\" {% if 'ecw' in dataset.data_format %}selected{% endif %}>ECW</option>
                        <option value=\"jp2\" {% if 'jp2' in dataset.data_format %}selected{% endif %}>JPEG2000</option>
                        <option value=\"sid\" {% if 'sid' in dataset.data_format %}selected{% endif %}>MrSID</option>
                        <option value=\"asc\" {% if 'asc' in dataset.data_format %}selected{% endif %}>ASCII Grid</option>
                        <option value=\"nc\" {% if 'nc' in dataset.data_format %}selected{% endif %}>NetCDF</option>
                        <option value=\"wms\" {% if 'wms' in dataset.data_format %}selected{% endif %}>WMS</option>
                        <option value=\"wfs\" {% if 'wfs' in dataset.data_format %}selected{% endif %}>WFS</option>
                        <option value=\"wcs\" {% if 'wcs' in dataset.data_format %}selected{% endif %}>WCS</option>
                        <option value=\"other\" {% if 'other' in dataset.data_format %}selected{% endif %}>Other</option>
                    </select>
                    <p class=\"mt-1 text-sm text-gray-500\">Hold Ctrl/Cmd to select multiple formats</p>
                </div>
                <div class=\"mb-4\">
                    <label for=\"distribution_url\" class=\"block text-sm font-medium text-gray-700 mb-1\">Distribution URL</label>
                    <input type=\"url\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"distribution_url\" name=\"distribution_url\" placeholder=\"Download or access URL\"
                           value=\"{{ dataset.distribution_url|default('') }}\">
                </div>
                <div class=\"mb-4\">
                    <label for=\"coupled_resource\" class=\"block text-sm font-medium text-gray-700 mb-1\">Coupled Resource</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"coupled_resource\" name=\"coupled_resource\" placeholder=\"Associated dataset if metadata is for a service\"
                           value=\"{{ dataset.coupled_resource|default('') }}\">
                </div>
            </div>
        </div>

        <div class=\"flex justify-end space-x-4\">
            <button type=\"submit\" class=\"bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                {% if is_edit %}Update{% else %}Save{% endif %} Metadata
            </button>
            <button type=\"button\" onclick=\"exportToXML()\" 
                    class=\"bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                Export to ISO 19115 + INSPIRE XML
            </button>
        </div>
    </form>
{% endblock %}

{% block extra_js %}
<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
<script src=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js\"></script>
<script>
    let wmsLayer = null;
    // Initialize OpenLayers map
    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([
                {% if dataset.west_longitude and dataset.east_longitude %}
                    {{ ((dataset.west_longitude + dataset.east_longitude) / 2)|default(0) }},
                {% else %}0,{% endif %}
                {% if dataset.south_latitude and dataset.north_latitude %}
                    {{ ((dataset.south_latitude + dataset.north_latitude) / 2)|default(0) }}
                {% else %}0{% endif %}
            ]),
            zoom: 5
        })
    });

    // If in edit mode and we have WMS info, initialize the layer select
    {% if is_edit and dataset.wms_url and dataset.wms_layer %}
    document.addEventListener('DOMContentLoaded', function() {
        const wmsUrl = document.getElementById('wms_url');
        const layerSelect = document.getElementById('wms_layer');
        const layerSelectContainer = document.getElementById('layerSelectContainer');
        
        // Show the layer select container
        layerSelectContainer.classList.remove('hidden');
        
        // Fetch layers for the WMS service
        fetch('/wms/capabilities', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url: wmsUrl.value })
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                // Populate the layer select
                result.layers.forEach(layer => {
                    const option = document.createElement('option');
                    option.value = layer.name;
                    option.textContent = layer.title;
                    option.dataset.metadata = JSON.stringify(layer);
                    if (layer.name === '{{ dataset.wms_layer }}') {
                        option.selected = true;
                    }
                    layerSelect.appendChild(option);
                });
                
                // Trigger the change event to update the map
                layerSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => {
            console.error('Error fetching WMS layers:', error);
        });
    });
    {% endif %}

    function exportToXML() {
        const metadataId = new URLSearchParams(window.location.search).get('id');
        if (metadataId) {
            window.location.href = `/metadata/\${metadataId}/xml`;
        } else {
            alert('Please save the metadata first before exporting to XML');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up form handlers');
        
        const form = document.getElementById('metadataForm');
        const gisFilesInput = document.getElementById('gis_files');
        const thumbnailInput = document.getElementById('thumbnail');
        
        if (!form) {
            console.error('Form element not found!');
            return;
        }
        
        // Handle GIS file selection and spatial extent extraction
        if (gisFilesInput) {
            gisFilesInput.addEventListener('change', async function(e) {
                e.preventDefault();
                console.log('GIS files selected:', this.files);
                
                if (this.files.length === 0) {
                    return;
                }

                // Validate file types
                const allowedTypes = ['.zip', '.shp', '.gpkg', '.tif', '.tiff', '.geotiff', '.img', '.ecw', '.jp2', '.sid', '.asc', '.grd', '.nc'];
                const invalidFiles = Array.from(this.files).filter(file => {
                    const ext = '.' + file.name.split('.').pop().toLowerCase();
                    return !allowedTypes.includes(ext);
                });

                if (invalidFiles.length > 0) {
                    alert('Invalid file type(s): ' + invalidFiles.map(f => f.name).join(', ') + 
                          '\\nSupported formats: Shapefile (.zip), GeoPackage (.gpkg), GeoTIFF (.tif, .tiff), ' +
                          'ECW (.ecw), JPEG2000 (.jp2), MrSID (.sid), ASCII Grid (.asc), NetCDF (.nc)');
                    this.value = ''; // Clear the input
                    return;
                }

                // Create FormData for spatial extent extraction only
                const formData = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    formData.append('gis_files[]', this.files[i]);
                    console.log('Added file to FormData for extent extraction:', this.files[i].name);
                }
                
                try {
                    // Send files to server for spatial extent extraction only
                    console.log('Sending files for spatial extent extraction');
                    const response = await fetch('/metadata/extract-spatial-extent', {
                        method: 'POST',
                        body: formData
                    });
                    
                    console.log('Response status:', response.status);
                    const responseText = await response.text();
                    console.log('Raw response text:', responseText);
                    
                    let result;
                    try {
                        result = JSON.parse(responseText);
                        console.log('Parsed response:', result);
                    } catch (error) {
                        console.error('Error parsing JSON response:', error);
                        console.error('Response text was:', responseText);
                        throw new Error('Invalid server response: ' + responseText);
                    }
                    
                    if (result.status === 'success' && result.spatial_extent) {
                        // Update the form fields with the extracted extents
                        const { west_longitude, east_longitude, south_latitude, north_latitude } = result.spatial_extent;
                        console.log('Extracted extents:', result.spatial_extent);
                        
                        document.getElementById('west_longitude').value = west_longitude;
                        document.getElementById('east_longitude').value = east_longitude;
                        document.getElementById('south_latitude').value = south_latitude;
                        document.getElementById('north_latitude').value = north_latitude;
                        
                        // Update the map view
                        const extent = [west_longitude, south_latitude, east_longitude, north_latitude];
                        console.log('Original extent:', extent);
                        
                        // Transform the extent from EPSG:4326 to EPSG:3857
                        const transformedExtent = ol.proj.transformExtent(extent, 'EPSG:4326', 'EPSG:3857');
                        console.log('Transformed extent:', transformedExtent);
                        
                        // Create a feature with the transformed extent
                        const extentFeature = new ol.Feature({
                            geometry: new ol.geom.Polygon.fromExtent(transformedExtent)
                        });
                        
                        // Remove any existing extent layer
                        map.getLayers().forEach(layer => {
                            if (layer.get('name') === 'extent') {
                                map.removeLayer(layer);
                            }
                        });
                        
                        // Add new extent layer
                        const extentLayer = new ol.layer.Vector({
                            name: 'extent',
                            source: new ol.source.Vector({
                                features: [extentFeature]
                            }),
                            style: new ol.style.Style({
                                stroke: new ol.style.Stroke({
                                    color: 'rgba(255, 0, 0, 1.0)',
                                    width: 2
                                }),
                                fill: new ol.style.Fill({
                                    color: 'rgba(255, 0, 0, 0.1)'
                                })
                            })
                        });
                        map.addLayer(extentLayer);
                        
                        // Fit map to transformed extent
                        map.getView().fit(transformedExtent, {
                            padding: [50, 50, 50, 50],
                            maxZoom: 15,
                            duration: 1000
                        });
                        
                        // Show success message
                        const uploadedFilesDiv = document.getElementById('uploadedFiles');
                        uploadedFilesDiv.innerHTML = `
                            <div class=\"bg-green-50 p-4 rounded-md\">
                                <p class=\"text-sm text-green-700\">
                                    Spatial extents extracted successfully:<br>
                                    West: \${west_longitude}<br>
                                    East: \${east_longitude}<br>
                                    South: \${south_latitude}<br>
                                    North: \${north_latitude}
                                </p>
                            </div>`;
                            
                        // Also show the selected files
                        Array.from(this.files).forEach(file => {
                            const fileDiv = document.createElement('div');
                            fileDiv.className = 'bg-gray-50 p-4 rounded-md mt-2';
                            fileDiv.innerHTML = `
                                <div class=\"flex items-start justify-between\">
                                    <div>
                                        <h4 class=\"text-sm font-medium text-gray-900\">\${file.name}</h4>
                                        <p class=\"text-sm text-gray-500\">
                                            Type: \${file.name.split('.').pop().toLowerCase()}<br>
                                            Size: \${(file.size / 1024).toFixed(2)} KB
                                        </p>
                                    </div>
                                </div>`;
                            uploadedFilesDiv.appendChild(fileDiv);
                        });
                    } else {
                        throw new Error(result.message || 'Error extracting spatial extents');
                    }
                } catch (error) {
                    console.error('Error in spatial extent extraction:', error);
                    const uploadedFilesDiv = document.getElementById('uploadedFiles');
                    uploadedFilesDiv.innerHTML = `
                        <div class=\"bg-red-50 p-4 rounded-md\">
                            <p class=\"text-sm text-red-700\">
                                Error extracting spatial extents: \${error.message}
                            </p>
                        </div>`;
                }
            });
        }
        
        // Handle thumbnail selection
        if (thumbnailInput) {
            thumbnailInput.addEventListener('change', function(e) {
                e.preventDefault();
                console.log('Thumbnail selected:', this.files);
                
                if (this.files.length === 0) {
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const file = this.files[0];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
                    this.value = ''; // Clear the input
                    return;
                }

                // Update UI to show selected thumbnail
                const uploadedFilesDiv = document.getElementById('uploadedFiles');
                const thumbnailDiv = document.createElement('div');
                thumbnailDiv.className = 'bg-gray-50 p-4 rounded-md';
                thumbnailDiv.innerHTML = `
                    <div class=\"flex items-start justify-between\">
                        <div>
                            <h4 class=\"text-sm font-medium text-gray-900\">\${file.name}</h4>
                            <p class=\"text-sm text-gray-500\">
                                Type: Thumbnail (\${file.type})<br>
                                Size: \${(file.size / 1024).toFixed(2)} KB
                            </p>
                        </div>
                    </div>`;
                uploadedFilesDiv.appendChild(thumbnailDiv);
            });
        }
        
        // Handle form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submit event triggered');
            
            const formData = new FormData(this);
            const isEdit = {% if is_edit %}true{% else %}false{% endif %};
            
            try {
                // Use the correct URL for metadata updates
                const url = isEdit ? `/metadata/{{ dataset.id }}/update` : '/metadata';
                const method = isEdit ? 'PUT' : 'POST';
                
                console.log('Submitting to URL:', url);
                console.log('Using method:', method);
                
                // For PUT requests, we need to handle both JSON data and file uploads
                if (isEdit) {
                    // Check if there are any files to upload
                    const hasFiles = formData.get('thumbnail')?.size > 0 || 
                                   Array.from(formData.getAll('gis_files[]')).some(file => file.size > 0);
                    
                    if (hasFiles) {
                        // If there are files, send as FormData with _method=PUT
                        formData.append('_method', 'PUT');
                        const response = await fetch(url, {
                            method: 'POST', // Use POST for file uploads
                            body: formData
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server response:', errorText);
                            throw new Error(errorText || 'Server error');
                        }
                        
                        const result = await response.json();
                        console.log('Update response:', result);
                        
                        if (result.status === 'success') {
                            window.location.href = `/datasets/\${result.id}`;
                        } else {
                            throw new Error(result.message || 'Error updating metadata');
                        }
                    } else {
                        // If no files, send as JSON
                        const jsonData = {};
                        formData.forEach((value, key) => {
                            // Handle arrays (like data_format)
                            if (key.endsWith('[]')) {
                                const baseKey = key.slice(0, -2);
                                if (!jsonData[baseKey]) {
                                    jsonData[baseKey] = [];
                                }
                                jsonData[baseKey].push(value);
                            } else {
                                jsonData[key] = value;
                            }
                        });
                        
                        console.log('Sending update data:', jsonData);
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(jsonData)
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server response:', errorText);
                            throw new Error(errorText || 'Server error');
                        }
                        
                        const result = await response.json();
                        console.log('Update response:', result);
                        
                        if (result.status === 'success') {
                            window.location.href = `/datasets/\${result.id}`;
                        } else {
                            throw new Error(result.message || 'Error updating metadata');
                        }
                    }
                } else {
                    // For POST requests (new records), send FormData as is
                    console.log('Sending POST request with FormData');
                    
                    // Show loading state
                    const submitButton = form.querySelector('button[type=\"submit\"]');
                    const originalButtonText = submitButton.textContent;
                    submitButton.disabled = true;
                    submitButton.textContent = 'Saving...';
                    
                    try {
                        const response = await fetch(url, {
                            method: method,
                            body: formData
                        });
                        
                        console.log('Response received:', response.status);
                        const responseText = await response.text();
                        console.log('Response text:', responseText);
                        
                        let result;
                        try {
                            result = JSON.parse(responseText);
                        } catch (error) {
                            console.error('Error parsing JSON response:', error);
                            throw new Error('Invalid server response: ' + responseText);
                        }

                        if (result.status === 'success') {
                            // If we have spatial extents from the uploaded files, update the map
                            if (result.files && result.files.some(f => f.west_longitude !== null)) {
                                const filesWithExtent = result.files.filter(f => f.west_longitude !== null);
                                if (filesWithExtent.length > 0) {
                                    // Use the first file's extent for now
                                    const { west_longitude, east_longitude, south_latitude, north_latitude } = filesWithExtent[0];
                                    
                                    // Update the map view
                                    const extent = [west_longitude, south_latitude, east_longitude, north_latitude];
                                    const transformedExtent = ol.proj.transformExtent(extent, 'EPSG:4326', 'EPSG:3857');
                                    
                                    // Create a feature with the transformed extent
                                    const extentFeature = new ol.Feature({
                                        geometry: new ol.geom.Polygon.fromExtent(transformedExtent)
                                    });
                                    
                                    // Remove any existing extent layer
                                    map.getLayers().forEach(layer => {
                                        if (layer.get('name') === 'extent') {
                                            map.removeLayer(layer);
                                        }
                                    });
                                    
                                    // Add new extent layer
                                    const extentLayer = new ol.layer.Vector({
                                        name: 'extent',
                                        source: new ol.source.Vector({
                                            features: [extentFeature]
                                        }),
                                        style: new ol.style.Style({
                                            stroke: new ol.style.Stroke({
                                                color: 'rgba(255, 0, 0, 1.0)',
                                                width: 2
                                            }),
                                            fill: new ol.style.Fill({
                                                color: 'rgba(255, 0, 0, 0.1)'
                                            })
                                        })
                                    });
                                    map.addLayer(extentLayer);
                                    
                                    // Fit map to transformed extent
                                    map.getView().fit(transformedExtent, {
                                        padding: [50, 50, 50, 50],
                                        maxZoom: 15,
                                        duration: 1000
                                    });
                                }
                            }
                            
                            // Update the uploaded files display
                            const uploadedFilesDiv = document.getElementById('uploadedFiles');
                            uploadedFilesDiv.innerHTML = ''; // Clear previous content
                            
                            result.files.forEach(file => {
                                const fileDiv = document.createElement('div');
                                fileDiv.className = 'bg-gray-50 p-4 rounded-md';
                                fileDiv.innerHTML = `
                                    <div class=\"flex items-start justify-between\">
                                        <div>
                                            <h4 class=\"text-sm font-medium text-gray-900\">\${file.file_name}</h4>
                                            <p class=\"text-sm text-gray-500\">
                                                Type: \${file.file_type}<br>
                                                Size: \${(file.file_size / 1024).toFixed(2)} KB
                                            </p>
                                            \${file.west_longitude !== null ? `
                                            <p class=\"text-sm text-gray-500 mt-2\">
                                                Extent:<br>
                                                West: \${file.west_longitude}<br>
                                                East: \${file.east_longitude}<br>
                                                South: \${file.south_latitude}<br>
                                                North: \${file.north_latitude}
                                            </p>
                                            ` : ''}
                                        </div>
                                    </div>`;
                                uploadedFilesDiv.appendChild(fileDiv);
                            });
                            
                            // Clear the file inputs
                            document.getElementById('gis_files').value = '';
                            document.getElementById('thumbnail').value = '';
                            
                            // Redirect to the dataset view
                            window.location.href = `/datasets/\${result.id}`;
                        } else {
                            throw new Error(result.message || 'Error saving metadata');
                        }
                    } finally {
                        // Restore button state
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    }
                }
            } catch (error) {
                console.error('Error in form submission:', error);
                alert(error.message || 'An error occurred while saving the metadata');
            }
        });
        
        console.log('Form handlers attached');
    });

    // WMS Layer Selection functionality
    document.getElementById('fetchLayersBtn').addEventListener('click', async function() {
        const wmsUrl = document.getElementById('wms_url').value;
        if (!wmsUrl) {
            alert('Please enter a WMS service URL');
            return;
        }

        try {
            const response = await fetch('/wms/capabilities', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ url: wmsUrl })
            });

            const result = await response.json();
            if (result.status === 'success') {
                const layerSelect = document.getElementById('wms_layer');
                layerSelect.innerHTML = '<option value=\"\">Select a layer...</option>';
                
                // Store service metadata for later use
                layerSelect.dataset.serviceMetadata = JSON.stringify(result.service);
                
                result.layers.forEach(layer => {
                    const option = document.createElement('option');
                    option.value = layer.name;
                    option.textContent = layer.title;
                    option.dataset.metadata = JSON.stringify(layer);
                    layerSelect.appendChild(option);
                });

                document.getElementById('layerSelectContainer').classList.remove('hidden');

                // Populate service-level metadata
                const serviceMetadata = result.service;
                if (serviceMetadata.title && !document.getElementById('title').value) {
                    document.getElementById('title').value = serviceMetadata.title;
                }
                if (serviceMetadata.abstract && !document.getElementById('abstract').value) {
                    document.getElementById('abstract').value = serviceMetadata.abstract;
                }
                if (serviceMetadata.keywords && serviceMetadata.keywords.length > 0 && !document.getElementById('keywords').value) {
                    document.getElementById('keywords').value = serviceMetadata.keywords.join(', ');
                }
                if (serviceMetadata.responsible_org && !document.getElementById('responsible_org').value) {
                    document.getElementById('responsible_org').value = serviceMetadata.responsible_org;
                }
                if (serviceMetadata.responsible_person && !document.getElementById('responsible_person').value) {
                    document.getElementById('responsible_person').value = serviceMetadata.responsible_person;
                }
                if (serviceMetadata.role && !document.getElementById('role').value) {
                    document.getElementById('role').value = serviceMetadata.role;
                }
                if (serviceMetadata.spatial_data_service_url && !document.getElementById('spatial_data_service_url').value) {
                    document.getElementById('spatial_data_service_url').value = serviceMetadata.spatial_data_service_url;
                }
            } else {
                alert('Error fetching layers: ' + result.message);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    document.getElementById('wms_layer').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const layerMetadata = JSON.parse(selectedOption.dataset.metadata);
            
            // Update spatial extent fields
            document.getElementById('west_longitude').value = layerMetadata.bbox[0];
            document.getElementById('south_latitude').value = layerMetadata.bbox[1];
            document.getElementById('east_longitude').value = layerMetadata.bbox[2];
            document.getElementById('north_latitude').value = layerMetadata.bbox[3];

            // Update other metadata fields if they're empty
            if (layerMetadata.title && !document.getElementById('title').value) {
                document.getElementById('title').value = layerMetadata.title;
            }
            if (layerMetadata.abstract && !document.getElementById('abstract').value) {
                document.getElementById('abstract').value = layerMetadata.abstract;
            }
            if (layerMetadata.keywords && layerMetadata.keywords.length > 0 && !document.getElementById('keywords').value) {
                document.getElementById('keywords').value = layerMetadata.keywords.join(', ');
            }
            if (layerMetadata.coordinate_system && !document.getElementById('coordinate_system').value) {
                document.getElementById('coordinate_system').value = layerMetadata.coordinate_system;
            }

            // Update map
            if (wmsLayer) {
                map.removeLayer(wmsLayer);
            }

            const wmsUrl = document.getElementById('wms_url').value;
            wmsLayer = new ol.layer.Tile({
                source: new ol.source.TileWMS({
                    url: wmsUrl,
                    params: {
                        'LAYERS': selectedOption.value,
                        'TILED': true
                    },
                    serverType: 'geoserver'
                })
            });

            map.addLayer(wmsLayer);

            // Fit map to layer extent
            const extent = ol.proj.transformExtent(
                layerMetadata.bbox,
                'EPSG:4326',
                'EPSG:3857'
            );
            map.getView().fit(extent, { padding: [50, 50, 50, 50] });
        }
    });
</script>
{% endblock %} ", "form.twig", "/var/www/novella/templates/form.twig");
    }
}
