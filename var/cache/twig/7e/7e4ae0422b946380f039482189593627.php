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

/* dataset_detail.twig */
class __TwigTemplate_61767d0f031ffc629bbea7e458277a70 extends Template
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "title", [], "any", false, false, false, 3), "html", null, true);
        yield " - Dataset Details";
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
        yield "    <div class=\"container mx-auto px-4 py-8\">
        <div class=\"flex justify-between items-center mb-8\">
            <h1 class=\"text-3xl font-bold\">";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "title", [], "any", false, false, false, 8), "html", null, true);
        yield "</h1>
            <div class=\"space-x-4\">
                ";
        // line 10
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "getCurrentUser", [], "method", false, false, false, 10) && CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "getCurrentUser", [], "method", false, false, false, 10), "hasPermission", ["edit_dataset"], "method", false, false, false, 10))) {
            // line 11
            yield "                    <button onclick=\"togglePublic()\" 
                            class=\"";
            // line 12
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "is_public", [], "any", false, false, false, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield "bg-green-600 hover:bg-green-700";
            } else {
                yield "bg-yellow-600 hover:bg-yellow-700";
            }
            yield " text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "is_public", [], "any", false, false, false, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield "focus:ring-green-500";
            } else {
                yield "focus:ring-yellow-500";
            }
            yield "\">
                        ";
            // line 13
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "is_public", [], "any", false, false, false, 13)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 14
                yield "                            <span class=\"flex items-center\">
                                <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                Public
                            </span>
                        ";
            } else {
                // line 21
                yield "                            <span class=\"flex items-center\">
                                <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\"></path>
                                </svg>
                                Private
                            </span>
                        ";
            }
            // line 28
            yield "                    </button>
                    <a href=\"/form/";
            // line 29
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 29), "html", null, true);
            yield "\" 
                       class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                        Edit Metadata
                    </a>
                    <button onclick=\"confirmDelete()\" 
                            class=\"bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2\">
                        Delete Dataset
                    </button>
                ";
        }
        // line 38
        yield "                <span style=\"display: inline-block;\">
                <a class=\"link\" href=\"/metadata/";
        // line 39
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 39), "html", null, true);
        yield "/xml\" 
                   target=\"_blank\">
                    <span class=\"flex items-center\">
                        <i class=\"fa-solid fa-file-code mr-2\"></i>
                        <span>View as XML</span>
                    </span>
                </a></span>

\t\t<span style=\"display: inline-block;\">
                <a href=\"/metadata/";
        // line 48
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 48), "html", null, true);
        yield "/pdf\" 
                   target=\"_blank\">
                    <span class=\"flex items-center\">
                        <i class=\"fa-solid fa-file-pdf mr-2\"></i>
                        <span>Save as PDF</span>
                    </span>
                </a>
