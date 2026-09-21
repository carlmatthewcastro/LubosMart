# System Architecture

## System Overview

A multi-domain e-commerce platform utilizing a Laravel application architecture. It features a single centralized backend API serving shared React surfaces for the four LuboSmart user roles.

The four user roles are Buyer, Seller, Courier, and Admin. The Buyer storefront, Seller workspace, Admin console, and Courier task experience are React surfaces backed by the same Laravel API. Shipment operations are an Admin-owned operational capability, not a fifth user role.

* **Authentication:** Laravel Sanctum
* **Architecture Pattern:** Monorepo (workspace)
* **Package Manager:** npm

## Technology Stack

| Layer | Technologies |
| --- | --- |
| **API (Backend)** | Laravel, Eloquent, PHP |
| **Storefront and role surfaces** | React, JavaScript, HTML, Tailwind CSS, Lucide icons |
| **Database** | MySQL 8.0+ |
| **Blob Storage** | Azure Blob Storage |

## Application Structure

The application is managed with Laravel, npm, and Docker Compose. React screens and shared resources live under the Laravel frontend source tree.
```text
/
├── app/                 # Laravel controllers, policies, services, and models
├── database/            # MySQL migrations, factories, and seeders
├── resources/js/        # React Buyer, Seller, Courier, and Admin surfaces
├── resources/css/       # Tailwind CSS and LuboSmart design tokens
├── routes/api.php       # Versioned Laravel API
├── routes/web.php       # Web entry points
├── tests/               # Backend and frontend-facing contract tests
└── docker-compose.yml   # Laravel, MySQL, worker, and frontend services
```
```

## Backend Architecture (Laravel)

### API Routing

* **Versioning Strategy:** All endpoints must be prefixed with `/api/v1/`.

### Strict Namespacing Rules

All role-specific classes MUST be scoped to their respective domains. **Do NOT cross-import role-specific classes** (e.g., never use `App\Enums\Admin\*` inside Buyer logic).

* **Controllers:** `App\Http\Controllers\{Role}\{Name}Controller`
* **Requests:** `App\Http\Requests\{Role}\{Name}Request`
* **Resources:** `App\Http\Resources\{Role}\{Name}Resource`
* **Enums (Role-specific):** `App\Enums\{Role}\{Name}`
* **Enums (Shared/Global):** `App\Enums\{Name}`

## Database Architecture

**Engine:** MySQL 8.0+ (Dockerized through Docker Compose)

**Migration Convention (Enums):**
Due to native enum column type errors in MySQL migrations, database columns must be defined as `string` in the migration files. The application will handle strict typing by casting to Enums at the API/Eloquent layer.

## Infrastructure & Environments

### Local Development

* **Database:** Hosted inside a local Docker container (`docker-compose.yml`).
* **Dependencies:** Runs on the local machine's native PHP, Composer, and Node environments.
* **Process launcher:** Root `npm dev` starts the Laravel HTTP server, database queue worker, Laravel scheduler, and all four current frontend applications (`webapp`, `seller`, `admin`, and `logistics`). The queue worker persists asynchronous notifications and audit events; the scheduler redispatches recoverable pending audit outbox events.

### Production Strategy

* **Frontend Hosting (Docker-hosted frontend):** All frontend applications (`webapp`, `seller`, `admin`, `logistics`) are deployed to Docker-hosted frontend.
* **Backend Hosting (Docker host):** The API is hosted on an Azure Virtual Machine using `docker-compose.yml`.
* **Container Stack:** PHP-FPM, Nginx, MySQL, Laravel queue worker/scheduler, Cloudflare Tunnel.
* **Ingress:** Cloudflare Tunnel is the only public ingress. Nginx, PHP-FPM, and MySQL have no host-published ports; Cloudflare terminates public HTTPS for the configured API hostname.

* **Domain Routing:**
* Storefront (`webapp`) uses the root domain.
* All dashboards and the API are routed via dedicated subdomains.
