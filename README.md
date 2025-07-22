# Novella GIS Metadata Manager

A PHP-PostgreSQL application for managing GIS metadata according to ISO 19115 + INSPIRE standards.

## Features

- Metadata entry form following ISO 19115 + INSPIRE standards
- PostgreSQL database storage
- XML export functionality
- RESTful API endpoints
- Modern web interface

## Requirements

- Ubuntu 24.04 LTS
- PHP 8.1 or higher
- PostgreSQL 12 or higher
- Apache 2.4
- Composer

## Installation on Ubuntu 24.04

1. Install PostgreSQL and Novella with demo:
```bash
cd novella
./installer/postgres.sh
./installer/novella_ubuntu24.sh
```

2. To skip demo use --no-demo option:
```bash
./installer/novella_ubuntu24.sh --no-demo --no-qgis
```

## Database Structure

The application uses the following main tables:
- `metadata_records`: Core metadata information
- `citations`: Citation information
- `contacts`: Contact information
- `geographic_extents`: Geographic bounding boxes
- `temporal_extents`: Temporal coverage
- `spatial_representations`: Coordinate system information
- `constraints`: Use constraints
- `inspire_metadata`: INSPIRE-specific metadata

## Security Considerations

1. Make sure to:
   - Keep PHP and all packages updated
   - Use HTTPS in production
   - Set proper file permissions
   - Configure firewall rules
   - Use strong database passwords
   - Keep your `.env` file secure

2. For production:
   - Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
   - Configure SSL/TLS certificates
   - Set up proper backup procedures
   - Monitor server logs

## Troubleshooting

1. Check Apache error logs:
```bash
sudo tail -f /var/log/apache2/novella-error.log
```

2. Check application logs:
```bash
tail -f /var/www/novella/storage/logs/app.log
```

3. Verify PHP configuration:
```bash
php -i | grep "php.ini"
```

4. Test database connection:
```bash
psql -U your_username -d novella -c "\dt"
```

## License

MIT License
