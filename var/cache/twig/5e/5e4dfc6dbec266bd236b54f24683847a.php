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

/* base.twig */
class __TwigTemplate_62a5a105ff970669d86692222a4f4062 extends Template
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

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'extra_css' => [$this, 'block_extra_css'],
            'content' => [$this, 'block_content'],
            'extra_js' => [$this, 'block_extra_js'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\" class=\"overflow-y-scroll\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <style>
        /* Always show scrollbar to prevent layout shift */
        html {
            overflow-y: scroll;
            scrollbar-width: thin; /* For Firefox */
            scrollbar-color: #CBD5E0 #F7FAFC; /* For Firefox */
        }
        /* For Webkit browsers (Chrome, Safari) */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F7FAFC;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #CBD5E0;
            border-radius: 4px;
        }
        /* Ensure consistent page width */
        body {
            min-width: 100vw;
            overflow-x: hidden;
        }
    </style>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link href=\"https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css\">
    ";
        // line 34
        yield from $this->unwrap()->yieldBlock('extra_css', $context, $blocks);
        // line 35
        yield "</head>
<body class=\"bg-gray-100 min-h-screen\">
    <!-- Navigation -->
    <nav class=\"bg-white\">
        <div class=\"container-fluid max-w-[1920px] mx-auto px-4\">
            <div class=\"flex justify-between items-center h-16\">
                <div class=\"flex items-center space-x-2\">
                    <img src=\"/storage/uploads/novella-logo-alt.png\" alt=\"Novella Logo\" class=\"h-8 w-auto\">
                    <a href=\"/\" class=\"text-x4 font-bold text-gray-800\" style=\"font-size:1.55rem\">Novella</a>
                </div>
                <div class=\"hidden md:flex items-center space-x-4 flex-1 justify-end\">
                    <div class=\"flex space-x-4\">
                        <a href=\"/\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Datasets</a>
                        <a href=\"/viewer\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Viewer</a>
                        <a href=\"/about\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">About</a>

                        ";
        // line 51
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "isLoggedIn", [], "method", false, false, false, 51)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 52
            yield "                        <a href=\"/form\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Add Metadata</a>
                        <a href=\"/harvest\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Harvest GIS</a>
                        <a href=\"/topics\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Topics</a>
                        <a href=\"/keywords\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Keywords</a>
\t\t\t<a href=\"/users\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Users</a>
                        ";
        }
        // line 58
        yield "                    </div>
                    <div class=\"w-20 text-right\">
                        ";
        // line 60
        if ((($tmp =  !CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "isLoggedIn", [], "method", false, false, false, 60)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 61
            yield "                        <a href=\"/login\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Login</a>
                        ";
        } else {
            // line 63
            yield "                        <a href=\"/logout\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Logout</a>
                        ";
        }
        // line 65
        yield "                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class=\"md:hidden flex items-center\">
                    <button type=\"button\" class=\"mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500\">
                        <span class=\"sr-only\">Open main menu</span>
                        <svg class=\"h-6 w-6\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 6h16M4 12h16M4 18h16\" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class=\"mobile-menu hidden md:hidden\">
            <div class=\"px-2 pt-2 pb-3 space-y-1 sm:px-3\">
                <a href=\"/\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Datasets</a>
                <a href=\"/viewer\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Viewer</a>
                <a href=\"/about\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">About</a>
                ";
        // line 85
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "isLoggedIn", [], "method", false, false, false, 85)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 86
            yield "                <a href=\"/form\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Add Metadata</a>
                <a href=\"/harvest\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Harvest GIS</a>
                <a href=\"/topics\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Topics</a>
                <a href=\"/keywords\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Keywords</a>
                ";
        }
        // line 91
        yield "                ";
        if ((($tmp =  !CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "isLoggedIn", [], "method", false, false, false, 91)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 92
            yield "                <a href=\"/login\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Login</a>
                ";
        } else {
            // line 94
            yield "                <a href=\"/logout\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Logout</a>
                ";
        }
        // line 96
        yield "            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class=\"container-fluid max-w-[1920px] mx-auto px-4 py-8\">
        ";
        // line 102
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 103
        yield "    </main>

    <!-- Scripts -->
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });
    </script>
    ";
        // line 113
        yield from $this->unwrap()->yieldBlock('extra_js', $context, $blocks);
        // line 114
        yield "</body>
