# Multi-Tenant B2B Feature Flag & Metering API

## 1. Executive Summary
A high-performance infrastructure SaaS that allows B2B organizations to manage feature toggles and meter API usage across multiple environments (Development, Staging, Production). Client applications communicate with this engine via sub-millisecond API endpoints secured by environment-scoped, hashed access tokens.

## 2. Core Domain Entities
* **Tenant (Organization):** The top-level billing and isolation boundary.
* **User:** Dashboard administrators who manage the tenant via Spatie RBAC (Owner, Developer, Viewer).
* **Project:** Logical groupings of features (e.g., "Mobile App", "Web Dashboard").
* **Environment:** Deployment targets (e.g., "Production", "Staging") belonging to a Project.
* **ApiKey:** Hashed, environment-specific Sanctum tokens used by external SDKs/APIs.
* **FeatureFlag:** The actual boolean or multivariate toggles.
* **UsageLog:** High-throughput ledger for metering API evaluations.

## 3. Request Lifecycle & Perimeter Security
1. External application sends `GET /api/v1/flags` with an `Authorization: Bearer <api_key>` header.
2. Laravel Sanctum authenticates the token and identifies the specific `Environment` and `Tenant`.
3. A custom Authorization Gate mathematically ensures the token cannot access flags from a different tenant.
4. The system evaluates the flags (falling back to a Cache layer for sub-millisecond response times).
5. A background event is dispatched to increment the atomic `UsageLog` counter without blocking the API response.

## 4. Technology Stack
* **Framework:** Laravel 11.x (PHP 8.4)
* **Database:** SQLite (MVP / Edge deployment) transitioning to PostgreSQL.
* **Auth:** Laravel Sanctum (API Keys) + Spatie Permission (Dashboard RBAC).
* **Caching:** Redis / Laravel Cache.