\t\t</span>
\t\t
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id=\"deleteModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
            <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
                <div class=\"mt-3 text-center\">
                    <h3 class=\"text-lg leading-6 font-medium text-gray-900\">Confirm Deletion</h3>
                    <div class=\"mt-2 px-7 py-3\">
                        <p class=\"text-sm text-gray-500\">
                            Are you sure you want to delete this dataset? This action cannot be undone.
                        </p>
                    </div>
                    <div class=\"flex justify-center space-x-4 mt-4\">
                        <button onclick=\"closeDeleteModal()\" 
                                class=\"bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                            Cancel
                        </button>
                        <button onclick=\"deleteDataset()\" 
                                class=\"bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2\">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class=\"bg-white overflow-hidden\">
            <!-- Header Section -->
            <div class=\"bg-gray-50 border-b px-8 py-6\">
                <h1 class=\"text-3xl font-bold text-gray-900\">";
        // line 87
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "title", [], "any", false, false, false, 87), "html", null, true);
        yield "</h1>
            </div>

            <div class=\"p-8\">
                <!-- Basic Information -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Basic Information</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Title</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">";
        // line 99
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "title", [], "any", false, false, false, 99), "html", null, true);
        yield "</td>
                                </tr>
                                ";
        // line 101
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_identifier", [], "any", false, false, false, 101)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 102
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Resource Identifier</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 104
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_identifier", [], "any", false, false, false, 104), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 107
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "abstract", [], "any", false, false, false, 107)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 108
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Abstract</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 110
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "abstract", [], "any", false, false, false, 110), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 113
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "purpose", [], "any", false, false, false, 113)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 114
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Purpose</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 116
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "purpose", [], "any", false, false, false, 116), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 119
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "keywords", [], "any", false, false, false, 119)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 120
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Keywords</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 122
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::join(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "keywords", [], "any", false, false, false, 122), ", "), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 125
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "topic_name", [], "any", false, false, false, 125)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 126
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Topic</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 128
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "topic_name", [], "any", false, false, false, 128), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 131
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "inspire_theme_name", [], "any", false, false, false, 131)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 132
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">INSPIRE Theme</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 134
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "inspire_theme_name", [], "any", false, false, false, 134), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 137
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_language", [], "any", false, false, false, 137)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 138
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Language</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 140
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_language", [], "any", false, false, false, 140), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 143
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 143)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 144
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Character Set</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 146
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "character_set", [], "any", false, false, false, 146), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 149
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_date", [], "any", false, false, false, 149)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 150
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Date</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 152
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_date", [], "any", false, false, false, 152), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 155
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 155)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 156
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Maintenance Frequency</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            ";
            // line 159
            $context["frequency_display"] = ["continual" => "Continual", "daily" => "Daily", "weekly" => "Weekly", "fortnightly" => "Fortnightly", "monthly" => "Monthly", "quarterly" => "Quarterly", "biannually" => "Biannually", "annually" => "Annually", "asNeeded" => "As Needed", "irregular" => "Irregular", "notPlanned" => "Not Planned", "unknown" => "Unknown"];
            // line 173
            yield "                                            ";
            yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["frequency_display"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 173), [], "array", true, true, false, 173) &&  !(null === (($_v0 = ($context["frequency_display"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess ? ($_v0[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 173)] ?? null) : null)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v1 = ($context["frequency_display"] ?? null)) && is_array($_v1) || $_v1 instanceof ArrayAccess ? ($_v1[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 173)] ?? null) : null), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "maintenance_frequency", [], "any", false, false, false, 173), "html", null, true)));
            yield "
                                        </td>
                                    </tr>
                                ";
        }
        // line 177
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Citation -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Citation</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                ";
        // line 188
        if ((((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "citation_date", [], "any", false, false, false, 188) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_org", [], "any", false, false, false, 188)) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_person", [], "any", false, false, false, 188)) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 188))) {
            // line 189
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "citation_date", [], "any", false, false, false, 189)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 190
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Date</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
                // line 192
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "citation_date", [], "any", false, false, false, 192), "html", null, true);
                yield "</td>
                                        </tr>
                                    ";
            }
            // line 195
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_org", [], "any", false, false, false, 195)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 196
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Organization</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
                // line 198
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_org", [], "any", false, false, false, 198), "html", null, true);
                yield "</td>
                                        </tr>
                                    ";
            }
            // line 201
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_person", [], "any", false, false, false, 201)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 202
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Person</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
                // line 204
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "responsible_person", [], "any", false, false, false, 204), "html", null, true);
                yield "</td>
                                        </tr>
                                    ";
            }
            // line 207
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 207)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 208
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Role</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">
                                                ";
                // line 211
                $context["role_display"] = ["pointOfContact" => "Point of Contact", "originator" => "Originator", "publisher" => "Publisher", "author" => "Author", "custodian" => "Custodian"];
                // line 218
                yield "                                                ";
                yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["role_display"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 218), [], "array", true, true, false, 218) &&  !(null === (($_v2 = ($context["role_display"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess ? ($_v2[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 218)] ?? null) : null)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v3 = ($context["role_display"] ?? null)) && is_array($_v3) || $_v3 instanceof ArrayAccess ? ($_v3[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 218)] ?? null) : null), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "role", [], "any", false, false, false, 218), "html", null, true)));
                yield "
                                            </td>
                                        </tr>
                                    ";
            }
            // line 222
            yield "                                ";
        } else {
            // line 223
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\" colspan=\"2\">No citation information available</td>
                                    </tr>
                                ";
        }
        // line 227
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Spatial Information -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Spatial Information</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Spatial Extent</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">
                                        ";
        // line 241
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 241), "html", null, true);
        yield "°W to ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 241), "html", null, true);
        yield "°E, 
                                        ";
        // line 242
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 242), "html", null, true);
        yield "°S to ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 242), "html", null, true);
        yield "°N
                                    </td>
                                </tr>
                                ";
        // line 245
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coordinate_system", [], "any", false, false, false, 245)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 246
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Coordinate Reference System</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 248
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coordinate_system", [], "any", false, false, false, 248), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 251
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_resolution", [], "any", false, false, false, 251)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 252
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Spatial Resolution</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 254
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_resolution", [], "any", false, false, false, 254), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 257
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Temporal Information -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Temporal Information</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Start Date</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">";
        // line 270
        yield ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "start_date", [], "any", false, false, false, 270)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "start_date", [], "any", false, false, false, 270), "html", null, true)) : ("Not specified"));
        yield "</td>
                                </tr>
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">End Date</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">";
        // line 274
        yield ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "end_date", [], "any", false, false, false, 274)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "end_date", [], "any", false, false, false, 274), "html", null, true)) : ("Not specified"));
        yield "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Responsible Parties -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Responsible Parties</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                ";
        // line 287
        if (((((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "contact_org", [], "any", false, false, false, 287) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_point_of_contact", [], "any", false, false, false, 287)) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_organization", [], "any", false, false, false, 287)) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_email", [], "any", false, false, false, 287)) || CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 287))) {
            // line 288
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "contact_org", [], "any", false, false, false, 288)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 289
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Contact Organization</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
                // line 291
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "contact_org", [], "any", false, false, false, 291), "html", null, true);
                yield "</td>
                                        </tr>
                                    ";
            }
            // line 294
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_point_of_contact", [], "any", false, false, false, 294)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 295
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Point of Contact</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
                // line 297
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_point_of_contact", [], "any", false, false, false, 297), "html", null, true);
                yield "</td>
                                        </tr>
                                    ";
            }
            // line 300
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_organization", [], "any", false, false, false, 300)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 301
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Organization</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">";
                // line 303
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_organization", [], "any", false, false, false, 303), "html", null, true);
                yield "</td>
                                        </tr>
                                    ";
            }
            // line 306
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_email", [], "any", false, false, false, 306)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 307
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Email</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">
                                                <a href=\"mailto:";
                // line 310
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_email", [], "any", false, false, false, 310), "html", null, true);
                yield "\" class=\"text-blue-600 hover:underline\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_email", [], "any", false, false, false, 310), "html", null, true);
                yield "</a>
                                            </td>
                                        </tr>
                                    ";
            }
            // line 314
            yield "                                    ";
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 314)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 315
                yield "                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Role</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">
                                                ";
                // line 318
                $context["role_display"] = ["pointOfContact" => "Point of Contact", "originator" => "Originator", "publisher" => "Publisher", "author" => "Author", "custodian" => "Custodian"];
                // line 325
                yield "                                                ";
                yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["role_display"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 325), [], "array", true, true, false, 325) &&  !(null === (($_v4 = ($context["role_display"] ?? null)) && is_array($_v4) || $_v4 instanceof ArrayAccess ? ($_v4[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 325)] ?? null) : null)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v5 = ($context["role_display"] ?? null)) && is_array($_v5) || $_v5 instanceof ArrayAccess ? ($_v5[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 325)] ?? null) : null), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "metadata_poc_role", [], "any", false, false, false, 325), "html", null, true)));
                yield "
                                            </td>
                                        </tr>
                                    ";
            }
            // line 329
            yield "                                ";
        } else {
            // line 330
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\" colspan=\"2\">No responsible parties information available</td>
                                    </tr>
                                ";
        }
        // line 334
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Data Quality -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Data Quality</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                ";
        // line 345
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "lineage", [], "any", false, false, false, 345)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 346
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Lineage</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 348
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "lineage", [], "any", false, false, false, 348), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 351
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 351)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 352
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Scope</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 354
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "resource_type", [], "any", false, false, false, 354), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 357
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 357)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 358
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Conformity Result</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            ";
            // line 361
            $context["conformity_display"] = ["conformant" => "Conformant", "non-conformant" => "Non-conformant", "unknown" => "Unknown"];
            // line 366
            yield "                                            ";
            yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["conformity_display"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 366), [], "array", true, true, false, 366) &&  !(null === (($_v6 = ($context["conformity_display"] ?? null)) && is_array($_v6) || $_v6 instanceof ArrayAccess ? ($_v6[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 366)] ?? null) : null)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v7 = ($context["conformity_display"] ?? null)) && is_array($_v7) || $_v7 instanceof ArrayAccess ? ($_v7[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 366)] ?? null) : null), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 366), "html", null, true)));
            yield "
                                        </td>
                                    </tr>
                                ";
        }
        // line 370
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Constraints -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Access and Use Constraints</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                ";
        // line 381
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_constraints", [], "any", false, false, false, 381)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 382
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 384
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_constraints", [], "any", false, false, false, 384), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        } else {
            // line 387
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\">Not specified</td>
                                    </tr>
                                ";
        }
        // line 392
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "access_constraints", [], "any", false, false, false, 392)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 393
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Access Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 395
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "access_constraints", [], "any", false, false, false, 395), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        } else {
            // line 398
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Access Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\">Not specified</td>
                                    </tr>
                                ";
        }
        // line 403
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_limitation", [], "any", false, false, false, 403)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 404
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Limitation</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 406
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "use_limitation", [], "any", false, false, false, 406), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        } else {
            // line 409
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Limitation</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\">Not specified</td>
                                    </tr>
                                ";
        }
        // line 414
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- INSPIRE Metadata -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">INSPIRE Metadata</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">

