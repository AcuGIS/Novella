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

/* users.twig */
class __TwigTemplate_044d37c7272f60afd7de1458618de8a9 extends Template
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
        yield "User Management";
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
        yield "<div class=\"container mx-auto px-4 py-8\">
    <div class=\"flex justify-between items-center mb-6\">
        <h1 class=\"text-2xl font-bold text-gray-900\">User Management</h1>
        <button onclick=\"showAddUserModal()\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
            Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class=\"bg-white shadow-md rounded-lg overflow-hidden\">
        <table class=\"min-w-full divide-y divide-gray-200\">
            <thead class=\"bg-gray-50\">
                <tr>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Username</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Email</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Roles</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Status</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Last Login</th>
                    <th class=\"px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>
                </tr>
            </thead>
            <tbody class=\"bg-white divide-y divide-gray-200\">
                ";
        // line 28
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["users"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 29
            yield "                <tr>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900\">";
            // line 30
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 30), "html", null, true);
            yield "</td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">";
            // line 31
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "email", [], "any", false, false, false, 31), "html", null, true);
            yield "</td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">
                        ";
            // line 33
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "roles", [], "any", false, false, false, 33));
            foreach ($context['_seq'] as $context["_key"] => $context["role"]) {
                // line 34
                yield "                            <span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1\">
                                ";
                // line 35
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["role"], "html", null, true);
                yield "
                            </span>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['role'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 38
            yield "                    </td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">
                        <span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ";
            // line 40
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["user"], "is_active", [], "any", false, false, false, 40)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("bg-green-100 text-green-800") : ("bg-red-100 text-red-800"));
            yield "\">
                            ";
            // line 41
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["user"], "is_active", [], "any", false, false, false, 41)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Active") : ("Inactive"));
            yield "
                        </span>
                    </td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">
                        ";
            // line 45
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["user"], "last_login", [], "any", false, false, false, 45)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "last_login", [], "any", false, false, false, 45), "Y-m-d H:i:s"), "html", null, true)) : ("Never"));
            yield "
                    </td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                        <button onclick=\"showEditUserModal(";
            // line 48
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "id", [], "any", false, false, false, 48), "html", null, true);
            yield ")\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</button>
                        <button onclick=\"deleteUser(";
            // line 49
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "id", [], "any", false, false, false, 49), "html", null, true);
            yield ")\" class=\"text-red-600 hover:text-red-900\">Delete</button>
                    </td>
                </tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['user'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 53
        yield "            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id=\"addUserModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
    <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
        <div class=\"mt-3\">
            <h3 class=\"text-lg font-medium leading-6 text-gray-900 mb-4\">Add New User</h3>
            <form id=\"addUserForm\" class=\"space-y-4\">
                <div>
                    <label for=\"username\" class=\"block text-sm font-medium text-gray-700\">Username</label>
                    <input type=\"text\" name=\"username\" id=\"username\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label for=\"email\" class=\"block text-sm font-medium text-gray-700\">Email</label>
                    <input type=\"email\" name=\"email\" id=\"email\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label for=\"password\" class=\"block text-sm font-medium text-gray-700\">Password</label>
                    <input type=\"password\" name=\"password\" id=\"password\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Roles</label>
                    <div class=\"space-y-2\">
                        ";
        // line 82
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["roles"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["role"]) {
            // line 83
            yield "                        <div class=\"flex items-center\">
                            <input type=\"checkbox\" name=\"roles[]\" value=\"";
            // line 84
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["role"], "name", [], "any", false, false, false, 84), "html", null, true);
            yield "\"
                                   class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                            <label class=\"ml-2 block text-sm text-gray-900\">";
            // line 86
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["role"], "name", [], "any", false, false, false, 86), "html", null, true);
            yield "</label>
                        </div>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['role'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 89
        yield "                    </div>
                </div>
                <div class=\"flex items-center\">
                    <input type=\"checkbox\" name=\"is_active\" id=\"is_active\" checked
                           class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                    <label for=\"is_active\" class=\"ml-2 block text-sm text-gray-900\">Active</label>
                </div>
                <div class=\"flex justify-end space-x-3 mt-5\">
                    <button type=\"button\" onclick=\"hideAddUserModal()\"
                            class=\"px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                        Cancel
                    </button>
                    <button type=\"submit\"
                            class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id=\"editUserModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
    <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
        <div class=\"mt-3\">
            <h3 class=\"text-lg font-medium leading-6 text-gray-900 mb-4\">Edit User</h3>
            <form id=\"editUserForm\" class=\"space-y-4\">
                <input type=\"hidden\" name=\"user_id\" id=\"edit_user_id\">
                <div>
                    <label for=\"edit_username\" class=\"block text-sm font-medium text-gray-700\">Username</label>
                    <input type=\"text\" name=\"username\" id=\"edit_username\" readonly
                           class=\"mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm\">
                </div>
                <div>
                    <label for=\"edit_email\" class=\"block text-sm font-medium text-gray-700\">Email</label>
                    <input type=\"email\" name=\"email\" id=\"edit_email\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label for=\"edit_password\" class=\"block text-sm font-medium text-gray-700\">New Password (leave blank to keep current)</label>
                    <input type=\"password\" name=\"password\" id=\"edit_password\"
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Roles</label>
                    <div class=\"space-y-2\">
                        ";
        // line 136
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["roles"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["role"]) {
            // line 137
            yield "                        <div class=\"flex items-center\">
                            <input type=\"checkbox\" name=\"roles[]\" value=\"";
            // line 138
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["role"], "name", [], "any", false, false, false, 138), "html", null, true);
            yield "\" id=\"edit_role_";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["role"], "name", [], "any", false, false, false, 138), "html", null, true);
            yield "\"
                                   class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                            <label for=\"edit_role_";
            // line 140
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["role"], "name", [], "any", false, false, false, 140), "html", null, true);
            yield "\" class=\"ml-2 block text-sm text-gray-900\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["role"], "name", [], "any", false, false, false, 140), "html", null, true);
            yield "</label>
                        </div>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['role'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 143
        yield "                    </div>
                </div>
                <div class=\"flex items-center\">
                    <input type=\"checkbox\" name=\"is_active\" id=\"edit_is_active\"
                           class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                    <label for=\"edit_is_active\" class=\"ml-2 block text-sm text-gray-900\">Active</label>
                </div>
                <div class=\"flex justify-end space-x-3 mt-5\">
                    <button type=\"button\" onclick=\"hideEditUserModal()\"
                            class=\"px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                        Cancel
                    </button>
                    <button type=\"submit\"
                            class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

";
        yield from [];
    }

    // line 167
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_extra_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 168
        yield "<script>