</html> ";
        yield from [];
    }

    // line 6
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "GIS Metadata Portal";
        yield from [];
    }

    // line 34
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_css(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 102
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 113
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.twig";
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
        return array (  231 => 113,  221 => 102,  211 => 34,  200 => 6,  194 => 114,  192 => 113,  180 => 103,  178 => 102,  170 => 96,  166 => 94,  162 => 92,  159 => 91,  152 => 86,  150 => 85,  128 => 65,  124 => 63,  120 => 61,  118 => 60,  114 => 58,  106 => 52,  104 => 51,  86 => 35,  84 => 34,  53 => 6,  46 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"en\" class=\"overflow-y-scroll\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{% block title %}GIS Metadata Portal{% endblock %}</title>
    <style>
        /* Always show scrollbar to prevent layout shift */
        html {
            overflow-y: scroll;
            scrollbar-width: thin; /* For Firefox */
            scrollbar-color: #CBD5E0 #F7FAFC; /* For Firefox */
        }
        /* For Webkit browsers (Chrome, Safari) */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F7FAFC;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #CBD5E0;
            border-radius: 4px;
        }
        /* Ensure consistent page width */
        body {
            min-width: 100vw;
            overflow-x: hidden;
        }
    </style>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link href=\"https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css\">
    {% block extra_css %}{% endblock %}
</head>
<body class=\"bg-gray-100 min-h-screen\">
    <!-- Navigation -->
    <nav class=\"bg-white\">
        <div class=\"container-fluid max-w-[1920px] mx-auto px-4\">
            <div class=\"flex justify-between items-center h-16\">
                <div class=\"flex items-center space-x-2\">
                    <img src=\"/storage/uploads/novella-logo-alt.png\" alt=\"Novella Logo\" class=\"h-8 w-auto\">
                    <a href=\"/\" class=\"text-x4 font-bold text-gray-800\" style=\"font-size:1.55rem\">Novella</a>
                </div>
                <div class=\"hidden md:flex items-center space-x-4 flex-1 justify-end\">
                    <div class=\"flex space-x-4\">
                        <a href=\"/\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Datasets</a>
                        <a href=\"/viewer\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Viewer</a>
                        <a href=\"/about\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">About</a>

                        {% if auth.isLoggedIn() %}
                        <a href=\"/form\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Add Metadata</a>
                        <a href=\"/harvest\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Harvest GIS</a>
                        <a href=\"/topics\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Topics</a>
                        <a href=\"/keywords\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Keywords</a>
\t\t\t<a href=\"/users\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Users</a>
                        {% endif %}
                    </div>
                    <div class=\"w-20 text-right\">
                        {% if not auth.isLoggedIn() %}
                        <a href=\"/login\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Login</a>
                        {% else %}
                        <a href=\"/logout\" class=\"text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap\">Logout</a>
                        {% endif %}
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class=\"md:hidden flex items-center\">
                    <button type=\"button\" class=\"mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500\">
                        <span class=\"sr-only\">Open main menu</span>
                        <svg class=\"h-6 w-6\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 6h16M4 12h16M4 18h16\" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class=\"mobile-menu hidden md:hidden\">
            <div class=\"px-2 pt-2 pb-3 space-y-1 sm:px-3\">
                <a href=\"/\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Datasets</a>
                <a href=\"/viewer\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Viewer</a>
                <a href=\"/about\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">About</a>
                {% if auth.isLoggedIn() %}
                <a href=\"/form\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Add Metadata</a>
                <a href=\"/harvest\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Harvest GIS</a>
                <a href=\"/topics\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Topics</a>
                <a href=\"/keywords\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Keywords</a>
                {% endif %}
                {% if not auth.isLoggedIn() %}
                <a href=\"/login\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Login</a>
                {% else %}
                <a href=\"/logout\" class=\"block px-3 py-2 rounded-md text-base font-medium text-gray-800 hover:text-gray-600\">Logout</a>
                {% endif %}
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class=\"container-fluid max-w-[1920px] mx-auto px-4 py-8\">
        {% block content %}{% endblock %}
    </main>

    <!-- Scripts -->
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });
    </script>
    {% block extra_js %}{% endblock %}
</body>
</html> ", "base.twig", "/var/www/novella/templates/base.twig");
    }
}
