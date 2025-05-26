-- Create user_groups table
CREATE TABLE IF NOT EXISTS user_groups (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert default groups
INSERT INTO user_groups (name, description) VALUES
    ('Admin', 'Administrators with full system access'),
    ('Publisher', 'Users who can publish and manage GIS data'),
    ('Editor', 'Users who can edit and contribute GIS data')
ON CONFLICT (name) DO NOTHING;

-- Add group_id to users table
ALTER TABLE users 
ADD COLUMN group_id INTEGER REFERENCES user_groups(id);

-- Create index for group_id
CREATE INDEX IF NOT EXISTS idx_users_group_id ON users(group_id);

-- Migrate existing users to appropriate groups
UPDATE users u
SET group_id = (
    CASE 
        WHEN u.roles::jsonb ? 'ROLE_ADMIN' THEN 
            (SELECT id FROM user_groups WHERE name = 'Admin')
        ELSE 
            (SELECT id FROM user_groups WHERE name = 'Editor')
    END
)
WHERE u.group_id IS NULL;

-- Make group_id required after migration
ALTER TABLE users ALTER COLUMN group_id SET NOT NULL;

-- Create trigger for user_groups updated_at
CREATE TRIGGER update_user_groups_updated_at
    BEFORE UPDATE ON user_groups
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column(); 