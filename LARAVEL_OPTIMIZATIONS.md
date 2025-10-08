# Laravel-Specific Performance Optimizations

This guide covers Laravel-specific optimizations to improve application performance.

## 🚀 Configuration Optimizations

### 1. Config Caching

Cache all configuration files into a single file:

```bash
php artisan config:cache
```

**Benefits:**
- Reduces file system calls
- Faster configuration loading
- **Note:** Run this only in production, not in development

**Important:** After running `config:cache`, `.env` file changes won't be reflected until you clear the cache with `php artisan config:clear`.

### 2. Route Caching

Cache all route registrations:

```bash
php artisan route:cache
```

**Benefits:**
- Dramatically speeds up route registration
- Reduces parsing overhead
- **Note:** Only works with controller-based routes

**Important:** Cannot use closures in routes when using route caching.

### 3. View Caching

Compile all Blade templates:

```bash
php artisan view:cache
```

**Benefits:**
- Pre-compiles Blade templates
- Eliminates compilation overhead
- Faster view rendering

### 4. Event Caching

Cache event to listener mappings:

```bash
php artisan event:cache
```

**Benefits:**
- Faster event discovery
- Reduced reflection overhead

---

## 📦 Autoloader Optimization

### Composer Autoloader

Optimize the Composer autoloader:

```bash
composer install --optimize-autoloader --no-dev
```

Or for production:

```bash
composer dump-autoload --optimize --classmap-authoritative --no-dev
```

**Flags explained:**
- `--optimize-autoloader`: Creates a class map for faster loading
- `--classmap-authoritative`: Only uses the class map (no file lookups)
- `--no-dev`: Skips dev dependencies

**Benefits:**
- 20-30% faster autoloading
- Reduced file system calls

---

## 💾 Database Optimizations

### 1. Eager Loading

**Bad (N+1 Query Problem):**
```php
$users = User::all();
foreach ($users as $user) {
    echo $user->posts->count(); // Triggers query for each user
}
```

**Good (Eager Loading):**
```php
$users = User::with('posts')->get();
foreach ($users as $user) {
    echo $user->posts->count(); // No additional queries
}
```

### 2. Lazy Eager Loading

Load relationships only when needed:

```php
$books = Book::all();

if ($needAuthors) {
    $books->load('author');
}
```

### 3. Query Optimization

```php
// Use select to fetch only needed columns
User::select('id', 'name', 'email')->get();

// Use chunks for large datasets
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});

// Use cursor for memory efficiency
foreach (User::cursor() as $user) {
    // Process user
}
```

### 4. Database Indexes

```php
Schema::table('users', function (Blueprint $table) {
    $table->index('email');
    $table->index('created_at');
    $table->index(['user_id', 'status']); // Composite index
});
```

### 5. Query Caching

```php
use Illuminate\Support\Facades\Cache;

$users = Cache::remember('users.all', 3600, function () {
    return User::all();
});
```

### 6. Database Connection Pooling

In `config/database.php`:

```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_PERSISTENT => true, // Enable persistent connections
    ]) : [],
],
```

---

## 🔄 Caching Strategies

### 1. Cache Driver Configuration

In `.env`:

```env
# Use Redis for better performance
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. Redis Configuration

Install Redis PHP extension and Laravel Redis package:

```bash
composer require predis/predis
```

In `config/database.php`:

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
],
```

### 3. Cache Usage Patterns

```php
// Simple caching
Cache::put('key', 'value', 3600);
$value = Cache::get('key');

// Cache forever
Cache::forever('key', 'value');

// Remember pattern
$value = Cache::remember('users', 3600, function () {
    return DB::table('users')->get();
});

// Cache tags (Redis/Memcached only)
Cache::tags(['people', 'authors'])->put('John', $john, 3600);
Cache::tags(['people', 'authors'])->get('John');

// Flush tagged cache
Cache::tags(['people'])->flush();
```