\t\t\t\t\t<tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Standard Name</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">ISO 19115 / INSPIRE</td>
                                    </tr>

                                ";
        // line 431
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "point_of_contact_org", [], "any", false, false, false, 431)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 432
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">INSPIRE Point of Contact Organization</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 434
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "point_of_contact_org", [], "any", false, false, false, 434), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 437
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 437)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 438
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Conformity Result</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            ";
            // line 441
            $context["conformity_display"] = ["conformant" => "Conformant", "non-conformant" => "Non-conformant", "unknown" => "Unknown"];
            // line 446
            yield "                                            ";
            yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["conformity_display"] ?? null), CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 446), [], "array", true, true, false, 446) &&  !(null === (($_v8 = ($context["conformity_display"] ?? null)) && is_array($_v8) || $_v8 instanceof ArrayAccess ? ($_v8[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 446)] ?? null) : null)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($_v9 = ($context["conformity_display"] ?? null)) && is_array($_v9) || $_v9 instanceof ArrayAccess ? ($_v9[CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 446)] ?? null) : null), "html", null, true)) : ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "conformity_result", [], "any", false, false, false, 446), "html", null, true)));
            yield "
                                        </td>
                                    </tr>
                                ";
        }
        // line 450
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_data_service_url", [], "any", false, false, false, 450)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 451
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Spatial Data Service URL</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            <a href=\"";
            // line 454
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_data_service_url", [], "any", false, false, false, 454), "html", null, true);
            yield "\" target=\"_blank\" class=\"text-blue-600 hover:underline\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "spatial_data_service_url", [], "any", false, false, false, 454), "html", null, true);
            yield "</a>
                                        </td>
                                    </tr>
                                ";
        }
        // line 458
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Distribution -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Distribution</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                ";
        // line 469
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "service_url", [], "any", false, false, false, 469)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 470
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Service URL</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            <a href=\"";
            // line 473
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "service_url", [], "any", false, false, false, 473), "html", null, true);
            yield "\" target=\"_blank\" class=\"text-blue-600 hover:underline\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "service_url", [], "any", false, false, false, 473), "html", null, true);
            yield "</a>
                                        </td>
                                    </tr>
                                ";
        }
        // line 477
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "distribution_url", [], "any", false, false, false, 477)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 478
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Distribution URL</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            <a href=\"";
            // line 481
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "distribution_url", [], "any", false, false, false, 481), "html", null, true);
            yield "\" target=\"_blank\" class=\"text-blue-600 hover:underline\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "distribution_url", [], "any", false, false, false, 481), "html", null, true);
            yield "</a>
                                        </td>
                                    </tr>
                                ";
        }
        // line 485
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 485)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 486
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Data Format(s)</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            ";
            // line 489
            if (is_iterable(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 489))) {
                // line 490
                yield "                                                ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::join(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 490), ", "), "html", null, true);
                yield "
                                            ";
            } else {
                // line 492
                yield "                                                ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "data_format", [], "any", false, false, false, 492), "html", null, true);
                yield "
                                            ";
            }
            // line 494
            yield "                                        </td>
                                    </tr>
                                ";
        }
        // line 497
        yield "                                ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coupled_resource", [], "any", false, false, false, 497)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 498
            yield "                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Coupled Resource</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">";
            // line 500
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "coupled_resource", [], "any", false, false, false, 500), "html", null, true);
            yield "</td>
                                    </tr>
                                ";
        }
        // line 503
        yield "                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Map Preview -->
                ";
        // line 509
        if (((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_url", [], "any", false, false, false, 509) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_layer", [], "any", false, false, false, 509)) || (((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 509) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 509)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 509)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 509)))) {
            // line 510
            yield "                    <div class=\"mb-8\">
                        <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Spatial Preview</h2>
                        <div id=\"map\" class=\"w-full h-96 rounded-lg border\"></div>
                    </div>
                    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css\">
                    <script src=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js\"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Initialize map with OSM base layer
                            var map = new ol.Map({
                                target: 'map',
                                layers: [
                                    new ol.layer.Tile({
                                        source: new ol.source.OSM()
                                    })
                                ],
                                view: new ol.View({
                                    center: ol.proj.fromLonLat([
                                        ";
            // line 528
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 528) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 528))) {
                // line 529
                yield "                                            ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::default(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 529) + CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 529)) / 2), 0), "html", null, true);
                yield ",
                                        ";
            } else {
                // line 530
                yield "0,";
            }
            // line 531
            yield "                                        ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 531) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 531))) {
                // line 532
                yield "                                            ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::default(((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 532) + CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 532)) / 2), 0), "html", null, true);
                yield "
                                        ";
            } else {
                // line 533
                yield "0";
            }
            // line 534
            yield "                                    ]),
                                    zoom: 5
                                })
                            });

                            ";
            // line 539
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_url", [], "any", false, false, false, 539) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_layer", [], "any", false, false, false, 539))) {
                // line 540
                yield "                            // Add WMS layer if available
                            map.addLayer(new ol.layer.Tile({
                                source: new ol.source.TileWMS({
                                    url: '";
                // line 543
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_url", [], "any", false, false, false, 543), "html", null, true);
                yield "',
                                    params: {
                                        'LAYERS': '";
                // line 545
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "wms_layer", [], "any", false, false, false, 545), "html", null, true);
                yield "',
                                        'TILED': true
                                    },
                                    serverType: 'geoserver'
                                })
                            }));

                            // If we have coordinates, fit the map to the extent
                            ";
                // line 553
                if ((((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 553) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 553)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 553)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 553))) {
                    // line 554
                    yield "                            // Debug: log the raw coordinates
                            console.log('Raw coordinates:', ";
                    // line 555
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 555), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 555), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 555), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 555), "html", null, true);
                    yield ");
                            const allZero =
                                ";
                    // line 557
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 557), "html", null, true);
                    yield " == 0 &&
                                ";
                    // line 558
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 558), "html", null, true);
                    yield " == 0 &&
                                ";
                    // line 559
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 559), "html", null, true);
                    yield " == 0 &&
                                ";
                    // line 560
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 560), "html", null, true);
                    yield " == 0;
                            const extent = ol.proj.transformExtent(
                                [";
                    // line 562
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 562), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 562), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 562), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 562), "html", null, true);
                    yield "],
                                'EPSG:4326',
                                'EPSG:3857'
                            );
                            const extentWidth = extent[2] - extent[0];
                            const extentHeight = extent[3] - extent[1];
                            const threshold = 1e-6;
                            if (allZero || (Math.abs(extentWidth) < threshold && Math.abs(extentHeight) < threshold)) {
                                // For point or all-zero extents, center at [0,0] and set max zoomed out
                                map.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                                map.getView().setZoom(0);
                            } else {
                                // For normal extents, fit the map to the extent
                                map.getView().fit(extent, { padding: [50, 50, 50, 50] });
                            }
                            ";
                }
                // line 578
                yield "
                            ";
            } else {
                // line 580
                yield "                            // Only show bounding box if no WMS layer is available
                            ";
                // line 581
                if ((((CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 581) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 581)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 581)) && CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 581))) {
                    // line 582
                    yield "                            const extent = ol.proj.transformExtent(
                                [";
                    // line 583
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "west_longitude", [], "any", false, false, false, 583), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "south_latitude", [], "any", false, false, false, 583), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "east_longitude", [], "any", false, false, false, 583), "html", null, true);
                    yield ", ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "north_latitude", [], "any", false, false, false, 583), "html", null, true);
                    yield "],
                                'EPSG:4326',
                                'EPSG:3857'
                            );

                            // Create a vector layer for the bounding box
                            const vectorSource = new ol.source.Vector();
                            const vectorLayer = new ol.layer.Vector({
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
                            });
                            map.addLayer(vectorLayer);

                            // Create a polygon from the extent
                            const polygon = new ol.Feature({
                                geometry: new ol.geom.Polygon.fromExtent(extent)
                            });
                            vectorSource.addFeature(polygon);

                            // Check if extent has zero width or height (point extent)
                            const extentWidth = extent[2] - extent[0];
                            const extentHeight = extent[3] - extent[1];
                            
                            if (Math.abs(extentWidth) < 1e-6 && Math.abs(extentHeight) < 1e-6) {
                                // For point extents, center at [0,0] and set a reasonable zoom level
                                map.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                                map.getView().setZoom(10);
                            } else {
                                // For normal extents, fit the map to the extent
                                map.getView().fit(extent, { padding: [50, 50, 50, 50] });
                            }
                            ";
                }
                // line 623
                yield "                            ";
            }
            // line 624
            yield "                        });
                    </script>
                ";
        }
        // line 627
        yield "            </div>
        </div>
    </div>
