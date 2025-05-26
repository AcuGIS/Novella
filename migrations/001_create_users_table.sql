CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    roles JSONB NOT NULL DEFAULT '["ROLE_USER"]',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(255)
);

-- Add username column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                  WHERE table_name = 'users' AND column_name = 'username') THEN
        ALTER TABLE users ADD COLUMN username VARCHAR(255);
        -- Copy name to username for existing users
        UPDATE users SET username = name WHERE username IS NULL;
        -- Make username not null and unique after copying data
        ALTER TABLE users ALTER COLUMN username SET NOT NULL;
        ALTER TABLE users ADD CONSTRAINT users_username_key UNIQUE (username);
    END IF;
END $$;

-- Check if we need to convert roles column
DO $$ 
BEGIN
    -- Check if roles column exists and is not already JSONB
    IF EXISTS (
        SELECT 1 
        FROM information_schema.columns 
        WHERE table_name = 'users' 
        AND column_name = 'roles' 
        AND data_type != 'jsonb'
    ) THEN
        -- First remove the default value
        ALTER TABLE users ALTER COLUMN roles DROP DEFAULT;
        
        -- Then convert roles column to JSONB
        ALTER TABLE users 
            ALTER COLUMN roles TYPE JSONB 
            USING CASE 
                WHEN roles = 'admin' THEN '["ROLE_ADMIN", "ROLE_USER"]'::jsonb
                ELSE '["ROLE_USER"]'::jsonb
            END;
            
        -- Add the new default value
        ALTER TABLE users ALTER COLUMN roles SET DEFAULT '["ROLE_USER"]'::jsonb;
    END IF;
END $$;

-- Create admin user if it doesn't exist
INSERT INTO users (username, email, password, roles, name)
VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: 'password'
    '["ROLE_ADMIN", "ROLE_USER"]',
    'Administrator'
) ON CONFLICT (username) DO UPDATE 
SET 
    email = EXCLUDED.email,
    password = EXCLUDED.password,
    roles = EXCLUDED.roles,
    name = EXCLUDED.name;

-- Drop existing indexes if they exist
DO $$ 
BEGIN
    IF EXISTS (SELECT 1 FROM pg_indexes WHERE indexname = 'idx_users_email') THEN
        DROP INDEX idx_users_email;
    END IF;
    IF EXISTS (SELECT 1 FROM pg_indexes WHERE indexname = 'idx_users_role') THEN
        DROP INDEX idx_users_role;
    END IF;
END $$;

-- Create new indexes
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- Create GIN index for roles only if it's JSONB
DO $$ 
BEGIN
    IF EXISTS (
        SELECT 1 
        FROM information_schema.columns 
        WHERE table_name = 'users' 
        AND column_name = 'roles' 
        AND data_type = 'jsonb'
    ) THEN
        CREATE INDEX IF NOT EXISTS idx_users_roles ON users USING gin(roles jsonb_path_ops);
    END IF;
END $$; 