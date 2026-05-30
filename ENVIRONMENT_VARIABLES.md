# Performance-Related Environment Variables

## Production Environment Configuration

Add these environment variables to your `.env` file for optimal performance.

```env
# ------------------------------------------------------------------------------
# Application
# ------------------------------------------------------------------------------
APP_NAME=YourAppName
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# ------------------------------------------------------------------------------
# Cache Configuration (IMPORTANT FOR PERFORMANCE)
# ------------------------------------------------------------------------------

# Cache Driver - Use Redis for best performance
# Options: file, redis, memcached, dynamodb, octane
CACHE_DRIVER=redis

# Session Driver - Use Redis for distributed sessions
# Options: file, cookie, redis, memcached, dynamodb
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue Connection - Use Redis for reliable queues
# Options: sync, redis, database, beanstalkd, sqs
QUEUE_CONNECTION=redis

# ------------------------------------------------------------------------------
# Redis Configuration
# ------------------------------------------------------------------------------
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Redis Cache Database
REDIS_CACHE_DB=1

# Redis Queue Database
REDIS_QUEUE_DB=2

# Redis Session Database
REDIS_SESSION_DB=3

# ------------------------------------------------------------------------------
# Database Configuration
# ------------------------------------------------------------------------------
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Database Connection Pooling
DB_PERSISTENT=true

# Query Logging (disable in production)
DB_LOG_QUERIES=false

# ------------------------------------------------------------------------------
# Logging
# ------------------------------------------------------------------------------
LOG_CHANNEL=stack
LOG_LEVEL=error

# Log Deprecations (disable in production)
LOG_DEPRECATIONS_CHANNEL=null

# ------------------------------------------------------------------------------
# Asset Configuration
# ------------------------------------------------------------------------------

# Asset URL (use CDN in production)
ASSET_URL=https://cdn.your-domain.com

# Mix URL (for Vite assets)
MIX_ASSET_URL="${ASSET_URL}"

# ------------------------------------------------------------------------------
# Performance Optimization Flags
# ------------------------------------------------------------------------------

# OPcache Settings (set in php.ini, documented here for reference)
# opcache.enable=1
# opcache.memory_consumption=256
# opcache.interned_strings_buffer=16
# opcache.max_accelerated_files=20000
# opcache.validate_timestamps=0

# View Compilation Path
VIEW_COMPILED_PATH=/path/to/compiled/views

# ------------------------------------------------------------------------------
# CDN Configuration (Optional)
# ------------------------------------------------------------------------------

# CDN for assets
CDN_URL=https://cdn.your-domain.com
CDN_ENABLED=true

# CloudFlare (if using)
CLOUDFLARE_API_KEY=your_api_key
CLOUDFLARE_ZONE_ID=your_zone_id

# ------------------------------------------------------------------------------
# Compression (Handled by web server, documented here)
# ------------------------------------------------------------------------------
# Gzip/Brotli compression is configured in .htaccess (Apache) or nginx config

# ------------------------------------------------------------------------------
# Mail Configuration (Use Queue for Performance)
# ------------------------------------------------------------------------------
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue Mail (recommended)
MAIL_QUEUE=true

# ------------------------------------------------------------------------------
# Monitoring & APM (Optional)
# ------------------------------------------------------------------------------

# New Relic
NEW_RELIC_LICENSE_KEY=your_license_key
NEW_RELIC_APP_NAME="${APP_NAME}"

# Datadog
DATADOG_API_KEY=your_api_key
DATADOG_APP_KEY=your_app_key

# Sentry
SENTRY_LARAVEL_DSN=your_sentry_dsn
SENTRY_TRACES_SAMPLE_RATE=0.1

# ------------------------------------------------------------------------------
# Security
# ------------------------------------------------------------------------------

# Trusted Proxies (for load balancers/CDN)
TRUSTED_PROXIES=*

# Trusted Hosts
TRUSTED_HOSTS=your-domain.com,www.your-domain.com

# ------------------------------------------------------------------------------
# Additional Performance Settings
# ------------------------------------------------------------------------------

# Filesystem Driver
FILESYSTEM_DISK=local

# Broadcast Driver
BROADCAST_DRIVER=log

# Rate Limiting
RATE_LIMIT_PER_MINUTE=60

# Telescope (disable in production or restrict access)
TELESCOPE_ENABLED=false

# Debugbar (disable in production)
DEBUGBAR_ENABLED=false

# Horizon (enable for queue monitoring)
HORIZON_ENABLED=true
```

---

## Cache Driver Comparison

