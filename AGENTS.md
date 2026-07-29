# AI Code Generation Guidelines

## 1. Architectural Constraints
* Enforce unidirectional workflows using explicit domain state machines.
* Prevent invalid state transitions at the domain layer.
* Track immutable historical logs and side effects using model observers.
* Validate all incoming HTTP payloads at the perimeter using Form Requests.

## 2. Security Mandates
* Protect every endpoint using Authorization Policies and Spatie RBAC.
* Ensure tenant isolation mathematically; cross-tenant data access must fail with a 403 Forbidden.
* Never expose unauthenticated administrative, migration, or seeding endpoints.

## 3. Code Quality Rules
* Do not include code comments.
* Use strict typing and explicit return types.