";
        yield from [];
    }

    // line 632
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 633
        yield "<script>
    function confirmDelete() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    async function deleteDataset() {
        try {
            const response = await fetch('/datasets/";
        // line 644
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 644), "html", null, true);
        yield "/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: \${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message and redirect to datasets list
                alert(result.message);
                window.location.href = '/';
            } else {
                throw new Error(result.message || 'Error deleting dataset');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    async function togglePublic() {
        try {
            const response = await fetch('/datasets/";
        // line 673
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dataset"] ?? null), "id", [], "any", false, false, false, 673), "html", null, true);
        yield "/toggle-public', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: \${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message and reload the page to update the UI
                alert(result.message);
                window.location.reload();
            } else {
                throw new Error(result.message || 'Error toggling dataset public status');
            }
        } catch (error) {
            alert('Error: ' + error.message);
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
        return "dataset_detail.twig";
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
        return array (  1155 => 673,  1123 => 644,  1110 => 633,  1103 => 632,  1095 => 627,  1090 => 624,  1087 => 623,  1038 => 583,  1035 => 582,  1033 => 581,  1030 => 580,  1026 => 578,  1001 => 562,  996 => 560,  992 => 559,  988 => 558,  984 => 557,  973 => 555,  970 => 554,  968 => 553,  957 => 545,  952 => 543,  947 => 540,  945 => 539,  938 => 534,  935 => 533,  929 => 532,  926 => 531,  923 => 530,  917 => 529,  915 => 528,  895 => 510,  893 => 509,  885 => 503,  879 => 500,  875 => 498,  872 => 497,  867 => 494,  861 => 492,  855 => 490,  853 => 489,  848 => 486,  845 => 485,  836 => 481,  831 => 478,  828 => 477,  819 => 473,  814 => 470,  812 => 469,  799 => 458,  790 => 454,  785 => 451,  782 => 450,  774 => 446,  772 => 441,  767 => 438,  764 => 437,  758 => 434,  754 => 432,  752 => 431,  733 => 414,  726 => 409,  720 => 406,  716 => 404,  713 => 403,  706 => 398,  700 => 395,  696 => 393,  693 => 392,  686 => 387,  680 => 384,  676 => 382,  674 => 381,  661 => 370,  653 => 366,  651 => 361,  646 => 358,  643 => 357,  637 => 354,  633 => 352,  630 => 351,  624 => 348,  620 => 346,  618 => 345,  605 => 334,  599 => 330,  596 => 329,  588 => 325,  586 => 318,  581 => 315,  578 => 314,  569 => 310,  564 => 307,  561 => 306,  555 => 303,  551 => 301,  548 => 300,  542 => 297,  538 => 295,  535 => 294,  529 => 291,  525 => 289,  522 => 288,  520 => 287,  504 => 274,  497 => 270,  482 => 257,  476 => 254,  472 => 252,  469 => 251,  463 => 248,  459 => 246,  457 => 245,  449 => 242,  443 => 241,  427 => 227,  421 => 223,  418 => 222,  410 => 218,  408 => 211,  403 => 208,  400 => 207,  394 => 204,  390 => 202,  387 => 201,  381 => 198,  377 => 196,  374 => 195,  368 => 192,  364 => 190,  361 => 189,  359 => 188,  346 => 177,  338 => 173,  336 => 159,  331 => 156,  328 => 155,  322 => 152,  318 => 150,  315 => 149,  309 => 146,  305 => 144,  302 => 143,  296 => 140,  292 => 138,  289 => 137,  283 => 134,  279 => 132,  276 => 131,  270 => 128,  266 => 126,  263 => 125,  257 => 122,  253 => 120,  250 => 119,  244 => 116,  240 => 114,  237 => 113,  231 => 110,  227 => 108,  224 => 107,  218 => 104,  214 => 102,  212 => 101,  207 => 99,  192 => 87,  150 => 48,  138 => 39,  135 => 38,  123 => 29,  120 => 28,  111 => 21,  102 => 14,  100 => 13,  86 => 12,  83 => 11,  81 => 10,  76 => 8,  72 => 6,  65 => 5,  53 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}{{ dataset.title }} - Dataset Details{% endblock %}

{% block content %}
    <div class=\"container mx-auto px-4 py-8\">
        <div class=\"flex justify-between items-center mb-8\">
            <h1 class=\"text-3xl font-bold\">{{ dataset.title }}</h1>
            <div class=\"space-x-4\">
                {% if auth.getCurrentUser() and auth.getCurrentUser().hasPermission('edit_dataset') %}
                    <button onclick=\"togglePublic()\" 
                            class=\"{% if dataset.is_public %}bg-green-600 hover:bg-green-700{% else %}bg-yellow-600 hover:bg-yellow-700{% endif %} text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 {% if dataset.is_public %}focus:ring-green-500{% else %}focus:ring-yellow-500{% endif %}\">
                        {% if dataset.is_public %}
                            <span class=\"flex items-center\">
                                <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                                </svg>
                                Public
                            </span>
                        {% else %}
                            <span class=\"flex items-center\">
                                <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\"></path>
                                </svg>
                                Private
                            </span>
                        {% endif %}
                    </button>
                    <a href=\"/form/{{ dataset.id }}\" 
                       class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                        Edit Metadata
                    </a>
                    <button onclick=\"confirmDelete()\" 
                            class=\"bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2\">
                        Delete Dataset
                    </button>
                {% endif %}
                <span style=\"display: inline-block;\">
                <a class=\"link\" href=\"/metadata/{{ dataset.id }}/xml\" 
                   target=\"_blank\">
                    <span class=\"flex items-center\">
                        <i class=\"fa-solid fa-file-code mr-2\"></i>
                        <span>View as XML</span>
                    </span>
                </a></span>

\t\t<span style=\"display: inline-block;\">
                <a href=\"/metadata/{{ dataset.id }}/pdf\" 
                   target=\"_blank\">
                    <span class=\"flex items-center\">
                        <i class=\"fa-solid fa-file-pdf mr-2\"></i>
                        <span>Save as PDF</span>
                    </span>
                </a>
\t\t</span>
\t\t
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id=\"deleteModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
            <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
                <div class=\"mt-3 text-center\">
                    <h3 class=\"text-lg leading-6 font-medium text-gray-900\">Confirm Deletion</h3>
                    <div class=\"mt-2 px-7 py-3\">
                        <p class=\"text-sm text-gray-500\">
                            Are you sure you want to delete this dataset? This action cannot be undone.
                        </p>
                    </div>
                    <div class=\"flex justify-center space-x-4 mt-4\">
                        <button onclick=\"closeDeleteModal()\" 
                                class=\"bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                            Cancel
                        </button>
                        <button onclick=\"deleteDataset()\" 
                                class=\"bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2\">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class=\"bg-white overflow-hidden\">
            <!-- Header Section -->
            <div class=\"bg-gray-50 border-b px-8 py-6\">
                <h1 class=\"text-3xl font-bold text-gray-900\">{{ dataset.title }}</h1>
            </div>

            <div class=\"p-8\">
                <!-- Basic Information -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Basic Information</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Title</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.title }}</td>
                                </tr>
                                {% if dataset.resource_identifier %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Resource Identifier</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.resource_identifier }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.abstract %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Abstract</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.abstract }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.purpose %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Purpose</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.purpose }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.keywords %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Keywords</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.keywords|join(', ') }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.topic_name %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Topic</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.topic_name }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.inspire_theme_name %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">INSPIRE Theme</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.inspire_theme_name }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.metadata_language %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Language</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.metadata_language }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.character_set %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Character Set</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.character_set }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.metadata_date %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Date</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.metadata_date }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.maintenance_frequency %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Maintenance Frequency</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            {% set frequency_display = {
                                                'continual': 'Continual',
                                                'daily': 'Daily',
                                                'weekly': 'Weekly',
                                                'fortnightly': 'Fortnightly',
                                                'monthly': 'Monthly',
                                                'quarterly': 'Quarterly',
                                                'biannually': 'Biannually',
                                                'annually': 'Annually',
                                                'asNeeded': 'As Needed',
                                                'irregular': 'Irregular',
                                                'notPlanned': 'Not Planned',
                                                'unknown': 'Unknown'
                                            } %}
                                            {{ frequency_display[dataset.maintenance_frequency] ?? dataset.maintenance_frequency }}
                                        </td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Citation -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Citation</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                {% if dataset.citation_date or dataset.responsible_org or dataset.responsible_person or dataset.role %}
                                    {% if dataset.citation_date %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Date</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.citation_date }}</td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.responsible_org %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Organization</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.responsible_org }}</td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.responsible_person %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Person</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.responsible_person }}</td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.role %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Role</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">
                                                {% set role_display = {
                                                    'pointOfContact': 'Point of Contact',
                                                    'originator': 'Originator',
                                                    'publisher': 'Publisher',
                                                    'author': 'Author',
                                                    'custodian': 'Custodian'
                                                } %}
                                                {{ role_display[dataset.role] ?? dataset.role }}
                                            </td>
                                        </tr>
                                    {% endif %}
                                {% else %}
                                    <tr>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\" colspan=\"2\">No citation information available</td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Spatial Information -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Spatial Information</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Spatial Extent</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">
                                        {{ dataset.west_longitude }}°W to {{ dataset.east_longitude }}°E, 
                                        {{ dataset.south_latitude }}°S to {{ dataset.north_latitude }}°N
                                    </td>
                                </tr>
                                {% if dataset.coordinate_system %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Coordinate Reference System</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.coordinate_system }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.spatial_resolution %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Spatial Resolution</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.spatial_resolution }}</td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Temporal Information -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Temporal Information</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Start Date</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.start_date ?: 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">End Date</td>
                                    <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.end_date ?: 'Not specified' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Responsible Parties -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Responsible Parties</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                {% if dataset.contact_org or dataset.metadata_point_of_contact or dataset.metadata_poc_organization or dataset.metadata_poc_email or dataset.metadata_poc_role %}
                                    {% if dataset.contact_org %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Contact Organization</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.contact_org }}</td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.metadata_point_of_contact %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Point of Contact</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.metadata_point_of_contact }}</td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.metadata_poc_organization %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Organization</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.metadata_poc_organization }}</td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.metadata_poc_email %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Email</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">
                                                <a href=\"mailto:{{ dataset.metadata_poc_email }}\" class=\"text-blue-600 hover:underline\">{{ dataset.metadata_poc_email }}</a>
                                            </td>
                                        </tr>
                                    {% endif %}
                                    {% if dataset.metadata_poc_role %}
                                        <tr>
                                            <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Role</td>
                                            <td class=\"px-6 py-4 text-sm text-gray-900\">
                                                {% set role_display = {
                                                    'pointOfContact': 'Point of Contact',
                                                    'originator': 'Originator',
                                                    'publisher': 'Publisher',
                                                    'author': 'Author',
                                                    'custodian': 'Custodian'
                                                } %}
                                                {{ role_display[dataset.metadata_poc_role] ?? dataset.metadata_poc_role }}
                                            </td>
                                        </tr>
                                    {% endif %}
                                {% else %}
                                    <tr>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\" colspan=\"2\">No responsible parties information available</td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Data Quality -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Data Quality</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                {% if dataset.lineage %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Lineage</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.lineage }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.resource_type %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Scope</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.resource_type }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.conformity_result %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Conformity Result</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            {% set conformity_display = {
                                                'conformant': 'Conformant',
                                                'non-conformant': 'Non-conformant',
                                                'unknown': 'Unknown'
                                            } %}
                                            {{ conformity_display[dataset.conformity_result] ?? dataset.conformity_result }}
                                        </td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Constraints -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Access and Use Constraints</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                {% if dataset.use_constraints %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.use_constraints }}</td>
                                    </tr>
                                {% else %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\">Not specified</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.access_constraints %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Access Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.access_constraints }}</td>
                                    </tr>
                                {% else %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Access Constraints</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\">Not specified</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.use_limitation %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Limitation</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.use_limitation }}</td>
                                    </tr>
                                {% else %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Use Limitation</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-500\">Not specified</td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- INSPIRE Metadata -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">INSPIRE Metadata</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">

\t\t\t\t\t<tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Metadata Standard Name</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">ISO 19115 / INSPIRE</td>
                                    </tr>

                                {% if dataset.point_of_contact_org %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">INSPIRE Point of Contact Organization</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.point_of_contact_org }}</td>
                                    </tr>
                                {% endif %}
                                {% if dataset.conformity_result %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Conformity Result</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            {% set conformity_display = {
                                                'conformant': 'Conformant',
                                                'non-conformant': 'Non-conformant',
                                                'unknown': 'Unknown'
                                            } %}
                                            {{ conformity_display[dataset.conformity_result] ?? dataset.conformity_result }}
                                        </td>
                                    </tr>
                                {% endif %}
                                {% if dataset.spatial_data_service_url %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Spatial Data Service URL</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            <a href=\"{{ dataset.spatial_data_service_url }}\" target=\"_blank\" class=\"text-blue-600 hover:underline\">{{ dataset.spatial_data_service_url }}</a>
                                        </td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Distribution -->
                <div class=\"mb-8\">
                    <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Distribution</h2>
                    <div class=\"bg-white border rounded-lg overflow-hidden\">
                        <table class=\"min-w-full divide-y divide-gray-200\">
                            <tbody class=\"divide-y divide-gray-200\">
                                {% if dataset.service_url %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Service URL</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            <a href=\"{{ dataset.service_url }}\" target=\"_blank\" class=\"text-blue-600 hover:underline\">{{ dataset.service_url }}</a>
                                        </td>
                                    </tr>
                                {% endif %}
                                {% if dataset.distribution_url %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Distribution URL</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            <a href=\"{{ dataset.distribution_url }}\" target=\"_blank\" class=\"text-blue-600 hover:underline\">{{ dataset.distribution_url }}</a>
                                        </td>
                                    </tr>
                                {% endif %}
                                {% if dataset.data_format %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Data Format(s)</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">
                                            {% if dataset.data_format is iterable %}
                                                {{ dataset.data_format|join(', ') }}
                                            {% else %}
                                                {{ dataset.data_format }}
                                            {% endif %}
                                        </td>
                                    </tr>
                                {% endif %}
                                {% if dataset.coupled_resource %}
                                    <tr>
                                        <td class=\"px-6 py-4 bg-gray-50 text-sm font-medium text-gray-500 w-1/4\">Coupled Resource</td>
                                        <td class=\"px-6 py-4 text-sm text-gray-900\">{{ dataset.coupled_resource }}</td>
                                    </tr>
                                {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Map Preview -->
                {% if (dataset.wms_url and dataset.wms_layer) or (dataset.west_longitude and dataset.east_longitude and dataset.south_latitude and dataset.north_latitude) %}
                    <div class=\"mb-8\">
                        <h2 class=\"text-xl font-semibold mb-4 text-gray-800\">Spatial Preview</h2>
                        <div id=\"map\" class=\"w-full h-96 rounded-lg border\"></div>
                    </div>
                    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css\">
                    <script src=\"https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js\"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Initialize map with OSM base layer
                            var map = new ol.Map({
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

                            {% if dataset.wms_url and dataset.wms_layer %}
                            // Add WMS layer if available
                            map.addLayer(new ol.layer.Tile({
                                source: new ol.source.TileWMS({
                                    url: '{{ dataset.wms_url }}',
                                    params: {
                                        'LAYERS': '{{ dataset.wms_layer }}',
                                        'TILED': true
                                    },
                                    serverType: 'geoserver'
                                })
                            }));

                            // If we have coordinates, fit the map to the extent
                            {% if dataset.west_longitude and dataset.east_longitude and dataset.south_latitude and dataset.north_latitude %}
                            // Debug: log the raw coordinates
                            console.log('Raw coordinates:', {{ dataset.west_longitude }}, {{ dataset.south_latitude }}, {{ dataset.east_longitude }}, {{ dataset.north_latitude }});
                            const allZero =
                                {{ dataset.west_longitude }} == 0 &&
                                {{ dataset.east_longitude }} == 0 &&
                                {{ dataset.south_latitude }} == 0 &&
                                {{ dataset.north_latitude }} == 0;
                            const extent = ol.proj.transformExtent(
                                [{{ dataset.west_longitude }}, {{ dataset.south_latitude }}, {{ dataset.east_longitude }}, {{ dataset.north_latitude }}],
                                'EPSG:4326',
                                'EPSG:3857'
                            );
                            const extentWidth = extent[2] - extent[0];
                            const extentHeight = extent[3] - extent[1];
                            const threshold = 1e-6;
                            if (allZero || (Math.abs(extentWidth) < threshold && Math.abs(extentHeight) < threshold)) {
                                // For point or all-zero extents, center at [0,0] and set max zoomed out
                                map.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                                map.getView().setZoom(0);
                            } else {
                                // For normal extents, fit the map to the extent
                                map.getView().fit(extent, { padding: [50, 50, 50, 50] });
                            }
                            {% endif %}

                            {% else %}
                            // Only show bounding box if no WMS layer is available
                            {% if dataset.west_longitude and dataset.east_longitude and dataset.south_latitude and dataset.north_latitude %}
                            const extent = ol.proj.transformExtent(
                                [{{ dataset.west_longitude }}, {{ dataset.south_latitude }}, {{ dataset.east_longitude }}, {{ dataset.north_latitude }}],
                                'EPSG:4326',
                                'EPSG:3857'
                            );

                            // Create a vector layer for the bounding box
                            const vectorSource = new ol.source.Vector();
                            const vectorLayer = new ol.layer.Vector({
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
                            });
                            map.addLayer(vectorLayer);

                            // Create a polygon from the extent
                            const polygon = new ol.Feature({
                                geometry: new ol.geom.Polygon.fromExtent(extent)
                            });
                            vectorSource.addFeature(polygon);

                            // Check if extent has zero width or height (point extent)
                            const extentWidth = extent[2] - extent[0];
                            const extentHeight = extent[3] - extent[1];
                            
                            if (Math.abs(extentWidth) < 1e-6 && Math.abs(extentHeight) < 1e-6) {
                                // For point extents, center at [0,0] and set a reasonable zoom level
                                map.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                                map.getView().setZoom(10);
                            } else {
                                // For normal extents, fit the map to the extent
                                map.getView().fit(extent, { padding: [50, 50, 50, 50] });
                            }
                            {% endif %}
                            {% endif %}
                        });
                    </script>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock %}

{% block extra_js %}
<script>
    function confirmDelete() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    async function deleteDataset() {
        try {
            const response = await fetch('/datasets/{{ dataset.id }}/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: \${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message and redirect to datasets list
                alert(result.message);
                window.location.href = '/';
            } else {
                throw new Error(result.message || 'Error deleting dataset');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    async function togglePublic() {
        try {
            const response = await fetch('/datasets/{{ dataset.id }}/toggle-public', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: \${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message and reload the page to update the UI
                alert(result.message);
                window.location.reload();
            } else {
                throw new Error(result.message || 'Error toggling dataset public status');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }
</script>
{% endblock %} ", "dataset_detail.twig", "/var/www/novella/templates/dataset_detail.twig");
    }
}
