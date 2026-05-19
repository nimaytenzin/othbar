# Othbar

Laravel application with a Filament admin panel at `/admin` (see `.env.example` for local URLs and admin seed defaults).

---

## Pushing changes to GitHub

### First-time setup

1. Create an empty repository on GitHub (no README/license if you already have this project locally).
2. In your project folder:

   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
   git branch -M main
   git push -u origin main
   ```

   Use SSH instead if you prefer:

   ```bash
   git remote add origin git@github.com:YOUR_USERNAME/YOUR_REPO.git
   ```

### Day-to-day workflow

```bash
git status
git add .
git commit -m "Describe your change clearly."
git push origin main
```

Use a feature branch when appropriate:

```bash
git checkout -b feature/your-feature
# ... commit work ...
git push -u origin feature/your-feature
```

Then open a pull request on GitHub.

---

## Creating a MySQL database in shared hosting (cPanel)

Exact labels vary slightly between hosts, but the flow is the same.

1. Log in to **cPanel**.
2. Open **MySQL® Databases** (or **MySQL Database Wizard**).
3. **Create a new database**  
   Enter a name (cPanel often prefixes it with your account username, e.g. `username_othbar`).
4. **Create a MySQL user**  
   Choose a strong password. Note the full username cPanel shows (often prefixed).
5. **Add the user to the database**  
   Select the user and database, then **Add**. Grant **All Privileges** for a typical Laravel app.
6. Note these values for `.env` on the server:
   - **Host**: Usually `localhost`. Some hosts use `127.0.0.1` or a remote hostname shown in cPanel—use whatever they document.
   - **Database name**: Full name including prefix.
   - **Username**: Full username including prefix.
   - **Password**: The one you set.

Optional: **phpMyAdmin** in cPanel can import a `.sql` dump if you are migrating data manually.

---

## Deploying on shared hosting (cPanel)

These steps assume **PHP 8.2+**, **Composer**, and **SSH access** if possible. Many hosts offer **Terminal** or **SSH Access** in cPanel; without SSH you may need **Git™ Version Control** and manual uploads—adapt accordingly.

### 1. PHP extensions

Ensure extensions Laravel needs are enabled (common ones: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `intl`). Enable **PHP version** 8.2 or newer in **MultiPHP Manager** (or equivalent).

### 2. Get the code onto the server

**Option A — Git (recommended if available)**  

In your hosting account (often above `public_html`), clone:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git othbar
cd othbar
```

**Option B — Upload**  

Upload a deployment archive or sync via FTP/SFTP. Do **not** rely on uploading `node_modules`; build assets on your machine (below) and deploy built files.

### 3. Document root (important)

Laravel’s web root must be the **`public`** directory, not the project root.

- **Best**: Point the domain’s document root to `.../othbar/public` (many hosts let you set this in **Domains** / **Addon Domains**).
- **Alternative**: Move only `public` contents into `public_html` and adjust `index.php` paths (only if your host forces `public_html` as doc root—see Laravel docs for the two-line bootstrap path fix).

### 4. Install PHP dependencies (production)

```bash
cd /path/to/othbar
composer install --no-dev --optimize-autoloader
```

### 5. Environment file

```bash
cp .env.example .env
nano .env   # or use cPanel File Manager editor
```

Set at minimum:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `APP_KEY=` → run `php artisan key:generate` once on the server.
- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` from the cPanel database steps above.

### 6. Frontend assets

Build locally (or on CI), then deploy the built files:

```bash
npm ci
npm run build
```

Commit `public/build` if your workflow deploys from Git, or upload `public/build` after building.

### 7. Database schema

```bash
php artisan migrate --force
```

Seed only if you intend to (e.g. admin user); avoid running seeders that wipe production data.

### 8. Storage and caches

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ensure `storage/` and `bootstrap/cache/` are writable by the web server (often `755` for directories and `644` for files; some hosts need ownership/group tweaks).

### 9. Queue and cron (optional)

If you use database queues, add a **cron** job in cPanel (every minute):

```text
* * * * * cd /path/to/othbar && php artisan schedule:run >> /dev/null 2>&1
```

Consult Laravel’s scheduler and queue docs if you rely on background workers—shared hosts often only support cron-driven `schedule:run` unless they provide queue workers.

### 10. HTTPS

Enable **SSL/TLS** (Let’s Encrypt or provider certificate) in cPanel so `APP_URL` uses `https://`.

---

## Local development

See `.env.example` and project scripts in `composer.json` (`setup`, `dev`). For Docker-specific notes, see `DOCKER.md` if present.

---

## Laravel

This application is built with [Laravel](https://laravel.com). Framework documentation: [https://laravel.com/docs](https://laravel.com/docs).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
