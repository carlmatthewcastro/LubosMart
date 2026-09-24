# MySQL Workbench and Laravel integration

## Important schema boundary

`database/sql/lubosmart_mysql.sql` is a target MySQL 8 schema based on the documented UUID marketplace and logistics contract. The current checked-out migrations are an earlier integer-ID scaffold and do not create all of these tables. Do not run this script against a database that contains the current scaffold unless you have a migration plan and a backup. Choose one strategy:

- Rebuild a fresh development database from the SQL, then align Laravel models and migrations to it.
- Write staged Laravel migrations that transform the existing integer schema into the UUID target. Do not edit migrations that have already run in a shared environment.

MySQL Workbench is an administration/query client; Laravel connects to the MySQL server, not to Workbench.

## Local environment

Set these values in `.env` (use a dedicated application user, never `root`):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lubosmart_test
DB_USERNAME=lubosmart_app
DB_PASSWORD=replace-with-a-secret

DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_0900_ai_ci
DB_PREFIX=
DB_STRICT=true
DB_ENGINE=InnoDB
```

Create the database and least-privilege user as an administrator, either in Workbench or the MySQL client:

```sql
CREATE DATABASE IF NOT EXISTS lubosmart_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER IF NOT EXISTS 'lubosmart_app'@'%' IDENTIFIED BY 'replace-with-a-secret';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE, CREATE TEMPORARY TABLES, LOCK TABLES
    ON lubosmart_test.* TO 'lubosmart_app'@'%';
FLUSH PRIVILEGES;
```

Run the DDL as an administrator, then use the application user for Laravel runtime traffic. In production, keep schema-change credentials separate from runtime credentials.

## Laravel configuration

Laravel already reads the `mysql` connection from `config/database.php`. Confirm the connection contains these settings:

```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'lubosmart'),
    'username' => env('DB_USERNAME', 'lubosmart_app'),
    'password' => env('DB_PASSWORD'),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
    'collation' => env('DB_COLLATION', 'utf8mb4_0900_ai_ci'),
    'prefix' => env('DB_PREFIX', ''),
    'prefix_indexes' => true,
    'strict' => (bool) env('DB_STRICT', true),
    'engine' => env('DB_ENGINE', 'InnoDB'),
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => env('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT', true),
    ]) : [],
],
```

Do not commit `.env`. Store the password and TLS certificate path in the deployment secret manager. For production TLS, set `MYSQL_ATTR_SSL_CA` to the provider CA bundle and leave certificate verification enabled.

## Connection and transaction example

Use Eloquent or Laravel's query builder. The framework uses PDO prepared statements and connection pooling is normally provided by the PHP worker model or an external proxy, not by Workbench.

```php
use Illuminate\Support\Facades\DB;

$result = DB::transaction(function () use ($buyerId, $idempotencyKey) {
    $existing = DB::table('checkout_batches')
        ->where('buyer_id', $buyerId)
        ->where('idempotency_key', $idempotencyKey)
        ->lockForUpdate()
        ->first();

    if ($existing) {
        return $existing;
    }

    // Lock variants in deterministic ID order before reserving inventory.
    $variants = DB::table('product_variants as v')
        ->join('inventory_balances as b', 'b.variant_id', '=', 'v.id')
        ->whereIn('v.id', $variantIds)
        ->orderBy('v.id')
        ->lockForUpdate()
        ->get();

    // Validate quantities/prices server-side, insert the batch/orders/items,
    // and update inventory_balances inside this same transaction.
});
```

Use `DB::transaction($callback, 3)` for retryable deadlocks where the operation is idempotent. Checkout, inventory reservation, pickup confirmation, dispatch acceptance, and custody transitions must be atomic. Never hold a transaction open while calling a map, email, payment, or file-storage provider; enqueue those side effects after commit.

## Verification checklist

1. Execute `database/sql/lubosmart_mysql.sql` in Workbench against a disposable database.
2. In Laravel, run `php artisan config:clear` after changing `.env`.
3. Verify connectivity with `php artisan db:show` and `php artisan migrate:status`.
4. Run the application test suite against MySQL 8, not only SQLite.
5. Confirm `SELECT @@character_set_database, @@collation_database, @@time_zone;` and configure UTC at the server/session level.
6. Add Laravel migrations for future changes; treat the SQL file as a bootstrap/export artifact rather than editing production tables manually.

## Docker note

The repository documentation describes a Dockerized MySQL service, but the checked-out `docker-compose.yml` should be reviewed before use. Ensure the MySQL service persists its data volume, does not publish port 3306 publicly, has a health check, and supplies the same `DB_*` values to the Laravel container. The host-side Workbench connection should use the intentionally published local development port only.