### 4. Model Caching

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected static function booted()
    {
        static::updated(function ($user) {
            Cache::forget("user.{$user->id}");
        });
        
        static::deleted(function ($user) {
            Cache::forget("user.{$user->id}");
        });
    }
    
    public static function findCached($id)
    {
        return Cache::remember("user.{$id}", 3600, function () use ($id) {
            return static::find($id);
        });
    }
}
```

---

## ⚡ Queue Optimization

### 1. Queue Configuration

Move slow tasks to queues:

```php
// Instead of
Mail::to($user)->send(new WelcomeEmail());

// Use
Mail::to($user)->queue(new WelcomeEmail());
```

### 2. Queue Workers

Run queue workers in production:

```bash
php artisan queue:work --tries=3 --timeout=60
```

### 3. Horizon (for Redis)

Install Laravel Horizon for better queue management:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

### 4. Job Batching

```php
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

Bus::batch([
    new ProcessPodcast(Podcast::find(1)),
    new ProcessPodcast(Podcast::find(2)),
    new ProcessPodcast(Podcast::find(3)),
])->dispatch();
```

---

## 🎯 Response Optimization

### 1. Response Caching

Cache entire responses:

```php
Route::get('/users', function () {
    return Cache::remember('users.index', 3600, function () {
        return view('users.index', [
            'users' => User::all()
        ]);
    });
});
```

### 2. HTTP Caching

Use HTTP caching headers:

```php
return response()->view('users.index')
    ->header('Cache-Control', 'public, max-age=3600');
```

### 3. API Resource Optimization

```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // Only load relationships when needed
            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
```

---

## 🔍 Debug and Monitoring

### 1. Laravel Debugbar (Development Only)

```bash
composer require barryvdh/laravel-debugbar --dev
```

### 2. Laravel Telescope (Development/Staging)

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

### 3. Query Logging

Monitor slow queries:

```php
DB::listen(function ($query) {
    if ($query->time > 1000) { // More than 1 second
        Log::warning('Slow query', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time,
        ]);
    }
});
```

### 4. APM Solutions

- **New Relic:** `composer require newrelic/newrelic-php-agent`
- **Blackfire:** `composer require blackfire/php-sdk`
- **Datadog:** `composer require datadog/dd-trace`

---

## 🛡️ Security & Performance

### 1. Rate Limiting

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/user', function () {
        //
    });
});
```

### 2. Trusted Proxies

In `app/Http/Middleware/TrustProxies.php`:

```php
protected $proxies = '*';
protected $headers = Request::HEADER_X_FORWARDED_ALL;
```

---

## 📊 Production Checklist

### Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Pre-Deployment Commands

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize
php artisan optimize

# Migrate database
php artisan migrate --force

# Restart queue workers
php artisan queue:restart
```

---

## 🎓 Advanced Optimizations

### 1. OPcache Configuration

Add to `php.ini`:

```ini
[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

### 2. PHP-FPM Optimization

In `php-fpm.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

### 3. MySQL Optimization

```sql
-- Add indexes
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_posts_user_created ON posts(user_id, created_at);

-- Optimize tables
OPTIMIZE TABLE users;
OPTIMIZE TABLE posts;
```

---

## 📈 Monitoring & Metrics

### Key Metrics to Track

1. **Response Time:** Average, 50th, 95th, 99th percentile
2. **Database Queries:** Count, time, slow queries
3. **Cache Hit Rate:** Percentage of cache hits vs misses
4. **Queue Processing:** Job throughput, failed jobs
5. **Memory Usage:** PHP memory, Redis memory
6. **Error Rate:** 4xx, 5xx errors per minute

### Tools

- Laravel Telescope (Development)
- Laravel Horizon (Queue monitoring)
- New Relic APM
- Datadog APM
- Blackfire Profiler
- Tideways

---

**Last Updated:** 2025-10-08
