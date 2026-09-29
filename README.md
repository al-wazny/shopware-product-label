# UggProductLabel — Setup

## Prerequisites

- Docker & Docker Compose (v2, i.e. the `docker compose` subcommand, not the legacy `docker-compose` binary)
- Git

Nothing else needs to be installed on your host — PHP, MySQL/MariaDB, Node.js, and Composer all run inside the containers.

## Quickstart

```bash
git clone <your-repo-url>
cd <repo-folder>
docker compose up -d
```

That's it for the containers. Give it a minute on first run — the app container's entrypoint installs Composer dependencies, runs migrations, and installs/activates the plugin automatically the first time it starts.

Once the containers report healthy:

```bash
docker compose ps
```

- Storefront: http://localhost:8000 (adjust the port if you changed it in `compose.yaml`)
- Admin panel: http://localhost:8000/admin
  - Default credentials: `admin` / `shopware` (change these if you're not running this purely locally)

## If the automatic install didn't run (fallback)

If this is your first time standing up the project, or the entrypoint's automatic install step didn't fire for any reason, run these manually inside the app container:

```bash
docker compose exec app bash

composer install
bin/console plugin:refresh
bin/console plugin:install --activate UggProductLabel
bin/console cache:clear
```

## Building the admin UI

The admin build is baked into the image at build time, so a normal `docker compose up -d` already includes it — you shouldn't need to do anything here. If you've made changes to the plugin's admin source and want to see them without rebuilding the image:

```bash
docker compose exec app bin/build-administration.sh
```

> **Node version note:** Shopware 6.5's admin build requires Node `^18 || ^19 || ^20`. The app image pins Node 20 for this reason — if you're running the build manually outside the container, or the container's Node version has drifted (e.g. after a base-image update), `bin/build-administration.sh` will fail with an `EBADENGINE` error. Rebuild the image (`docker compose build --no-cache app`) to restore the pinned version rather than installing Node manually inside a running container.

## Running the test suite

```bash
docker compose exec app bin/phpunit -c custom/plugins/UggProductLabel/phpunit.xml
```

This runs both the integration suite (product_label repository, migrations, translations) and the unit suite (storefront label resolution logic).

## Stopping / resetting

```bash
docker compose down          # stop containers, keep volumes (DB data persists)
docker compose down -v       # stop containers AND wipe volumes (full reset, re-runs install from scratch next `up`)
```
