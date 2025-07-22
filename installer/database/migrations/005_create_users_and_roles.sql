-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    last_login TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create user_roles table (many-to-many relationship)
CREATE TABLE IF NOT EXISTS user_roles (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    role_id INTEGER REFERENCES roles(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id)
);

-- Add triggers for updated_at
CREATE TRIGGER update_roles_updated_at
    BEFORE UPDATE ON roles
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Insert default roles
INSERT INTO roles (name, description) VALUES
    ('admin', 'Administrator with all permissions'),
    ('publisher', 'Can publish datasets and edit published datasets'),
    ('editor', 'Can edit datasets');

-- Insert default users (password: Novella)
INSERT INTO users (username, email, password_hash) VALUES
    ('admin', 'admin@novella.local', '$2y$10$2Q4yQuI0cV/h/w8iqYJgoONbbxdzfxEayqagrbx7Y0l4s5T9ts/gm'),
    ('publisher', 'publisher@novella.local', '$2y$10$2Q4yQuI0cV/h/w8iqYJgoONbbxdzfxEayqagrbx7Y0l4s5T9ts/gm'),
    ('editor', 'editor@novella.local', '$2y$10$2Q4yQuI0cV/h/w8iqYJgoONbbxdzfxEayqagrbx7Y0l4s5T9ts/gm');

-- Assign roles to users
INSERT INTO user_roles (user_id, role_id)
SELECT u.id, r.id
FROM users u
CROSS JOIN roles r
WHERE (u.username = 'admin' AND r.name = 'admin')
   OR (u.username = 'publisher' AND r.name = 'publisher')
   OR (u.username = 'editor' AND r.name = 'editor');
