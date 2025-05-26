<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use GeoLibre\Model\User;

class SecurityController
{
    public function __construct(
        private User $user,
        private Twig $twig
    ) {}

    public function login(Request $request, Response $response): Response
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("SecurityController: Login method called with method: " . $request->getMethod());
        error_log("SecurityController: Raw POST data: " . print_r($request->getParsedBody(), true));
        error_log("SecurityController: Current session data: " . print_r($_SESSION, true));
        
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            
            $username = $data['_username'] ?? '';
            $password = $data['_password'] ?? '';

            error_log("SecurityController: Attempting login for username: " . $username);

            // Get user from database
            $user = $this->user->findByUsername($username);
            error_log("SecurityController: User data from database: " . print_r($user, true));

            // Verify password and set session
            if ($user && password_verify($password, $user['password'])) {
                error_log("SecurityController: Login successful for user: " . $username);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'roles' => $user['roles'] ?? ['ROLE_USER'],
                    'group_name' => $user['group_name'] ?? 'Editor'
                ];

                error_log("SecurityController: Session data after login: " . print_r($_SESSION, true));

                // Redirect to the original URL or home page
                $redirectUrl = $_SESSION['redirect_after_login'] ?? '/';
                unset($_SESSION['redirect_after_login']);
                
                return $response->withHeader('Location', $redirectUrl)->withStatus(302);
            }

            error_log("SecurityController: Login failed for username: " . $username);

            // If login fails, show error
            return $this->twig->render($response, 'login.twig', [
                'error' => 'Invalid username or password',
                'last_username' => $username
            ]);
        }

        return $this->twig->render($response, 'login.twig', [
            'error' => null,
            'last_username' => ''
        ]);
    }

    public function logout(Request $request, Response $response): Response
    {
        error_log("Logout called");
        
        // Clear all session data
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        error_log("Session destroyed");
        
        // Redirect to login page
        return $response->withHeader('Location', '/auth/login')->withStatus(302);
    }

    public function getProfile(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user_id'])) {
            return $response->withStatus(401);
        }

        $user = $this->user->findById($_SESSION['user_id']);
        if (!$user) {
            return $response->withStatus(404);
        }

        $data = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'roles' => $user['roles'] ?? ['ROLE_USER']
        ];
        
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUsers(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user']['roles']) || !in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            return $response->withStatus(403);
        }

        $users = $this->user->findAll();
        $data = array_map(function($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'roles' => $user['roles'] ?? ['ROLE_USER']
            ];
        }, $users);
        
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function usersPage(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user']['roles']) || !in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            return $response->withStatus(403);
        }

        $users = $this->user->findAll();
        return $this->twig->render($response, 'admin_users.twig', [
            'users' => $users
        ]);
    }

    public function createUser(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['user']['roles']) || !in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            return $response->withStatus(403);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            
            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                return $this->twig->render($response, 'admin_user_form.twig', [
                    'error' => 'All fields are required'
                ]);
            }

            // Check if username already exists
            if ($this->user->findByUsername($data['username'])) {
                return $this->twig->render($response, 'admin_user_form.twig', [
                    'error' => 'Username already exists'
                ]);
            }

            // Create user
            $userId = $this->user->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'roles' => $data['roles'] ?? ['ROLE_USER']
            ]);

            if ($userId) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'User created successfully'
                ];
                return $response->withHeader('Location', '/admin/users')->withStatus(302);
            }

            return $this->twig->render($response, 'admin_user_form.twig', [
                'error' => 'Failed to create user'
            ]);
        }

        return $this->twig->render($response, 'admin_user_form.twig');
    }

    public function editUser(Request $request, Response $response, array $args): Response
    {
        if (!isset($_SESSION['user']['roles']) || !in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            return $response->withStatus(403);
        }

        $userId = (int) $args['id'];
        $user = $this->user->findById($userId);

        if (!$user) {
            return $response->withStatus(404);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            
            // Validate required fields
            if (empty($data['username']) || empty($data['email'])) {
                return $this->twig->render($response, 'admin_user_form.twig', [
                    'user' => $user,
                    'error' => 'Username and email are required'
                ]);
            }

            // Check if username is taken by another user
            $existingUser = $this->user->findByUsername($data['username']);
            if ($existingUser && $existingUser['id'] !== $userId) {
                return $this->twig->render($response, 'admin_user_form.twig', [
                    'user' => $user,
                    'error' => 'Username already exists'
                ]);
            }

            // Process roles
            $roles = ['ROLE_USER']; // Default role
            if (isset($data['roles']) && is_array($data['roles'])) {
                $roles = $data['roles'];
            }

            // Update user
            $updateData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'roles' => $roles
            ];

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $this->user->update($userId, $updateData);
            
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'User updated successfully'
            ];
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }

        return $this->twig->render($response, 'admin_user_form.twig', [
            'user' => $user
        ]);
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        if (!isset($_SESSION['user']['roles']) || !in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            return $response->withStatus(403);
        }

        $userId = (int) $args['id'];
        
        // Prevent deleting yourself
        if ($userId === $_SESSION['user_id']) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'You cannot delete your own account'
            ];
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }

        if ($this->user->delete($userId)) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'User deleted successfully'
            ];
        } else {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Failed to delete user'
            ];
        }

        return $response->withHeader('Location', '/admin/users')->withStatus(302);
    }
} 