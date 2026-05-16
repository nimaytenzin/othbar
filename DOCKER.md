# Running this app with Docker (Ubuntu 20.04–friendly)

Your host OS may not ship PHP 8.2+ (for example Ubuntu 20.04). Docker lets you use current PHP and Composer without upgrading the whole system.

**Requirements:** [Docker Engine](https://docs.docker.com/engine/install/) installed and your user able to run `docker` (either in the `docker` group or via `sudo docker`).

---

## One command (recommended)

From the **repository root**:

```bash
chmod +x docker-dev.sh   # first time only
./docker-dev.sh
```

This will:

1. Create `.env` from `.env.example` if missing  
2. Build a **PHP 8.4** dev image (`docker/dev/Dockerfile`) with Composer, PDO MySQL, `soap`, `gd`, `exif`, etc.  
3. Run `composer install` if `vendor/` is missing  
4. Generate `APP_KEY` if unset  
5. Run **`php artisan config:clear`** if `bootstrap/cache/config.php` exists (avoids stale `DB_HOST`)  
6. Start **MariaDB** (Docker), **Laravel** on [http://localhost:8000](http://localhost:8000), and **Vite** on [http://localhost:5173](http://localhost:5173)

First-time DB schema:

```bash
./docker-dev.sh --migrate
```

**Admin login (after migrate + seed):** run `docker compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed` (or `./docker-dev.sh --migrate` on a fresh volume) to apply the app schema and seed catalog data, sample coupon **WELCOME10**, and an administrator user.

Default staff account from [`AdminUserSeeder`](database/seeders/AdminUserSeeder.php):

| | |
|--|--|
| **Email** | `admin@othbar.local` (override with `ADMIN_EMAIL`) |
| **Password** | `password` (override with `ADMIN_PASSWORD`) |
| **Admin URL** | `http://localhost:8000/cpanel/login` (Filament panel) |

Storefront “Login” in the header opens `/login`, then **Continue to admin login** goes to the same panel.

**Useful flags:**

| Flag | Meaning |
|------|---------|
| `./docker-dev.sh --migrate` | Run `php artisan migrate --force` before serving |
| `./docker-dev.sh --no-vite` | Only PHP / Artisan serve (run `npm run dev` on the host yourself if needed) |
| `./docker-dev.sh --build` | Force rebuild the PHP image (`--no-cache`) |
| `./docker-dev.sh --install` | Always run `composer install` |

**Ports:** `APP_PORT` and `VITE_PORT` (defaults `8000` / `5173`):

```bash
APP_PORT=8080 VITE_PORT=5174 ./docker-dev.sh
```

The compose file maps **`OTHBAR_APP_PORT` / `OTHBAR_VITE_PORT`** (exported by the script from `APP_PORT` / `VITE_PORT`) so blank Laravel `.env` keys cannot break Docker port mapping.

**Database (MariaDB in Docker):** The stack includes a **`mariadb`** service. Compose forces **`DB_CONNECTION=mysql`**, **`DB_HOST=mariadb`**, **`DB_PORT=3306`** for the `app` container (your `.env` may still say `sqlite` / `127.0.0.1` — overrides apply only while running this compose file).

Align credentials in `.env` with what MariaDB expects:

| Variable | Purpose |
|----------|---------|
| `DB_DATABASE` | Database name (default `othbar`) |
| `DB_USERNAME` | Must **not** be `root` (MariaDB Docker rejects `MYSQL_USER=root`). Use e.g. `othbar`. |
| `DB_PASSWORD` | Same password MariaDB assigns to `MYSQL_USER` |
| `MYSQL_ROOT_PASSWORD` | Root password inside the container (default `devroot` if unset) |
| `MYSQL_PUBLISH_PORT` | Host port mapped to container `3306` (default **`3307`** so it does not collide with MariaDB/MySQL on the host at `:3306`). Set `MYSQL_PUBLISH_PORT=3306` only when that port is free. |

Data persists in the **`mariadb_data`** Docker volume. If credentials change after the volume was created, run **`docker compose -f docker-compose.dev.yml down -v`** (drops DB data) then **`./docker-dev.sh --migrate`** again.

Implementation files: `docker/dev/Dockerfile`, `docker-compose.dev.yml`, `docker/app.compose.env`, `docker-dev.sh`.

---

## Manual steps (same stack, copy–paste)

### 1. From the project root

```bash
cd /path/to/othbar
```

All commands below assume you run them here.

---

### 2. Install PHP dependencies (`composer install`)

Uses the official Composer image (bundled PHP suitable for Laravel dependencies).

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$PWD:/app" \
  -w /app \
  composer:2 install
```

If permissions on `vendor/` look wrong afterward, fix ownership once:

```bash
sudo chown -R "$(id -u):$(id -g)" vendor bootstrap/cache storage
```

(Adjust if you prefer not using `sudo`; often `--user` avoids root-owned files.)

---

### 3. Environment file

```bash
cp -n .env.example .env   # skip if .env already exists
```

Generate an app key **inside Docker** (same pattern as Composer):

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$PWD:/app" \
  -w /app \
  composer:2 php artisan key:generate
```

---

### 4. Database

**Default (with `./docker-dev.sh`):** MariaDB runs in Docker (`mariadb` service). Set non-root **`DB_USERNAME`** / **`DB_PASSWORD`** / **`DB_DATABASE`** in `.env` (see “One command” above). Run migrations once:

```bash
./docker-dev.sh --migrate
```

**Optional — DB on the host instead:** Remove or stop the `mariadb` service and point `app` at `host.docker.internal` (you must edit **`docker-compose.dev.yml`** accordingly). Inside a container, `127.0.0.1` is **not** your laptop.

Example `.env` values when the DB listens on the host and the app container uses `host.docker.internal`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=host.docker.internal
DB_PORT=3306
```

**Docker flag** (Linux) so `host.docker.internal` resolves:

```bash
--add-host=host.docker.internal:host-gateway
```

Legacy one-off migrate using stock PHP image (prefer `./docker-dev.sh` / `docker/dev` image):

```bash
docker run --rm \
  --add-host=host.docker.internal:host-gateway \
  -u "$(id -u):$(id -g)" \
  -v "$PWD:/app" \
  -w /app \
  php:8.4-cli bash -lc \
  "docker-php-ext-install pdo_mysql mysqli >/dev/null 2>&1 || true; php artisan migrate"
```

**Alternative:** run PHP with **`--network host`** so `DB_HOST=127.0.0.1` matches MariaDB on the host:

```bash
docker run --rm --network host \
  -u "$(id -u):$(id -g)" \
  -v "$PWD:/app" \
  -w /app \
  php:8.4-cli bash -lc \
  "docker-php-ext-install pdo_mysql mysqli >/dev/null 2>&1 || true; php artisan migrate"
```

---

### 5. Local web server (`php artisan serve`)

Expose port `8000` and listen on all interfaces inside the container:

```bash
docker run --rm -it \
  --add-host=host.docker.internal:host-gateway \
  -p 8000:8000 \
  -u "$(id -u):$(id -g)" \
  -v "$PWD:/app" \
  -w /app \
  php:8.4-cli bash -lc \
  "docker-php-ext-install pdo_mysql mysqli >/dev/null 2>&1 || true; php artisan serve --host=0.0.0.0 --port=8000 --no-reload"
```

Use **`--no-reload`** when DB credentials come from Docker **`-e` / `environment`** (not only `.env`). Laravel’s dev server otherwise spawns a child PHP process that receives only a short env allowlist and falls back to **`.env`** (`DB_HOST=127.0.0.1`), which breaks DB inside the container.

Frontend assets (Vite) still run on the host unless you containerize Node as well:

```bash
npm install
npm run dev
```

---

### 6. PHP dev image used by `./docker-dev.sh`

The repo ships **`docker/dev/Dockerfile`** (PHP 8.4 + Composer + `pdo_mysql`, `intl`, `zip`, `soap`, `gd`, `exif`, etc.). Rebuild after changing it:

```bash
./docker-dev.sh --build
```

---

### 7. Optional: Laravel Sail (full Docker stack)

After `composer install` works, Sail can manage PHP, MySQL, Redis, etc.:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 php artisan sail:install
```

Follow Sail’s prompts, then use `./vendor/bin/sail up`. See [Laravel Sail docs](https://laravel.com/docs/sail).

---

## Quick reference

| Task              | Approach                                      |
|-------------------|-----------------------------------------------|
| Daily dev         | `./docker-dev.sh`                             |
| Install deps      | `docker run … composer:2 install`             |
| Artisan / migrate | `./docker-dev.sh --migrate` or `./docker-dev.sh --migrate-fresh` (wipes DB) or `docker/dev` image |
| DB                | MariaDB service **`mariadb`** in compose; credentials from `.env` |
| Ship to prod      | Match server PHP **8.4** (lock uses Symfony 8) + extensions **soap**, **gd**, **exif** |

---

## Troubleshooting

- **`Host: 127.0.0.1` / `SQLSTATE[HY000] [2002]` inside the Compose `app` service:** Laravel **`php artisan serve`** (without **`--no-reload`**) forwards only a small env list to the built-in server’s worker when **`.env`** exists, so Compose **`DB_HOST=mariadb`** never reaches PHP and the app uses **`.env`**’s **`127.0.0.1`**. **`docker-compose.dev.yml`** uses **`--no-reload`** for this reason. If PHP runs **on your machine** instead, use **`DB_HOST=127.0.0.1`** and **`DB_PORT`** = **`MYSQL_PUBLISH_PORT`** (default **3307**) and keep MariaDB running (`./docker-dev.sh --db-only` or full stack). Clear **`bootstrap/cache/config.php`** if you ran **`config:cache`**. Ensure **`mariadb`** is healthy in **`docker compose ps`**.
- **`address already in use` on `0.0.0.0:3306`:** Your host already runs MariaDB/MySQL on port 3306. This stack maps Docker MariaDB to **`localhost:3307`** by default (`MYSQL_PUBLISH_PORT`). Optionally set **`MYSQL_PUBLISH_PORT=3309`** (or any free port) in `.env`. Inside the **`app`** container, **`DB_PORT` stays `3306`** (Docker network to `mariadb`).
- **`Access denied for user`:** `DB_USERNAME` / `DB_PASSWORD` in `.env` must match **`MYSQL_USER` / `MYSQL_PASSWORD`** Compose passes into MariaDB. **`DB_USERNAME=root`** is invalid for `MYSQL_USER`; use e.g. **`othbar`**.
- **`composer install` fails with zip/memory:** add `-e COMPOSER_MEMORY_LIMIT=-1` or raise Docker memory.
- **`pdo_mysql` missing:** rebuild `./docker-dev.sh --build` or use `docker/dev/Dockerfile`; manual snippets below install extensions per-run.
- **`migrate` fails with `Table 'sh_…' already exists`:** the database schema and Laravel’s **`migrations`** table are out of sync (often from an interrupted run: MySQL DDL auto-commits, so tables can exist while the migration was never recorded). For **local/dev** data you can recreate the schema (**destroys all data in that DB**): `./docker-dev.sh --migrate-fresh`, or equivalently `docker compose -f docker-compose.dev.yml run --rm app php artisan migrate:fresh --force`. Optionally reset the volume entirely: `docker compose -f docker-compose.dev.yml down -v` then bring the stack back up and run migrations again.
