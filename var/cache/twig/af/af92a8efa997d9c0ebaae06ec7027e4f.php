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

/* keywords.twig */
class __TwigTemplate_b050fc3b78b88c6c837275e205d87fb4 extends Template
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
        yield "GIS Keywords Management";
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
        yield "    <h1 class=\"text-3xl font-bold mb-8\">GIS Keywords Management</h1>
    
    <!-- Add Keyword Form -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Add New Keyword</h2>
        </div>
        <div class=\"p-6\">
            <form action=\"/keywords/add\" method=\"POST\">
                <div class=\"mb-4\">
                    <label for=\"keyword\" class=\"block text-sm font-medium text-gray-700 mb-1\">Keyword</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"keyword\" name=\"keyword\" required>
                </div>
                <div class=\"mb-4\">
                    <label for=\"description\" class=\"block text-sm font-medium text-gray-700 mb-1\">Description</label>
                    <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                              id=\"description\" name=\"description\" rows=\"3\"></textarea>
                </div>
                <button type=\"submit\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                    Add Keyword
                </button>
            </form>
        </div>
    </div>

    <!-- Keywords List -->
    <div class=\"bg-white rounded-lg\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Keywords List</h2>
        </div>
        <div class=\"p-6\">
            <div class=\"overflow-x-auto\">
                <table class=\"min-w-full divide-y divide-gray-200\">
                    <thead>
                        <tr>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Keyword</th>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Description</th>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Created</th>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>
                        </tr>
                    </thead>
                    <tbody class=\"bg-white divide-y divide-gray-200\">
                        ";
        // line 49
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["keywords"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["keyword"]) {
            // line 50
            yield "                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">";
            // line 51
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "keyword", [], "any", false, false, false, 51), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 52
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "description", [], "any", false, false, false, 52), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">";
            // line 53
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "created_at", [], "any", false, false, false, 53), "Y-m-d H:i"), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium\">
                                <button type=\"button\" class=\"text-blue-600 hover:text-blue-900 mr-3\" 
                                        data-bs-toggle=\"modal\" 
                                        data-bs-target=\"#editModal";
            // line 57
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 57), "html", null, true);
            yield "\">
                                    Edit
                                </button>
                                <form action=\"/keywords/delete\" method=\"POST\" class=\"inline\">
                                    <input type=\"hidden\" name=\"id\" value=\"";
            // line 61
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 61), "html", null, true);
            yield "\">
                                    <button type=\"submit\" class=\"text-red-600 hover:text-red-900\" 
                                            onclick=\"return confirm('Are you sure you want to delete this keyword?')\">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class=\"modal fade\" id=\"editModal";
            // line 71
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 71), "html", null, true);
            yield "\" tabindex=\"-1\">
                            <div class=\"modal-dialog\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Edit Keyword</h5>
                                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
                                    </div>
                                    <form action=\"/keywords/edit\" method=\"POST\">
                                        <div class=\"modal-body\">
                                            <input type=\"hidden\" name=\"id\" value=\"";
            // line 80
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 80), "html", null, true);
            yield "\">
                                            <div class=\"mb-3\">
                                                <label for=\"edit_keyword";
            // line 82
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 82), "html", null, true);
            yield "\" class=\"form-label\">Keyword</label>
                                                <input type=\"text\" class=\"form-control\" 
                                                       id=\"edit_keyword";
            // line 84
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 84), "html", null, true);
            yield "\" 
                                                       name=\"keyword\" 
                                                       value=\"";
            // line 86
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "keyword", [], "any", false, false, false, 86), "html", null, true);
            yield "\" 
                                                       required>
                                            </div>
                                            <div class=\"mb-3\">
                                                <label for=\"edit_description";
            // line 90
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 90), "html", null, true);
            yield "\" class=\"form-label\">Description</label>
                                                <textarea class=\"form-control\" 
                                                          id=\"edit_description";
            // line 92
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "id", [], "any", false, false, false, 92), "html", null, true);
            yield "\" 
                                                          name=\"description\" 
                                                          rows=\"3\">";
            // line 94
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["keyword"], "description", [], "any", false, false, false, 94), "html", null, true);
            yield "</textarea>
                                            </div>
                                        </div>
                                        <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Cancel</button>
                                            <button type=\"submit\" class=\"btn btn-primary\">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        ";
            $context['_iterated'] = true;
        }
        // line 105
        if (!$context['_iterated']) {
            // line 106
            yield "                        <tr>
                            <td colspan=\"4\" class=\"px-6 py-4 text-center text-sm text-gray-500\">No keywords found.</td>
                        </tr>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['keyword'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 110
        yield "                    </tbody>
                </table>
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
        return "keywords.twig";
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
        return array (  229 => 110,  220 => 106,  218 => 105,  202 => 94,  197 => 92,  192 => 90,  185 => 86,  180 => 84,  175 => 82,  170 => 80,  158 => 71,  145 => 61,  138 => 57,  131 => 53,  127 => 52,  123 => 51,  120 => 50,  115 => 49,  70 => 6,  63 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}GIS Keywords Management{% endblock %}

{% block content %}
    <h1 class=\"text-3xl font-bold mb-8\">GIS Keywords Management</h1>
    
    <!-- Add Keyword Form -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Add New Keyword</h2>
        </div>
        <div class=\"p-6\">
            <form action=\"/keywords/add\" method=\"POST\">
                <div class=\"mb-4\">
                    <label for=\"keyword\" class=\"block text-sm font-medium text-gray-700 mb-1\">Keyword</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"keyword\" name=\"keyword\" required>
                </div>
                <div class=\"mb-4\">
                    <label for=\"description\" class=\"block text-sm font-medium text-gray-700 mb-1\">Description</label>
                    <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                              id=\"description\" name=\"description\" rows=\"3\"></textarea>
                </div>
                <button type=\"submit\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                    Add Keyword
                </button>
            </form>
        </div>
    </div>

    <!-- Keywords List -->
    <div class=\"bg-white rounded-lg\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Keywords List</h2>
        </div>
        <div class=\"p-6\">
            <div class=\"overflow-x-auto\">
                <table class=\"min-w-full divide-y divide-gray-200\">
                    <thead>
                        <tr>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Keyword</th>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Description</th>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Created</th>
                            <th class=\"px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>
                        </tr>
                    </thead>
                    <tbody class=\"bg-white divide-y divide-gray-200\">
                        {% for keyword in keywords %}
                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">{{ keyword.keyword }}</td>
                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ keyword.description }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">{{ keyword.created_at|date('Y-m-d H:i') }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium\">
                                <button type=\"button\" class=\"text-blue-600 hover:text-blue-900 mr-3\" 
                                        data-bs-toggle=\"modal\" 
                                        data-bs-target=\"#editModal{{ keyword.id }}\">
                                    Edit
                                </button>
                                <form action=\"/keywords/delete\" method=\"POST\" class=\"inline\">
                                    <input type=\"hidden\" name=\"id\" value=\"{{ keyword.id }}\">
                                    <button type=\"submit\" class=\"text-red-600 hover:text-red-900\" 
                                            onclick=\"return confirm('Are you sure you want to delete this keyword?')\">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class=\"modal fade\" id=\"editModal{{ keyword.id }}\" tabindex=\"-1\">
                            <div class=\"modal-dialog\">
                                <div class=\"modal-content\">
                                    <div class=\"modal-header\">
                                        <h5 class=\"modal-title\">Edit Keyword</h5>
                                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
                                    </div>
                                    <form action=\"/keywords/edit\" method=\"POST\">
                                        <div class=\"modal-body\">
                                            <input type=\"hidden\" name=\"id\" value=\"{{ keyword.id }}\">
                                            <div class=\"mb-3\">
                                                <label for=\"edit_keyword{{ keyword.id }}\" class=\"form-label\">Keyword</label>
                                                <input type=\"text\" class=\"form-control\" 
                                                       id=\"edit_keyword{{ keyword.id }}\" 
                                                       name=\"keyword\" 
                                                       value=\"{{ keyword.keyword }}\" 
                                                       required>
                                            </div>
                                            <div class=\"mb-3\">
                                                <label for=\"edit_description{{ keyword.id }}\" class=\"form-label\">Description</label>
                                                <textarea class=\"form-control\" 
                                                          id=\"edit_description{{ keyword.id }}\" 
                                                          name=\"description\" 
                                                          rows=\"3\">{{ keyword.description }}</textarea>
                                            </div>
                                        </div>
                                        <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Cancel</button>
                                            <button type=\"submit\" class=\"btn btn-primary\">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {% else %}
                        <tr>
                            <td colspan=\"4\" class=\"px-6 py-4 text-center text-sm text-gray-500\">No keywords found.</td>
                        </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{% endblock %} ", "keywords.twig", "/var/www/novella/templates/keywords.twig");
    }
}
