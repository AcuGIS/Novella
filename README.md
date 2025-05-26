# Novella


# GeoLibre GIS Catalog

A modern PHP application for managing, harvesting, and sharing GIS data with ISO 19115 metadata support and OAI-PMH functionality.

## Features

- GIS data catalog with PostgreSQL backend
- ISO 19115 metadata support
- OAI-PMH protocol implementation for data sharing
- Data harvesting capabilities
- Manual data entry interface
- Modern web interface for data visualization
- RESTful API for data access

## Requirements

- PHP 8.1 or higher
- PostgreSQL 12 or higher
- Composer
- Apache/Nginx web server
- PHP extensions: pgsql, json, xml, mbstring

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/geolibre.git
cd geolibre
```

2. Install dependencies:
```bash
composer install
```

3. Create a `.env` file in the root directory with the following content:
```env
# JWT Configuration
JWT_SECRET=your-super-secret-key-change-this-in-production

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=geolibre
DB_USERNAME=root
DB_PASSWORD=

# Application Configuration
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080
```

4. Create the database and run migrations:
```bash
php bin/doctrine orm:schema-tool:create
```

5. Configure environment:
- Copy `.env.example` to `.env`
- Update database credentials and other settings in `.env`

6. Start the development server:
```bash
php -S localhost:8080 -t public
```

## Development

- Run tests: `composer test`
- Start development server: `php -S localhost:8000 -t public`

## Project Structure

```
geolibre/
├── bin/              # Console commands
├── config/           # Configuration files
├── public/           # Web root
├── src/              # Application source
│   ├── Controller/   # Controllers
│   ├── Model/        # Database models
│   ├── Service/      # Business logic
│   └── Util/         # Utilities
├── templates/        # Twig templates
├── tests/            # Test files
└── var/              # Runtime files
```

## License

This project is licensed under the MIT License - see the LICENSE file for details. 