// Modal functions
function showAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

function hideAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.getElementById('addUserForm').reset();
}

function showEditUserModal(userId) {
    // Fetch user data
    fetch(`/api/users/\${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const user = data.user;
                document.getElementById('edit_user_id').value = user.id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_is_active').checked = user.is_active;
                
                // Reset all role checkboxes
                document.querySelectorAll('input[name=\"roles[]\"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // Check the user's roles
                user.roles.forEach(role => {
                    const checkbox = document.getElementById(`edit_role_\${role}`);
                    if (checkbox) checkbox.checked = true;
                });
                
                document.getElementById('editUserModal').classList.remove('hidden');
            } else {
                alert('Error loading user data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user data');
        });
}

function hideEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
}

// Form submissions
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.roles = formData.getAll('roles[]');
    data.is_active = formData.get('is_active') === 'on';
    
    fetch('/api/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Error creating user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating user');
    });
});

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('edit_user_id').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.roles = formData.getAll('roles[]');
    data.is_active = formData.get('is_active') === 'on';
    
    // Remove empty password
    if (!data.password) {
        delete data.password;
    }
    
    fetch(`/api/users/\${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating user');
    });
});

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`/api/users/\${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message || 'Error deleting user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting user');
        });
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
        return "users.twig";
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
        return array (  322 => 168,  315 => 167,  288 => 143,  277 => 140,  270 => 138,  267 => 137,  263 => 136,  214 => 89,  205 => 86,  200 => 84,  197 => 83,  193 => 82,  162 => 53,  152 => 49,  148 => 48,  142 => 45,  135 => 41,  131 => 40,  127 => 38,  118 => 35,  115 => 34,  111 => 33,  106 => 31,  102 => 30,  99 => 29,  95 => 28,  71 => 6,  64 => 5,  53 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"base.twig\" %}

{% block title %}User Management{% endblock %}

{% block content %}
<div class=\"container mx-auto px-4 py-8\">
    <div class=\"flex justify-between items-center mb-6\">
        <h1 class=\"text-2xl font-bold text-gray-900\">User Management</h1>
        <button onclick=\"showAddUserModal()\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
            Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class=\"bg-white shadow-md rounded-lg overflow-hidden\">
        <table class=\"min-w-full divide-y divide-gray-200\">
            <thead class=\"bg-gray-50\">
                <tr>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Username</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Email</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Roles</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Status</th>
                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">Last Login</th>
                    <th class=\"px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider\">Actions</th>
                </tr>
            </thead>
            <tbody class=\"bg-white divide-y divide-gray-200\">
                {% for user in users %}
                <tr>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900\">{{ user.username }}</td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">{{ user.email }}</td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">
                        {% for role in user.roles %}
                            <span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1\">
                                {{ role }}
                            </span>
                        {% endfor %}
                    </td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">
                        <span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}\">
                            {{ user.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\">
                        {{ user.last_login ? user.last_login|date('Y-m-d H:i:s') : 'Never' }}
                    </td>
                    <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                        <button onclick=\"showEditUserModal({{ user.id }})\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</button>
                        <button onclick=\"deleteUser({{ user.id }})\" class=\"text-red-600 hover:text-red-900\">Delete</button>
                    </td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id=\"addUserModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
    <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
        <div class=\"mt-3\">
            <h3 class=\"text-lg font-medium leading-6 text-gray-900 mb-4\">Add New User</h3>
            <form id=\"addUserForm\" class=\"space-y-4\">
                <div>
                    <label for=\"username\" class=\"block text-sm font-medium text-gray-700\">Username</label>
                    <input type=\"text\" name=\"username\" id=\"username\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label for=\"email\" class=\"block text-sm font-medium text-gray-700\">Email</label>
                    <input type=\"email\" name=\"email\" id=\"email\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label for=\"password\" class=\"block text-sm font-medium text-gray-700\">Password</label>
                    <input type=\"password\" name=\"password\" id=\"password\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Roles</label>
                    <div class=\"space-y-2\">
                        {% for role in roles %}
                        <div class=\"flex items-center\">
                            <input type=\"checkbox\" name=\"roles[]\" value=\"{{ role.name }}\"
                                   class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                            <label class=\"ml-2 block text-sm text-gray-900\">{{ role.name }}</label>
                        </div>
                        {% endfor %}
                    </div>
                </div>
                <div class=\"flex items-center\">
                    <input type=\"checkbox\" name=\"is_active\" id=\"is_active\" checked
                           class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                    <label for=\"is_active\" class=\"ml-2 block text-sm text-gray-900\">Active</label>
                </div>
                <div class=\"flex justify-end space-x-3 mt-5\">
                    <button type=\"button\" onclick=\"hideAddUserModal()\"
                            class=\"px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                        Cancel
                    </button>
                    <button type=\"submit\"
                            class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id=\"editUserModal\" class=\"fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full\">
    <div class=\"relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white\">
        <div class=\"mt-3\">
            <h3 class=\"text-lg font-medium leading-6 text-gray-900 mb-4\">Edit User</h3>
            <form id=\"editUserForm\" class=\"space-y-4\">
                <input type=\"hidden\" name=\"user_id\" id=\"edit_user_id\">
                <div>
                    <label for=\"edit_username\" class=\"block text-sm font-medium text-gray-700\">Username</label>
                    <input type=\"text\" name=\"username\" id=\"edit_username\" readonly
                           class=\"mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm\">
                </div>
                <div>
                    <label for=\"edit_email\" class=\"block text-sm font-medium text-gray-700\">Email</label>
                    <input type=\"email\" name=\"email\" id=\"edit_email\" required
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label for=\"edit_password\" class=\"block text-sm font-medium text-gray-700\">New Password (leave blank to keep current)</label>
                    <input type=\"password\" name=\"password\" id=\"edit_password\"
                           class=\"mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500\">
                </div>
                <div>
                    <label class=\"block text-sm font-medium text-gray-700 mb-2\">Roles</label>
                    <div class=\"space-y-2\">
                        {% for role in roles %}
                        <div class=\"flex items-center\">
                            <input type=\"checkbox\" name=\"roles[]\" value=\"{{ role.name }}\" id=\"edit_role_{{ role.name }}\"
                                   class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                            <label for=\"edit_role_{{ role.name }}\" class=\"ml-2 block text-sm text-gray-900\">{{ role.name }}</label>
                        </div>
                        {% endfor %}
                    </div>
                </div>
                <div class=\"flex items-center\">
                    <input type=\"checkbox\" name=\"is_active\" id=\"edit_is_active\"
                           class=\"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded\">
                    <label for=\"edit_is_active\" class=\"ml-2 block text-sm text-gray-900\">Active</label>
                </div>
                <div class=\"flex justify-end space-x-3 mt-5\">
                    <button type=\"button\" onclick=\"hideEditUserModal()\"
                            class=\"px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2\">
                        Cancel
                    </button>
                    <button type=\"submit\"
                            class=\"px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2\">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{% endblock %}

{% block extra_js %}
<script>
// Modal functions
function showAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

function hideAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.getElementById('addUserForm').reset();
}

function showEditUserModal(userId) {
    // Fetch user data
    fetch(`/api/users/\${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const user = data.user;
                document.getElementById('edit_user_id').value = user.id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_is_active').checked = user.is_active;
                
                // Reset all role checkboxes
                document.querySelectorAll('input[name=\"roles[]\"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // Check the user's roles
                user.roles.forEach(role => {
                    const checkbox = document.getElementById(`edit_role_\${role}`);
                    if (checkbox) checkbox.checked = true;
                });
                
                document.getElementById('editUserModal').classList.remove('hidden');
            } else {
                alert('Error loading user data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user data');
        });
}

function hideEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
}

// Form submissions
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.roles = formData.getAll('roles[]');
    data.is_active = formData.get('is_active') === 'on';
    
    fetch('/api/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Error creating user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating user');
    });
});

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('edit_user_id').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.roles = formData.getAll('roles[]');
    data.is_active = formData.get('is_active') === 'on';
    
    // Remove empty password
    if (!data.password) {
        delete data.password;
    }
    
    fetch(`/api/users/\${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating user');
    });
});

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`/api/users/\${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message || 'Error deleting user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting user');
        });
    }
}
</script>
{% endblock %} ", "users.twig", "/var/www/novella/templates/users.twig");
    }
}
