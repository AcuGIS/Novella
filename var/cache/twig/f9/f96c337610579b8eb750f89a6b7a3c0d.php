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

/* datasets.twig */
class __TwigTemplate_56469926fff8466c934f393ef5e24729 extends Template
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
            'extra_js' => [$this, 'block_extra_js'],
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
        yield "Datasets";
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
        yield "    ";
        yield from $this->yieldParentBlock("extra_css", $context, $blocks);
        yield "
    <!-- OpenLayers CSS -->
    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css\">
    <style>
        .flex.gap-8 {
            max-width: 1600px;
            margin: 0 auto;
        }
        .flex-grow {
            max-width: calc(1600px - 352px); /* 1600px - (sidebar width + gap) */
        }
    </style>
";
        yield from [];
    }

    // line 20
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 21
        yield "    ";
        yield from $this->yieldParentBlock("extra_js", $context, $blocks);
        yield "
    <!-- OpenLayers JS -->
    <script src=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js\"></script>
";
        yield from [];
    }

    // line 26
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 27
        yield "    <div class=\"flex gap-8\">
        <!-- Search Sidebar -->
        <div class=\"w-80 flex-shrink-0\">
            <div class=\"bg-white rounded-lg shadow-sm p-4 sticky top-4\">
                <h2 class=\"text-lg font-semibold mb-4\">Search & Filter</h2>
                
                <!-- Search Box -->
                <div class=\"mb-6\">
                    <label for=\"search-input\" class=\"block text-sm font-medium text-gray-700 mb-2\">Search Datasets</label>
                    <div class=\"relative\">
                        <input type=\"text\" 
                               id=\"search-input\" 
                               class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500\"
                               placeholder=\"Search by title, abstract...\">
                        <button id=\"search-reset\" class=\"absolute right-2 top-2 text-gray-400 hover:text-gray-600\" title=\"Clear search\">×</button>
                    </div>
                </div>

                <!-- Map Search Widget -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Map</label>
                    <div class=\"mb-2\">
                        <div id=\"mini-map\" style=\"width: 100%; height: 160px; border-radius: 6px; overflow: hidden;\"></div>
                    </div>
                    <div class=\"flex items-center space-x-4 mb-2\">
                        <label class=\"flex items-center text-sm\">
                            <input type=\"radio\" name=\"spatial-relation\" value=\"any\" checked class=\"mr-1\"> Any
                        </label>
                        <label class=\"flex items-center text-sm\">
                            <input type=\"radio\" name=\"spatial-relation\" value=\"intersects\" class=\"mr-1\"> Intersects
                        </label>
                        <label class=\"flex items-center text-sm\">
                            <input type=\"radio\" name=\"spatial-relation\" value=\"within\" class=\"mr-1\"> Within
                        </label>
                    </div>
                    <div class=\"flex space-x-2\">
                        <button id=\"map-search-btn\" class=\"flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500\">Search</button>
                        <button id=\"clear-box-btn\" class=\"px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500\" title=\"Clear drawn box\">×</button>
                    </div>
                </div>

                <!-- Topic Filter -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Topics</label>
                    <select id=\"topic-select\"
                            class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\"
                            onchange=\"updateTopicSelection(this)\"
                            size=\"6\">
                        <option value=\"\">All Topics</option>
                        ";
        // line 76
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["topics"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["topic"]) {
            // line 77
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "id", [], "any", false, false, false, 77), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "topic", [], "any", false, false, false, 77), "html", null, true);
            yield "</option>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['topic'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 79
        yield "                    </select>
                </div>

                <!-- Keyword Filter -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Keywords</label>
                    <select id=\"keyword-select\"
                            class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\"
                            onchange=\"updateKeywordSelection(this)\"
                            size=\"6\">
                        <option value=\"\">All Keywords</option>
                        ";
        // line 90
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["keywords"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["keyword"]) {
            // line 91
            yield "                        <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::replace(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "keyword", [], "any", false, false, false, 91), ["'" => "\\'"]), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "keyword", [], "any", false, false, false, 91), "html", null, true);
            yield "</option>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['keyword'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 93
        yield "                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Date Range</label>
                    <div class=\"space-y-2\">
                        <div>
                            <label for=\"date-from\" class=\"block text-xs text-gray-500\">From</label>
                            <input type=\"date\" 
                                   id=\"date-from\" 
                                   class=\"mt-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500\">
                        </div>
                        <div>
                            <label for=\"date-to\" class=\"block text-xs text-gray-500\">To</label>
                            <input type=\"date\" 
                                   id=\"date-to\" 
                                   class=\"mt-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500\">
                        </div>
                    </div>
                </div>

                <!-- Reset Filters Button -->
                <button id=\"reset-filters\" 
                        class=\"w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500\">
                    Reset All Filters
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class=\"flex-grow\">
<div class=\"bg-white rounded-lg shadow-md p-6 mb-8\">
            <h1 class=\"text-3xl font-bold mb-8\">Datasets</h1>
            <div id=\"datasets-container\" class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6\">


\t\t

                ";
        // line 132
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["datasets"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["dataset"]) {
            // line 133
            yield "                    <div class=\"dataset-card bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col h-full\"
                         data-dataset-id=\"";
            // line 134
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "id", [], "any", false, false, false, 134), "html", null, true);
            yield "\"
                         data-title=\"";
            // line 135
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::lower($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "title", [], "any", false, false, false, 135)), "html", null, true);
            yield "\"
                         data-abstract=\"";
            // line 136
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::lower($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "abstract", [], "any", false, false, false, 136)), "html", null, true);
            yield "\"
                         data-topic-id=\"";
            // line 137
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "topic_id", [], "any", false, false, false, 137), "html", null, true);
            yield "\"
                         data-topic-name=\"";
            // line 138
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "topic_name", [], "any", false, false, false, 138), "html", null, true);
            yield "\"
                         data-inspire-theme-id=\"";
            // line 139
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "inspire_theme_id", [], "any", false, false, false, 139), "html", null, true);
            yield "\"
                         data-inspire-theme-name=\"";
            // line 140
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::lower($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "inspire_theme_name", [], "any", false, false, false, 140)), "html", null, true);
            yield "\"
                         data-keywords=\"";
            // line 141
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(json_encode(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "keywords", [], "any", false, false, false, 141)), "html", null, true);
            yield "\"
                         data-date=\"";
            // line 142
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "metadata_date", [], "any", false, false, false, 142), "html", null, true);
            yield "\">
                        ";
            // line 143
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "thumbnail_path", [], "any", false, false, false, 143)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 144
                yield "                            <div class=\"relative h-24 w-full overflow-hidden\">
                                <img src=\"/storage/uploads/thumbnails/";
                // line 145
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "thumbnail_path", [], "any", false, false, false, 145), "html", null, true);
                yield "\" 
                                     alt=\"";
                // line 146
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "title", [], "any", false, false, false, 146), "html", null, true);
                yield "\" 
                                     class=\"w-full h-full object-cover transition-transform duration-300 hover:scale-105\">
                            </div>
                        ";
            } else {
                // line 150
                yield "                            <div class=\"relative h-24 w-full overflow-hidden\">
                                <img src=\"/storage/uploads/thumbnails/default.png\" 
                                     alt=\"No thumbnail available\" 
                                     class=\"w-full h-full object-cover transition-transform duration-300 hover:scale-105\">
                            </div>
                        ";
            }
            // line 156
            yield "                        
                        <div class=\"p-4 flex-grow flex flex-col\">
                            <h2 class=\"text-lg font-semibold text-gray-900 mb-2 line-clamp-2\">";
            // line 158
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "title", [], "any", false, false, false, 158), "html", null, true);
            yield "</h2>
                            <p class=\"text-sm text-gray-600 mb-3 line-clamp-3\">";
            // line 159
            yield (((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "abstract", [], "any", false, false, false, 159)) > 120)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "abstract", [], "any", false, false, false, 159), 0, 120) . "..."), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "abstract", [], "any", false, false, false, 159), "html", null, true)));
            yield "</p>
                            
                            <div class=\"mt-auto space-y-2\">
                                ";
            // line 162
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "topic_name", [], "any", false, false, false, 162)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 163
                yield "                                    <div class=\"flex items-center text-sm text-gray-600\">
                                        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z\"></path>
                                        </svg>
                                        ";
                // line 167
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "topic_name", [], "any", false, false, false, 167), "html", null, true);
                yield "
                                    </div>
                                ";
            }
            // line 170
            yield "                                
                                ";
            // line 171
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "inspire_theme_name", [], "any", false, false, false, 171)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 172
                yield "                                    <div class=\"flex items-center text-sm text-gray-600\">
                                        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\"></path>
                                        </svg>
                                        ";
                // line 176
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "inspire_theme_name", [], "any", false, false, false, 176), "html", null, true);
                yield "
                                    </div>
                                ";
            }
            // line 179
            yield "                                
                                <div class=\"mt-auto flex items-center justify-between pt-4\">
                                    <div class=\"flex items-center text-sm text-gray-500\">
                                        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\"></path>
                                        </svg>
                                        Last updated: ";
            // line 185
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "metadata_date", [], "any", false, false, false, 185)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "metadata_date", [], "any", false, false, false, 185), "Y-m-d H:i:s"), "html", null, true)) : ("N/A"));
            yield "
                                    </div>
                                    <a href=\"/datasets/";
            // line 187
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["dataset"], "id", [], "any", false, false, false, 187), "html", null, true);
            yield "\" class=\"text-blue-600 text-sm font-medium hover:underline focus:outline-none\">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['dataset'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 193
        yield "            </div>
            
            ";
        // line 195
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_pages", [], "any", false, false, false, 195) > 1)) {
            // line 196
            yield "            <div class=\"mt-8 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6\">
                <div class=\"flex flex-1 justify-between sm:hidden\">
                    ";
            // line 198
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "has_previous", [], "any", false, false, false, 198)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 199
                yield "                        <a href=\"?page=";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 199) - 1), "html", null, true);
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 199), "per_page", [], "any", false, false, false, 199)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 199), "per_page", [], "any", false, false, false, 199), "html", null, true);
                }
                yield "\" 
                           class=\"relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50\">
                            Previous
                        </a>
                    ";
            }
            // line 204
            yield "                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "has_next", [], "any", false, false, false, 204)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 205
                yield "                        <a href=\"?page=";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 205) + 1), "html", null, true);
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 205), "per_page", [], "any", false, false, false, 205)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 205), "per_page", [], "any", false, false, false, 205), "html", null, true);
                }
                yield "\" 
                           class=\"relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50\">
                            Next
                        </a>
                    ";
            }
            // line 210
            yield "                </div>
                <div class=\"hidden sm:flex sm:flex-1 sm:items-center sm:justify-between\">
                    <div>
                        <p class=\"text-sm text-gray-700\">
                            Showing
                            <span class=\"font-medium\">";
            // line 215
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 215) - 1) * CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "per_page", [], "any", false, false, false, 215)) + 1), "html", null, true);
            yield "</span>
                            to
                            <span class=\"font-medium\">";
            // line 217
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(min((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 217) * CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "per_page", [], "any", false, false, false, 217)), CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_items", [], "any", false, false, false, 217)), "html", null, true);
            yield "</span>
                            of
                            <span class=\"font-medium\">";
            // line 219
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_items", [], "any", false, false, false, 219), "html", null, true);
            yield "</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class=\"isolate inline-flex -space-x-px rounded-md shadow-sm\" aria-label=\"Pagination\">
                            ";
            // line 225
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "has_previous", [], "any", false, false, false, 225)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 226
                yield "                                <a href=\"?page=";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 226) - 1), "html", null, true);
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 226), "per_page", [], "any", false, false, false, 226)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 226), "per_page", [], "any", false, false, false, 226), "html", null, true);
                }
                yield "\" 
                                   class=\"relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">
                                    <span class=\"sr-only\">Previous</span>
                                    <svg class=\"h-5 w-5\" viewBox=\"0 0 20 20\" fill=\"currentColor\" aria-hidden=\"true\">
                                        <path fill-rule=\"evenodd\" d=\"M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z\" clip-rule=\"evenodd\" />
                                    </svg>
                                </a>
                            ";
            }
            // line 234
            yield "                            
                            ";
            // line 235
            $context["start_page"] = max(1, (CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 235) - 2));
            // line 236
            yield "                            ";
            $context["end_page"] = min(CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_pages", [], "any", false, false, false, 236), (CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 236) + 2));
            // line 237
            yield "                            
                            ";
            // line 238
            if ((($context["start_page"] ?? null) > 1)) {
                // line 239
                yield "                                <a href=\"?page=1";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 239), "per_page", [], "any", false, false, false, 239)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 239), "per_page", [], "any", false, false, false, 239), "html", null, true);
                }
                yield "\" 
                                   class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">1</a>
                                ";
                // line 241
                if ((($context["start_page"] ?? null) > 2)) {
                    // line 242
                    yield "                                    <span class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0\">...</span>
                                ";
                }
                // line 244
                yield "                            ";
            }
            // line 245
            yield "                            
                            ";
            // line 246
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(range(($context["start_page"] ?? null), ($context["end_page"] ?? null)));
            foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                // line 247
                yield "                                <a href=\"?page=";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["p"], "html", null, true);
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 247), "per_page", [], "any", false, false, false, 247)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 247), "per_page", [], "any", false, false, false, 247), "html", null, true);
                }
                yield "\" 
                                   class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold ";
                // line 248
                if (($context["p"] == CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 248))) {
                    yield "bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600";
                } else {
                    yield "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0";
                }
                yield "\">
                                    ";
                // line 249
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["p"], "html", null, true);
                yield "
                                </a>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['p'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 252
            yield "                            
                            ";
            // line 253
            if ((($context["end_page"] ?? null) < CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_pages", [], "any", false, false, false, 253))) {
                // line 254
                yield "                                ";
                if ((($context["end_page"] ?? null) < (CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_pages", [], "any", false, false, false, 254) - 1))) {
                    // line 255
                    yield "                                    <span class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0\">...</span>
                                ";
                }
                // line 257
                yield "                                <a href=\"?page=";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_pages", [], "any", false, false, false, 257), "html", null, true);
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 257), "per_page", [], "any", false, false, false, 257)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 257), "per_page", [], "any", false, false, false, 257), "html", null, true);
                }
                yield "\" 
                                   class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">";
                // line 258
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "total_pages", [], "any", false, false, false, 258), "html", null, true);
                yield "</a>
                            ";
            }
            // line 260
            yield "                            
                            ";
            // line 261
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "has_next", [], "any", false, false, false, 261)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 262
                yield "                                <a href=\"?page=";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((CoreExtension::getAttribute($this->env, $this->source, ($context["pagination"] ?? null), "current_page", [], "any", false, false, false, 262) + 1), "html", null, true);
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 262), "per_page", [], "any", false, false, false, 262)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "&per_page=";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["request"] ?? null), "query", [], "any", false, false, false, 262), "per_page", [], "any", false, false, false, 262), "html", null, true);
                }
                yield "\" 
                                   class=\"relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">
                                    <span class=\"sr-only\">Next</span>
                                    <svg class=\"h-5 w-5\" viewBox=\"0 0 20 20\" fill=\"currentColor\" aria-hidden=\"true\">
                                        <path fill-rule=\"evenodd\" d=\"M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z\" clip-rule=\"evenodd\" />
                                    </svg>
                                </a>
                            ";
            }
            // line 270
            yield "                        </nav>
                    </div>
                </div>
            </div>
            ";
        }
        // line 275
        yield "            
            <div id=\"no-results\" class=\"col-span-3 text-center text-gray-500 py-8\" style=\"display: none;\">
                No datasets match your search criteria.
            </div>
        </div>
    </div>
 </div>

    <script>
        // Global variables for selected items
        let selectedTopic = '";
        // line 285
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("selected_topic", $context)) ? (Twig\Extension\CoreExtension::default(($context["selected_topic"] ?? null), "")) : ("")), "html", null, true);
        yield "';
        let selectedKeyword = '";
        // line 286
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("selected_keyword", $context)) ? (Twig\Extension\CoreExtension::default(($context["selected_keyword"] ?? null), "")) : ("")), "html", null, true);
        yield "';
        let allTopics = new Map();
        let allKeywords = new Set();
        let miniMap, drawInteraction;
        let drawnFeature = null;  // Explicitly declare as null

        // Map search functionality
        const mapSearchBtn = document.getElementById('map-search-btn');
        const mapSearchResults = document.getElementById('map-search-results');
        const mapSearchCount = document.getElementById('map-search-count');

        // Function to update the URL with search parameters
        function updateSearchUrl() {
            const searchParams = new URLSearchParams(window.location.search);
            const searchInput = document.getElementById('search-input');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            
            // Update search parameters
            if (searchInput.value) {
                searchParams.set('search', searchInput.value);
            } else {
                searchParams.delete('search');
            }
            
            if (selectedTopic) {
                searchParams.set('topic', selectedTopic);
            } else {
                searchParams.delete('topic');
            }
            
            if (selectedKeyword) {
                searchParams.set('keyword', selectedKeyword);
            } else {
                searchParams.delete('keyword');
            }
            
            if (dateFrom.value) {
                searchParams.set('date_from', dateFrom.value);
            } else {
                searchParams.delete('date_from');
            }
            
            if (dateTo.value) {
                searchParams.set('date_to', dateTo.value);
            } else {
                searchParams.delete('date_to');
            }
            
            // Reset to first page when search parameters change
            searchParams.set('page', '1');
            
            // Update URL without reloading
            const newUrl = window.location.pathname + (searchParams.toString() ? '?' + searchParams.toString() : '');
            window.history.pushState({}, '', newUrl);
            
            // Reload the page to get new results
            window.location.reload();
        }

        // Initialize the map when the DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initMiniMap();

            // Add search input functionality
            const searchInput = document.getElementById('search-input');
            const searchReset = document.getElementById('search-reset');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            const resetFiltersBtn = document.getElementById('reset-filters');
            const topicSelect = document.getElementById('topic-select');
            const keywordSelect = document.getElementById('keyword-select');

            // Set initial values from URL parameters
            if (searchInput) {
                searchInput.value = '";
        // line 361
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("search_term", $context)) ? (Twig\Extension\CoreExtension::default(($context["search_term"] ?? null), "")) : ("")), "html", null, true);
        yield "';
            }
            if (dateFrom) {
                dateFrom.value = '";
        // line 364
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("date_from", $context)) ? (Twig\Extension\CoreExtension::default(($context["date_from"] ?? null), "")) : ("")), "html", null, true);
        yield "';
            }
            if (dateTo) {
                dateTo.value = '";
        // line 367
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("date_to", $context)) ? (Twig\Extension\CoreExtension::default(($context["date_to"] ?? null), "")) : ("")), "html", null, true);
        yield "';
            }
            if (topicSelect && selectedTopic) {
                topicSelect.value = selectedTopic;
            }
            if (keywordSelect && selectedKeyword) {
                keywordSelect.value = selectedKeyword;
            }

            // Add debounced search input handler
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(updateSearchUrl, 500); // Wait 500ms after user stops typing
                });
            }

            if (searchReset) {
                searchReset.addEventListener('click', function() {
                    searchInput.value = '';
                    dateFrom.value = '';
                    dateTo.value = '';
                    updateSearchUrl();
                });
            }

            // Add date input event listeners
            if (dateFrom) {
                dateFrom.addEventListener('change', updateSearchUrl);
            }

            if (dateTo) {
                dateTo.addEventListener('change', updateSearchUrl);
            }

            // Add reset all filters functionality
            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', function() {
                    // Clear search input
                    if (searchInput) searchInput.value = '';
                    
                    // Clear date inputs
                    if (dateFrom) dateFrom.value = '';
                    if (dateTo) dateTo.value = '';
                    
                    // Reset topic selection
                    if (topicSelect) {
                        topicSelect.value = '';
                        selectedTopic = '';
                    }
                    
                    // Reset keyword selection
                    if (keywordSelect) {
                        keywordSelect.value = '';
                        selectedKeyword = '';
                    }
                    
                    // Reset map search
                    if (miniMap) {
                        // Clear the drawn box
                        const vectorSource = miniMap.getLayers().getArray()[1].getSource();
                        vectorSource.clear();
                        drawnFeature = null;
                        
                        // Reset map view to default
                        miniMap.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                        miniMap.getView().setZoom(2);
                    }
                    
                    // Reset spatial relation radio buttons
                    const spatialRelationRadios = document.querySelectorAll('input[name=\"spatial-relation\"]');
                    if (spatialRelationRadios.length > 0) {
                        spatialRelationRadios[0].checked = true; // Set to \"Any\"
                    }
                    
                    // Update URL and reload
                    updateSearchUrl();
                });
            }
        });

        // Update the JavaScript functions for radio buttons
        function updateTopicSelection(select) {
            selectedTopic = select.value;
            updateSearchUrl();
        }

        function updateKeywordSelection(select) {
            selectedKeyword = select.value;
            updateSearchUrl();
        }

        // Update pagination links to preserve search parameters
        document.addEventListener('DOMContentLoaded', function() {
            const paginationLinks = document.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const searchParams = new URLSearchParams(window.location.search);
                    const page = this.getAttribute('href').match(/page=(\\d+)/)?.[1];
                    if (page) {
                        searchParams.set('page', page);
                        window.location.href = window.location.pathname + '?' + searchParams.toString();
                    }
                });
            });
        });

        function initMiniMap() {
            // Create vector source at the top level so it's accessible to all functions
            const vectorSource = new ol.source.Vector();
            
            miniMap = new ol.Map({
                target: 'mini-map',
                interactions: ol.interaction.defaults.defaults({
                    doubleClickZoom: false,
                    dragPan: true,
                    mouseWheelZoom: false,
                    shiftDragZoom: false,
                    pinchZoom: false
                }),
                controls: [
                    new ol.control.Zoom()
                ],
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.OSM()
                    }),
                    new ol.layer.Vector({
                        source: vectorSource,
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: 'rgba(0, 0, 255, 1.0)',
                                width: 2
                            }),
                            fill: new ol.style.Fill({
                                color: 'rgba(0, 0, 255, 0.1)'
                            })
                        })
                    })
                ],
                view: new ol.View({
                    center: ol.proj.fromLonLat([0, 0]),
                    zoom: 2,
                    minZoom: 1,
                    maxZoom: 8
                })
            });

            // Function to clear the drawn box
            function clearDrawnBox() {
                console.log('Attempting to clear box, current drawnFeature:', drawnFeature);
                if (drawnFeature) {
                    vectorSource.clear(); // Clear all features from the source
                    drawnFeature = null;
                    console.log('Box cleared, vectorSource features:', vectorSource.getFeatures().length);
                }
            }

            // Add drawing interaction
            drawInteraction = new ol.interaction.Draw({
                source: vectorSource,
                type: 'Circle',
                geometryFunction: ol.interaction.Draw.createBox(),
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'rgba(0, 0, 255, 1.0)',
                        width: 2
                    }),
                    fill: new ol.style.Fill({
                        color: 'rgba(0, 0, 255, 0.1)'
                    })
                })
            });

            // Clear old rectangle when starting to draw a new one
            drawInteraction.on('drawstart', function() {
                clearDrawnBox();
            });

            // Handle draw end
            drawInteraction.on('drawend', function(event) {
                // Get the extent of the drawn box
                const extent = event.feature.getGeometry().getExtent();
                
                // Create a new polygon from the extent
                const coordinates = [
                    [extent[0], extent[1]], // bottom-left
                    [extent[2], extent[1]], // bottom-right
                    [extent[2], extent[3]], // top-right
                    [extent[0], extent[3]], // top-left
                    [extent[0], extent[1]]  // close the polygon
                ];
                
                // Create a new feature with the box polygon
                drawnFeature = new ol.Feature({
                    geometry: new ol.geom.Polygon([coordinates])
                });
                
                // Clear any existing features and add the new one
                vectorSource.clear();
                vectorSource.addFeature(drawnFeature);

                // Log for debugging
                console.log('Box drawn, vectorSource features:', vectorSource.getFeatures().length);
            });

            // Add the interaction to the map
            miniMap.addInteraction(drawInteraction);

            // Initialize clear button
            const clearBoxBtn = document.getElementById('clear-box-btn');
            if (clearBoxBtn) {
                clearBoxBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent any default button behavior
                    clearDrawnBox();
                });
            }

            // Prevent the map from clearing the box on click
            miniMap.on('click', function(event) {
                // Only prevent default if we're not in the middle of drawing
                if (!drawInteraction.getActive()) {
                    event.preventDefault();
                }
            });
        }

        if (mapSearchBtn) {
            mapSearchBtn.addEventListener('click', async function() {
                console.log('Search clicked, drawnFeature:', drawnFeature);
                
                if (!drawnFeature) {
                    alert('Please draw a rectangle on the map.');
                    return;
                }

                // Get the spatial relation from the radio buttons
                const spatialRelation = document.querySelector('input[name=\"spatial-relation\"]:checked').value;
                console.log('Using spatial relation:', spatialRelation);

                // Get rectangle coordinates in EPSG:4326
                const extent = drawnFeature.getGeometry().getExtent();
                const bottomLeft = ol.proj.toLonLat([extent[0], extent[1]]);
                const topRight = ol.proj.toLonLat([extent[2], extent[3]]);
                
                // Log the coordinates in both projections for debugging
                console.log('Map coordinates (EPSG:3857):', {
                    west: extent[0],
                    south: extent[1],
                    east: extent[2],
                    north: extent[3]
                });
                console.log('Geographic coordinates (EPSG:4326):', {
                    west: bottomLeft[0],
                    south: bottomLeft[1],
                    east: topRight[0],
                    north: topRight[1]
                });
                
                const bbox = {
                    west: bottomLeft[0],
                    south: bottomLeft[1],
                    east: topRight[0],
                    north: topRight[1]
                };

                // Send search request
                const searchData = {
                    bbox: bbox,
                    spatialRelation: spatialRelation  // Use the selected value instead of hardcoding 'intersects'
                };
                console.log('Sending search request:', searchData);

                try {
                    // Make sure we're using the correct URL
                    const searchUrl = '/api/datasets/search-by-bbox';
                    console.log('Sending search request to:', searchUrl);
                    
                    const response = await fetch(searchUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'  // Explicitly request JSON response
                        },
                        body: JSON.stringify(searchData)
                    });
                    
                    // Log response details for debugging
                    console.log('Response status:', response.status);
                    console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                    
                    if (!response.ok) {
                        const text = await response.text();
                        console.error('Error response body:', text);
                        throw new Error(`HTTP error! status: \${response.status}, body: \${text.substring(0, 200)}...`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    console.log('Response content type:', contentType);
                    
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Unexpected content type. Response body:', text);
                        throw new TypeError(`Expected JSON but got \${contentType}. Response body: \${text.substring(0, 200)}...`);
                    }
                    
                    const data = await response.json();
                    console.log('Search response:', data);
                    
                    if (data.status === 'success') {
                        const datasetIds = data.datasets.map(d => d.id);
                        console.log('Found datasets:', datasetIds);
                        
                        // Log spatial relations for debugging
                        data.datasets.forEach(dataset => {
                            console.log(`Dataset \${dataset.id} (\${dataset.title}):`, {
                                dataset_bbox: dataset.bbox,
                                search_bbox: bbox,
                                spatial_relation: getSpatialRelation(dataset.bbox, bbox)
                            });
                        });

                        // Instead of filtering existing cards, we'll make a request to get all matching datasets
                        const searchParams = new URLSearchParams({
                            ids: datasetIds.join(','),
                            spatial_search: 'true'
                        });
                        
                        const byIdsUrl = `/api/datasets/by-ids?\${searchParams}`;
                        console.log('Fetching dataset details from:', byIdsUrl);
                        console.log('Dataset IDs being requested:', datasetIds);
                        
                        // Fetch the full dataset details
                        const datasetsResponse = await fetch(byIdsUrl);
                        console.log('Response status:', datasetsResponse.status);
                        console.log('Response headers:', Object.fromEntries(datasetsResponse.headers.entries()));
                        
                        if (!datasetsResponse.ok) {
                            const text = await datasetsResponse.text();
                            console.error('Error response from by-ids:', text);
                            throw new Error(`HTTP error! status: \${datasetsResponse.status}, body: \${text.substring(0, 200)}...`);
                        }
                        
                        const contentType = datasetsResponse.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await datasetsResponse.text();
                            console.error('Unexpected content type from by-ids. Response body:', text);
                            throw new TypeError(`Expected JSON but got \${contentType}. Response body: \${text.substring(0, 200)}...`);
                        }
                        
                        const datasetsData = await datasetsResponse.json();
                        
                        if (datasetsData.status === 'success') {
                            // Clear existing grid
                            const grid = document.querySelector('.grid');
                            grid.innerHTML = '';
                            
                            // Add new dataset cards
                            datasetsData.datasets.forEach(dataset => {
                                const card = createDatasetCard(dataset);
                                grid.appendChild(card);
                            });
                            
                            // Show/hide no results message
                            const noResults = document.querySelector('.no-results-message');
                            if (noResults) {
                                noResults.style.display = datasetsData.datasets.length === 0 ? 'block' : 'none';
                            } else if (datasetsData.datasets.length === 0) {
                                const noResultsDiv = document.createElement('div');
                                noResultsDiv.className = 'col-span-full text-center py-8 text-gray-500 no-results-message';
                                noResultsDiv.textContent = 'No datasets found in the selected area';
                                grid.appendChild(noResultsDiv);
                            }
                            
                            console.log('Visible datasets:', datasetsData.datasets.length);
                        }
                    } else {
                        console.error('Search failed:', data.message);
                        alert('Search failed: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error during search:', error);
                    alert('An error occurred during the search. Please try again.');
                }
            });
        }

        // Helper function to determine spatial relation between two bounding boxes
        function getSpatialRelation(datasetBbox, searchBbox) {
            if (!datasetBbox) return 'no_coordinates';
            
            // Check if dataset is completely within search box
            const isWithin = 
                datasetBbox.west >= searchBbox.west &&
                datasetBbox.east <= searchBbox.east &&
                datasetBbox.south >= searchBbox.south &&
                datasetBbox.north <= searchBbox.north;
            
            // Check if dataset intersects with search box
            const isIntersecting = 
                datasetBbox.west <= searchBbox.east &&
                datasetBbox.east >= searchBbox.west &&
                datasetBbox.south <= searchBbox.north &&
                datasetBbox.north >= searchBbox.south;
            
            // Handle coordinate wrapping
            const datasetWraps = datasetBbox.west > datasetBbox.east;
            const searchWraps = searchBbox.west > searchBbox.east;
            
            if (datasetWraps || searchWraps) {
                // More complex intersection check for wrapped coordinates
                const isWrappedIntersecting = 
                    (datasetBbox.west <= searchBbox.east || datasetBbox.east >= searchBbox.west) &&
                    datasetBbox.south <= searchBbox.north &&
                    datasetBbox.north >= searchBbox.south;
                
                return isWrappedIntersecting ? 'intersects_wrapped' : 'no_intersect_wrapped';
            }
            
            if (isWithin) return 'within';
            if (isIntersecting) return 'intersects';
            return 'no_intersect';
        }

        // Add this helper function to create dataset cards
        function createDatasetCard(dataset) {
            const card = document.createElement('div');
            card.className = 'dataset-card bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200';
            card.setAttribute('data-dataset-id', dataset.id);
            card.setAttribute('data-title', dataset.title.toLowerCase());
            
            card.innerHTML = `
                <div class=\"p-4\">
                    <h3 class=\"text-lg font-semibold text-gray-800 mb-2\">\${dataset.title}</h3>
                    \${dataset.abstract ? `<p class=\"text-sm text-gray-600 mb-4 line-clamp-3\">\${dataset.abstract}</p>` : ''}
                    \${dataset.thumbnail_path ? `
                        <div class=\"mb-4\">
                            <img src=\"\${dataset.thumbnail_path}\" alt=\"\${dataset.title}\" class=\"w-full h-32 object-cover rounded\">
                        </div>
                    ` : ''}
                    <div class=\"flex flex-wrap gap-2\">
                        \${dataset.topic_name ? `
                            <span class=\"px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full\">
                                \${dataset.topic_name}
                            </span>
                        ` : ''}
                        \${dataset.inspire_theme_name ? `
                            <span class=\"px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full\">
                                \${dataset.inspire_theme_name}
                            </span>
                        ` : ''}
                    </div>
                    <div class=\"mt-4\">
                        <a href=\"/datasets/\${dataset.id}\" class=\"text-blue-600 hover:text-blue-800 text-sm font-medium\">
                            View Details →
                        </a>
                    </div>
                </div>
            `;
            
            return card;
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
        return "datasets.twig";
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
        return array (  696 => 367,  690 => 364,  684 => 361,  606 => 286,  602 => 285,  590 => 275,  583 => 270,  567 => 262,  565 => 261,  562 => 260,  557 => 258,  548 => 257,  544 => 255,  541 => 254,  539 => 253,  536 => 252,  527 => 249,  519 => 248,  510 => 247,  506 => 246,  503 => 245,  500 => 244,  496 => 242,  494 => 241,  485 => 239,  483 => 238,  480 => 237,  477 => 236,  475 => 235,  472 => 234,  456 => 226,  454 => 225,  445 => 219,  440 => 217,  435 => 215,  428 => 210,  415 => 205,  412 => 204,  399 => 199,  397 => 198,  393 => 196,  391 => 195,  387 => 193,  375 => 187,  370 => 185,  362 => 179,  356 => 176,  350 => 172,  348 => 171,  345 => 170,  339 => 167,  333 => 163,  331 => 162,  325 => 159,  321 => 158,  317 => 156,  309 => 150,  302 => 146,  298 => 145,  295 => 144,  293 => 143,  289 => 142,  285 => 141,  281 => 140,  277 => 139,  273 => 138,  269 => 137,  265 => 136,  261 => 135,  257 => 134,  254 => 133,  250 => 132,  209 => 93,  198 => 91,  194 => 90,  181 => 79,  170 => 77,  166 => 76,  115 => 27,  108 => 26,  98 => 21,  91 => 20,  72 => 6,  65 => 5,  54 => 3,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}Datasets{% endblock %}

{% block extra_css %}
    {{ parent() }}
    <!-- OpenLayers CSS -->
    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css\">
    <style>
        .flex.gap-8 {
            max-width: 1600px;
            margin: 0 auto;
        }
        .flex-grow {
            max-width: calc(1600px - 352px); /* 1600px - (sidebar width + gap) */
        }
    </style>
{% endblock %}

{% block extra_js %}
    {{ parent() }}
    <!-- OpenLayers JS -->
    <script src=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js\"></script>
{% endblock %}

{% block content %}
    <div class=\"flex gap-8\">
        <!-- Search Sidebar -->
        <div class=\"w-80 flex-shrink-0\">
            <div class=\"bg-white rounded-lg shadow-sm p-4 sticky top-4\">
                <h2 class=\"text-lg font-semibold mb-4\">Search & Filter</h2>
                
                <!-- Search Box -->
                <div class=\"mb-6\">
                    <label for=\"search-input\" class=\"block text-sm font-medium text-gray-700 mb-2\">Search Datasets</label>
                    <div class=\"relative\">
                        <input type=\"text\" 
                               id=\"search-input\" 
                               class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500\"
                               placeholder=\"Search by title, abstract...\">
                        <button id=\"search-reset\" class=\"absolute right-2 top-2 text-gray-400 hover:text-gray-600\" title=\"Clear search\">×</button>
                    </div>
                </div>

                <!-- Map Search Widget -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Map</label>
                    <div class=\"mb-2\">
                        <div id=\"mini-map\" style=\"width: 100%; height: 160px; border-radius: 6px; overflow: hidden;\"></div>
                    </div>
                    <div class=\"flex items-center space-x-4 mb-2\">
                        <label class=\"flex items-center text-sm\">
                            <input type=\"radio\" name=\"spatial-relation\" value=\"any\" checked class=\"mr-1\"> Any
                        </label>
                        <label class=\"flex items-center text-sm\">
                            <input type=\"radio\" name=\"spatial-relation\" value=\"intersects\" class=\"mr-1\"> Intersects
                        </label>
                        <label class=\"flex items-center text-sm\">
                            <input type=\"radio\" name=\"spatial-relation\" value=\"within\" class=\"mr-1\"> Within
                        </label>
                    </div>
                    <div class=\"flex space-x-2\">
                        <button id=\"map-search-btn\" class=\"flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500\">Search</button>
                        <button id=\"clear-box-btn\" class=\"px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500\" title=\"Clear drawn box\">×</button>
                    </div>
                </div>

                <!-- Topic Filter -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Topics</label>
                    <select id=\"topic-select\"
                            class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\"
                            onchange=\"updateTopicSelection(this)\"
                            size=\"6\">
                        <option value=\"\">All Topics</option>
                        {% for topic in topics %}
                        <option value=\"{{ topic.id }}\">{{ topic.topic }}</option>
                        {% endfor %}
                    </select>
                </div>

                <!-- Keyword Filter -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Keywords</label>
                    <select id=\"keyword-select\"
                            class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\"
                            onchange=\"updateKeywordSelection(this)\"
                            size=\"6\">
                        <option value=\"\">All Keywords</option>
                        {% for keyword in keywords %}
                        <option value=\"{{ keyword.keyword|replace({\"'\": \"\\\\'\"}) }}\">{{ keyword.keyword }}</option>
                        {% endfor %}
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class=\"mb-6\">
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Date Range</label>
                    <div class=\"space-y-2\">
                        <div>
                            <label for=\"date-from\" class=\"block text-xs text-gray-500\">From</label>
                            <input type=\"date\" 
                                   id=\"date-from\" 
                                   class=\"mt-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500\">
                        </div>
                        <div>
                            <label for=\"date-to\" class=\"block text-xs text-gray-500\">To</label>
                            <input type=\"date\" 
                                   id=\"date-to\" 
                                   class=\"mt-1 w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500\">
                        </div>
                    </div>
                </div>

                <!-- Reset Filters Button -->
                <button id=\"reset-filters\" 
                        class=\"w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500\">
                    Reset All Filters
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class=\"flex-grow\">
<div class=\"bg-white rounded-lg shadow-md p-6 mb-8\">
            <h1 class=\"text-3xl font-bold mb-8\">Datasets</h1>
            <div id=\"datasets-container\" class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6\">


\t\t

                {% for dataset in datasets %}
                    <div class=\"dataset-card bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col h-full\"
                         data-dataset-id=\"{{ dataset.id }}\"
                         data-title=\"{{ dataset.title|lower }}\"
                         data-abstract=\"{{ dataset.abstract|lower }}\"
                         data-topic-id=\"{{ dataset.topic_id }}\"
                         data-topic-name=\"{{ dataset.topic_name }}\"
                         data-inspire-theme-id=\"{{ dataset.inspire_theme_id }}\"
                         data-inspire-theme-name=\"{{ dataset.inspire_theme_name|lower }}\"
                         data-keywords=\"{{ dataset.keywords|json_encode }}\"
                         data-date=\"{{ dataset.metadata_date }}\">
                        {% if dataset.thumbnail_path %}
                            <div class=\"relative h-24 w-full overflow-hidden\">
                                <img src=\"/storage/uploads/thumbnails/{{ dataset.thumbnail_path }}\" 
                                     alt=\"{{ dataset.title }}\" 
                                     class=\"w-full h-full object-cover transition-transform duration-300 hover:scale-105\">
                            </div>
                        {% else %}
                            <div class=\"relative h-24 w-full overflow-hidden\">
                                <img src=\"/storage/uploads/thumbnails/default.png\" 
                                     alt=\"No thumbnail available\" 
                                     class=\"w-full h-full object-cover transition-transform duration-300 hover:scale-105\">
                            </div>
                        {% endif %}
                        
                        <div class=\"p-4 flex-grow flex flex-col\">
                            <h2 class=\"text-lg font-semibold text-gray-900 mb-2 line-clamp-2\">{{ dataset.title }}</h2>
                            <p class=\"text-sm text-gray-600 mb-3 line-clamp-3\">{{ dataset.abstract|length > 120 ? dataset.abstract[:120] ~ '...' : dataset.abstract }}</p>
                            
                            <div class=\"mt-auto space-y-2\">
                                {% if dataset.topic_name %}
                                    <div class=\"flex items-center text-sm text-gray-600\">
                                        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z\"></path>
                                        </svg>
                                        {{ dataset.topic_name }}
                                    </div>
                                {% endif %}
                                
                                {% if dataset.inspire_theme_name %}
                                    <div class=\"flex items-center text-sm text-gray-600\">
                                        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\"></path>
                                        </svg>
                                        {{ dataset.inspire_theme_name }}
                                    </div>
                                {% endif %}
                                
                                <div class=\"mt-auto flex items-center justify-between pt-4\">
                                    <div class=\"flex items-center text-sm text-gray-500\">
                                        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\"></path>
                                        </svg>
                                        Last updated: {{ dataset.metadata_date ? dataset.metadata_date|date('Y-m-d H:i:s') : 'N/A' }}
                                    </div>
                                    <a href=\"/datasets/{{ dataset.id }}\" class=\"text-blue-600 text-sm font-medium hover:underline focus:outline-none\">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
            
            {% if pagination.total_pages > 1 %}
            <div class=\"mt-8 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6\">
                <div class=\"flex flex-1 justify-between sm:hidden\">
                    {% if pagination.has_previous %}
                        <a href=\"?page={{ pagination.current_page - 1 }}{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                           class=\"relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50\">
                            Previous
                        </a>
                    {% endif %}
                    {% if pagination.has_next %}
                        <a href=\"?page={{ pagination.current_page + 1 }}{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                           class=\"relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50\">
                            Next
                        </a>
                    {% endif %}
                </div>
                <div class=\"hidden sm:flex sm:flex-1 sm:items-center sm:justify-between\">
                    <div>
                        <p class=\"text-sm text-gray-700\">
                            Showing
                            <span class=\"font-medium\">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
                            to
                            <span class=\"font-medium\">{{ min(pagination.current_page * pagination.per_page, pagination.total_items) }}</span>
                            of
                            <span class=\"font-medium\">{{ pagination.total_items }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class=\"isolate inline-flex -space-x-px rounded-md shadow-sm\" aria-label=\"Pagination\">
                            {% if pagination.has_previous %}
                                <a href=\"?page={{ pagination.current_page - 1 }}{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                                   class=\"relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">
                                    <span class=\"sr-only\">Previous</span>
                                    <svg class=\"h-5 w-5\" viewBox=\"0 0 20 20\" fill=\"currentColor\" aria-hidden=\"true\">
                                        <path fill-rule=\"evenodd\" d=\"M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z\" clip-rule=\"evenodd\" />
                                    </svg>
                                </a>
                            {% endif %}
                            
                            {% set start_page = max(1, pagination.current_page - 2) %}
                            {% set end_page = min(pagination.total_pages, pagination.current_page + 2) %}
                            
                            {% if start_page > 1 %}
                                <a href=\"?page=1{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                                   class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">1</a>
                                {% if start_page > 2 %}
                                    <span class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0\">...</span>
                                {% endif %}
                            {% endif %}
                            
                            {% for p in range(start_page, end_page) %}
                                <a href=\"?page={{ p }}{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                                   class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold {% if p == pagination.current_page %}bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600{% else %}text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0{% endif %}\">
                                    {{ p }}
                                </a>
                            {% endfor %}
                            
                            {% if end_page < pagination.total_pages %}
                                {% if end_page < pagination.total_pages - 1 %}
                                    <span class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0\">...</span>
                                {% endif %}
                                <a href=\"?page={{ pagination.total_pages }}{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                                   class=\"relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">{{ pagination.total_pages }}</a>
                            {% endif %}
                            
                            {% if pagination.has_next %}
                                <a href=\"?page={{ pagination.current_page + 1 }}{% if request.query.per_page %}&per_page={{ request.query.per_page }}{% endif %}\" 
                                   class=\"relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0\">
                                    <span class=\"sr-only\">Next</span>
                                    <svg class=\"h-5 w-5\" viewBox=\"0 0 20 20\" fill=\"currentColor\" aria-hidden=\"true\">
                                        <path fill-rule=\"evenodd\" d=\"M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z\" clip-rule=\"evenodd\" />
                                    </svg>
                                </a>
                            {% endif %}
                        </nav>
                    </div>
                </div>
            </div>
            {% endif %}
            
            <div id=\"no-results\" class=\"col-span-3 text-center text-gray-500 py-8\" style=\"display: none;\">
                No datasets match your search criteria.
            </div>
        </div>
    </div>
 </div>

    <script>
        // Global variables for selected items
        let selectedTopic = '{{ selected_topic|default(\"\") }}';
        let selectedKeyword = '{{ selected_keyword|default(\"\") }}';
        let allTopics = new Map();
        let allKeywords = new Set();
        let miniMap, drawInteraction;
        let drawnFeature = null;  // Explicitly declare as null

        // Map search functionality
        const mapSearchBtn = document.getElementById('map-search-btn');
        const mapSearchResults = document.getElementById('map-search-results');
        const mapSearchCount = document.getElementById('map-search-count');

        // Function to update the URL with search parameters
        function updateSearchUrl() {
            const searchParams = new URLSearchParams(window.location.search);
            const searchInput = document.getElementById('search-input');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            
            // Update search parameters
            if (searchInput.value) {
                searchParams.set('search', searchInput.value);
            } else {
                searchParams.delete('search');
            }
            
            if (selectedTopic) {
                searchParams.set('topic', selectedTopic);
            } else {
                searchParams.delete('topic');
            }
            
            if (selectedKeyword) {
                searchParams.set('keyword', selectedKeyword);
            } else {
                searchParams.delete('keyword');
            }
            
            if (dateFrom.value) {
                searchParams.set('date_from', dateFrom.value);
            } else {
                searchParams.delete('date_from');
            }
            
            if (dateTo.value) {
                searchParams.set('date_to', dateTo.value);
            } else {
                searchParams.delete('date_to');
            }
            
            // Reset to first page when search parameters change
            searchParams.set('page', '1');
            
            // Update URL without reloading
            const newUrl = window.location.pathname + (searchParams.toString() ? '?' + searchParams.toString() : '');
            window.history.pushState({}, '', newUrl);
            
            // Reload the page to get new results
            window.location.reload();
        }

        // Initialize the map when the DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initMiniMap();

            // Add search input functionality
            const searchInput = document.getElementById('search-input');
            const searchReset = document.getElementById('search-reset');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            const resetFiltersBtn = document.getElementById('reset-filters');
            const topicSelect = document.getElementById('topic-select');
            const keywordSelect = document.getElementById('keyword-select');

            // Set initial values from URL parameters
            if (searchInput) {
                searchInput.value = '{{ search_term|default(\"\") }}';
            }
            if (dateFrom) {
                dateFrom.value = '{{ date_from|default(\"\") }}';
            }
            if (dateTo) {
                dateTo.value = '{{ date_to|default(\"\") }}';
            }
            if (topicSelect && selectedTopic) {
                topicSelect.value = selectedTopic;
            }
            if (keywordSelect && selectedKeyword) {
                keywordSelect.value = selectedKeyword;
            }

            // Add debounced search input handler
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(updateSearchUrl, 500); // Wait 500ms after user stops typing
                });
            }

            if (searchReset) {
                searchReset.addEventListener('click', function() {
                    searchInput.value = '';
                    dateFrom.value = '';
                    dateTo.value = '';
                    updateSearchUrl();
                });
            }

            // Add date input event listeners
            if (dateFrom) {
                dateFrom.addEventListener('change', updateSearchUrl);
            }

            if (dateTo) {
                dateTo.addEventListener('change', updateSearchUrl);
            }

            // Add reset all filters functionality
            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', function() {
                    // Clear search input
                    if (searchInput) searchInput.value = '';
                    
                    // Clear date inputs
                    if (dateFrom) dateFrom.value = '';
                    if (dateTo) dateTo.value = '';
                    
                    // Reset topic selection
                    if (topicSelect) {
                        topicSelect.value = '';
                        selectedTopic = '';
                    }
                    
                    // Reset keyword selection
                    if (keywordSelect) {
                        keywordSelect.value = '';
                        selectedKeyword = '';
                    }
                    
                    // Reset map search
                    if (miniMap) {
                        // Clear the drawn box
                        const vectorSource = miniMap.getLayers().getArray()[1].getSource();
                        vectorSource.clear();
                        drawnFeature = null;
                        
                        // Reset map view to default
                        miniMap.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                        miniMap.getView().setZoom(2);
                    }
                    
                    // Reset spatial relation radio buttons
                    const spatialRelationRadios = document.querySelectorAll('input[name=\"spatial-relation\"]');
                    if (spatialRelationRadios.length > 0) {
                        spatialRelationRadios[0].checked = true; // Set to \"Any\"
                    }
                    
                    // Update URL and reload
                    updateSearchUrl();
                });
            }
        });

        // Update the JavaScript functions for radio buttons
        function updateTopicSelection(select) {
            selectedTopic = select.value;
            updateSearchUrl();
        }

        function updateKeywordSelection(select) {
            selectedKeyword = select.value;
            updateSearchUrl();
        }

        // Update pagination links to preserve search parameters
        document.addEventListener('DOMContentLoaded', function() {
            const paginationLinks = document.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const searchParams = new URLSearchParams(window.location.search);
                    const page = this.getAttribute('href').match(/page=(\\d+)/)?.[1];
                    if (page) {
                        searchParams.set('page', page);
                        window.location.href = window.location.pathname + '?' + searchParams.toString();
                    }
                });
            });
        });

        function initMiniMap() {
            // Create vector source at the top level so it's accessible to all functions
            const vectorSource = new ol.source.Vector();
            
            miniMap = new ol.Map({
                target: 'mini-map',
                interactions: ol.interaction.defaults.defaults({
                    doubleClickZoom: false,
                    dragPan: true,
                    mouseWheelZoom: false,
                    shiftDragZoom: false,
                    pinchZoom: false
                }),
                controls: [
                    new ol.control.Zoom()
                ],
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.OSM()
                    }),
                    new ol.layer.Vector({
                        source: vectorSource,
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: 'rgba(0, 0, 255, 1.0)',
                                width: 2
                            }),
                            fill: new ol.style.Fill({
                                color: 'rgba(0, 0, 255, 0.1)'
                            })
                        })
                    })
                ],
                view: new ol.View({
                    center: ol.proj.fromLonLat([0, 0]),
                    zoom: 2,
                    minZoom: 1,
                    maxZoom: 8
                })
            });

            // Function to clear the drawn box
            function clearDrawnBox() {
                console.log('Attempting to clear box, current drawnFeature:', drawnFeature);
                if (drawnFeature) {
                    vectorSource.clear(); // Clear all features from the source
                    drawnFeature = null;
                    console.log('Box cleared, vectorSource features:', vectorSource.getFeatures().length);
                }
            }

            // Add drawing interaction
            drawInteraction = new ol.interaction.Draw({
                source: vectorSource,
                type: 'Circle',
                geometryFunction: ol.interaction.Draw.createBox(),
                style: new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'rgba(0, 0, 255, 1.0)',
                        width: 2
                    }),
                    fill: new ol.style.Fill({
                        color: 'rgba(0, 0, 255, 0.1)'
                    })
                })
            });

            // Clear old rectangle when starting to draw a new one
            drawInteraction.on('drawstart', function() {
                clearDrawnBox();
            });

            // Handle draw end
            drawInteraction.on('drawend', function(event) {
                // Get the extent of the drawn box
                const extent = event.feature.getGeometry().getExtent();
                
                // Create a new polygon from the extent
                const coordinates = [
                    [extent[0], extent[1]], // bottom-left
                    [extent[2], extent[1]], // bottom-right
                    [extent[2], extent[3]], // top-right
                    [extent[0], extent[3]], // top-left
                    [extent[0], extent[1]]  // close the polygon
                ];
                
                // Create a new feature with the box polygon
                drawnFeature = new ol.Feature({
                    geometry: new ol.geom.Polygon([coordinates])
                });
                
                // Clear any existing features and add the new one
                vectorSource.clear();
                vectorSource.addFeature(drawnFeature);

                // Log for debugging
                console.log('Box drawn, vectorSource features:', vectorSource.getFeatures().length);
            });

            // Add the interaction to the map
            miniMap.addInteraction(drawInteraction);

            // Initialize clear button
            const clearBoxBtn = document.getElementById('clear-box-btn');
            if (clearBoxBtn) {
                clearBoxBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent any default button behavior
                    clearDrawnBox();
                });
            }

            // Prevent the map from clearing the box on click
            miniMap.on('click', function(event) {
                // Only prevent default if we're not in the middle of drawing
                if (!drawInteraction.getActive()) {
                    event.preventDefault();
                }
            });
        }

        if (mapSearchBtn) {
            mapSearchBtn.addEventListener('click', async function() {
                console.log('Search clicked, drawnFeature:', drawnFeature);
                
                if (!drawnFeature) {
                    alert('Please draw a rectangle on the map.');
                    return;
                }

                // Get the spatial relation from the radio buttons
                const spatialRelation = document.querySelector('input[name=\"spatial-relation\"]:checked').value;
                console.log('Using spatial relation:', spatialRelation);

                // Get rectangle coordinates in EPSG:4326
                const extent = drawnFeature.getGeometry().getExtent();
                const bottomLeft = ol.proj.toLonLat([extent[0], extent[1]]);
                const topRight = ol.proj.toLonLat([extent[2], extent[3]]);
                
                // Log the coordinates in both projections for debugging
                console.log('Map coordinates (EPSG:3857):', {
                    west: extent[0],
                    south: extent[1],
                    east: extent[2],
                    north: extent[3]
                });
                console.log('Geographic coordinates (EPSG:4326):', {
                    west: bottomLeft[0],
                    south: bottomLeft[1],
                    east: topRight[0],
                    north: topRight[1]
                });
                
                const bbox = {
                    west: bottomLeft[0],
                    south: bottomLeft[1],
                    east: topRight[0],
                    north: topRight[1]
                };

                // Send search request
                const searchData = {
                    bbox: bbox,
                    spatialRelation: spatialRelation  // Use the selected value instead of hardcoding 'intersects'
                };
                console.log('Sending search request:', searchData);

                try {
                    // Make sure we're using the correct URL
                    const searchUrl = '/api/datasets/search-by-bbox';
                    console.log('Sending search request to:', searchUrl);
                    
                    const response = await fetch(searchUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'  // Explicitly request JSON response
                        },
                        body: JSON.stringify(searchData)
                    });
                    
                    // Log response details for debugging
                    console.log('Response status:', response.status);
                    console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                    
                    if (!response.ok) {
                        const text = await response.text();
                        console.error('Error response body:', text);
                        throw new Error(`HTTP error! status: \${response.status}, body: \${text.substring(0, 200)}...`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    console.log('Response content type:', contentType);
                    
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Unexpected content type. Response body:', text);
                        throw new TypeError(`Expected JSON but got \${contentType}. Response body: \${text.substring(0, 200)}...`);
                    }
                    
                    const data = await response.json();
                    console.log('Search response:', data);
                    
                    if (data.status === 'success') {
                        const datasetIds = data.datasets.map(d => d.id);
                        console.log('Found datasets:', datasetIds);
                        
                        // Log spatial relations for debugging
                        data.datasets.forEach(dataset => {
                            console.log(`Dataset \${dataset.id} (\${dataset.title}):`, {
                                dataset_bbox: dataset.bbox,
                                search_bbox: bbox,
                                spatial_relation: getSpatialRelation(dataset.bbox, bbox)
                            });
                        });

                        // Instead of filtering existing cards, we'll make a request to get all matching datasets
                        const searchParams = new URLSearchParams({
                            ids: datasetIds.join(','),
                            spatial_search: 'true'
                        });
                        
                        const byIdsUrl = `/api/datasets/by-ids?\${searchParams}`;
                        console.log('Fetching dataset details from:', byIdsUrl);
                        console.log('Dataset IDs being requested:', datasetIds);
                        
                        // Fetch the full dataset details
                        const datasetsResponse = await fetch(byIdsUrl);
                        console.log('Response status:', datasetsResponse.status);
                        console.log('Response headers:', Object.fromEntries(datasetsResponse.headers.entries()));
                        
                        if (!datasetsResponse.ok) {
                            const text = await datasetsResponse.text();
                            console.error('Error response from by-ids:', text);
                            throw new Error(`HTTP error! status: \${datasetsResponse.status}, body: \${text.substring(0, 200)}...`);
                        }
                        
                        const contentType = datasetsResponse.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await datasetsResponse.text();
                            console.error('Unexpected content type from by-ids. Response body:', text);
                            throw new TypeError(`Expected JSON but got \${contentType}. Response body: \${text.substring(0, 200)}...`);
                        }
                        
                        const datasetsData = await datasetsResponse.json();
                        
                        if (datasetsData.status === 'success') {
                            // Clear existing grid
                            const grid = document.querySelector('.grid');
                            grid.innerHTML = '';
                            
                            // Add new dataset cards
                            datasetsData.datasets.forEach(dataset => {
                                const card = createDatasetCard(dataset);
                                grid.appendChild(card);
                            });
                            
                            // Show/hide no results message
                            const noResults = document.querySelector('.no-results-message');
                            if (noResults) {
                                noResults.style.display = datasetsData.datasets.length === 0 ? 'block' : 'none';
                            } else if (datasetsData.datasets.length === 0) {
                                const noResultsDiv = document.createElement('div');
                                noResultsDiv.className = 'col-span-full text-center py-8 text-gray-500 no-results-message';
                                noResultsDiv.textContent = 'No datasets found in the selected area';
                                grid.appendChild(noResultsDiv);
                            }
                            
                            console.log('Visible datasets:', datasetsData.datasets.length);
                        }
                    } else {
                        console.error('Search failed:', data.message);
                        alert('Search failed: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error during search:', error);
                    alert('An error occurred during the search. Please try again.');
                }
            });
        }

        // Helper function to determine spatial relation between two bounding boxes
        function getSpatialRelation(datasetBbox, searchBbox) {
            if (!datasetBbox) return 'no_coordinates';
            
            // Check if dataset is completely within search box
            const isWithin = 
                datasetBbox.west >= searchBbox.west &&
                datasetBbox.east <= searchBbox.east &&
                datasetBbox.south >= searchBbox.south &&
                datasetBbox.north <= searchBbox.north;
            
            // Check if dataset intersects with search box
            const isIntersecting = 
                datasetBbox.west <= searchBbox.east &&
                datasetBbox.east >= searchBbox.west &&
                datasetBbox.south <= searchBbox.north &&
                datasetBbox.north >= searchBbox.south;
            
            // Handle coordinate wrapping
            const datasetWraps = datasetBbox.west > datasetBbox.east;
            const searchWraps = searchBbox.west > searchBbox.east;
            
            if (datasetWraps || searchWraps) {
                // More complex intersection check for wrapped coordinates
                const isWrappedIntersecting = 
                    (datasetBbox.west <= searchBbox.east || datasetBbox.east >= searchBbox.west) &&
                    datasetBbox.south <= searchBbox.north &&
                    datasetBbox.north >= searchBbox.south;
                
                return isWrappedIntersecting ? 'intersects_wrapped' : 'no_intersect_wrapped';
            }
            
            if (isWithin) return 'within';
            if (isIntersecting) return 'intersects';
            return 'no_intersect';
        }

        // Add this helper function to create dataset cards
        function createDatasetCard(dataset) {
            const card = document.createElement('div');
            card.className = 'dataset-card bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200';
            card.setAttribute('data-dataset-id', dataset.id);
            card.setAttribute('data-title', dataset.title.toLowerCase());
            
            card.innerHTML = `
                <div class=\"p-4\">
                    <h3 class=\"text-lg font-semibold text-gray-800 mb-2\">\${dataset.title}</h3>
                    \${dataset.abstract ? `<p class=\"text-sm text-gray-600 mb-4 line-clamp-3\">\${dataset.abstract}</p>` : ''}
                    \${dataset.thumbnail_path ? `
                        <div class=\"mb-4\">
                            <img src=\"\${dataset.thumbnail_path}\" alt=\"\${dataset.title}\" class=\"w-full h-32 object-cover rounded\">
                        </div>
                    ` : ''}
                    <div class=\"flex flex-wrap gap-2\">
                        \${dataset.topic_name ? `
                            <span class=\"px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full\">
                                \${dataset.topic_name}
                            </span>
                        ` : ''}
                        \${dataset.inspire_theme_name ? `
                            <span class=\"px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full\">
                                \${dataset.inspire_theme_name}
                            </span>
                        ` : ''}
                    </div>
                    <div class=\"mt-4\">
                        <a href=\"/datasets/\${dataset.id}\" class=\"text-blue-600 hover:text-blue-800 text-sm font-medium\">
                            View Details →
                        </a>
                    </div>
                </div>
            `;
            
            return card;
        }
    </script>
{% endblock %} ", "datasets.twig", "/var/www/novella/templates/datasets.twig");
    }
}
