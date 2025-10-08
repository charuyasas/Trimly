#!/bin/bash

###############################################################################
# Laravel Performance Optimization Script
# Run this script to optimize your Laravel application for production
###############################################################################

echo "=========================================="
echo "Laravel Performance Optimization"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print success messages
success() {
    echo -e "${GREEN}✓${NC} $1"
}

# Function to print error messages
error() {
    echo -e "${RED}✗${NC} $1"
}

# Function to print warning messages
warning() {
    echo -e "${YELLOW}!${NC} $1"
}

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    error "This doesn't appear to be a Laravel project (artisan file not found)"
    exit 1
fi

success "Laravel project detected"
echo ""

# 1. Install/Update Node dependencies
echo "Step 1: Node Dependencies"
echo "------------------------"
if [ -f "package.json" ]; then
    npm install
    success "Node dependencies installed"
else
    warning "No package.json found, skipping npm install"
fi
echo ""

# 2. Install/Update Composer dependencies
echo "Step 2: Composer Dependencies"
echo "----------------------------"
if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev
    success "Composer dependencies installed with optimized autoloader"
else
    warning "Composer not found, skipping composer install"
fi
echo ""

# 3. Clear all caches
echo "Step 3: Clearing Caches"
echo "----------------------"
php artisan cache:clear
success "Application cache cleared"

php artisan config:clear
success "Configuration cache cleared"

php artisan route:clear
success "Route cache cleared"

php artisan view:clear
success "View cache cleared"

if php artisan | grep -q "event:clear"; then
    php artisan event:clear
    success "Event cache cleared"
fi
echo ""

# 4. Build frontend assets
echo "Step 4: Building Frontend Assets"
echo "--------------------------------"
if [ -f "package.json" ]; then
    NODE_ENV=production npm run build
    success "Frontend assets built for production"
else
    warning "No package.json found, skipping build"
fi
echo ""

# 5. Cache Laravel configurations
echo "Step 5: Caching Configurations"
echo "------------------------------"
php artisan config:cache
success "Configuration cached"

php artisan route:cache
success "Routes cached"

php artisan view:cache
success "Views cached"

if php artisan | grep -q "event:cache"; then
    php artisan event:cache
    success "Events cached"
fi
echo ""

# 6. Optimize Laravel
echo "Step 6: Laravel Optimization"
echo "---------------------------"
php artisan optimize
success "Laravel optimized"
echo ""

# 7. Storage permissions
echo "Step 7: Setting Permissions"
echo "--------------------------"
if [ -d "storage" ]; then
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    success "Storage permissions set"
else
    warning "Storage directory not found"
fi
echo ""

# 8. Display optimization summary
echo "=========================================="
echo "Optimization Complete!"
echo "=========================================="
echo ""
echo "Summary of optimizations:"
echo "  • Autoloader optimized"
echo "  • Configuration cached"
echo "  • Routes cached"
echo "  • Views cached"
echo "  • Frontend assets built (minified & compressed)"
echo "  • Storage permissions set"
echo ""
echo "Additional recommendations:"
echo "  1. Enable OPcache in php.ini"
echo "  2. Use Redis/Memcached for cache driver"
echo "  3. Set up queue workers for background jobs"
echo "  4. Configure CDN for static assets"
echo "  5. Enable HTTP/2 on your web server"
echo "  6. Monitor performance with New Relic/DataDog"
echo ""
echo "To verify performance improvements:"
echo "  • Run: php artisan route:list (should be fast)"
echo "  • Check bundle sizes in public/build/"
echo "  • Test with Google Lighthouse"
echo ""

success "All done! Your application is optimized for production."