### File Cache (Default)
- **Pros:** Simple, no dependencies
- **Cons:** Slower, not distributed
- **Use Case:** Development only

### Redis Cache (Recommended)
- **Pros:** Very fast, distributed, persistent
- **Cons:** Requires Redis server
- **Use Case:** Production (recommended)

### Memcached
- **Pros:** Fast, distributed
- **Cons:** Not persistent, requires Memcached server
- **Use Case:** High-traffic applications

### Array Cache
- **Pros:** Fastest (in-memory)
- **Cons:** Only for current request
- **Use Case:** Testing only

---

## Session Driver Comparison

### File Session
- **Pros:** Simple
- **Cons:** Not scalable, slower
- **Use Case:** Single-server setups

### Redis Session (Recommended)
- **Pros:** Fast, distributed, scalable
- **Cons:** Requires Redis
- **Use Case:** Production with multiple servers

### Database Session
- **Pros:** Persistent, queryable
- **Cons:** Slower than Redis
- **Use Case:** When Redis is not available

### Cookie Session
- **Pros:** No server storage
- **Cons:** Limited size, security concerns
- **Use Case:** Stateless applications

---

## Queue Connection Comparison

### Sync
- **Pros:** Immediate execution
- **Cons:** Blocks request
- **Use Case:** Development only

### Redis Queue (Recommended)
- **Pros:** Fast, reliable, distributed
- **Cons:** Requires Redis
- **Use Case:** Production

### Database Queue
- **Pros:** Simple, no dependencies
- **Cons:** Slower than Redis
- **Use Case:** Low-traffic applications

### SQS
- **Pros:** Fully managed, scalable
- **Cons:** AWS-specific, costs money
- **Use Case:** AWS environments

---

## Redis Configuration Best Practices

### Multiple Databases

Use separate Redis databases for different purposes:

```env
REDIS_DB=0          # Default
REDIS_CACHE_DB=1    # Cache
REDIS_QUEUE_DB=2    # Queues
REDIS_SESSION_DB=3  # Sessions
```

### Redis Persistence

In `redis.conf`:

```conf
# RDB Persistence
save 900 1      # Save after 900 seconds if 1 key changed
save 300 10     # Save after 300 seconds if 10 keys changed
save 60 10000   # Save after 60 seconds if 10000 keys changed

# AOF Persistence (more durable)
appendonly yes
appendfsync everysec
```

### Redis Memory Management

```conf
# Max memory
maxmemory 256mb

# Eviction policy
maxmemory-policy allkeys-lru
```

---

## Production Checklist

### Required Settings
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `CACHE_DRIVER=redis`
- [ ] `SESSION_DRIVER=redis`
- [ ] `QUEUE_CONNECTION=redis`

### Optional but Recommended
- [ ] CDN configured
- [ ] APM/Monitoring configured
- [ ] Error tracking (Sentry)
- [ ] Redis configured with persistence
- [ ] Database connection pooling enabled

### Security
- [ ] `APP_KEY` generated and secure
- [ ] All secrets rotated
- [ ] Trusted proxies configured
- [ ] HTTPS enforced
- [ ] CORS configured properly

---

## Development vs Production

### Development (.env)
```env
APP_ENV=local
APP_DEBUG=true
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
DEBUGBAR_ENABLED=true
TELESCOPE_ENABLED=true
```

### Production (.env)
```env
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
DEBUGBAR_ENABLED=false
TELESCOPE_ENABLED=false
```

---

## Testing Configuration Changes

After changing environment variables:

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart

# Restart web server/PHP-FPM
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx  # or apache2
```

---

## Monitoring Environment Variables

Set these up for production monitoring:

```env
# Application Performance Monitoring
APM_ENABLED=true
APM_SERVICE_NAME="${APP_NAME}"
APM_SAMPLE_RATE=0.1

# Error Tracking
ERROR_TRACKING_ENABLED=true

# Performance Metrics
METRICS_ENABLED=true
METRICS_ENDPOINT=https://metrics.your-domain.com
```

---

## Common Issues & Solutions

### Issue: Cache Not Working
**Check:**
```bash
# Verify Redis connection
redis-cli ping
# Should return: PONG

# Check Laravel Redis connection
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');
```

### Issue: Sessions Not Persisting
**Solution:**
- Verify `SESSION_DRIVER=redis`
- Check Redis connection
- Clear session cache: `php artisan session:clear`

### Issue: Queues Not Processing
**Solution:**
- Verify queue worker is running
- Check `QUEUE_CONNECTION=redis`
- Restart workers: `php artisan queue:restart`

---

**Last Updated:** 2025-10-08
