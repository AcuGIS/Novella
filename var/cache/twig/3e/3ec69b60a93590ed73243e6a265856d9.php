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

/* topics.twig */
class __TwigTemplate_fab234e43dd993f0609f7c039ad2cc95 extends Template
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
        yield "GIS Topics Management";
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
        yield "    <h1 class=\"text-3xl font-bold mb-8\">GIS Topics Management</h1>
    
    <!-- Add Topic Form -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Add New Topic</h2>
        </div>
        <div class=\"p-6\">
            <form action=\"/topics/add\" method=\"POST\">
                <div class=\"mb-4\">
                    <label for=\"topic\" class=\"block text-sm font-medium text-gray-700 mb-1\">Topic</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"topic\" name=\"topic\" required>
                </div>
                <div class=\"mb-4\">
                    <label for=\"description\" class=\"block text-sm font-medium text-gray-700 mb-1\">Description</label>
                    <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                              id=\"description\" name=\"description\" rows=\"3\"></textarea>
                </div>
                <button type=\"submit\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                    Add Topic
                </button>
            </form>
        </div>
    </div>

    <!-- Topics List -->
    <div class=\"bg-white rounded-lg\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Existing Topics</h2>
        </div>
        <div class=\"overflow-x-auto\">
            <table class=\"min-w-full divide-y divide-gray-200\">
                <thead class=\"bg-gray-50\">
                    <tr>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Topic</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Description</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Created At</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>
                    </tr>
                </thead>
                <tbody class=\"bg-white divide-y divide-gray-200\">
                    ";
        // line 48
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["topics"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["topic"]) {
            // line 49
            yield "                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900\">";
            // line 50
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "topic", [], "any", false, false, false, 50), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 text-sm text-gray-500\">";
            // line 51
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "description", [], "any", false, false, false, 51), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">";
            // line 52
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "created_at", [], "any", false, false, false, 52), "html", null, true);
            yield "</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                                <button onclick=\"editTopic('";
            // line 54
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "id", [], "any", false, false, false, 54), "html", null, true);
            yield "')\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</button>
                                <button onclick=\"deleteTopic('";
            // line 55
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["topic"], "id", [], "any", false, false, false, 55), "html", null, true);
            yield "')\" class=\"text-red-600 hover:text-red-900\">Delete</button>
                            </td>
                        </tr>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['topic'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 59
        yield "                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editTopic(id) {
            // TODO: Implement edit functionality
            alert('Edit functionality coming soon');
        }

        function deleteTopic(id) {
            if (confirm('Are you sure you want to delete this topic?')) {
                // TODO: Implement delete functionality
                alert('Delete functionality coming soon');
            }
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
        return "topics.twig";
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
        return array (  148 => 59,  138 => 55,  134 => 54,  129 => 52,  125 => 51,  121 => 50,  118 => 49,  114 => 48,  70 => 6,  63 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}GIS Topics Management{% endblock %}

{% block content %}
    <h1 class=\"text-3xl font-bold mb-8\">GIS Topics Management</h1>
    
    <!-- Add Topic Form -->
    <div class=\"bg-white rounded-lg mb-8\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Add New Topic</h2>
        </div>
        <div class=\"p-6\">
            <form action=\"/topics/add\" method=\"POST\">
                <div class=\"mb-4\">
                    <label for=\"topic\" class=\"block text-sm font-medium text-gray-700 mb-1\">Topic</label>
                    <input type=\"text\" class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                           id=\"topic\" name=\"topic\" required>
                </div>
                <div class=\"mb-4\">
                    <label for=\"description\" class=\"block text-sm font-medium text-gray-700 mb-1\">Description</label>
                    <textarea class=\"w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500\" 
                              id=\"description\" name=\"description\" rows=\"3\"></textarea>
                </div>
                <button type=\"submit\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                    Add Topic
                </button>
            </form>
        </div>
    </div>

    <!-- Topics List -->
    <div class=\"bg-white rounded-lg\">
        <div class=\"px-6 py-4 border-b\">
            <h2 class=\"text-xl font-semibold\">Existing Topics</h2>
        </div>
        <div class=\"overflow-x-auto\">
            <table class=\"min-w-full divide-y divide-gray-200\">
                <thead class=\"bg-gray-50\">
                    <tr>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Topic</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Description</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Created At</th>
                        <th scope=\"col\" class=\"px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>
                    </tr>
                </thead>
                <tbody class=\"bg-white divide-y divide-gray-200\">
                    {% for topic in topics %}
                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900\">{{ topic.topic }}</td>
                            <td class=\"px-6 py-4 text-sm text-gray-500\">{{ topic.description }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">{{ topic.created_at }}</td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                                <button onclick=\"editTopic('{{ topic.id }}')\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</button>
                                <button onclick=\"deleteTopic('{{ topic.id }}')\" class=\"text-red-600 hover:text-red-900\">Delete</button>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editTopic(id) {
            // TODO: Implement edit functionality
            alert('Edit functionality coming soon');
        }

        function deleteTopic(id) {
            if (confirm('Are you sure you want to delete this topic?')) {
                // TODO: Implement delete functionality
                alert('Delete functionality coming soon');
            }
        }
    </script>
{% endblock %} ", "topics.twig", "/var/www/novella/templates/topics.twig");
    }
}
